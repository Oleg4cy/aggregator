<?php

namespace App\Orchid\Layouts\Shop\Edit;

use Orchid\Screen\Fields\CheckBox;
use App\Orchid\Fields\Title;
use App\Orchid\Layouts\Shop\Edit\ShopEditRow;
use App\Models\Shop;

class ShopOptions extends ShopEditRow
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
            Title::make('Возможности')->class('pt-4'),
            CheckBox::make('shop.open_24_hours')
                ->title('Круглосуточно')
                ->checked($shop->open_24_hours ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.online_estimate')
                ->title('Онлайн-оценка ремонта')
                ->checked($shop->online_estimate ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.warranty')
                ->title('Гарантия на ремонт')
                ->checked($shop->warranty ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.onsite_repair')
                ->title('Выезд мастера')
                ->checked($shop->onsite_repair ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.courier')
                ->title('Забор и доставка курьером')
                ->checked($shop->courier ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.original_parts')
                ->title('Оригинальные запчасти')
                ->checked($shop->original_parts ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.buyback')
                ->title('Выкуп техники')
                ->checked($shop->buyback ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.trade_in')
                ->title('Trade-in')
                ->checked($shop->trade_in ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.buy_for_parts')
                ->title('Выкуп на запчасти')
                ->checked($shop->buy_for_parts ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.show')
                ->title('Показывать в списке')
                ->checked($shop->show ?? true)
                ->sendTrueOrFalse()
                ->horizontal(),
        ];

        return $row;
    }

    public function getMethod(): string
    {
        return 'options';
    }

    protected function getSaveMethod(): string
    {
        return 'saveOptions';
    }
}
