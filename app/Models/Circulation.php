<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Circulation extends Model
{
    protected $fillable = [
        'user_id',
        'duration',
        'amount',
        'total',
        'status'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
