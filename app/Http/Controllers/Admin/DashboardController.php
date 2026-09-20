<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_services' => Service::count(),
            'pending_services' => Service::where('approval_status', 'pending')->count(),
            'verified_services' => Service::where('is_verified', true)->count(),
            'pending_reports' => Report::where('report_status', 'pending')->count(),
            'approved_reports' => Report::where('report_status', 'approved')->count(),
            'total_categories' => ServiceCategory::count(),
        ];

        $recentServices = Service::with(['category', 'owner', 'location'])
            ->latest()
            ->take(5)
            ->get();

        $recentReports = Report::with(['service', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentServices', 'recentReports'));
    }
}
