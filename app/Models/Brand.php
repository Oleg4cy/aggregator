<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function scopeGetByServiceCenterId(Builder $query, int $serviceCenterId): Builder
    {
        return $query->with('equipmentType')
            ->whereHas('serviceCenters', function ($query) use ($serviceCenterId) {
                $query->where('service_centers.id', $serviceCenterId);
            });
    }   public $timestamps = false;

    public function equipmentType(): belongsTo
    {
        return $this->belongsTo(\App\Models\EquipmentType::class, 'equipment_type_id');
    }

    public function serviceCenters(): belongsToMany
    {
        return $this->belongsToMany(\App\Models\ServiceCenter::class, 'service_center_brand', 'brand_id', 'service_center_id');
    }
}
