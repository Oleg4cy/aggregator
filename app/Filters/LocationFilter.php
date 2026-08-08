<?php

namespace App\Filters;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\CookieController;
use App\Constants\CookieConstants;
use App\Filters\BaseFilter;
use App\Models\Area;
use App\Models\City;

class LocationFilter extends BaseFilter
{
    private $subway;
    private $area;

    /**
     * @param $items = [
     *   [
     *      'name' => string,                      -- query option name
     *      'label' => string                      -- label for otion
     *      'attributes' => array ['key'=>'value'] -- any html attributes
     *      '*relations*' => array                 -- relations items
     *   ]
     * ];
     */

    public function __construct(
        string $name,
        string $label,
        string $field,
        array $attributes = [],
        ?string $related = null
    ) {
        parent::__construct($name, $label, $field, $attributes, $related);
        $this->subway = $this->request['subways'] ?? false;
        $this->area = $this->request['areas'] ?? false;
    }

    public function getItems(int $cityID): Collection
    {
        if (!$cityID) return new  Collection([]);
        return Area::getByCityIdWithSubways($cityID)->get();
    }

    // Отрисовка фильтра
    public function render(int|null $cityID = null): View|Factory|Application
    {
        if (!$cityID) $cityID = $this->request['city'] ?? (CookieController::getCookie(CookieConstants::LOCATION) ?? null);
        if (!$cityID) $cityID = City::START_CITY;
        $items = $this->getItems(+$cityID);
        CookieController::setCookie(CookieConstants::LOCATION, $cityID, CookieController::getYears(1));
        return view('filters.' . $this->getName(), ['filter' => $this, 'request' => $this->request, 'cityID' => $cityID, 'items'=> $items]);
    }

    public function apply(Builder $query): Builder
    {
        if ($this->subway) $query = $query->whereHas('subways', function (Builder $query) {
            return $query->whereIn('id', $this->subway);
        });
        else if ($this->area) $query = $query->whereIn('area_id', $this->area);
        return $query;
    }
}

