<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Location;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['category', 'location', 'documents', 'owner'])
            ->where('approval_status', 'approved');

        // Search Query (Service Name, Category, City, Area)
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('location', function ($locQ) use ($search) {
                      $locQ->where('city', 'like', "%{$search}%")
                           ->orWhere('governorate', 'like', "%{$search}%")
                           ->orWhere('area', 'like', "%{$search}%")
                           ->orWhere('address_text', 'like', "%{$search}%");
                  });
            });
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('current_status', $request->input('status'));
        }

        // Verified Filter
        if ($request->has('is_verified') && $request->input('is_verified') !== '') {
            $query->where('is_verified', (bool) $request->input('is_verified'));
        }

        // Location Filters (City / Governorate)
        if ($request->filled('city')) {
            $city = $request->input('city');
            $query->whereHas('location', function ($lq) use ($city) {
                $lq->where('city', 'like', "%{$city}%")
                   ->orWhere('governorate', 'like', "%{$city}%")
                   ->orWhere('area', 'like', "%{$city}%");
            });
        }

        // Pagination
        $services = $query->latest()->paginate(12)->withQueryString();
        $categories = ServiceCategory::where('status', 'active')->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function map(Request $request)
    {
        $categories = ServiceCategory::where('status', 'active')->get();

        $query = Service::with(['category', 'location', 'documents'])
            ->where('approval_status', 'approved')
            ->whereHas('location', function ($q) {
                $q->whereNotNull('latitude')->whereNotNull('longitude');
            });

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('location', function ($locQ) use ($search) {
                      $locQ->where('city', 'like', "%{$search}%")
                           ->orWhere('governorate', 'like', "%{$search}%")
                           ->orWhere('area', 'like', "%{$search}%")
                           ->orWhere('address_text', 'like', "%{$search}%");
                  });
            });
        }

        $services = $query->get();

        $servicesMapData = $services->map(function ($service) {
            $document = $service->documents->first();
            $imageUrl = $document ? $document->file_url : asset('default-service.png');

            return [
                'id' => $service->service_id,
                'name' => $service->name,
                'description' => Str::limit($service->description ?? '', 100),
                'category_id' => $service->category_id,
                'category_name' => $service->category->name ?? 'خدمة',
                'status' => $service->current_status,
                'phone' => $service->phone,
                'lat' => (float) ($service->location->latitude ?? 31.5000),
                'lng' => (float) ($service->location->longitude ?? 34.4667),
                'city' => $service->location->city ?? 'غزة',
                'area' => $service->location->area ?? '',
                'address' => $service->location->address_text ?? '',
                'image_url' => $imageUrl,
                'show_url' => route('services.show', $service->service_id),
                'is_verified' => (bool) $service->is_verified,
            ];
        });

        return view('services.map', compact('categories', 'services', 'servicesMapData'));
    }

    public function show($id)
    {
        $service = Service::with(['category', 'location', 'documents', 'owner', 'reports'])
            ->findOrFail($id);

        $isFavorited = false;
        if (Auth::check()) {
            $isFavorited = Auth::user()->favorites()->where('service_id', $service->service_id)->exists();
        }

        $relatedServices = Service::with(['category', 'location', 'documents'])
            ->where('approval_status', 'approved')
            ->where('category_id', $service->category_id)
            ->where('service_id', '!=', $service->service_id)
            ->take(3)
            ->get();

        return view('services.show', compact('service', 'isFavorited', 'relatedServices'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('status', 'active')->get();
        return view('services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:service_categories,category_id'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_status' => ['required', 'in:open,closed,crowded,unknown'],
            'opening_hours_text' => ['nullable', 'string'],
            'governorate' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'address_text' => ['nullable', 'string'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
            'file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ], [
            'name.required' => 'اسم الخدمة مطلوب',
            'category_id.required' => 'اختر تصنيف الخدمة',
            'current_status.required' => 'حدد حالة الخدمة الإقليمية',
        ]);

        $openingHoursText = $request->input('opening_hours_text');

        $service = Service::create([
            'user_id' => Auth::user()->user_id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'current_status' => $validated['current_status'],
            'opening_hours' => $openingHoursText ? ['details' => $openingHoursText] : null,
            'is_verified' => false,
            'approval_status' => 'pending',
        ]);

        // Create Location Record
        Location::create([
            'service_id' => $service->service_id,
            'governorate' => !empty($validated['governorate']) ? $validated['governorate'] : 'غزة',
            'city' => !empty($validated['city']) ? $validated['city'] : 'غزة',
            'area' => $validated['area'] ?? null,
            'address_text' => $validated['address_text'] ?? null,
            'latitude' => (!empty($validated['latitude']) && is_numeric($validated['latitude'])) ? (float)$validated['latitude'] : 31.5000,
            'longitude' => (!empty($validated['longitude']) && is_numeric($validated['longitude'])) ? (float)$validated['longitude'] : 34.4667,
        ]);

        // File Upload if provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('services_documents', 'public');
            Document::create([
                'user_id' => Auth::user()->user_id,
                'service_id' => $service->service_id,
                'file_type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'document',
                'file_url' => Storage::url($filePath),
                'description' => 'صورة/ملف مرفق مع الخدمة',
                'status' => 'approved',
            ]);
        }

        return redirect()->route('services.mine')->with('success', 'تم إرسال الخدمة بنجاح، وهي الآن قيد المراجعة والاعتماد من الإدارة.');
    }

    public function edit($id)
    {
        $service = Service::with('location')->where('user_id', Auth::user()->user_id)->findOrFail($id);
        $categories = ServiceCategory::where('status', 'active')->get();
        return view('services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::where('user_id', Auth::user()->user_id)->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:service_categories,category_id'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_status' => ['required', 'in:open,closed,crowded,unknown'],
            'governorate' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'area' => ['nullable', 'string'],
            'address_text' => ['nullable', 'string'],
        ]);

        $service->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'current_status' => $validated['current_status'],
        ]);

        if ($service->location) {
            $service->location->update([
                'governorate' => $validated['governorate'] ?? $service->location->governorate,
                'city' => $validated['city'] ?? $service->location->city,
                'area' => $validated['area'] ?? $service->location->area,
                'address_text' => $validated['address_text'] ?? $service->location->address_text,
            ]);
        }

        return redirect()->route('services.mine')->with('success', 'تم تحديث بيانات الخدمة بنجاح.');
    }

    public function myServices()
    {
        $services = Service::with(['category', 'location', 'reports'])
            ->where('user_id', Auth::user()->user_id)
            ->latest()
            ->get();

        return view('services.my_services', compact('services'));
    }
}
