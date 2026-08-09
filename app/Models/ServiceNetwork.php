<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceNetwork extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function serviceCenters(): HasMany
    {
        return $this->hasMany(\App\Models\ServiceCenter::class, 'service_network_id');
    }
}
