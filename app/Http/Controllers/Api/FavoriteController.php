<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{

    public function toggle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,service_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->user_id;
        $serviceId = $request->service_id;

        $favorite = Favorite::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => true,
                'is_favorite' => false,
                'message' => 'تم إزالة الخدمة من المفضلة'
            ]);
        } else {
            Favorite::create([
                'user_id' => $userId,
                'service_id' => $serviceId,
            ]);

            return response()->json([
                'status' => true,
                'is_favorite' => true,
                'message' => 'تم إضافة الخدمة إلى المفضلة'
            ], 201);
        }
    }


    public function index(Request $request)
    {
        $favorites = Favorite::where('user_id', $request->user()->user_id)
            ->with(['service.category', 'service.location'])
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'count' => $favorites->count(),
            'data' => $favorites
        ]);
    }
}
