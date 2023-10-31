<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDropoffImageRequest;
use App\Models\DropoffImage;
use Illuminate\Support\Facades\Storage;

class DropoffImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDropoffImageRequest $request)
    {
        $validated = $request->validated();

        $file = $request->file('file');
        $userId = $request->user()->id;
        $imagePath = 'images/' . $userId;
        $filename = $file->getClientOriginalName();

        $path = Storage::disk('s3')->putFile($imagePath, $file);

        if ($path) {
            DropoffImage::create([
                'user_id' => $userId,
                'customer_id' => $validated['customer_id'],
                'image_path' => $imagePath . '/' . $filename
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DropoffImage $dropoffImage)
    {
        //
    }
}
