<?php

namespace App\Orchid\Layouts\ServiceCenter\Edit;

use Illuminate\Database\Eloquent\Collection;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\CheckBox;
use App\Orchid\Fields\Title;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterEditRow;
use App\Models\ServiceCenter;
use App\Models\ServiceCenterWorkingHour;
use App\Services\DayService;
use Carbon\Carbon;

class ServiceCenterWorkingHours extends ServiceCenterEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    private function openTime(Collection|null $workingHours, int $day)
    {
        $workingHour = $workingHours?->get($day);

        return $workingHour ? Carbon::parse($workingHour->open_time ?? '00:00')->format('H:i') : null;
    }

    private function closeTime(Collection|null $workingHours, int $day)
    {
        $workingHour = $workingHours?->get($day);

        return $workingHour ? Carbon::parse($workingHour->close_time ?? '23:59')->format('H:i') : null;
    }

    private function isDayOff(Collection|null $workingHours, int $day)
    {
        $workingHour = $workingHours?->get($day);

        return $workingHour ? !$workingHour->is_open : false;
    }

    public function getRow(ServiceCenter $serviceCenter): iterable
    {
        $workingHours = null;
        if ($serviceCenter->id) {
            $workingHours = ServiceCenterWorkingHour::getByServiceCenterId($serviceCenter->id)->get()->keyBy('day_of_week');
        }

        $group = [Title::make('Режим работы')->class('pt-4')];
        for ($i = 0; $i < 7; $i++) {
            $day = $i + 1;
            $group = array_merge($group, [
                Group::make([
                    Label::make('')->title(DayService::getDayByNum($day)),
                    DateTimer::make('working_hours[' . $day . '][open]')
                        ->value($this->openTime($workingHours, $day))
                        ->title('с')
                        ->noCalendar()
                        ->format('H:i')
                        ->format24hr(),
                    DateTimer::make('working_hours[' . $day . '][close]')
                        ->value($this->closeTime($workingHours, $day))
                        ->title('до')
                        ->noCalendar()
                        ->format('H:i')
                        ->format24hr(),
                    CheckBox::make('working_hours[' . $day . '][is_day_off]')
                        ->checked($this->isDayOff($workingHours, $day))
                        ->sendTrueOrFalse()
                        ->title('Выходной'),
                ])->widthColumns('3rem 7rem 7rem max-content')
                    ->set('align', 'align-items-center service-center-working-hours__row'),
            ]);
        }

        return $group;
    }

    public function getMethod(): string
    {
        return 'workingHours';
    }

    protected function getSaveMethod(): string
    {
        return 'saveWorkingHours';
    }
}
