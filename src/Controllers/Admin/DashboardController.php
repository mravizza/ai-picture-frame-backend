<?php

namespace App\Controllers\Admin;

use App\Auth;
use App\Models\Person;
use App\Models\Photo;
use App\Models\Device;
use App\Models\Log;

class DashboardController
{
    public static function index(): void
    {
        Auth::requireAdmin();

        $personCount = Person::count();
        $activePersonCount = Person::count(true);
        $photoCount = Photo::count();
        $activeDevices = Device::countActive();
        $recentLogs = Log::countRecent(24);
        $latestLogs = Log::findAll([], 10);

        $pageTitle = 'Dashboard';
        $template = 'dashboard/index';
        include TEMPLATE_PATH . '/layout.php';
    }
}
