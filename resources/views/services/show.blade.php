@extends('layouts.app')

@section('title', $service->name . ' - تفاصيل الخدمة')

@push('styles')
    <!-- Leaflet CSS for Interactive Embedded Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')

@php
    $document = $service->documents->first();
    $imageUrl = $document ? $document->file_url : asset('default-service.png');
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Breadcrumb Nav -->
    <nav class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">الرئيسية</a>
        <i class="fa-solid fa-chevron-left text-[10px]"></i>
        <a href="{{ route('services.index') }}" class="hover:text-emerald-600">الخدمات</a>
        <i class="fa-solid fa-chevron-left text-[10px]"></i>
        <span class="text-slate-900 truncate">{{ $service->name }}</span>
    </nav>

    <!-- Details Card Header -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-3">
        
        <!-- Image Box -->
        <div class="lg:col-span-1 h-72 lg:h-full bg-slate-100 relative">
            <img src="{{ $imageUrl }}" alt="{{ $service->name }}" class="w-full h-full object-cover" onError="this.src='{{ asset('default-service.png') }}'">
            
            <span class="absolute top-4 right-4 bg-white/90 backdrop-blur-md text-slate-800 text-xs font-extrabold px-3.5 py-1.5 rounded-full shadow-md">
                {{ $service->category->name ?? 'خدمة' }}
            </span>
        </div>

        <!-- Details Main Info -->
        <div class="lg:col-span-2 p-6 sm:p-8 flex flex-col justify-between space-y-6">
            
            <div class="space-y-4">
                
                <!-- Status & Badges -->
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-2">
                        @include('components.status-badge', ['status' => $service->current_status])

                        @if($service->is_verified)
                            <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> موثقة إدارياً
                            </span>
                        @endif
                    </div>

                    <span class="text-xs text-slate-400 font-medium">
                        تاريخ النشر: {{ $service->created_at->format('Y-m-d') }}
                    </span>
                </div>

                <!-- Title & Owner -->
                <div>
                    <h1 class="text-3xl font-black text-slate-900 mb-2">{{ $service->name }}</h1>
                    <p class="text-xs text-slate-500 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-user text-slate-400"></i> بواسطة المقدم: <span class="text-slate-800 font-bold">{{ $service->owner->full_name ?? 'مستخدم موثوق' }}</span>
                    </p>
                </div>

                <!-- Phone & Address -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs">
                    <div>
                        <span class="block text-slate-400 font-medium mb-1">رقم الهاتف والتواصل</span>
                        @if($service->phone)
                            <a href="tel:{{ $service->phone }}" class="font-extrabold text-slate-900 text-sm hover:text-emerald-600 dir-ltr inline-block">
                                <i class="fa-solid fa-phone text-emerald-600 ml-1"></i> {{ $service->phone }}
                            </a>
                        @else
                            <span class="font-bold text-slate-500">غير متوفر</span>
                        @endif
                    </div>

                    <div>
                        <span class="block text-slate-400 font-medium mb-1">العنوان الجغرافي</span>
                        <span class="font-extrabold text-slate-900 text-sm">
                            <i class="fa-solid fa-location-dot text-rose-500 ml-1"></i>
                            {{ $service->location->governorate ?? 'غزة' }} - {{ $service->location->city ?? '' }} {{ $service->location->area ? '('.$service->location->area.')' : '' }}
                        </span>
                    </div>
                </div>

                @if($service->location && $service->location->latitude && $service->location->longitude)
                    <div id="service-detail-distance" 
                         data-lat="{{ $service->location->latitude }}" 
                         data-lng="{{ $service->location->longitude }}" 
                         class="hidden items-center gap-2 bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-2.5 rounded-2xl text-xs font-bold shadow-sm">
                        <i class="fa-solid fa-person-walking text-emerald-600"></i>
                        <span>تبعد عن موقعك الحالي: <span class="font-black text-emerald-700">...</span></span>
                    </div>
                @endif

            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-100">
                
                @auth
                    <!-- Favorite Form Button -->
                    <form action="{{ route('favorites.toggle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->service_id }}">
                        <button type="submit" class="px-5 py-3 rounded-2xl font-bold text-xs transition-all flex items-center gap-2 {{ $isFavorited ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            <i class="fa-solid fa-heart {{ $isFavorited ? 'text-rose-500' : 'text-slate-400' }}"></i>
                            <span>{{ $isFavorited ? 'محفوظة بالمفضلة' : 'إضافة للمفضلة' }}</span>
                        </button>
                    </form>

                    <!-- Report Button -->
                    <a href="{{ route('reports.create', ['service_id' => $service->service_id]) }}" 
                       class="bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 px-5 py-3 rounded-2xl font-bold text-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-flag text-amber-600"></i>
                        <span>إرسال بلاغ عن تحديث الحالة</span>
                    </a>
                @else
                    <!-- Guest Notice Buttons -->
                    <div x-data="{ noticeModal: false }" class="w-full flex flex-wrap gap-3">
                        <button @click="noticeModal = true" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-3 rounded-2xl font-bold text-xs transition-all flex items-center gap-2">
                            <i class="fa-solid fa-heart text-slate-400"></i>
                            <span>إضافة للمفضلة</span>
                        </button>

                        <button @click="noticeModal = true" class="bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 px-5 py-3 rounded-2xl font-bold text-xs transition-all flex items-center gap-2">
                            <i class="fa-solid fa-flag text-amber-600"></i>
                            <span>إرسال بلاغ أو تحديث</span>
                        </button>

                        <!-- Guest Prompt Modal -->
                        <div x-show="noticeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                            <div @click.away="noticeModal = false" class="bg-white max-w-md w-full rounded-3xl p-6 shadow-2xl text-center space-y-4">
                                <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-2xl">
                                    <i class="fa-solid fa-user-lock"></i>
                                </div>
                                <h3 class="font-extrabold text-slate-900 text-lg">يتطلب هذا الإجراء تسجيل الدخول</h3>
                                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                    لإكمال عملية التفاعل كإضافة الخدمة للمفضلة أو تقديم بلاغ عن حالتها الحالية، يجب عليك تسجيل الدخول أو إنشاء حساب جديد.
                                </p>
                                <div class="flex items-center gap-2 pt-2">
                                    <a href="{{ route('login') }}" class="w-1/2 bg-emerald-600 text-white font-bold py-2.5 rounded-xl text-xs text-center">تسجيل الدخول</a>
                                    <a href="{{ route('register') }}" class="w-1/2 border border-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-xs text-center">حساب جديد</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth

            </div>

        </div>

    </div>

    <!-- Description & Location Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Description & Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-900 text-lg border-b border-slate-100 pb-3">وصف ومحتوى الخدمة</h3>
                <p class="text-sm text-slate-700 leading-relaxed font-normal whitespace-pre-line">
                    {{ $service->description ?? 'لا يوجد وصف تفصيلي إضافي تم إدخاله لهذه الخدمة.' }}
                </p>
            </div>

            <!-- Documents & Files attached -->
            @if($service->documents->count() > 0)
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3">الصور والمستندات المرفقة</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($service->documents as $doc)
                            <a href="{{ $doc->file_url }}" target="_blank" class="group block rounded-2xl overflow-hidden border border-slate-200 h-32 relative bg-slate-100">
                                <img src="{{ $doc->file_url }}" alt="وثيقة" class="w-full h-full object-cover group-hover:scale-105 transition-transform" onError="this.src='{{ asset('default-service.png') }}'">
                                <span class="absolute bottom-2 right-2 bg-slate-900/80 text-white text-[10px] px-2 py-0.5 rounded-md font-bold">عرض</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Embedded Platform Location Map & Sidebar -->
        <div class="space-y-6">
            
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>خريطة الموقع التفاعلية</span>
                    <span class="text-xs text-emerald-600 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-map-location-dot"></i> داخل المنصة
                    </span>
                </h3>
                
                <div class="space-y-2 text-xs text-slate-600">
                    <p class="flex items-start gap-2">
                        <i class="fa-solid fa-map-pin text-rose-500 mt-0.5"></i>
                        <span class="font-semibold">{{ $service->location->address_text ?? 'العنوان: غزة، فلسطين' }}</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fa-solid fa-compass text-emerald-600"></i>
                        <span>الإحداثيات: {{ $service->location->latitude ?? '31.5000' }}, {{ $service->location->longitude ?? '34.4667' }}</span>
                    </p>
                </div>

                <!-- Interactive Leaflet Map Container -->
                <div id="service-map" class="w-full h-64 rounded-2xl border border-slate-200 overflow-hidden shadow-inner z-10"></div>

                <div class="pt-1 flex items-center justify-between">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $service->location->latitude ?? 31.5000 }},{{ $service->location->longitude ?? 34.4667 }}" 
                       target="_blank" 
                       class="text-[11px] text-slate-400 hover:text-emerald-600 font-bold flex items-center gap-1">
                        <span>فتح خارجي في Google Maps</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    </a>

                    <a href="{{ route('services.map') }}" 
                       class="text-[11px] text-emerald-600 hover:underline font-bold flex items-center gap-1">
                        <span>خريطة المنصة الكاملة</span>
                        <i class="fa-solid fa-chevron-left text-[9px]"></i>
                    </a>
                </div>
            </div>

            <!-- Related Services in category -->
            @if($relatedServices->count() > 0)
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3">خدمات مشابهة بنفس التصنيف</h3>
                    <div class="space-y-3">
                        @foreach($relatedServices as $rel)
                            <a href="{{ route('services.show', $rel->service_id) }}" class="flex items-center justify-between p-2 rounded-2xl hover:bg-slate-50 transition-colors">
                                <span class="font-bold text-xs text-slate-800 hover:text-emerald-600 truncate">{{ $rel->name }}</span>
                                @include('components.status-badge', ['status' => $rel->current_status])
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>

@endsection

@push('scripts')
    <!-- Leaflet JS Script for Interactive Map -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lat = {{ $service->location->latitude ?? 31.5000 }};
            const lng = {{ $service->location->longitude ?? 34.4667 }};
            const serviceName = @json($service->name);
            const addressText = @json($service->location->address_text ?? 'غزة، فلسطين');
            
            // Initialize Leaflet Map centered at service coordinates
            const map = L.map('service-map').setView([lat, lng], 15);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Add Custom Marker & Popup
            const marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(`
                <div style="font-family:'IBM Plex Sans Arabic', sans-serif; text-align:right; direction:rtl;">
                    <strong style="font-family:'Thmanyah', 'IBM Plex Sans Arabic', sans-serif; font-size:14px; color:#0f172a;">${serviceName}</strong><br>
                    <span style="font-size:11px; color:#475569;">${addressText}</span>
                </div>
            `).openPopup();
        });
    </script>
@endpush
