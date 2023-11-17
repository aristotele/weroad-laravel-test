<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Travel extends Model
{
    use HasFactory;

    protected $table = 'travels';

    protected $guarded = [];

    protected $casts = [
        'isPublic' => 'boolean',
        'moods' => 'array',
    ];

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'travelId', 'id');
    }


    /* -------------------------------------------------------------------------- */
    /*                                Query Scopes                                */
    /* -------------------------------------------------------------------------- */
    public function scopePublic(Builder $query)
    {
        return $query->where('isPublic', true);
    }


    /* -------------------------------------------------------------------------- */
    /*                                  Accessors                                 */
    /* -------------------------------------------------------------------------- */
    public function getNumberOfNightsAttribute()
    {
        return $this->numberOfDays - 1;
    }
}
