<?php

namespace App\Controllers\Admin;

use App\Auth;
use App\Models\Log;
use App\Models\Person;

class LogController
{
    public static function index(): void
    {
        Auth::requireAdmin();

        $filters = [
            'device_id' => $_GET['device_id'] ?? '',
            'person_id' => $_GET['person_id'] ?? '',
            'type'      => $_GET['type'] ?? '',
            'from'      => $_GET['from'] ?? '',
            'to'        => $_GET['to'] ?? '',
        ];

        $logs = Log::findAll(array_filter($filters));
        $logTypes = Log::getTypes();
        $logDeviceIds = Log::getDeviceIds();
        $persons = Person::findAll();

        $pageTitle = 'Logs';
        $template = 'logs/index';
        include TEMPLATE_PATH . '/layout.php';
    }

    public static function detail(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $log = Log::findById($id);
        if (!$log) {
            redirect('/admin/logs');
        }

        $pageTitle = 'Log #' . $id;
        $template = 'logs/detail';
        include TEMPLATE_PATH . '/layout.php';
    }
}
