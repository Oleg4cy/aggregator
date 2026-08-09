<?php

namespace App\Orchid\Layouts\ServiceCenter\Edit;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Label;
use App\Orchid\Fields\Title;
use App\Orchid\Fields\SelectRelation;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterEditRow;
use App\Models\ServiceCenter;

class ServiceCenterLocation extends ServiceCenterEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    public function getRow(ServiceCenter $serviceCenter): iterable
    {
        if ($serviceCenter->id) {
            $subways = ServiceCenter::find($serviceCenter->id)->subways->pluck('id')->toArray();
            $coord = json_decode($serviceCenter->coord);
        }

        $row = [
            Title::make('Регион')->class('pt-4'),
            SelectRelation::make('location')
                ->controller('location')
                ->inputsGroups([
                    [
                        'region' => [
                            'name' => 'serviceCenter[region_id]',
                            'id' => 'select-region',
                            'default' => true,
                            'placeholder' => 'Выбрать регион',
                            'current' => $serviceCenter->region_id,
                        ],
                        'city' =>  [
                            'name' => 'serviceCenter[city_id]',
                            'id' => 'select-city',
                            'title' => 'Город',
                            'placeholder' => 'Выбрать город',
                            'current' => $serviceCenter->city_id,
                        ],
                        'area' => [
                            'name' => 'serviceCenter[area_id]',
                            'id' => 'select-area',
                            'title' => 'Район',
                            'placeholder' => 'Выбрать район',
                            'current' => $serviceCenter->area_id,
                        ],
                        'subways' => [
                            'name' => 'subways[]',
                            'id' => 'select-subways',
                            'multiple' => true,
                            'title' => 'Метро',
                            'placeholder' => 'Выбрать метро',
                            'current' => implode(',', ($subways ?? [])),
                        ],
                    ],
                ]),
            Label::make('')->title('Координаты'),
            Group::make([
                Input::make('serviceCenter.lat')
                    ->placeholder('Широта')
                    ->value($coord->lat ?? '')
                    ->mask([
                        'mask' => '99.99999',
                        'numericInput' => true
                    ]),
                Input::make('serviceCenter.long')
                    ->placeholder('Долгота')
                    ->value($coord->long ?? '')
                    ->mask([
                        'mask' => '99.99999',
                        'numericInput' => true
                    ]),
            ])->autoWidth(),
        ];

        return $row;
    }

    public function getMethod(): string
    {
        return 'location';
    }

    protected function getSaveMethod(): string
    {
        return 'saveLocation';
    }
}
