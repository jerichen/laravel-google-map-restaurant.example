<?php

namespace App\Traits;

trait BasicResponseTrait
{
    protected function getBasicResponse()
    {
        return $collect = collect([
            'success' => true,
            'code' => '0000',
            'msg' => 'loading success',
            'items' => [],
        ]);
    }

    protected function getFailResponse($error_message)
    {
        return $collect = collect([
            'success' => false,
            'code' => '0001',
            'msg' => $error_message,
            'items' => []
        ]);
    }
}
