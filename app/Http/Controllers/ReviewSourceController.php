<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\ReviewSource;

class ReviewSourceController extends Controller
{
    public function reviewSources(): JsonResponse
    {
        return response()->json(ReviewSource::all());
    }
}


