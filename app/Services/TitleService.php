<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\CookieController;
use App\Constants\CookieConstants;
use App\Models\Area;
use App\Models\City;
use App\Models\Brand;
use App\Models\Subway;
use \App\Services\CityTimeService;
use \App\Services\DayService;
use \App\Helpers;

class TitleService
{
    public static function homePage(Request $request, Collection|LengthAwarePaginator $serviceCenters): string
    {
        $equipmentTypes = self::getEquipmentTypes($request);
        $areas = self::getAreas($request, $serviceCenters);
        $city = self::getCity($request, $serviceCenters);

        $location = '';
        if ($areas != '') $location = ' в ' . $areas;
        else if ($city != '') $location = ' в ' . $city;

        $title = $equipmentTypes . $location;
        if ($title == '') return 'Все сервисные центры';
        else if (($equipmentTypes == '') && ($location != '')) return 'Все сервисные центры' . $location;
        else if (($location == '') && ($equipmentTypes != '')) return 'Сервисные центры ' . $equipmentTypes;

        return 'Сервисные центры ' . $title;
    }

    private static function getAreas(Request $request, Collection|LengthAwarePaginator $serviceCenters): string
    {
        $areas_ids = $request->get('area') ?? [];
        $areas = Area::whereIn('id', $areas_ids)->get();
        $areasTitles = $areas->map(function ($area, $key) {
            return $area->name_for_title;
        });
        $areasTitles = $areasTitles->toArray();

        $subwayIDs = $request->get('subway') ?? [];
        $subways = Subway::with('area')->whereIn('id', $subwayIDs)->get();
        $subwaysAreas = $subways->map(function ($subway, $key) {
            return $subway->area?->name_for_title;
        });

        $areasTitles = array_filter([...$areasTitles, ...$subwaysAreas->toArray()], fn ($title) => is_string($title) && trim($title) !== '');
        $areasTitles = array_values(array_unique($areasTitles));
        $string = self::getStringFromAtrray($areasTitles);

        if (count($areasTitles) == 1) $string .= ' районе';
        else if ($string != '') $string .= ' районах';

        return $string;
    }

    private static function getCity(Request $request, Collection|LengthAwarePaginator $serviceCenters): string
    {
        $city = $serviceCenters->map(function ($serviceCenter, $key) {
            return $serviceCenter->city?->name_for_title;
        });
        $city = array_values($city->filter(fn ($title) => is_string($title) && trim($title) !== '')->unique()->toArray());

        if (count($city) < 1) {
            $cityID = $request->get('city') ?? CookieController::getCookie(CookieConstants::LOCATION) ?? false;
            if ($cityID) {
                $city = City::where('id', $cityID)->get()->first();
                if ($city) $city = [$city['name_for_title']];
                else $city = [];
            } else {
                $city = [];
            }
        }

        if (count($city) > 1) return '';
        else if (isset($city[0])) return $city[0];

        return '';
    }

    private static function getEquipmentTypes(Request $request): string
    {
        $brandIds = $request->get('sub_category') ?? [];
        $brands = Brand::with('equipmentType')->whereIn('id', $brandIds)->get();
        $equipmentTypeTitles = $brands->map(function ($brand, $key) {
            return $brand->equipmentType?->name_for_title;
        });
        $equipmentTypeTitles = $equipmentTypeTitles->filter(fn ($title) => is_string($title) && trim($title) !== '')->unique();
        $equipmentTypeTitles = array_values($equipmentTypeTitles->unique()->toArray());

        $string = self::getStringFromAtrray($equipmentTypeTitles);

        return $string;
    }

    private static function getStringFromAtrray(array $arr): string
    {
        if (count($arr) < 1) return '';

        $string = $arr[0];
        for ($i = 1; $i < count($arr); $i++) {
            if (isset($arr[$i + 1])) $separator = ', ';
            else $separator = ' и ';
            $string .= $separator . $arr[$i];
        }

        return $string;
    }

    public static function timeBeforeClose(Model $serviceCenter, bool $justTime = false): string
    {
        $timezone = $serviceCenter->region?->timezone;
        if ($timezone === null || $serviceCenter->workingHours->isEmpty()) return '';
        $dayNum = (int) DayService::getDayNumByDate(CityTimeService::getDate($timezone));
        $mode = $serviceCenter->workingHours->firstWhere('day_of_week', $dayNum);
        if (!$mode) return '';
        $openingTime = $mode->open_time;
        $closingTime = $mode->close_time;
        $isOpen = $mode->is_open;

        [$year, $currentTime] = explode(' ', CityTimeService::getFullTimeAndDate($timezone));
        $openTime = $openingTime ? Carbon::parse($currentTime . ' ' . $openingTime) : null;
        $closeTime = $closingTime ? Carbon::parse($currentTime . ' ' . $closingTime) : null;
        $nowTime = Carbon::parse($currentTime . ' ' . $year);

        if (!$isOpen) {
            return '<span class="info__isclosed">Сервисный центр закрыт</span>';
        }

        if (is_null($openTime) && is_null($closeTime)) {
            return '<span class="info__isopen">Сервисный центр открыт круглосуточно</span>';
        }

        if (!is_null($openTime) && $openTime->greaterThan($nowTime)) {
            return self::getOpeningStatus($openTime, $nowTime, $justTime);
        } elseif (!is_null($openTime) && !is_null($closeTime) && $closeTime->greaterThan($nowTime) && $closeTime->greaterThan($openTime)) {
            return self::getClosingStatus($closeTime, $nowTime, $justTime, $closingTime);
        } elseif (!is_null($openTime) && is_null($closeTime) && $nowTime->greaterThan($openTime)) {
            return '<span class="info__isopen">Сервисный центр открыт круглосуточно</span>';
        } else {
            return '<span class="info__isclosed">Сервисный центр закрыт</span>';
        }
    }

    private static function getOpeningStatus($openTime, $nowTime, $justTime)
    {
        $timeBeforeOpen = $openTime->diff($nowTime);
        $hours = $timeBeforeOpen->h;
        $minutes = $timeBeforeOpen->i;

        if ($hours == 0 && $minutes > 0) {
            return '<span class="info__isopen">Сервисный центр откроется</span> через '
                . $minutes
                . ' '
                . getNumEnding($minutes, array('минута', 'минуты', 'минут'));
        } elseif ($hours > 0 && $hours <= 12) {
            return '<span class="info__isopen">Сервисный центр откроется</span> через '
                . $hours
                . ' '
                . getNumEnding($hours, array('час', 'часа', 'часов'))
                . ' '
                . $minutes
                . ' '
                . getNumEnding($minutes, array('минута', 'минуты', 'минут'));
        } else {
            return '<span class="info__isopen">Сервисный центр открыт круглосуточно</span>';
        }
    }

    private static function getClosingStatus($closeTime, $nowTime, $justTime, $closingTime)
    {
        $timeBeforeClose = $closeTime->diff($nowTime);
        $hours = $timeBeforeClose->h;
        $minutes = $timeBeforeClose->i;

        if ($hours == 0 && $minutes > 0) {
            if ($justTime) return '<span class="info__isopen">Работает до</span> ' . $closingTime;
            return '<span class="info__isopen">До закрытия</span> сервисного центра осталось '
                . $minutes
                . ' '
                . getNumEnding($minutes, array('минута', 'минуты', 'минут'));
        } elseif ($hours > 0 && $hours <= 12) {
            if ($justTime) return '<span class="info__isopen">Работает до</span> ' . $closingTime;
            return '<span class="info__isopen">До закрытия</span> сервисного центра осталось '
                . $hours
                . ' '
                . getNumEnding($hours, array('час', 'часа', 'часов'))
                . ' '
                . $minutes
                . ' '
                . getNumEnding($minutes, array('минута', 'минуты', 'минут'));
        } else {
            return '<span class="info__isopen">Сервисный центр открыт круглосуточно</span>';
        }
    }
}
