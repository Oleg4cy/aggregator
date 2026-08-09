<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EquipmentType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public $timestamps = false;

    public function brands(): HasMany
    {
        return $this->hasMany(\App\Models\Brand::class, 'equipment_type_id');
    }

    public function serviceCenters(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\ServiceCenter::class, 'service_center_equipment_type', 'equipment_type_id', 'service_center_id');
    }
}
