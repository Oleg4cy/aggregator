<?php

namespace App\Orchid\Layouts\Shop\Edit;

use Illuminate\Database\Eloquent\Collection;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\CheckBox;
use App\Orchid\Fields\Title;
use App\Orchid\Layouts\Shop\Edit\ShopEditRow;
use App\Models\Shop;
use App\Services\DayService;
use Carbon\Carbon;

class ShopWorkingMode extends ShopEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    private function openTime(Collection|null $workingMode, int $day)
    {
        $mode = $workingMode?->get($day);

        return $mode ? Carbon::parse($mode->open_time ?? '00:00')->format('H:i') : null;
    }

    private function closeTime(Collection|null $workingMode, int $day)
    {
        $mode = $workingMode?->get($day);

        return $mode ? Carbon::parse($mode->close_time ?? '23:59')->format('H:i') : null;
    }

    private function isDayOff(Collection|null $workingMode, int $day)
    {
        $mode = $workingMode?->get($day);

        return $mode ? !$mode->is_open : false;
    }

    public function getRow(Shop $shop): iterable
    {
        $workingMode = null;
        if ($shop->id) {
            $workingMode = \App\Models\ShopWorkingMode::getByShopID($shop->id)->get()->keyBy('day_of_week');
        }

        $group = [Title::make('Режим работы')->class('pt-4')];
        for ($i = 0; $i < 7; $i++) {
            $day = $i + 1;
            $group = array_merge($group, [
                Group::make([
                    Label::make('')->title(DayService::getDayByNum($day)),
                    DateTimer::make('working_mode[' . $day . '][open]')
                        ->value($this->openTime($workingMode, $day))
                        ->title('с')
                        ->noCalendar()
                        ->format('H:i')
                        ->format24hr(),
                    DateTimer::make('working_mode[' . $day . '][close]')
                        ->value($this->closeTime($workingMode, $day))
                        ->title('до')
                        ->noCalendar()
                        ->format('H:i')
                        ->format24hr(),
                    CheckBox::make('working_mode[' . $day . '][is_day_off]')
                        ->checked($this->isDayOff($workingMode, $day))
                        ->sendTrueOrFalse()
                        ->title('Выходной'),
                ])->autoWidth(),
            ]);
        }

        return $group;
    }

    public function getMethod(): string
    {
        return 'workingmode';
    }

    protected function getSaveMethod(): string
    {
        return 'saveWorkingMode';
    }
}
