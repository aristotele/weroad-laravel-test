<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Travel extends Model
{
    use HasFactory;

    protected $table = 'travels';

    protected $guarded = [];

    protected $casts = [
        'moods' => 'array'
    ];

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'travelId', 'id');
    }

    public function getNumberOfNightsAttribute()
    {
        return $this->numberOfDays - 1;
    }
}
