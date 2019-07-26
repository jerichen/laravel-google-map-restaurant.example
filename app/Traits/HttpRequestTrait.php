<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait HttpRequestTrait
{
    protected function googleMapRequests($method, $uri, $params)
    {
        $client = new Client();
        $response = $client->request($method, $uri, [
            'query' => $params,
        ]);

        return json_decode($response->getBody(), true);
    }
}
