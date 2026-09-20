<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Location;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = ServiceCategory::where('status', 'active')
            ->withCount(['services' => function ($query) {
                $query->where('approval_status', 'approved');
            }])
            ->get();

        $featuredServices = Service::with(['category', 'location', 'documents'])
            ->where('approval_status', 'approved')
            ->latest()
            ->take(6)
            ->get();

        $totalServices = Service::where('approval_status', 'approved')->count();
        $openServices = Service::where('approval_status', 'approved')
            ->where('current_status', 'open')
            ->count();
        $totalCategories = $categories->count();

        return view('home.index', compact('categories', 'featuredServices', 'totalServices', 'openServices', 'totalCategories'));
    }
}
