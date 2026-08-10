<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\hasMany;
use App\Services\FilterService;
use App\Filters\SimilarBrandsFilter;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class ServiceCenter extends Model
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
                ->with('workingHours')
                ->with('area')
                ->with('city')
                ->with('region')
                ->with('subways')
                ->with('brands')
            ;
        }
        return $query;
    }

    public function scopeSimilarFilter(Builder $query, int $cityId, int $serviceCenterId, array $brands = []): Builder
    {
            $query = (new SimilarBrandsFilter())->apply($query, $brands)
                ->with('area')
                ->with('city')
                ->with('region')
                ->with('equipmentTypes')
                ->with('brands')
                ->with('subways')
                ->where('id', '!=',  $serviceCenterId)
                ->where('city_id',  $cityId)
            ;
        return $query;
    }

    public function scopeGetByID(Builder $query, string|int $id): Builder
    {
        return $query->where('id', $id)
            ->with('workingHours')
            ->with('region')
            ->with('area')
            ->with('city')
            ->with('subways')
            ->with('equipmentTypes')
            ->with('brands')
            ->with('reviewSources')
            ->with('buybackPrices')
        ;
    }

    public function scopeGetByName(Builder $query, string $name): Builder
    {
        return $query->select('name', 'id')
            ->where('name', 'LIKE', '%'.$name.'%')
        ;
    }

    public function network(): belongsTo
    {
        return $this->belongsTo(\App\Models\ServiceNetwork::class, 'service_network_id');
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
        return $this->belongsToMany(\App\Models\Subway::class, 'service_center_subway', 'service_center_id', 'subway_id');
    }

    public function equipmentTypes(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\EquipmentType::class, 'service_center_equipment_type', 'service_center_id', 'equipment_type_id')
            ->withPivot('created_at', 'position');
    }

    public function brands(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\Brand::class, 'service_center_brand', 'service_center_id', 'brand_id')
            ->withPivot('created_at', 'position');
    }

    public function workingHours(): hasMany
    {
        return $this->hasMany(\App\Models\ServiceCenterWorkingHour::class, 'service_center_id');
    }

    public function buybackPrices(): hasMany
    {
        return $this->hasMany(\App\Models\BuybackPrice::class, 'service_center_id');
    }

    public function reviewSources(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\ReviewSource::class, 'service_center_review_source', 'service_center_id', 'review_source_id')
            ->withPivot('rating', 'rating_count', 'link', 'comments')
        ;
    }
}
