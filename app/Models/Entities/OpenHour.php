<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class OpenHour extends Model
{
    protected $table = 'open_hours';

    protected $fillable = [
        'restaurant_id',
        'day0',
        'day1',
        'day2',
        'day3',
        'day4',
        'day5',
        'day6',
    ];

    public function restaurant()
    {
        return $this->belongsTo('App\Models\Entities\Restaurant');
    }
}
