<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ValidationResponse
{
    public function validationToJson($data):JsonResponse
    {
        return response()->json([ 'error' => true , 'errors' => $data]);
    }
}