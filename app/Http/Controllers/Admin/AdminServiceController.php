<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = Service::with(['category', 'owner', 'location', 'documents']);

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('approval_status', $status);
        }

        $services = $query->latest()->paginate(15)->withQueryString();

        return view('admin.services.index', compact('services', 'status'));
    }

    public function approve($id)
    {
        $service = Service::findOrFail($id);
        $service->update([
            'approval_status' => 'approved',
            'is_verified' => true,
        ]);

        return back()->with('success', "تم اعتماد وإظهار الخدمة '{$service->name}' بنجاح.");
    }

    public function reject($id)
    {
        $service = Service::findOrFail($id);
        $service->update([
            'approval_status' => 'rejected',
        ]);

        return back()->with('info', "تم رفض نشر الخدمة '{$service->name}'.");
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return back()->with('success', 'تم حذف الخدمة بنجاح.');
    }
}
