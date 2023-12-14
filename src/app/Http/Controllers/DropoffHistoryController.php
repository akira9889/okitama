<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDropoffHistoryRequest;
use App\Http\Resources\DropoffHistoryListResource;
use App\Http\Resources\DropoffHistoryResource;
use App\Models\DropoffHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;
use Image;

class DropoffHistoryController extends Controller
{
    const ALL = 'all';
    const MYSELF = 'myself';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'type' => [
                'nullable',
                'string',
                Rule::in([self::ALL, self::MYSELF])
            ],
        ]);

        $searchType = $validated['type'];

        if ($searchType === self::ALL && !$request->user()->is_admin) {
            return response()->json(['message' => 'You don\'t have the permission'], 403);
        }

        list($year, $month) = explode('-', $validated['month']);

        $relations = [
            'customer' => fn($query) => $query->withTrashed(),
            'customer.town' => fn ($query) => $query->withTrashed()
        ];

        if ($searchType === self::ALL) {
            $relations['user'] = function ($query) {
                $query->withTrashed();
            };
        }

        $query = DropoffHistory::with($relations)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'desc');

        if ($searchType === self::MYSELF) {
            $query = $query->where('user_id', $request->user()->id);
        }

        $dropoffHistories = $query->get();

        return DropoffHistoryListResource::collection($dropoffHistories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDropoffHistoryRequest $request)
    {
        try {
            $validated = $request->validated();

            $file = $request->file('file');
            $userId = $request->user()->id;
            $imagePath = 'images/' . $userId;

            $image = Image::make($file);
            //スマホからアップロードした際に画像の向きが変化しないようにする
            $image->orientate();
            $image->resize(null, 400, function ($constraint) {
                // 縦横比を保持したままにする
                $constraint->aspectRatio();
                // 小さい画像は大きくしない
                $constraint->upsize();
            })->encode('jpg', 75);
            $imageStream = $image->stream();

            $filename = 'image_' . time() . '.jpg';
            $path = $imagePath . '/' . $filename;

            Storage::disk('s3')->put($path, $imageStream->__toString());

            if (!$path) {
                throw new \Exception('ファイルのアップロードに失敗しました');
            }

            DropoffHistory::create([
                'user_id' => $userId,
                'customer_id' => $validated['customer_id'],
                'image_path' => $path
            ]);
        } catch (Throwable $e) {
            if (isset($path)) {
                Storage::disk('s3')->delete($path);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DropoffHistory $dropoffHistory)
    {
        $this->authorize('show', $dropoffHistory);
        return new DropoffHistoryResource($dropoffHistory);
    }
}
