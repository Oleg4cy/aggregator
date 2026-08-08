<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chain extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function shops(): HasMany
    {
        return $this->hasMany(\App\Models\Shop::class, 'chain_id');
    }
}
