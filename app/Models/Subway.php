<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Subway extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public $timestamps = false;

    public function scopeGetByShopId(Builder $query, $id): Builder
    {
        return $query->whereHas('shops', function (Builder $query) use ($id) {
            $query->where('shops.id', $id);
        });
    }

    public function scopeGetByAreasIds(Builder $query, $ids): Builder
    {
        return $query->whereIn('area_id', $ids);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Region::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    public function area(): belongsTo
    {
        return $this->belongsTo(\App\Models\Area::class);
    }

    public function shops(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\Shop::class, 'shop_subway', 'subway_id', 'shop_id');
    }
}
