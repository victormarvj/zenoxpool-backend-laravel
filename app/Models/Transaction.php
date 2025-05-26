<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'type',
        'name',
        'type_amount',
        'type_name',
        'amount',
        'address',
        'status',
    ];

    public function getNameAttribute($value) {
        return ucwords($value);
    }
}
