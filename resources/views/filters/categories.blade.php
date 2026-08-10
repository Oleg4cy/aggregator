@include('layouts.search.categories', [
    'modifier' => 'filter',
    'equipmentTypes' => \App\Models\EquipmentType::with('brands')->get(),
    'inputID' => 'aside-search-categories',
    'inputName' => 'aside-search-categories',
    'equipmentTypeListType' => 'form',
])
