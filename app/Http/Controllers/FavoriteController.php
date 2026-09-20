<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::with(['service', 'service.category', 'service.location', 'service.documents'])
            ->where('user_id', Auth::user()->user_id)
            ->latest()
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,service_id'],
        ]);

        $userId = Auth::user()->user_id;
        $serviceId = $request->input('service_id');

        $existing = Favorite::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'تم إزالة الخدمة من المفضلة.';
            $added = false;
        } else {
            Favorite::create([
                'user_id' => $userId,
                'service_id' => $serviceId,
            ]);
            $message = 'تم إضافة الخدمة إلى المفضلة.';
            $added = true;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'message' => $message,
            ]);
        }

        return back()->with('info', $message);
    }
}
