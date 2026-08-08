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
use App\Models\SubCategory;
use App\Models\Subway;
use \App\Services\CityTimeService;
use \App\Services\DayService;
use \App\Helpers;

class TitleService
{
    public static function homePage(Request $request, Collection|LengthAwarePaginator $shops): string
    {
        $categories = self::getCategories($request);
        $areas = self::getAreas($request, $shops);
        $city = self::getCity($request, $shops);

        $location = '';
        if ($areas != '') $location = ' в ' . $areas;
        else if ($city != '') $location = ' в ' . $city;

        $title = $categories . $location;
        if ($title == '') return 'Все скупки';
        else if (($categories == '') && ($location != '')) return 'Все скупки' . $location;
        else if (($location == '') && ($categories != '')) return 'Все скупки' . $categories;

        return 'Скупки ' . $title;
    }

    private static function getAreas(Request $request, Collection|LengthAwarePaginator $shops): string
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

    private static function getCity(Request $request, Collection|LengthAwarePaginator $shops): string
    {
        $city = $shops->map(function ($shop, $key) {
            return $shop->city?->name_for_title;
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

    private static function getCategories(Request $request): string
    {
        $subCategoriesIds = $request->get('sub_category') ?? [];
        $subCategories = SubCategory::with('category')->whereIn('id', $subCategoriesIds)->get();
        $categoriesTitles = $subCategories->map(function ($subCategory, $key) {
            return $subCategory->category?->name_for_title;
        });
        $categoriesTitles = $categoriesTitles->filter(fn ($title) => is_string($title) && trim($title) !== '')->unique();
        $categoriesTitles = array_values($categoriesTitles->unique()->toArray());

        $string = self::getStringFromAtrray($categoriesTitles);

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

    public static function timeBeforeClose(Model $shop, bool $justTime = false): string
    {
        $timezone = $shop->region?->timezone;
        if ($timezone === null || $shop->workingMode->isEmpty()) return '';
        $dayNum = (int) DayService::getDayNumByDate(CityTimeService::getDate($timezone));
        $mode = $shop->workingMode->firstWhere('day_of_week', $dayNum);
        if (!$mode) return '';
        $shopOpen = $mode->open_time;
        $shopClose = $mode->close_time;
        $shopIsOpen = $mode->is_open;

        [$year, $currentTime] = explode(' ', CityTimeService::getFullTimeAndDate($timezone));
        $openTime = $shopOpen ? Carbon::parse($currentTime . ' ' . $shopOpen) : null;
        $closeTime = $shopClose ? Carbon::parse($currentTime . ' ' . $shopClose) : null;
        $nowTime = Carbon::parse($currentTime . ' ' . $year);

        if (!$shopIsOpen) {
            return '<span class="info__isclosed">Магазин закрыт</span>';
        }

        if (is_null($openTime) && is_null($closeTime)) {
            return '<span class="info__isopen">Магазин открыт круглосуточно</span>';
        }

        if (!is_null($openTime) && $openTime->greaterThan($nowTime)) {
            return self::getOpeningStatus($openTime, $nowTime, $justTime);
        } elseif (!is_null($openTime) && !is_null($closeTime) && $closeTime->greaterThan($nowTime) && $closeTime->greaterThan($openTime)) {
            return self::getClosingStatus($closeTime, $nowTime, $justTime, $shopClose);
        } elseif (!is_null($openTime) && is_null($closeTime) && $nowTime->greaterThan($openTime)) {
            return '<span class="info__isopen">Магазин открыт круглосуточно</span>';
        } else {
            return '<span class="info__isclosed">Магазин закрыт</span>';
        }
    }

    private static function getOpeningStatus($openTime, $nowTime, $justTime)
    {
        $timeBeforeOpen = $openTime->diff($nowTime);
        $hours = $timeBeforeOpen->h;
        $minutes = $timeBeforeOpen->i;

        if ($hours == 0 && $minutes > 0) {
            return '<span class="info__isopen">Магазин откроется</span> через '
                . $minutes
                . ' '
                . getNumEnding($minutes, array('минута', 'минуты', 'минут'));
        } elseif ($hours > 0 && $hours <= 12) {
            return '<span class="info__isopen">Магазин откроется</span> через '
                . $hours
                . ' '
                . getNumEnding($hours, array('час', 'часа', 'часов'))
                . ' '
                . $minutes
                . ' '
                . getNumEnding($minutes, array('минута', 'минуты', 'минут'));
        } else {
            return '<span class="info__isopen">Магазин открыт круглосуточно</span>';
        }
    }

    private static function getClosingStatus($closeTime, $nowTime, $justTime, $shopClose)
    {
        $timeBeforeClose = $closeTime->diff($nowTime);
        $hours = $timeBeforeClose->h;
        $minutes = $timeBeforeClose->i;

        if ($hours == 0 && $minutes > 0) {
            if ($justTime) return '<span class="info__isopen">Работает до</span> ' . $shopClose;
            return '<span class="info__isopen">До закрытия</span> магазина осталось '
                . $minutes
                . ' '
                . getNumEnding($minutes, array('минута', 'минуты', 'минут'));
        } elseif ($hours > 0 && $hours <= 12) {
            if ($justTime) return '<span class="info__isopen">Работает до</span> ' . $shopClose;
            return '<span class="info__isopen">До закрытия</span> магазина осталось '
                . $hours
                . ' '
                . getNumEnding($hours, array('час', 'часа', 'часов'))
                . ' '
                . $minutes
                . ' '
                . getNumEnding($minutes, array('минута', 'минуты', 'минут'));
        } else {
            return '<span class="info__isopen">Магазин открыт круглосуточно</span>';
        }
    }
}
