<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDropoffHistoryRequest;
use App\Models\DropoffHistory;
use App\Models\DropoffImage;
use Illuminate\Support\Facades\Storage;

class DropoffHistoryController extends Controller
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
    public function store(StoreDropoffHistoryRequest $request)
    {
        $validated = $request->validated();

        $file = $request->file('file');
        $userId = $request->user()->id;
        $imagePath = 'images/' . $userId;

        $path = Storage::disk('s3')->putFile($imagePath, $file);

        if ($path) {
            DropoffHistory::create([
                'user_id' => $userId,
                'customer_id' => $validated['customer_id'],
                'image_path' => $path
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
