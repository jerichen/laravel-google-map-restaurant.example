<?php

namespace App\Http\Controllers\Api;

use App\Traits\BasicResponseTrait;
use App\Traits\HttpRequestTrait;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use App\Repositories\RestaurantRepository;
use Illuminate\Support\Facades\Cache;

class GoogleMapApiController extends Controller
{
    use HttpRequestTrait;
    use BasicResponseTrait;

    protected $response;
    protected $google_api_key;
    protected $retaurant_repo;

    public function __construct(RestaurantRepository $retaurant_repo)
    {
        $this->google_api_key = env('GOOGLEAPIKEY');
        $this->retaurant_repo = $retaurant_repo;
    }

    private function getRestaurantDetailByPlaceId($place_id)
    {
        $key = 'place_id_' . hash('md5', $place_id) . '_details';
        $minutes = rand(60, 100) * 60;
        $details = Cache::remember($key, $minutes, function () use ($place_id) {
            $method = 'GET';
            $uri = 'https://maps.googleapis.com/maps/api/place/details/json';
            $params = [
                'placeid' => $place_id,
                'language' => 'zh-TW',
                'key' => $this->google_api_key,
            ];
            $response_array = $this->googleMapRequests($method, $uri, $params);
            return $response_array['result'];
        });
        return $details;
    }

    public function getGeolocation()
    {
        try {
            $method = 'POST';
            $uri = 'https://www.googleapis.com/geolocation/v1/geolocate';
            $params = [
                'language' => 'zh-TW',
                'key' => $this->google_api_key,
            ];
            $response_array = $this->googleMapRequests($method, $uri, $params);
            $items = $response_array['location'];
            $this->response = $this->getBasicResponse();
            $this->response['items'] = $items;
        } catch (Exception $ex) {
            Log::error(__CLASS__ . '.' . __FUNCTION__ . ':' . $ex->getMessage());
            $msg = $ex->getMessage();
            $this->response = $this->getFailResponse($msg);
        }

        return $this->response;
    }

    public function getRestaurants(Request $request)
    {
        try {
            $keyword = $request->get('keyword');
            $query = ($keyword == null) ? '早午餐' : $keyword;
            $key = 'query_' . hash('md5', $query);
            $minutes = rand(60, 100) * 60;
            $items = Cache::remember($key, $minutes, function () use ($query) {
                $method = 'GET';
                $uri = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
                $params = [
                    'query' => $query,
                    'language' => 'zh-TW',
                    'key' => $this->google_api_key,
                ];
                $response_array = $this->googleMapRequests($method, $uri, $params);
                return $response_array['results'];
            });
            $this->retaurant_repo->updateRestaurants($key, $items);
            $this->response = $this->getBasicResponse();
            $this->response['items'] = $items;
            $this->response['query'] = $query;
            $this->response['cache_name'] = $key;
        } catch (Exception $ex) {
            Log::error(__CLASS__ . '.' . __FUNCTION__ . ':' . $ex->getMessage());
            $msg = $ex->getMessage();
            $this->response = $this->getFailResponse($msg);
        }

        return $this->response;
    }

    public function getRestaurantDetail(Request $request)
    {
        try {
            $place_id = $request->get('place_id');
            $items = $this->getRestaurantDetailByPlaceId($place_id);
            $this->retaurant_repo->updateOpeningHours($items['opening_hours']['periods'], $place_id);
            $this->response = $this->getBasicResponse();
            $this->response['items'] = $items;
        } catch (Exception $ex) {
            Log::error(__CLASS__ . '.' . __FUNCTION__ . ':' . $ex->getMessage());
            $msg = $ex->getMessage();
            $this->response = $this->getFailResponse($msg);
        }

        return $this->response;
    }

    public function getRestaurantWorkDays(Request $request)
    {
        try {
            $place_id = $request->get('place_id');
            $key = 'place_id_' . hash('md5', $place_id) . '_work_days';
            $minutes = rand(60, 100) * 60;
            $items = Cache::remember($key, $minutes, function () use ($place_id) {
                return $this->retaurant_repo->getRestaurantWorkDaysByPlaceId($place_id);
            });
            $this->response = $this->getBasicResponse();
            $this->response['items'] = $items;
        } catch (Exception $ex) {
            Log::error(__CLASS__ . '.' . __FUNCTION__ . ':' . $ex->getMessage());
            $msg = $ex->getMessage();
            $this->response = $this->getFailResponse($msg);
        }

        return $this->response;
    }

    public function getRestaurantReviews(Request $request)
    {
        try {
            $place_id = $request->get('place_id');
            $key = 'place_id_' . hash('md5', $place_id) . '_reviews';
            $minutes = rand(60, 100) * 60;
            $items = Cache::remember($key, $minutes, function () use ($place_id) {
                $details =  $this->getRestaurantDetailByPlaceId($place_id);
                return $details['reviews'];
            });
            $this->response = $this->getBasicResponse();
            $this->response['items'] = $items;
        } catch (Exception $ex) {
            Log::error(__CLASS__ . '.' . __FUNCTION__ . ':' . $ex->getMessage());
            $msg = $ex->getMessage();
            $this->response = $this->getFailResponse($msg);
        }

        return $this->response;
    }

    public function getRestaurantsBySort(Request $request)
    {
        try {
            $sort = $request->get('sort');
            $cache_name = $request->get('cache_name');
            $key = 'restaurants_by_sort_' . hash('md5', $sort) . '_cache_name_' . hash('md5', $cache_name);
            $minutes = rand(60, 100) * 60;
            $items = Cache::remember($key, $minutes, function () use ($sort, $cache_name) {
                return $this->retaurant_repo->getRestaurantsBySort($sort, $cache_name);
            });
            $this->response = $this->getBasicResponse();
            $this->response['items'] = $items;
        } catch (Exception $ex) {
            Log::error(__CLASS__ . '.' . __FUNCTION__ . ':' . $ex->getMessage());
            $msg = $ex->getMessage();
            $this->response = $this->getFailResponse($msg);
        }

        return $this->response;
    }
}
