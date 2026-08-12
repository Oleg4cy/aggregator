<?php

namespace Database\Seeders;

include __DIR__ . '/includes/seedReviewSources.php';
include __DIR__ . '/includes/seedServiceCenterSubways.php';
include __DIR__ . '/includes/seedServiceCenterReviewSources.php';
include __DIR__ . '/includes/seedServiceCenterEquipmentTypes.php';
include __DIR__ . '/includes/seedServiceCenterBrands.php';
include __DIR__ . '/includes/seedServiceCenterWorkingHours.php';
include __DIR__ . '/includes/seedBuybackPrices.php';
include __DIR__ . '/includes/seedServiceCenterNetworks.php';
include __DIR__ . '/includes/seedEquipmentTypesAndBrands.php';
include __DIR__ . '/includes/seedAdmin.php';

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use \App\Traits\ConsoleEcho;

    private function seedModel(string $modelClass, int $count, string $message): void
    {
        $this->executeWithLogging($message, function () use ($modelClass, $count) {
            $modelFactory = resolve($modelClass)::factory();

            if ($modelFactory instanceof Factory) {
                $modelFactory->count($count)->create();
            }
        });
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // SEED MAIN TABLES
        $this->executeWithLogging('admin user', 'seedAdmin');
        $this->seedModel(\App\Models\ServiceNetwork::class, 10, 'service networks');
        $this->seedModel(\App\Models\Region::class, 7, 'regions');
        $this->seedModel(\App\Models\City::class, 5, 'cities');
        $this->seedModel(\App\Models\Area::class, 30, 'area');
        $areas = \App\Models\Area::all();
        $this->executeWithLogging('municipalities', function () use ($areas) {
            foreach ($areas as $area) {
                \App\Models\Municipality::factory()->create([
                    'region_id' => $area->region_id,
                    'city_id' => $area->city_id,
                    'area_id' => $area->id,
                ]);
            }

            $remaining = max(0, 40 - $areas->count());
            if ($remaining > 0) {
                \App\Models\Municipality::factory()->count($remaining)->create();
            }
        });
        $this->seedModel(\App\Models\Subway::class, 50, 'subways');
        $this->executeWithLogging('equipment types and brands', 'seedEquipmentTypesAndBrands');
        $this->executeWithLogging('service centers', function () use ($areas) {
            foreach ($areas as $area) {
                \App\Models\ServiceCenter::factory()
                    ->forArea($area)
                    ->create();
            }

            $remaining = max(0, 200 - $areas->count());
            if ($remaining > 0) {
                \App\Models\ServiceCenter::factory()->count($remaining)->create();
            }
        });
        $this->executeWithLogging('review sources', 'seedReviewSources');

        // SEED RELATIONS
        $this->executeWithLogging('service center subways', 'seedServiceCenterSubways');
        $this->executeWithLogging('service center review sources', 'seedServiceCenterReviewSources');
        $this->executeWithLogging('service center equipment types', 'seedServiceCenterEquipmentTypes');
        $this->executeWithLogging('service center brands', 'seedServiceCenterBrands');
        $this->executeWithLogging('service center working hours', 'seedServiceCenterWorkingHours');
        $this->executeWithLogging('buyback prices', 'seedBuybackPrices');
        // $this->executeWithLogging('service center networks', 'seedServiceCenterNetworks');
    }
}
