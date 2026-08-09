<?php

namespace App\Orchid\Screens\Shop;

use App\Orchid\Screens\Shop\ShopEditScreen;

class ShopAddScreen extends ShopEditScreen
{
    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Добавить сервисный центр';
    }
}
