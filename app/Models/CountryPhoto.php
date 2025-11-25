<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryPhoto extends Model
{
    protected $fillable = [
        'user_id',
        'country_id',
        'image_path',
        'caption',
        'description',
        'taken_at',
        'location',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    /**
     * ユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 国
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
