<?php

namespace App\Controllers\Api;

use App\Auth;
use App\Models\Photo;

class PhotoController
{
    public static function index(): void
    {
        Auth::requireDevice();

        $personId = isset($_GET['personId']) ? (int) $_GET['personId'] : null;
        $since = $_GET['since'] ?? null;

        if (!$personId) {
            json_response(['error' => 'personId is required'], 400);
        }

        $photos = Photo::findAll($personId, $since);

        $appUrl = rtrim(config('app_url'), '/');
        $result = [];
        foreach ($photos as $p) {
            $result[] = [
                'id'          => (int) $p['id'],
                'url'         => $appUrl . '/uploads/' . $p['filename'],
                'checksum'    => $p['checksum'],
                'mime'        => $p['mime'],
                'description' => $p['description'] ?? null,
                'createdAt'   => date('c', strtotime($p['created_at'])),
            ];
        }

        json_response(['photos' => $result]);
    }
}
