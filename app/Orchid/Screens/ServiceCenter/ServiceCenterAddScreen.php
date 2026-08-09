<?php

namespace App\Orchid\Screens\ServiceCenter;

use App\Orchid\Screens\ServiceCenter\ServiceCenterEditScreen;

class ServiceCenterAddScreen extends ServiceCenterEditScreen
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
