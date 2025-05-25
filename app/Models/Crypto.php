<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crypto extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'network',
        'address',
        'value',
        'image',
        'status',
    ];
}