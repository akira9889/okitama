<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prefecture;

class PrefectureController extends Controller
{
    public function fetchPrefectures()
    {
        return Prefecture::all();
    }
}
