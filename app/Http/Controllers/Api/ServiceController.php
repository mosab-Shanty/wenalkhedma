<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Location;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function categories()
    {
        return response()->json([
            'status' => true,
            'data' => ServiceCategory::all()
        ]);
    }


    public function index(Request $request)
    {
        $query = Service::with([
            'category',
            'location',
            'owner'
        ])
            ->where('approval_status', 'approved');


        // فلترة حسب التصنيف
        if ($request->filled('category_id')) {

            $query->where(
                'category_id',
                $request->category_id
            );

        }


        // فلترة حسب حالة الخدمة
        if ($request->filled('status')) {

            $query->where(
                'current_status',
                $request->status
            );

        }


        // البحث
        if ($request->filled('search')) {

            $search = $request->search;


            $query->where(function ($q) use ($search) {


                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )
                    ->orWhere(
                        'description',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        '%' . $search . '%'
                    );


            });

        }


        $services = $query
            ->latest()
            ->get();


        return response()->json([

            'status' => true,

            'count' => $services->count(),

            'data' => $services

        ]);

    }


    public function nearby(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'يرجى تزويد الإحداثيات بشكل صحيح',
                'errors' => $validator->errors()
            ], 422);
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $radius = $request->radius ?? 5;

        $locations = Location::select('*')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
                [$lat, $lng, $lat]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->with(['service.category'])
            ->get();

        return response()->json([
            'status' => true,
            'count' => $locations->count(),
            'data' => $locations
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:service_categories,category_id',
            'phone' => 'nullable|string',
            'description' => 'nullable|string',
            'current_status' => 'required|in:open,closed,crowded,unknown',

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'governorate' => 'nullable|string',
            'city' => 'nullable|string',
            'area' => 'nullable|string',
            'address_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $service = Service::create([
                'user_id' => $request->user()->user_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'phone' => $request->phone,
                'current_status' => $request->current_status,
                'opening_hours' => $request->opening_hours ?? null,
            ]);

            $location = Location::create([
                'service_id' => $service->service_id,
                'governorate' => $request->governorate,
                'city' => $request->city,
                'area' => $request->area,
                'address_text' => $request->address_text,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'تمت إضافة الخدمة وموقعها بنجاح',
                'data' => $service->load('location')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء حفظ الخدمة',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $service = Service::with([
            'category',
            'location',
            'reports',
            'owner',
            'documents'
        ])->find($id);

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'الخدمة غير موجودة'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $service
        ]);
    }


    // عرض الخدمات التي تنتظر المراجعة
    public function pendingServices()
    {
        $services = Service::with([
            'category',
            'location',
            'owner'
        ])
            ->where('approval_status', 'pending')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'count' => $services->count(),
            'data' => $services
        ]);
    }


    // اعتماد الخدمة
    public function approveService($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'الخدمة غير موجودة'
            ], 404);
        }

        $service->approval_status = 'approved';
        $service->is_verified = true;
        $service->save();

        return response()->json([
            'status' => true,
            'message' => 'تم اعتماد الخدمة بنجاح',
            'data' => $service
        ]);
    }

    // رفض الخدمة
    public function rejectService($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'الخدمة غير موجودة'
            ], 404);
        }

        $service->approval_status = 'rejected';
        $service->is_verified = false;
        $service->save();

        return response()->json([
            'status' => true,
            'message' => 'تم رفض الخدمة بنجاح',
            'data' => $service
        ]);
    }

    // عرض خدمات المستخدم الحالي
    public function myServices(Request $request)
    {

        $services = Service::with([
            'category',
            'location'
        ])
            ->where(
                'user_id',
                $request->user()->user_id
            )
            ->latest()
            ->get();


        return response()->json([

            'status' => true,

            'count' => $services->count(),

            'data' => $services

        ]);

    }
}
