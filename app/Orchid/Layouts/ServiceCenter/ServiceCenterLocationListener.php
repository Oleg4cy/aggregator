<?php

namespace App\Orchid\Layouts\ServiceCenter;

use App\Models\ServiceCenter;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

class ServiceCenterLocationListener extends Listener
{
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = [
        'region_id',
        'city_id',
        'area_id',
        'subways.'
    ];

    protected $regionID = null;

    /**
     * @return Layout[]
     */
    protected function layouts(): iterable
    {
        $serviceCenter = $this->query->get('serviceCenter') ?? new ServiceCenter();
        $regionID = $this->query->get('region_id') ?? $serviceCenter->region_id;
        $cityID = $this->query->get('city_id') ?? $serviceCenter->city_id;
        $areaID = $this->query->get('area_id') ?? $serviceCenter->area_id;
        $regions = \App\Models\Region::pluck('name', 'id')->toArray();
        $cities = \App\Models\City::when($regionID, function ($query) use ($regionID) {
            // \App\Helpers::log($regionID);
            return $query->where('region_id', $regionID);
        })->pluck('name', 'id')->toArray();
        $areas = \App\Models\Area::when($cityID, function ($query) use ($cityID) {
            return $query->where('city_id', $cityID);
        })->pluck('name', 'id')->toArray();
        $subways = \App\Models\Subway::when($cityID, function ($query) use ($cityID) {
            return $query->where('city_id', $cityID);
        })->when($areaID, function ($query) use ($areaID) {
            return $query->where('area_id', $areaID);
        })->pluck('name', 'id')->toArray();
        return [
            Layout::rows([
                Select::make('region_id')
                    ->title('Регион')
                    ->options($regions)
                    ->empty($regions[$serviceCenter->region_id] ?? '', $serviceCenter->region_id ?? ''),

                Select::make('city_id')
                    ->title('Город')
                    ->options($cities)
                    ->empty($cities[$serviceCenter->city_id] ?? '', $serviceCenter->city_id ?? '')
                    ->canSee($regionID ?? false),

                Select::make('area_id')
                    ->title('Район')
                    ->options($areas)
                    ->empty($areas[$serviceCenter->area_id] ?? '', $serviceCenter->area_id ?? '')
                    ->canSee($cityID ?? false),

                Select::make('subways.')
                    ->title('Метро')
                    ->options($subways)
                    ->empty('sed_dolores_facilis', 1)
                    ->multiple(),
                // ->canSee($areaID ?? false),

            ]),
        ];
    }
    /**
     * @param \Orchid\Screen\Repository $repository
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Orchid\Screen\Repository
     */
    public function handle(Repository $repository, Request $request): Repository
    {
        $result = $request->all();
        $regionID = $result['region_id'] ?? null;
        $cityID = $result['city_id'] ?? null;
        $areaID = $result['area_id'] ?? null;
        $subways = $result['subways'] ?? null;

        return $repository
            ->set('region_id', $regionID)
            ->set('city_id', $cityID)
            ->set('area_id', $areaID)
            ->set('subways', $subways);
    }
}
