<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCenterWorkingHour extends Model
{
    public $timestamps = false;

    public function scopeGetByServiceCenterId(Builder $query, $serviceCenterId): Builder
    {
        return $query->where('service_center_id', $serviceCenterId);
    }

    public function serviceCenter(): belongsTo
    {
        return $this->belongsTo(\App\Models\ServiceCenter::class, 'service_center_id');
    }
}
