<?php

namespace App\Http\Traits;

trait ResponseFormat
{
    public function sendRespose($message , $status = true, $data = [])
    {
        return json_encode([
            'success' => $status,
            'msg' => $message,
            'data' => $data
        ]);
    }
}