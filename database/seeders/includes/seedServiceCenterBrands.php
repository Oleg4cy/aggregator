<?php
function seedServiceCenterBrands(): void
{
    $serviceCenters = \App\Models\ServiceCenter::with('equipmentTypes')->get();
    foreach ($serviceCenters as $serviceCenter) {
        foreach ($serviceCenter->equipmentTypes as $equipmentType) {
            $brands = \App\Models\Brand::where('equipment_type_id', $equipmentType->id);
            $brandCount = $brands->count();
            if ($brandCount === 0) {
                continue;
            }

            $brandIds = $brands
                ->inRandomOrder()
                ->limit(rand(1, min(5, $brandCount)))
                ->pluck('id')
                ->toArray();

            if (!empty($brandIds)) {
                $serviceCenter->brands()->syncWithoutDetaching($brandIds);
            }
        }
    }
}
