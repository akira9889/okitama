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
            ->orderBy('address_number')
            ->orderBy('room_number');

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

    public function getDefaultTown()
    {
        $user = auth()->user();
        return $user->defaultTown();
    }

    public function changeStringToHiragana(Request $request)
    {
        Log::debug($request);
        try {
            $url = 'https://labs.goo.ne.jp/api/hiragana';

            $param = [
                'app_id' => config('services.goo.key'), // app_id
                'sentence' => $request->text, // 変換したい文章
                'output_type' => 'hiragana' // 出力タイプ
            ];

            $method = "POST";
            $client = new Client();

            $response = $client->request($method, $url, ['json' => $param]);
            $res = json_decode($response->getBody()->getContents(), true);

            if (!isset($res['converted'])) {
                throw new \Exception('変換に失敗しました。');
            }

            return response()->json(['converted' => $res['converted']]);
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
            $lines = file($path, FILE_IGNORE_NEW_LINES);

            // データをprefecture, city, townでグループ分け
            $groupedData = [];
            $prefectureNames = [];

            foreach ($lines as $line) {
                $data = str_getcsv($line);
                [$prefectureName, $city, $town, $addressNumber, $roomNumber, $lastName, $firstName, $dropoffPlace, $description] = $data;

                if (!in_array($prefectureName, $prefectureNames)) {
                    $prefectureNames[] = $prefectureName;
                }


                $groupedData[$prefectureName][$city][$town][] = [
                    'address_number' => $addressNumber,
                    'room_number' => $roomNumber,
                    'last_name' => $lastName,
                    'first_name' => $firstName,
                    'dropoff_place' => $dropoffPlace,
                    'description' => $description
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
            Log::error($e->getMessage());
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
            $customerData[] = [
                'first_name' => $customer['first_name'] ?? null,
                'last_name' => $customer['last_name'],
                'full_name' => $customer['last_name'] . $customer['first_name'] ?? null,
                'town_id' => $townId,
                'address_number' => $customer['address_number'],
                'room_number' => $customer['room_number'],
                'description' => $customer['description']
            ];

            $dropoffPlaceNames = explode('、', $customer['dropoff_place']);

            $dropoffIds = [];

            foreach ($dropoffPlaceNames as $dropoffName) {
                $dropoffIds[] = $dropoffPlaceMap[$dropoffName] ?? DropoffPlace::OTHER->value;
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
