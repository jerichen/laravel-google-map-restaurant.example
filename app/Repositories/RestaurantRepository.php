<?php

namespace App\Repositories;

use App\Models\Entities\Restaurant;
use App\Models\Entities\OpenHour;
use App\Models\Entities\Cache;

class RestaurantRepository
{
    public function updateRestaurants($cache_name, $items)
    {
        $cache = Cache::updateOrCreate(
            ['name' => $cache_name]
        );

        foreach ($items as $item) {
            $model = collect();
            $model->put('name', $item['name']);
            $model->put('formatted_address', $item['formatted_address']);
            $model->put('lat', $item['geometry']['location']['lat']);
            $model->put('lng', $item['geometry']['location']['lng']);
            $model->put('rating', $item['rating']);
            $model->put('user_ratings_total', $item['user_ratings_total']);
            $model->put('place_id', $item['place_id']);
            $restaurant = Restaurant::updateOrCreate(
                $model->toArray(),
                ['place_id' => $item['place_id']]
            );
            $restaurant->caches()->sync([$cache->id]);
        }
    }

    public function updateOpeningHours($periods, $place_id)
    {
        $restaurant_id = Restaurant::where('place_id', $place_id)->pluck('id')->first();

        $collect = collect($periods);
        $work_day_group = $collect->mapToGroups(function ($item) {
            return [
                $item['close']['day'] => $item['open']['time'] . '-' . $item['close']['time']
            ];
        })
            ->toArray();

        $model = collect();
        foreach ($work_day_group as $key => $val) {
            $model->put('restaurant_id', $restaurant_id);
            $model->put('day' . $key, implode($val, ','));
        }
        OpenHour::updateOrCreate(
            $model->toArray(),
            ['restaurant_id' => $restaurant_id]
        );
    }

    public function getRestaurantWorkDaysByPlaceId($place_id)
    {
        $restaurant = Restaurant::where('place_id', $place_id)
            ->with('openHour')
            ->first();

        return $restaurant->openHour->toArray();
    }

    public function getRestaurantsBySort($sort, $cache_name)
    {
        $restaurants = Restaurant::with('caches')
            ->whereHas('caches', function ($query) use ($cache_name) {
                $query->where('name', $cache_name);
            })
            ->orderBy($sort, 'DESC')
            ->get();

        return $restaurants;
    }
}
