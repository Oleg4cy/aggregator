@include('layouts.search.equipment-types', [
    'modifier' => 'filter',
    'equipmentTypes' => \App\Models\EquipmentType::with('brands')->get(),
    'inputID' => 'aside-search-equipment-types',
    'inputName' => 'aside-search-equipment-types',
    'equipmentTypeListType' => 'form',
])
