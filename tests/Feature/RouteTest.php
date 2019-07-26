<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteTest extends TestCase
{
    /**
     * @test
     */
    public function index()
    {
        $uri = route('index');
        $response = $this->get($uri);
        $response->assertStatus(200);
    }
}
