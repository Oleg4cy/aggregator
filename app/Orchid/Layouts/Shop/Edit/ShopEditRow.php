<?php

namespace App\Orchid\Layouts\Shop\Edit;

use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Actions\Button;
use App\Models\Shop;

abstract class ShopEditRow extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    abstract function getRow(Shop $shop): iterable;
    abstract function getMethod(): string;

    protected function getSaveMethod(): string
    {
        return 'save-' . $this->getMethod();
    }

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        $shop = $this->query->get('shop');
        $rows = $this->getRow($shop);

        if ($shop->id) {
            $rows[] = Button::make('Сохранить')
                ->method($this->getSaveMethod())
                ->class('btn btn-success m-auto');
        }

        return $rows;
    }
}
