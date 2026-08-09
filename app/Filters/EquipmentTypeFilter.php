<?php

namespace App\Filters;

use App\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class EquipmentTypeFilter extends BaseFilter
{
    private array $brands = [];

    public function apply(Builder $query): Builder
    {
        $this->brands = $this->request['sub_categories'] ?? [];
        if ($this->brands) {
            return $query->whereHas('brands', function (Builder $query) {
                return $query->whereIn('id', $this->brands);
            });
        }

        return $query;
    }
}
