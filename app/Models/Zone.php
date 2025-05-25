<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'duration_1',
        'roi_1',
        'duration_2',
        'roi_2',
        'duration_3',
        'roi_3',
        'status',
    ];
}
