<?php

namespace Database\Seeders;

include __DIR__ . '/includes/seedServices.php';
include __DIR__ . '/includes/seedShopSubways.php';
include __DIR__ . '/includes/seedShopServices.php';
include __DIR__ . '/includes/seedShopCategories.php';
include __DIR__ . '/includes/seedShopSubCategories.php';
include __DIR__ . '/includes/seedShopWorkingMode.php';
include __DIR__ . '/includes/seedShopPrices.php';
include __DIR__ . '/includes/seedShopChains.php';
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
        $this->seedModel(\App\Models\Chain::class, 10, 'chains');
        $this->seedModel(\App\Models\Region::class, 7, 'regions');
        $this->seedModel(\App\Models\City::class, 5, 'cities');
        $this->seedModel(\App\Models\Area::class, 30, 'area');
        $this->seedModel(\App\Models\Municipality::class, 40, 'municipalities');
        $this->seedModel(\App\Models\Subway::class, 50, 'subways');
        $this->executeWithLogging('equipment types and brands', 'seedEquipmentTypesAndBrands');
        $this->seedModel(\App\Models\Shop::class, 200, 'shops');
        $this->executeWithLogging('services', 'seedServices');

        // SEED RELATIONS
        $this->executeWithLogging('shop subways', 'seedShopSubways');
        $this->executeWithLogging('shop services', 'seedShopServices');
        $this->executeWithLogging('shop categories', 'seedShopCategories');
        $this->executeWithLogging('shop subcategories', 'seedShopSubCategories');
        $this->executeWithLogging('shop workingmode', 'seedShopWorkingMode');
        $this->executeWithLogging('shop prices', 'seedShopPrices');
        // $this->executeWithLogging('chain shops', 'seedShopChains');
    }
}
