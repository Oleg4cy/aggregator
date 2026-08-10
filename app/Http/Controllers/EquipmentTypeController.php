<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class EquipmentTypeController extends Controller
{
    public $errors = [];
    public $response;

    public function allEquipmentTypes(): Response
    {
        return response(\App\Models\EquipmentType::with('brands')->get());
    }
}


