<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'crypto_id',
        'type',
        'name',
        'type_amount',
        'type_name',
        'amount',
        'address',
        'status',
        'no_of_codes',
    ];
}
