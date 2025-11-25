<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCountryStatus extends Model
{
    protected $fillable = [
        'user_id',
        'country_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
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

    /**
     * ステータスの日本語名を取得
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'lived' => '住んだことがある',
            'stayed' => '宿泊したことがある',
            'visited' => '日帰りで訪れたことがある',
            'passed' => '通ったことがある',
            'not_visited' => '行ったことがない',
            default => '不明',
        };
    }
}
