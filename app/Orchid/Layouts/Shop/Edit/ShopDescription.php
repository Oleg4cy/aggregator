<?php

namespace App\Orchid\Layouts\Shop\Edit;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\SimpleMDE;
use App\Orchid\Fields\Title;
use Orchid\Screen\Actions\Button;
use App\Orchid\Layouts\Shop\Edit\ShopEditRow;
use App\Models\Shop;

class ShopDescription extends ShopEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    public function getRow(Shop $shop): iterable
    {
        $row = [
            Title::make('Описание')->class('pt-4'),
            Input::make('shop.name')
                ->title('Название')
                ->value($shop->name ?? '')
                ->required(),
            Input::make('shop.title')
                ->title('Заголовок')
                ->value($shop->title ?? '')
                ->popover('Заголовок для карточки магазина')
                ->required(),
            SimpleMDE::make('shop.description')->value($shop->description ?? ''),
        ];

        return $row;
    }

    public function getMethod(): string
    {
        return 'desc';
    }
}
