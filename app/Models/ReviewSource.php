<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Builder;

class ReviewSource extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function scopeGetServiceCenterReviewSource(Builder $query, int $reviewSourceId, int $serviceCenterId)
    {
        return $query->where('id', $reviewSourceId)->whereHas('serviceCenters', function (Builder $query) use ($serviceCenterId) {
            return $query->where('service_centers.id', $serviceCenterId);
        })->get();
    }

    public function serviceCenters(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\ServiceCenter::class, 'service_center_review_source', 'review_source_id', 'service_center_id');
    }
}

