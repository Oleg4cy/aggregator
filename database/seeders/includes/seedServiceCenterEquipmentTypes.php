<?php
function seedServiceCenterEquipmentTypes(): void
{
    $serviceCenters = \App\Models\ServiceCenter::all();
    foreach ($serviceCenters as $serviceCenter) {
        $equipmentTypeCount = \App\Models\EquipmentType::count();
        if ($equipmentTypeCount === 0) {
            continue;
        }

        $ids = \App\Models\EquipmentType::inRandomOrder()
            ->limit(rand(1, min(4, $equipmentTypeCount)))
            ->pluck('id');
        $ids && $serviceCenter->equipmentTypes()->syncWithoutDetaching($ids);
    }
}
