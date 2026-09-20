<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount(['services', 'reports'])
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $newStatus = $user->status === 'active' ? 'blocked' : 'active';

        $user->update(['status' => $newStatus]);

        return back()->with('success', "تم تغيير حالة المستخدم {$user->full_name} إلى {$newStatus}.");
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => ['required', 'in:user,moderator,admin'],
        ]);

        $user->update(['role' => $request->input('role')]);

        return back()->with('success', "تم تحديث دور المستخدم {$user->full_name} إلى {$request->input('role')}.");
    }
}
