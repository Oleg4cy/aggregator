<?php

namespace App\Orchid\Layouts\Shop\Edit;

use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Select;
use App\Orchid\Fields\Title;
use App\Orchid\Layouts\Shop\Edit\ShopEditRow;
use App\Models\Shop;

class ShopChain extends ShopEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    public function getRow(Shop $shop): iterable
    {
        $chains = \App\Models\Chain::all()->pluck('name', 'id');

        $row = [
            Title::make('Принадлежит сети'),
            Group::make([
                Select::make('shop.chain_id')
                    ->options($chains)
                    ->empty('Не принадлежит сети')
                    ->value($shop->chain_id),
            ]),
        ];

        return $row;
    }

    public function getMethod(): string
    {
        return 'chain';
    }

    protected function getSaveMethod(): string
    {
        return 'saveChain';
    }
}
