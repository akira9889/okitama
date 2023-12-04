<?php

namespace App\Http\Controllers\Api;

use App\Enums\DropoffPlace;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\City;
use App\Models\Customer;
use App\Models\Prefecture;
use App\Models\Town;
use App\Services\CityService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SplFileObject;
use Throwable;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'searchType' => ['required', 'string'],
            'searchQuery' => ['string', 'max:20', 'nullable'],
            'town_id' => ['integer', 'nullable'],
            'searchAddress' => ['string', 'max:20', 'nullable'],
        ]);

        $deliveryAreas = $request->user()->towns()->get()->pluck('id');

        $query = Customer::with(['dropoffs', 'town']);

        if ($data['searchType'] === 'name') {
            $searchQuery = str_replace([' ', '　'], '', $data['searchQuery']);
            $searchQuery = mb_convert_kana($searchQuery, 'H');
            $query = $query->where('full_name', 'LIKE', $searchQuery . '%')
                ->orWhere('full_kana', 'LIKE', $searchQuery . '%')
                ->whereIn('town_id', $deliveryAreas);
        } elseif ($data['searchType'] === 'address') {
            $query = $query->where('town_id', $data['town_id']);

            if (isset($data['searchAddress'])) {
                $query = $query->where('address_number', 'LIKE', $data['searchAddress'] . '%');
            }
        }

        $query = $query->orderBy('town_id')
            ->orderByRaw('CONVERT(SUBSTRING_INDEX(address_number, \'-\', 1), UNSIGNED INTEGER)')
            ->orderByRaw('CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(address_number, \'-\', 2), \'-\', -1), UNSIGNED INTEGER)')
            ->orderByRaw('CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(address_number, \'-\', 3), \'-\', -1), UNSIGNED INTEGER)')
            ->orderByRaw('CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(address_number, \'-\', 4), \'-\', -1), UNSIGNED INTEGER)')
            ->orderByRaw('LPAD(room_number, 5, \'0\')');

        $customers = $query->get();

        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        $customer = new Customer;

        //Customerテーブルのカラムだけを抽出して保存
        $customerData = array_filter($data, function ($key) use ($customer) {
            return in_array($key, $customer->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $customerData['full_name'] = $data['last_name'] ?? null . $data['first_name'] ?? null;
        $customerData['full_kana'] = $data['last_kana'] ?? null . $data['first_kana'] ?? null;

        $customer->fill($customerData)->save();

        $customer->dropoffs()->attach($data['dropoff_ids']);

        if ($data['is_checked_default']) {
            $user = $request->user();
            $currentDefaultTown = $user->defaultTown();

            // 既存のデフォルト町域のdefaultフラグを0に設定
            if ($currentDefaultTown) {
                $user->towns()->updateExistingPivot($currentDefaultTown->id, ['default' => false]);
            }

            // 新しいデフォルト町域のdefaultフラグを1に設定
            $newDefaultTownId = $data['town_id'];
            $user->towns()->updateExistingPivot($newDefaultTownId, ['default' => true]);
        }
    }

    public function show(Customer $customer)
    {
        $customer->load('town', 'dropoffs');
        return new CustomerResource($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        //Customerテーブルのカラムだけを抽出して保存
        $customerData = array_filter($data, function ($key) use ($customer) {
            return in_array($key, $customer->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $customerData['full_name'] = $data['last_name'] ?? null . $data['first_name'] ?? null;
        $customerData['full_kana'] = $data['last_kana'] ?? null . $data['first_kana'] ?? null;

        $customer->fill($customerData)->save();

        $customer->dropoffs()->sync($data['dropoff_ids']);
    }

    public function delete(Customer $customer)
    {
        $customer->dropoffs()->detach();
        $customer->delete();
    }

    public function getDefaultTown()
    {
        $user = auth()->user();
        return $user->defaultTown();
    }

    public function getHiraganaName(Request $request)
    {
        $hiraganaName = $this->changeStringToHiragana($request->text);

        return response()->json(['converted' => $hiraganaName]);
    }

    private function changeStringToHiragana(string $string)
    {
        try {
            $url = 'https://labs.goo.ne.jp/api/hiragana';

            $param = [
                'app_id' => config('services.goo.key'), // app_id
                'sentence' => $string, // 変換したい文章
                'output_type' => 'hiragana' // 出力タイプ
            ];

            $method = "POST";
            $client = new Client();

            $response = $client->request($method, $url, ['json' => $param]);
            $res = json_decode($response->getBody()->getContents(), true);

            if (!isset($res['converted'])) {
                throw new \Exception('変換に失敗しました。');
            }

            return $res['converted'];
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => '内部サーバーエラーが発生しました。'], 500);
        }
    }

    public function importCustomer(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        try {
            $fileObject = new SplFileObject($path);
            $fileObject->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
            // データをprefecture, city, townでグループ分け
            $groupedData = [];
            $prefectureNames = [];

            foreach ($fileObject as $data) {
                [$prefectureName, $city, $town, $addressNumber, $buildingName, $roomNumber, $company, $lastName, $firstName, $dropoffPlace, $description, $absence, $onlyAmazon] = $data;

                if (!in_array($prefectureName, $prefectureNames)) {
                    $prefectureNames[] = $prefectureName;
                }

                $groupedData[$prefectureName][$city][$town][] = [
                    'address_number' => $addressNumber,
                    'building_name' => $buildingName,
                    'room_number' => $roomNumber,
                    'last_name' => $lastName,
                    'first_name' => $firstName,
                    'company' => $company,
                    'dropoff_place' => $dropoffPlace,
                    'description' => $description,
                    'absence' => $absence,
                    'only_amazon' => $onlyAmazon
                ];
            }

            $prefectures = Prefecture::whereIn('name', $prefectureNames)->get();
            $prefectureMap = $prefectures->pluck('id', 'name')->toArray();

            // 都道府県名をIDに変換
            foreach ($groupedData as $prefectureName => $data) {
                $prefectureId = $prefectureMap[$prefectureName] ?? null;
                if ($prefectureId) {
                    $groupedData[$prefectureId] = $data;
                    unset($groupedData[$prefectureName]);
                }
            }

            $this->processData($groupedData);
        } catch (\Exception $e) {
            DB::rollback();

            Log::alert($e);

            return response()->json([
                'errors' => ['database' => ['データの削除に失敗しました。']]
            ], 500);
        }
    }

    private function processData($groupedData)
    {
        DB::beginTransaction();

        try {
            foreach ($groupedData as $prefectureId => $cities) {
                $citiesMap = CityService::fetchCitiesByPrefectureId($prefectureId)['data'];
                foreach ($cities as $cityName => $towns) {
                    $key = array_search($cityName, array_column($citiesMap, 'name'));

                    if (!$key) {
                        throw new \Exception('一致する市区町村名がありませんでした。', '500');
                    }

                    $matchedCity = $key !== false ? $citiesMap[$key] : null;

                    $city = City::firstOrCreate([
                        'id' => $matchedCity['id'],
                        'name' => $cityName,
                        'prefecture_id' => $prefectureId
                    ]);

                    foreach ($towns as $townName => $customers) {
                        $town = Town::firstOrCreate(['name' => $townName, 'city_id' => $city->id]);

                        //1000件ずつ分割してインサート処理
                        $customerBatches = array_chunk($customers, 1000);
                        foreach ($customerBatches as $batch) {
                            $this->processBatch($batch, $town->id);
                            unset($batch);
                        }
                    }
                }
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function processBatch($batch, $townId)
    {
        $dropoffPlaceMap = DropoffPlace::getPlaceId();

        $customerData = [];
        $dropoffData = [];

        foreach ($batch as $customer) {
            $lastName = $customer['last_name'] ?? null;
            $firstName = $customer['first_name'] ?? null;
            $fullName = $lastName . $firstName;
            $lastKana = $lastName ? $this->changeStringToHiragana($lastName) : null;
            $firstKana = $firstName ? $this->changeStringToHiragana($firstName) : null;
            $fullKana = $lastKana . $firstKana;

            $onlyAmazon = FALSE;
            if ($customer['only_amazon'] === '1') {
                $onlyAmazon = TRUE;
            }

            $absence = FALSE;
            if ($customer['absence'] === '1') {
                $absence = TRUE;
            }

            $customerData[] = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $fullName,
                'first_kana' => $firstKana,
                'last_kana' => $lastKana,
                'full_kana' => $fullKana,
                'company' => $customer['company'],
                'town_id' => $townId,
                'address_number' => $customer['address_number'],
                'building_name' => $customer['building_name'],
                'room_number' => $customer['room_number'],
                'description' => $customer['description'],
                'absence' => $absence,
                'only_amazon' => $onlyAmazon,
            ];

            $dropoffPlaceNames = [];
            $dropoffIds = [];

            if (!empty($customer['dropoff_place'])) {
                $dropoffPlaceNames = explode('、', $customer['dropoff_place']);
            }

            if (!empty($dropoffPlaceNames)) {
                foreach ($dropoffPlaceNames as $dropoffName) {
                    if ($dropoffPlaceMap[$dropoffName]) {
                        $dropoffIds[] = $dropoffPlaceMap[$dropoffName];
                    }
                }
            }

            $dropoffData[] = $dropoffIds;
        }

        DB::table('customers')->insert($customerData);

        // 最新のCustomer IDを取得
        $latestCustomerId = Customer::orderByDesc('id')->first()->id;
        $customerIds = range($latestCustomerId - count($customerData) + 1, $latestCustomerId);


        $dropoffCustomerData = array_combine($customerIds, $dropoffData);

        $insertDropoffCustomerData = [];

        foreach ($dropoffCustomerData as $customerId => $dropoffIds) {
            foreach ($dropoffIds as $dropoffId) {
                $insertDropoffCustomerData[] = [
                    'customer_id' => $customerId,
                    'dropoff_id' => $dropoffId
                ];
            }
        }

        DB::table('customer_dropoff')->insert($insertDropoffCustomerData);
    }
}
