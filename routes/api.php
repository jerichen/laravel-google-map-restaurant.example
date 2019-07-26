<?php

Route::get('getRestaurants', 'Api\GoogleMapApiController@getRestaurants')
    ->name('api.get.restaurants');

Route::get('getRestaurantDetail', 'Api\GoogleMapApiController@getRestaurantDetail')
    ->name('api.get.restaurant.detail');

Route::get('getRestaurantWorkDays', 'Api\GoogleMapApiController@getRestaurantWorkDays')
    ->name('api.get.restaurant.work.days');

Route::get('getRestaurantReviews', 'Api\GoogleMapApiController@getRestaurantReviews')
    ->name('api.get.restaurant.reviews');

Route::get('getRestaurantsBySort', 'Api\GoogleMapApiController@getRestaurantsBySort')
    ->name('api.get.restaurants.by.sort');

Route::get('getGeolocation', 'Api\GoogleMapApiController@getGeolocation')
    ->name('api.get.geolocation');
