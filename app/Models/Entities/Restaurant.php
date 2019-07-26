<?php
namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $table = 'restaurants';

    protected $fillable = [
        'name',
        'formatted_address',
        'lat',
        'lng',
        'rating',
        'user_ratings_total',
        'price_level',
        'place_id',
    ];

    public function openHour()
    {
        return $this->hasOne('App\Models\Entities\OpenHour');
    }

    public function caches()
    {
        return $this->belongsToMany(
            'App\Models\Entities\Cache',
            'restaurant_cache',
            'restaurant_id',
            'cache_id'
        )->withTimestamps();
    }
}
