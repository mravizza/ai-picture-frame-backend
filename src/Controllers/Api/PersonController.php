<?php

namespace App\Controllers\Api;

use App\Auth;
use App\Models\Person;
use App\Models\PersonPhoto;

class PersonController
{
    public static function index(): void
    {
        Auth::requireDevice();

        $persons = Person::findAll(true); // active only

        $result = [];
        foreach ($persons as $p) {
            $result[] = [
                'id'                => (int) $p['id'],
                'vorname'           => $p['vorname'],
                'rolleBeziehung'    => $p['rolle_beziehung'],
                'bevorzugteAnrede'  => $p['bevorzugte_anrede'],
                'greetingText'      => $p['greeting_text'],
                'themen'            => $p['themen'],
                'gespraechslaenge'  => $p['gespraechslaenge'],
                'photoCount'        => (int) ($p['photo_count'] ?? 0),
            ];
        }

        json_response(['persons' => $result]);
    }
}
