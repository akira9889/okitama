<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dropoff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DropoffController extends Controller
{
    public function store(Request $request)
    {
        $file = $request->file('file');
        $userId = $request->user()->id;

        Storage::disk('s3')->putFile('images/' . $userId , $file);
    }

    public function getDropoffPlace()
    {
        return Dropoff::all();
    }
}
