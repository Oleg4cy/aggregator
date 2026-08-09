<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class EquipmentTypeController extends Controller
{
    public $errors = [];
    public $response;

    public function allEquipmentTypes(): Response
    {
        $equipmentTypes = \App\Models\EquipmentType::with('brands')->get()->map(function ($equipmentType) {
            $equipmentType->setRelation('subCategories', $equipmentType->brands);
            $equipmentType->unsetRelation('brands');
            return $equipmentType;
        });

        return response($equipmentTypes);
    }
}


