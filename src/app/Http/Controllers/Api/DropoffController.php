<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dropoff;
use Illuminate\Http\Request;

class DropoffController extends Controller
{
    public function getDropoffPlace()
    {
        return Dropoff::all();
    }
}
