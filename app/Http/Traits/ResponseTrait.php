<?php

namespace App\Http\Traits;

/**
 *  Trait for response
 */
trait ResponseTrait
{
    // Helper to give response a basic format
    public function response($success, $message, $data, $responseCode)
    {
        return response()->json(compact('success', 'message', 'data'), $responseCode);
    }
}
