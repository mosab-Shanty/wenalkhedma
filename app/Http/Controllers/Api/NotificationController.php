<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->user_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'count' => $notifications->count(),
            'unread_count' => $notifications->where('is_read', false)->count(),
            'data' => $notifications
        ]);
    }


    public function markAsRead($id, Request $request)
    {
        $notification = Notification::where('notification_id', $id)
            ->where('user_id', $request->user()->user_id)
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'الإشعار غير موجود'
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث حالة الإشعار بنجاح'
        ]);
    }
}
