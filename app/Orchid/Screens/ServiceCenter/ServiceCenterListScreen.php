<?php

namespace App\Orchid\Screens\ServiceCenter;

use Orchid\Screen\Screen;
use App\Orchid\Layouts\ServiceCenter\ServiceCenterListTable;
use Orchid\Screen\Actions\Link;
use App\Models\ServiceCenter;

class ServiceCenterListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'serviceCenters' => ServiceCenter::filters()
                ->defaultSort('id')
                ->with('city')
                ->with('region')
                ->with('area')
                ->with('subways')
                ->with('municipality')
                ->paginate(20),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Список сервисных центров';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить сервисный центр')->icon('bs.plus-circle')->route('platform.service-centers.add'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            ServiceCenterListTable::class,
        ];
    }
}
