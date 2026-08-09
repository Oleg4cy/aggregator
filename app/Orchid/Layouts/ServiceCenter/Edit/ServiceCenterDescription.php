<?php

namespace App\Orchid\Layouts\ServiceCenter\Edit;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\SimpleMDE;
use App\Orchid\Fields\Title;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterEditRow;
use App\Models\ServiceCenter;

class ServiceCenterDescription extends ServiceCenterEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    public function getRow(ServiceCenter $serviceCenter): iterable
    {
        $row = [
            Title::make('Описание')->class('pt-4'),
            Input::make('serviceCenter.name')
                ->title('Название')
                ->value($serviceCenter->name ?? '')
                ->required(),
            Input::make('serviceCenter.title')
                ->title('Заголовок')
                ->value($serviceCenter->title ?? '')
                ->popover('Заголовок для карточки сервисного центра')
                ->required(),
            SimpleMDE::make('serviceCenter.description')->value($serviceCenter->description ?? ''),
        ];

        return $row;
    }

    public function getMethod(): string
    {
        return 'desc';
    }

    protected function getSaveMethod(): string
    {
        return 'saveDescription';
    }
}
