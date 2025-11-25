<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'continent',
        'population',
        'capital',
        'languages',
        'currency',
        'description',
        'background_image',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'languages' => 'array',
        'population' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * ユーザーの国ステータス
     */
    public function userCountryStatuses(): HasMany
    {
        return $this->hasMany(UserCountryStatus::class);
    }

    /**
     * 国の写真
     */
    public function photos(): HasMany
    {
        return $this->hasMany(CountryPhoto::class);
    }
}
