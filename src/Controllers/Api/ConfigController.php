<?php

namespace App\Controllers\Api;

use App\Auth;
use App\Models\Device;

class ConfigController
{
    public static function index(): void
    {
        $authDevice = Auth::requireDevice();

        $deviceId = $_GET['deviceId'] ?? $authDevice['device_id'];
        $device = Device::findByDeviceId($deviceId);

        if (!$device) {
            json_response(['error' => 'Device not found'], 404);
        }

        json_response([
            'deviceId'              => $device['device_id'],
            'slideshowIntervalSec'  => (int) $device['slideshow_interval_sec'],
            'faceStableSec'         => (int) $device['face_stable_sec'],
            'cooldownSec'           => (int) $device['cooldown_sec'],
            'similarityThreshold'   => (float) $device['similarity_threshold'],
            'ttsEnabled'            => (bool) $device['tts_enabled'],
            'updatedAt'             => date('c', strtotime($device['updated_at'])),
        ]);
    }
}
