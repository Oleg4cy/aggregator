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

    public function scopeGetByServiceCenterId(Builder $query, $serviceCenterId): Builder
    {
        return $query->whereHas('serviceCenters', function (Builder $query) use ($serviceCenterId) {
            $query->where('service_centers.id', $serviceCenterId);
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

    public function serviceCenters(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\ServiceCenter::class, 'service_center_subway', 'subway_id', 'service_center_id');
    }
}
