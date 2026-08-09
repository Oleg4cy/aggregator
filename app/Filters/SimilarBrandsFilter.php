<?php

namespace App\Filters;

use App\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class SimilarBrandsFilter extends BaseFilter
{
    private array $brands = [];

    public function __construct() {}

    public function apply(Builder $query, array $brands = []): Builder
    {
        $this->brands = $brands;
        if ($this->brands) {
            return $query->whereHas('brands', function (Builder $query) {
                return $query->whereIn('id', $this->brands);
            });
        }

        return $query;
    }
}
