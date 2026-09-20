<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,service_id',
            'reported_status' => 'required|in:open,closed,crowded,unknown',
            'note' => 'nullable|string|max:500',
            'image_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات البلاغ غير مكتملة',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            $report = Report::create([
                'service_id' => $request->service_id,
                'user_id' => $request->user()->user_id,
                'reported_status' => $request->reported_status,
                'note' => $request->note,
                'image_url' => $request->image_url ?? null,
                'report_status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'تم إرسال البلاغ بنجاح وهو الآن بانتظار المراجعة',
                'data' => $report->load('user:user_id,full_name')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء حفظ البلاغ',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function serviceReports($service_id)
    {
        $reports = Report::where('service_id', $service_id)
            ->with('user:user_id,full_name')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'count' => $reports->count(),
            'data' => $reports
        ]);
    }

    // عرض البلاغات التي تنتظر المراجعة
    public function pendingReports()
    {
        $reports = Report::with([
            'user:user_id,full_name',
            'service'
        ])
            ->where('report_status', 'pending')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'count' => $reports->count(),
            'data' => $reports
        ]);
    }

    public function approveReport($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'status' => false,
                'message' => 'البلاغ غير موجود'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $report->report_status = 'approved';
            $report->save();

            $service = Service::find($report->service_id);

            if ($service) {
                $service->current_status = $report->reported_status;
                $service->save();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'تم اعتماد البلاغ وتحديث حالة الخدمة بنجاح',
                'data' => $report->load('service')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء اعتماد البلاغ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rejectReport($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'status' => false,
                'message' => 'البلاغ غير موجود'
            ], 404);
        }

        $report->report_status = 'rejected';
        $report->save();

        return response()->json([
            'status' => true,
            'message' => 'تم رفض البلاغ بنجاح',
            'data' => $report
        ]);
    }
}
