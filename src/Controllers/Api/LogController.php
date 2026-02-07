<?php

namespace App\Controllers\Api;

use App\Auth;
use App\Models\Log;

class LogController
{
    public static function store(): void
    {
        $authDevice = Auth::requireDevice();

        $body = json_body();

        $type = trim($body['type'] ?? '');
        if ($type === '') {
            json_response(['error' => 'type is required'], 400);
        }

        $logId = Log::create([
            'device_id' => $body['deviceId'] ?? $authDevice['device_id'],
            'person_id' => isset($body['personId']) ? (int) $body['personId'] : null,
            'type'      => $type,
            'message'   => $body['message'] ?? null,
            'payload'   => isset($body['payload']) ? json_encode($body['payload']) : null,
        ]);

        json_response(['ok' => true, 'logId' => $logId], 201);
    }
}
