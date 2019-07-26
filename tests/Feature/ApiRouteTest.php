<?php

namespace Tests\Feature;

use App\Models\Entities\Cache;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiRouteTest extends TestCase
{
    protected $place_id;

    protected function setUp(): void
    {
        parent::setUp();
        $this->place_id = 'ChIJWVwELAWoQjQRpy_JjH79qKQ';
    }

    /**
     * @test
     */
    public function get_restaurants()
    {
        $uri = route('api.get.restaurants');
        $response = $this->json('GET', $uri);
        $response
            ->assertStatus(200)
            ->assertJson([
                'msg' => 'loading success'
            ]);
    }

    /**
     * @test
     */
    public function get_restaurant_detail()
    {
        $uri = route('api.get.restaurant.detail', [
            'place_id' => $this->place_id
        ]);
        $response = $this->json('GET', $uri);
        $response
            ->assertStatus(200)
            ->assertJson([
                'msg' => 'loading success'
            ]);
    }

    /**
     * @test
     */
    public function get_restaurant_work_days()
    {
        $uri = route('api.get.restaurant.work.days', [
            'place_id' => $this->place_id
        ]);
        $response = $this->json('GET', $uri);
        $response
            ->assertStatus(200)
            ->assertJson([
                'msg' => 'loading success'
            ]);
    }

    /**
     * @test
     */
    public function get_restaurant_reviews()
    {
        $uri = route('api.get.restaurant.reviews', [
            'place_id' => $this->place_id
        ]);
        $response = $this->json('GET', $uri);
        $response
            ->assertStatus(200)
            ->assertJson([
                'msg' => 'loading success'
            ]);
    }

    /**
     * @test
     */
    public function get_restaurants_by_sort()
    {
        $cache_name = Cache::pluck('name')->first();
        $uri = route('api.get.restaurants.by.sort', [
            'sort' => 'rating',
            'cache_name' => $cache_name
        ]);
        $response = $this->json('GET', $uri);
        $response
            ->assertStatus(200)
            ->assertJson([
                'msg' => 'loading success'
            ]);
    }

    /**
     * @test
     */
    public function get_geolocation()
    {
        $uri = route('api.get.geolocation');
        $response = $this->json('GET', $uri);
        $response
            ->assertStatus(200)
            ->assertJson([
                'msg' => 'loading success'
            ]);
    }
}
