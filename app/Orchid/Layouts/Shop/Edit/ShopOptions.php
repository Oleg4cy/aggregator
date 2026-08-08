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
            Title::make('Опции')->class('pt-4'),
            CheckBox::make('shop.convenience_shop')
                ->title('Круглосуточный магазин')
                ->checked($shop->convenience_shop ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.appraisal_online')
                ->title('Оценка онлайн')
                ->checked($shop->appraisal_online ?? false)
                ->sendTrueOrFalse()
                ->horizontal(),
            CheckBox::make('shop.pawnshop')
                ->title('Ломбард')
                ->checked($shop->pawnshop ?? false)
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
