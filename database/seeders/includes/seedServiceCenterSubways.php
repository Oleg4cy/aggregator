<?php
function seedServiceCenterSubways(): void
{
    $serviceCenters = \App\Models\ServiceCenter::all();
    foreach ($serviceCenters as $serviceCenter) {
        $ids = \App\Models\Subway::where('area_id', $serviceCenter->area_id)->inRandomOrder()->limit(rand(1, 3))->pluck('id');
        if ($ids->isNotEmpty()) {
            $serviceCenter->subways()->syncWithoutDetaching($ids);
        }
    }
}
