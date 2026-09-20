<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['service', 'service.location'])
            ->where('user_id', Auth::user()->user_id)
            ->latest()
            ->get();

        return view('reports.index', compact('reports'));
    }

    public function create(Request $request)
    {
        $serviceId = $request->query('service_id');
        $service = null;
        if ($serviceId) {
            $service = Service::where('approval_status', 'approved')->find($serviceId);
        }

        $services = Service::where('approval_status', 'approved')->select('service_id', 'name')->get();

        return view('reports.create', compact('service', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,service_id'],
            'reported_status' => ['required', 'in:open,closed,crowded,wrong_location'],
            'note' => ['required', 'string', 'min:5'],
            'image' => ['nullable', 'image', 'max:5120'],
        ], [
            'service_id.required' => 'اختر الخدمة المبلغ عنها',
            'reported_status.required' => 'حدد موضوع/حالة البلاغ',
            'note.required' => 'اكتب تفاصيل أو ملاحظات البلاغ',
            'note.min' => 'يجب أن لا تقل الملاحظات عن 5 أحرف',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('reports_images', 'public');
            $imageUrl = Storage::url($path);
        }

        Report::create([
            'user_id' => Auth::user()->user_id,
            'service_id' => $validated['service_id'],
            'reported_status' => $validated['reported_status'],
            'note' => $validated['note'],
            'image_url' => $imageUrl,
            'report_status' => 'pending',
        ]);

        return redirect()->route('reports.index')->with('success', 'شكراً لك! تم إرسال البلاغ وسيقوم مشرفو المنصة بمراجعته والتحقق منه.');
    }
}
