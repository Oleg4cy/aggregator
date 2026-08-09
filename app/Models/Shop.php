<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\hasMany;
use App\Services\FilterService;
use App\Filters\SimilarCategoriesFilter;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Shop extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    public $guarded = [];
    protected $allowedSorts = [
        'id',
        'region_id',
        'city_id',
        'area_id',
        'municipality_id',
        'name',
        'open_24_hours',
        'online_estimate',
        'warranty',
        'onsite_repair',
        'courier',
        'original_parts',
        'buyback',
        'trade_in',
        'buy_for_parts',
        'average_rating',
        'show',
        'created_at',
        'updated_at',
    ];
    protected $allowedFilters = [
        'id' => Where::class,
        'region_id'  => Where::class,
        'city_id'  => Where::class,
        'area_id'  => Where::class,
        'municipality_id'  => Where::class,
        'address'  => Like::class,
        'name'  => Like::class,
        'phone'  => Like::class,
        'whatsapp'  => Like::class,
        'telegram'  => Like::class,
        'emails'  => Like::class,
        'average_rating'  => Like::class,
        'created_at'  => Like::class,
        'updated_at'  => Like::class,
    ];

    public function getRouteKey()
    {
        return 'id';
    }

    public function scopeFilter(Builder $query, ): Builder
    {
        foreach (app(FilterService::class)->getFilters() as $filter) {
            $query = $filter->apply($query)
                ->with('workingMode')
                ->with('area')
                ->with('city')
                ->with('region')
                ->with('subways')
                ->with('subCategories')
            ;
        }
        return $query;
    }

    public function scopeSimilarFilter(Builder $query, int $cityID, int $shop_id, array $subCategories = []): Builder
    {
            $query = (new SimilarCategoriesFilter())->apply($query, $subCategories)
                ->with('area')
                ->with('city')
                ->with('region')
                ->with('categories')
                ->with('subCategories')
                ->with('subways')
                ->where('id', '!=',  $shop_id)
                ->where('city_id',  $cityID)
            ;
        return $query;
    }

    public function scopeGetByID(Builder $query, string|int $id): Builder
    {
        return $query->where('id', $id)
            ->with('workingMode')
            ->with('region')
            ->with('area')
            ->with('city')
            ->with('subways')
            ->with('categories')
            ->with('subCategories')
            ->with('services')
            ->with('prices')
        ;
    }

    public function scopeGetByName(Builder $query, string $name): Builder
    {
        return $query->select('name', 'id')
            ->where('name', 'LIKE', '%'.$name.'%')
        ;
    }

    public function chain(): belongsTo
    {
        return $this->belongsTo(\App\Models\Chain::class, 'chain_id');
    }

    public function municipality(): belongsTo
    {
        return $this->belongsTo(\App\Models\Municipality::class);
    }

    public function city(): belongsTo
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    public function region(): belongsTo
    {
        return $this->belongsTo(\App\Models\Region::class);
    }

    public function area(): belongsTo
    {
        return $this->belongsTo(\App\Models\Area::class);
    }

    public function subways(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\Subway::class, 'shop_subway', 'shop_id', 'subway_id');
    }

    public function categories(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\Category::class, 'shop_category', 'shop_id', 'category_id')
            ->withPivot('created_at', 'position');
    }

    public function subCategories(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\SubCategory::class, 'shop_sub_category', 'shop_id', 'sub_category_id')
            ->withPivot('created_at', 'position');
    }

    public function workingMode(): hasMany
    {
        return $this->hasMany(\App\Models\ShopWorkingMode::class, 'shop_id');
    }

    public function prices(): hasMany
    {
        return $this->hasMany(\App\Models\ShopPrices::class, 'shop_id');
    }

    public function services(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\Service::class, 'shop_services', 'shop_id', 'service_id')
            ->withPivot('rating', 'rating_count', 'comments')
        ;
    }
}
