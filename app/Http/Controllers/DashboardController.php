<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Website;
use App\Models\MaintenanceReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $clientsCount = Client::count();
        $websitesCount = Website::count();
        $upcomingMaintenances = Website::whereNotNull('next_maintenance_date')
            ->where('next_maintenance_date', '<=', Carbon::now()->addDays(7))
            ->where('next_maintenance_date', '>=', Carbon::now())
            ->with('client')
            ->orderBy('next_maintenance_date')
            ->get();
        $overdueMaintenances = Website::whereNotNull('next_maintenance_date')
            ->where('next_maintenance_date', '<', Carbon::now())
            ->with('client')
            ->orderBy('next_maintenance_date')
            ->get();
        $recentReports = MaintenanceReport::with(['website.client', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'clientsCount',
            'websitesCount',
            'upcomingMaintenances',
            'overdueMaintenances',
            'recentReports'
        ));
    }
}
