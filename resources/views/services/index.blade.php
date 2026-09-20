@extends('layouts.app')

@section('title', 'استكشاف الخدمات - وين الخدمة')

@section('content')

    <div class="bg-slate-100 border-b border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900">دليل استكشاف الخدمات</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">تصفح وفلترة الخدمات والمرافق المتاحة بحسب المنطقة والتصنيف والحالة الفورية</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('services.map', request()->query()) }}" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-2xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>عرض الخدمات على الخريطة</span>
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Filters Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-filter text-emerald-600"></i> الفلاتر والبحث
                        </h3>
                        @if(request()->hasAny(['q', 'category_id', 'status', 'city', 'is_verified']))
                            <a href="{{ route('services.index') }}" class="text-xs text-rose-600 font-bold hover:underline">
                                إعادة ضبط
                            </a>
                        @endif
                    </div>

                    <form action="{{ route('services.index') }}" method="GET" class="space-y-4">
                        
                        <!-- Query Search -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">كلمة البحث</label>
                            <input type="text" 
                                   name="q" 
                                   value="{{ request('q') }}" 
                                   placeholder="اسم الخدمة، صيدلية..." 
                                   class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 focus:outline-none focus:border-emerald-600">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">التصنيف</label>
                            <select name="category_id" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 focus:outline-none focus:border-emerald-600">
                                <option value="">كل التصنيفات</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City / Location Filter -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">المدينة / المحافظة</label>
                            <input type="text" 
                                   name="city" 
                                   value="{{ request('city') }}" 
                                   placeholder="غزة، خانيونس، الرمال..." 
                                   class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 focus:outline-none focus:border-emerald-600">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">حالة الخدمة</label>
                            <select name="status" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 focus:outline-none focus:border-emerald-600">
                                <option value="">جميع الحالات</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>متاحة / مفتوحة</option>
                                <option value="crowded" {{ request('status') == 'crowded' ? 'selected' : '' }}>مزدحمة جداً</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة / غير متاحة</option>
                            </select>
                        </div>

                        <!-- Verified Only -->
                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" 
                                   name="is_verified" 
                                   id="is_verified" 
                                   value="1" 
                                   {{ request('is_verified') ? 'checked' : '' }} 
                                   class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <label for="is_verified" class="text-xs font-bold text-slate-700 cursor-pointer">
                                الخدمات الموثقة فقط <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                            </label>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl text-xs transition-all shadow-md shadow-emerald-600/20">
                            تطبيق الفلاتر
                        </button>
                    </form>
                </div>
            </div>

            <!-- Services Grid -->
            <div class="lg:col-span-3 space-y-6">
                
                <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 text-xs text-slate-600 font-bold">
                    <div class="flex items-center gap-2">
                        <span>النتائج: ({{ $services->total() }}) خدمة</span>
                        <button type="button" id="btn-index-locate" class="text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1 rounded-xl transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-location-crosshairs text-emerald-600"></i>
                            <span id="index-loc-text">حساب المسافة عن موقعي</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('services.map', request()->query()) }}" class="text-emerald-600 hover:text-emerald-700 font-extrabold flex items-center gap-1.5 bg-emerald-50/60 hover:bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 transition-colors">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <span>عرض على الخريطة</span>
                        </a>
                        <span class="text-slate-400">الصفحة {{ $services->currentPage() }} من {{ $services->lastPage() }}</span>
                    </div>
                </div>

                @if($services->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($services as $service)
                            @include('components.service-card', ['service' => $service])
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="pt-6">
                        {{ $services->links() }}
                    </div>
                @else
                    <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 p-8 space-y-3">
                        <i class="fa-solid fa-magnifying-glass text-4xl text-slate-300"></i>
                        <h3 class="font-black text-slate-800 text-lg">لم يتم العثور على خدمات مطابقة</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            جرّب تغيير كلمات البحث أو إزالة الفلاتر المحددة للوصول إلى نتائج أكثر.
                        </p>
                        <a href="{{ route('services.index') }}" class="inline-block bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-4 py-2 rounded-xl transition-colors">
                            إلغاء الفلاتر
                        </a>
                    </div>
                @endif

            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const locateBtn = document.getElementById('btn-index-locate');
        const locateText = document.getElementById('index-loc-text');

        if (locateBtn && window.WenAlKhedmaGeo) {
            const saved = window.WenAlKhedmaGeo.getUserCoords();
            if (saved && locateText) {
                locateText.textContent = 'تم تحديد موقعك (تحديث)';
            }

            locateBtn.addEventListener('click', function () {
                locateText.textContent = 'جاري تحديد موقعك...';
                window.WenAlKhedmaGeo.requestLocation(
                    function () {
                        locateText.textContent = 'تم تحديد موقعك بنجاح';
                    },
                    function () {
                        alert('تعذر الوصول إلى موقعك الحالي. تأكد من تفعيل إذن الوصول للموقع في المتصفح.');
                        locateText.textContent = 'حساب المسافة عن موقعي';
                    }
                );
            });
        }
    });
</script>
@endpush
