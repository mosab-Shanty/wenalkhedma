@extends('layouts.app')

@section('title', 'إضافة خدمة جديدة - وين الخدمة')

@push('styles')
    <!-- Leaflet CSS for Map Picker -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-10 space-y-8">

        <!-- Form Title -->
        <div class="border-b border-slate-100 pb-6 text-center sm:text-right">
            <h1 class="text-2xl font-black text-slate-900">إضافة خدمة جديدة</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">قم بتعبئة بيانات الخدمة بدقة وسيتم مراجعتها واعتمدها من المشرفين فوراً</p>
        </div>

        <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Basic Info Section -->
            <div class="space-y-6">
                <h3 class="font-extrabold text-sm text-emerald-700 bg-emerald-50 px-4 py-2 rounded-xl inline-block">
                    <i class="fa-solid fa-info-circle ml-1"></i> الخطوة 1: المعلومات الأساسية
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">اسم الخدمة / المنشأة *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: صيدلية الرمال المركزية" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">التصنيف الرئيسي *</label>
                        <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
                            <option value="">اختر التصنيف المناسب</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}" {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">حالة الخدمة التشغيلية الآن *</label>
                        <select name="current_status" required class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
                            <option value="open" {{ old('current_status') == 'open' ? 'selected' : '' }}>متاحة / مفتوحة</option>
                            <option value="crowded" {{ old('current_status') == 'crowded' ? 'selected' : '' }}>مزدحمة جداً</option>
                            <option value="closed" {{ old('current_status') == 'closed' ? 'selected' : '' }}>مغلقة / غير متاحة</option>
                            <option value="unknown" {{ old('current_status') == 'unknown' ? 'selected' : '' }}>غير محدد</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">رقم الهاتف للتواصل</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="059xxxxxxx" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600 dir-ltr">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">وصف تفصيلي للخدمة والتسهيلات المتاحة</label>
                    <textarea name="description" rows="3" placeholder="اكتب وصفاً قصيراً يشرح طبيعة الخدمات أو المواد التموينية المتوفرة..." class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Location Section with Interactive Map Picker -->
            <div class="space-y-6 pt-6 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <h3 class="font-extrabold text-sm text-emerald-700 bg-emerald-50 px-4 py-2 rounded-xl inline-block">
                        <i class="fa-solid fa-map-location-dot ml-1"></i> الخطوة 2: موقع الخدمة والجغرافيا
                    </h3>

                    <button type="button" id="btn-locate-me" class="bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-bold text-xs px-3 py-1.5 rounded-xl border border-slate-200 transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-location-crosshairs text-emerald-600"></i> تحديد موقعي الحالي تلقائياً
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">المحافظة</label>
                        <input type="text" name="governorate" value="{{ old('governorate', 'غزة') }}" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">المدينة / البلدة</label>
                        <input type="text" name="city" value="{{ old('city', 'غزة') }}" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">المنطقة / الحي</label>
                        <input type="text" name="area" value="{{ old('area') }}" placeholder="الرمال، تل الهوى..." class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">العنوان بالتفصيل المعلم البارز</label>
                    <input type="text" name="address_text" value="{{ old('address_text') }}" placeholder="مثال: بجوار مستشفى الشفاء، شارع النصر" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
                </div>

                <!-- Interactive Platform Map for Selection -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">حدد الموقع بدقة بالنقر على الخريطة مباشرة:</label>
                    <div id="picker-map" class="w-full h-72 rounded-2xl border border-slate-200 overflow-hidden shadow-inner z-10"></div>
                    <p class="text-[11px] text-slate-400">انقر في أي مكان على الخريطة لتحديد دبوس الموقع تلقائياً.</p>
                </div>

                <!-- Latitude / Longitude hidden inputs -->
                <input type="hidden" name="latitude" id="lat-input" value="{{ old('latitude', '31.5000') }}">
                <input type="hidden" name="longitude" id="lng-input" value="{{ old('longitude', '34.4667') }}">
            </div>

            <!-- Upload Media Section -->
            <div class="space-y-6 pt-6 border-t border-slate-100">
                <h3 class="font-extrabold text-sm text-emerald-700 bg-emerald-50 px-4 py-2 rounded-xl inline-block">
                    <i class="fa-solid fa-image ml-1"></i> الخطوة 3: صورة/ملف توثيقي للخدمة
                </h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">رفع واجهة الخدمة أو ترخيص المرفق (اختياري)</label>
                    <input type="file" name="file" accept="image/*,.pdf" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3 focus:outline-none focus:border-emerald-600">
                    <p class="text-[11px] text-slate-400 mt-1">أنواع الملفات المسموحة: JPG, PNG, PDF (بحد أقصى 5 ميجابايت)</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-4">
                <a href="{{ route('services.mine') }}" class="text-slate-600 hover:text-slate-900 font-bold text-xs">إلغاء</a>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-8 py-3.5 rounded-2xl shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> حفظ وإرسال للتحقق
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <!-- Leaflet JS Script for Interactive Map Picker -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const latInput = document.getElementById('lat-input');
            const lngInput = document.getElementById('lng-input');
            const locateBtn = document.getElementById('btn-locate-me');

            let initialLat = parseFloat(latInput.value) || 31.5000;
            let initialLng = parseFloat(lngInput.value) || 34.4667;

            // Initialize Map Picker
            const map = L.map('picker-map').setView([initialLat, initialLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

            function updateInputs(lat, lng) {
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
            }

            // Drag marker event
            marker.on('dragend', function (e) {
                const coord = marker.getLatLng();
                updateInputs(coord.lat, coord.lng);
            });

            // Map click event
            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                updateInputs(e.latlng.lat, e.latlng.lng);
            });

            // Locate Me Button
            locateBtn.addEventListener('click', function () {
                if (navigator.geolocation) {
                    locateBtn.innerText = 'جاري التحديد...';
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        const userLat = pos.coords.latitude;
                        const userLng = pos.coords.longitude;
                        map.setView([userLat, userLng], 16);
                        marker.setLatLng([userLat, userLng]);
                        updateInputs(userLat, userLng);
                        locateBtn.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i> تم تحديد الموقع بنجاح!';
                    }, function (err) {
                        alert('تعذر الوصول لموقعك الحالي، يرجى تحديد الدبوس يدويًا على الخريطة.');
                        locateBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs text-emerald-600"></i> تحديد موقعي الحالي تلقائياً';
                    });
                }
            });
        });
    </script>
@endpush
