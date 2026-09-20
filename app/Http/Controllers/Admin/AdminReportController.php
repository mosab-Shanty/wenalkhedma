<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = Report::with(['service', 'user', 'service.location']);

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('report_status', $status);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reports.index', compact('reports', 'status'));
    }

    public function approve($id)
    {
        $report = Report::with('service')->findOrFail($id);
        $report->update(['report_status' => 'approved']);

        // Update service status if reported status is valid service status
        if (in_array($report->reported_status, ['open', 'closed', 'crowded'])) {
            $report->service->update(['current_status' => $report->reported_status]);
        }

        return back()->with('success', 'تم قبول البلاغ وتحديث حالة الخدمة المرتبطة به بنجاح.');
    }

    public function reject($id)
    {
        $report = Report::findOrFail($id);
        $report->update(['report_status' => 'rejected']);

        return back()->with('info', 'تم رفض البلاغ.');
    }
}
