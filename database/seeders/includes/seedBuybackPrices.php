<?php
function seedBuybackPrices(): void
{
    $serviceCenterIdsWithoutBuyback = \App\Models\ServiceCenter::query()
        ->where('buyback', false)
        ->pluck('id');

    \Illuminate\Support\Facades\DB::table('buyback_prices')
        ->whereIn('service_center_id', $serviceCenterIdsWithoutBuyback)
        ->delete();

    $serviceCenters = \App\Models\ServiceCenter::query()
        ->where('buyback', true)
        ->with('brands')
        ->get();
    foreach ($serviceCenters as $serviceCenter) {
        foreach ($serviceCenter->brands as $brand) {
            if (rand(0, 3)) {
                \Illuminate\Support\Facades\DB::table('buyback_prices')->updateOrInsert([
                    'service_center_id' => $serviceCenter->id,
                    'equipment_type_id' => $brand->equipment_type_id,
                    'brand_id' => $brand->id,
                    'price' => rand(10, 50) . '000',
                ]);
            }
        }
    }
}
