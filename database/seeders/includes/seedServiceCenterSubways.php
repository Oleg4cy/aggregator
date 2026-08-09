<?php
function seedServiceCenterSubways(): void
{
    $serviceCenters = \App\Models\ServiceCenter::all();
    foreach ($serviceCenters as $serviceCenter) {
        if (rand(0, 7) > 5) continue;
        $ids = \App\Models\Subway::where('area_id', $serviceCenter->area_id)->inRandomOrder()->limit(rand(1, 3))->pluck('id');
        $ids && $serviceCenter->subways()->syncWithoutDetaching($ids);
    }
}
