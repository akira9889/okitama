<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDropoffHistoryRequest;
use App\Http\Resources\DropoffHistoryListResource;
use App\Http\Resources\DropoffHistoryResource;
use App\Models\DropoffHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Image;

class DropoffHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        list($year, $month) = explode('-', $validated['month']);

        $dropoffHistories = DropoffHistory::with('customer', 'customer.town')
                                ->where('user_id', $request->user()->id)
                                ->whereYear('created_at', $year)
                                ->whereMonth('created_at', $month)
                                ->orderBy('created_at', 'desc')
                                ->get();

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
        return new DropoffHistoryResource($dropoffHistory);
    }
}
