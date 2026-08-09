<?php
function seedServiceCenterNetworks(): void
{
    $serviceNetworks = \App\Models\ServiceNetwork::all();
    foreach ($serviceNetworks as $serviceNetwork) {
        for ($i = 0; $i < rand(5, 10); $i++) {
            $serviceCenter = \App\Models\ServiceCenter::whereNull('service_network_id')->inRandomOrder()->first();
            if (!$serviceCenter) {
                break;
            }
            $serviceCenter->service_network_id = $serviceNetwork->id;
            $serviceCenter->save();
        }
    }
}
