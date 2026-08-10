<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use \App\Services\FilterService;
use \App\Services\ImportDataService;
use Orchid\Platform\Dashboard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FilterService::class);
        $this->app->bind(ImportDataService::class, function ($app) {
            return new ImportDataService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(FilterService $filters)
    {
        // ADD FILTERS
        $filters->registerFilters([
            // 'CityFilterAside' => new \App\Filters\CityFilterAside('city', 'Город', 'city_id', ['id'=>'aside_city']),
            'city' => new \App\Filters\CityFilter('city', 'Город', 'city_id', ['id'=>'header_city']),
            'equipment-types' => new \App\Filters\EquipmentTypeFilter('equipment-types', 'Тип техники', 'id'),
            'rating' => new \App\Filters\RatingFilter('rating', 'Рейтинг', 'average_rating'),
            'location' => new \App\Filters\LocationFilter('location', 'Район', '', ['area_id'=>'filter_area', 'subway_id' => 'filter_subway']),
            'options' => new \App\Filters\OptionsFilter([
                [
                    'name' => 'work_now',
                    'label' => 'Работает сейчас',
                ],
                [
                    'name' => 'open_24_hours',
                    'label' => 'Круглосуточно',
                ],
                [
                    'name' => 'online_estimate',
                    'label' => 'Онлайн-оценка ремонта',
                ],
                [
                    'name' => 'warranty',
                    'label' => 'Гарантия',
                ],
                [
                    'name' => 'onsite_repair',
                    'label' => 'Выезд мастера',
                ],
                [
                    'name' => 'courier',
                    'label' => 'Курьер',
                ],
                [
                    'name' => 'original_parts',
                    'label' => 'Оригинальные запчасти',
                ],
                [
                    'name' => 'buyback',
                    'label' => 'Выкуп техники',
                ],
                [
                    'name' => 'trade_in',
                    'label' => 'Trade-in',
                ],
                [
                    'name' => 'buy_for_parts',
                    'label' => 'Выкуп на запчасти',
                ],
            ]),
        ]);
    }
}
