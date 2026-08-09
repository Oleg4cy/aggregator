<?php

namespace App\Orchid\Layouts\Shop\Edit;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\SimpleMDE;
use App\Orchid\Fields\Title;
use App\Orchid\Layouts\Shop\Edit\ShopEditRow;
use App\Models\ServiceCenter;

class ShopDescription extends ShopEditRow
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
            Input::make('shop.name')
                ->title('Название')
                ->value($serviceCenter->name ?? '')
                ->required(),
            Input::make('shop.title')
                ->title('Заголовок')
                ->value($serviceCenter->title ?? '')
                ->popover('Заголовок для карточки сервисного центра')
                ->required(),
            SimpleMDE::make('shop.description')->value($serviceCenter->description ?? ''),
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
