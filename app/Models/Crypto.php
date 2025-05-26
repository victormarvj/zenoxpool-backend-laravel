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

    public function getNameAttribute($value) {
        return ucfirst($value);
    }

    public function getAbbreviationAttribute($value) {
        return strtoupper($value);
    }
}
