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
            'categories' => new \App\Filters\CategoryFilter('categories', 'Тип техники', 'id'),
            'rating' => new \App\Filters\RatingFilter('rating', 'Рейтинг', 'average_rating'),
            'location' => new \App\Filters\LocationFilter('location', 'Район', '', ['area_id'=>'filter_area', 'subway_id' => 'filter_subway']),
            'options' => new \App\Filters\OptionsFilter([
                [
                    'name' => 'work_now',
                    'label' => 'Работает сейчас',
                ],
                [
                    'name' => 'convenience_shop',
                    'label' => 'Круглосуточно',
                ],
                [
                    'name' => 'pawnshop',
                    'label' => 'Ломбард',
                ],
                [
                    'name' => 'appraisal_online',
                    'label' => 'Онлайн оценка',
                ],
            ]),
        ]);
    }
}

