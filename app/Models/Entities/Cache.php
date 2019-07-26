<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
    protected $table = 'caches';

    protected $fillable = [
        'name',
    ];

    public function restaurants()
    {
        return $this->belongsToMany(
            'App\Models\Entities\Restaurant',
            'restaurant_cache',
            'cache_id',
            'restaurant_id'
        )->withTimestamps();
    }
}
