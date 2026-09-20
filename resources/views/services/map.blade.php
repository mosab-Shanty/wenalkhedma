@extends('layouts.app')

@section('title', 'خريطة الخدمات التفاعلية - وين الخدمة')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .leaflet-popup-content-wrapper {
            border-radius: 1.25rem !important;
            padding: 0 !important;
            overflow: hidden !important;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.15), 0 8px 10px -6px rgb(0 0 0 / 0.15) !important;
            border: 1px solid #e2e8f0;
        }
        .leaflet-popup-content {
            margin: 0 !important;
            line-height: 1.5 !important;
            font-family: 'IBM Plex Sans Arabic', sans-serif !important;
            direction: rtl !important;
            text-align: right !important;
        }
        .leaflet-popup-tip {
            background: white !important;
        }
        /* Custom Marker Pulse */
        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 0.3; }
            100% { transform: scale(0.9); opacity: 0.8; }
        }
        .user-pulse-marker {
            animation: pulse-ring 2s infinite ease-in-out;
        }
    </style>
@endpush

@section('content')

    <!-- Map Header Bar -->
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                        <h1 class="text-2xl font-black text-slate-900">خريطة الخدمات الحية</h1>
                        <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full border border-emerald-200">
                            تفاعلي مباشر
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                        استكشف مواقع الخدمات والمرافق الجغرافية مع حساب المسافة الفورية وفلاتر سريعة
                    </p>
                </div>

                <!-- Action Controls -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <button id="btn-map-locate" 
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl transition-all shadow-md shadow-emerald-600/20 flex items-center gap-2">
                        <i class="fa-solid fa-location-crosshairs"></i>
                        <span id="btn-locate-text">تحديد موقعي لحساب المسافات</span>
                    </button>

                    <a href="{{ route('services.index') }}" 
                       class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-list text-slate-400"></i>
                        <span>عرض القائمة</span>
                    </a>
                </div>
            </div>

            <!-- Interactive Filter Controls Bar -->
            <div class="mt-6 pt-4 border-t border-slate-100 space-y-4">
                
                <!-- Category Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">
                    <span class="text-xs font-black text-slate-500 whitespace-nowrap ml-1 flex items-center gap-1">
                        <i class="fa-solid fa-filter text-emerald-600"></i> التصنيف:
                    </span>

                    <button type="button" 
                            data-filter-category="all"
                            class="filter-category-btn px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-emerald-600 text-white shadow-sm shadow-emerald-600/20">
                        <i class="fa-solid fa-border-all ml-1"></i> الكل
                        <span class="opacity-80 text-[11px] mr-1">({{ $services->count() }})</span>
                    </button>

                    @foreach($categories as $cat)
                        @php
                            $catCount = $services->where('category_id', $cat->category_id)->count();
                            $catIcon = 'fa-layer-group';
                            if (str_contains($cat->name, 'مخبز') || str_contains($cat->name, 'فرن')) {
                                $catIcon = 'fa-wheat-awn';
                            } elseif (str_contains($cat->name, 'صيدل')) {
                                $catIcon = 'fa-prescription-bottle-medical';
                            } elseif (str_contains($cat->name, 'مياه')) {
                                $catIcon = 'fa-droplet';
                            } elseif (str_contains($cat->name, 'عياد') || str_contains($cat->name, 'طبي') || str_contains($cat->name, 'مستشف')) {
                                $catIcon = 'fa-stethoscope';
                            } elseif (str_contains($cat->name, 'شحن') || str_contains($cat->name, 'كهرب')) {
                                $catIcon = 'fa-bolt';
                            }
                        @endphp
                        <button type="button" 
                                data-filter-category="{{ $cat->category_id }}"
                                class="filter-category-btn px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-slate-100 hover:bg-slate-200 text-slate-700">
                            <i class="fa-solid {{ $catIcon }} ml-1 text-slate-400"></i> {{ $cat->name }}
                            <span class="opacity-60 text-[11px] mr-1">({{ $catCount }})</span>
                        </button>
                    @endforeach
                </div>

                <!-- Secondary Filters: Search & Operational Status -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-80">
                        <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="text" 
                               id="map-search-input" 
                               placeholder="ابحث باسم الخدمة، مخبز العائلات، الحي..." 
                               class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl pr-9 pl-8 py-2.5 focus:outline-none focus:border-emerald-600 placeholder-slate-400">
                        <button type="button" id="clear-search-btn" class="hidden absolute left-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                    </div>

                    <!-- Status Selector Pills -->
                    <div class="flex items-center gap-1.5 self-start sm:self-auto overflow-x-auto">
                        <span class="text-xs font-bold text-slate-400 whitespace-nowrap ml-1">الحالة:</span>
                        
                        <button type="button" data-filter-status="all" class="filter-status-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-white transition-all">
                            جميع الحالات
                        </button>
                        <button type="button" data-filter-status="open" class="filter-status-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition-all">
                            <i class="fa-solid fa-circle text-[8px] text-emerald-500 ml-1"></i> متاح الآن
                        </button>
                        <button type="button" data-filter-status="crowded" class="filter-status-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 transition-all">
                            <i class="fa-solid fa-circle text-[8px] text-amber-500 ml-1"></i> مزدحم
                        </button>
                        <button type="button" data-filter-status="closed" class="filter-status-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 transition-all">
                            <i class="fa-solid fa-circle text-[8px] text-rose-500 ml-1"></i> مغلق
                        </button>
                    </div>

                    <!-- Counter Indicator -->
                    <div class="hidden lg:flex items-center gap-2 text-xs font-bold text-slate-500 whitespace-nowrap">
                        <i class="fa-solid fa-map-pin text-emerald-600"></i>
                        <span>الخدمات الظاهرة:</span>
                        <span id="visible-count" class="font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                            {{ $services->count() }}
                        </span>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Main Map Viewport & Sidebar -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Map Container (Large Area) -->
            <div class="lg:col-span-8 xl:col-span-9">
                <div class="relative bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden" style="height: 680px;">
                    
                    <!-- Leaflet Map Element -->
                    <div id="interactive-map" class="w-full h-full z-10"></div>

                    <!-- Floating Map Overlay Controls -->
                    <div class="absolute top-4 right-4 z-20 flex flex-col gap-2">
                        <button type="button" 
                                id="btn-recenter" 
                                title="إعادة ضبط مركز الخريطة"
                                class="bg-white/95 hover:bg-white text-slate-700 hover:text-emerald-600 w-10 h-10 rounded-xl shadow-lg border border-slate-200 flex items-center justify-center transition-all">
                            <i class="fa-solid fa-arrows-to-dot text-sm"></i>
                        </button>
                    </div>

                    <!-- User Location Status Bar (Bottom overlay) -->
                    <div id="user-location-banner" class="hidden absolute bottom-4 right-4 left-4 z-20 bg-slate-900/90 backdrop-blur-md text-white px-4 py-2.5 rounded-2xl shadow-xl flex items-center justify-between text-xs border border-white/10">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-400 animate-pulse"></span>
                            <span id="user-coords-text" class="font-bold">تم تحديد موقعك بدقة - المسافات معروضة ومحدّثة</span>
                        </div>
                        <button type="button" id="btn-dismiss-banner" class="text-slate-400 hover:text-white text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Matching Services Sidebar List -->
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-4 space-y-3 flex flex-col" style="height: 680px;">
                    
                    <!-- Sidebar Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-rose-500"></i> قائمة الخدمات
                        </h3>
                        <span id="sidebar-count" class="text-xs font-bold text-slate-400">
                            {{ $services->count() }} خدمة
                        </span>
                    </div>

                    <!-- Quick Location Notice if not detected -->
                    <div id="sidebar-loc-prompt" class="bg-emerald-50 border border-emerald-200/80 p-3 rounded-2xl text-xs text-emerald-800 space-y-2">
                        <p class="font-bold flex items-center gap-1">
                            <i class="fa-solid fa-location-arrow text-emerald-600"></i> هل ترغب بحساب المسافة بدقة؟
                        </p>
                        <p class="text-[11px] text-emerald-700/90 leading-relaxed font-normal">
                            انقر لمعرفة كم تبعد الخدمات عن مكانك الآن وترتيبها حسب الأقرب لك.
                        </p>
                        <button type="button" id="btn-sidebar-locate" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 rounded-xl text-[11px] transition-colors">
                            تحديد موقعي الآن
                        </button>
                    </div>

                    <!-- Scrollable Services List -->
                    <div id="services-sidebar-list" class="flex-grow overflow-y-auto space-y-2.5 pr-1 pl-1 scrollbar-thin">
                        <!-- Populated dynamically by JavaScript -->
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Raw Services Data passed from Laravel Controller
            const rawServices = @json($servicesMapData);

            let map;
            let markersLayer = L.layerGroup();
            let userMarker = null;
            let userCircle = null;
            let userCoords = null;
            let markersMap = new Map(); // service.id -> marker

            // Filter state
            let currentCategoryId = 'all';
            let currentStatus = 'all';
            let currentSearchTerm = '';

            // Status Badges & Translations
            const statusLabels = {
                open: { text: 'مفتوح ومتاح', bg: '#ecfdf5', color: '#047857', dot: '#10b981' },
                crowded: { text: 'مزدحم جداً', bg: '#fffbeb', color: '#b45309', dot: '#f59e0b' },
                closed: { text: 'مغلق حالياً', bg: '#fef2f2', color: '#b91c1c', dot: '#ef4444' },
                unknown: { text: 'غير محدد', bg: '#f8fafc', color: '#475569', dot: '#94a3b8' }
            };

            // Custom Leaflet Marker Icon Generator
            function createCustomMarkerIcon(categoryName, status) {
                let iconClass = 'fa-location-dot';
                if (categoryName.includes('مخبز') || categoryName.includes('فرن')) iconClass = 'fa-wheat-awn';
                else if (categoryName.includes('صيدل')) iconClass = 'fa-prescription-bottle-medical';
                else if (categoryName.includes('مياه')) iconClass = 'fa-droplet';
                else if (categoryName.includes('طبي') || categoryName.includes('عياد') || categoryName.includes('مستشف')) iconClass = 'fa-stethoscope';
                else if (categoryName.includes('شحن') || categoryName.includes('كهرب')) iconClass = 'fa-bolt';

                let pinBg = '#059669'; // default emerald
                if (status === 'crowded') pinBg = '#d97706';
                if (status === 'closed') pinBg = '#e11d48';

                const html = `
                    <div style="position: relative; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                        <div style="background-color: ${pinBg}; color: white; width: 34px; height: 34px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 2px solid #ffffff;">
                            <i class="fa-solid ${iconClass}" style="transform: rotate(45deg); font-size: 14px;"></i>
                        </div>
                    </div>
                `;

                return L.divIcon({
                    html: html,
                    className: 'custom-service-pin',
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                    popupAnchor: [0, -36]
                });
            }

            // Initialize Map
            function initMap() {
                // Default coordinates (Gaza Center: 31.5000, 34.4667)
                const defaultCenter = [31.5000, 34.4667];
                map = L.map('interactive-map', {
                    center: defaultCenter,
                    zoom: 13,
                    zoomControl: true
                });

                // OpenStreetMap Tile Layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                markersLayer.addTo(map);

                // Check stored coordinates
                if (window.WenAlKhedmaGeo) {
                    const saved = window.WenAlKhedmaGeo.getUserCoords();
                    if (saved) {
                        setUserLocation(saved.lat, saved.lng, false);
                    }
                }

                // Render initial services
                renderServices();
            }

            // Filter Services based on current state
            function getFilteredServices() {
                return rawServices.filter(s => {
                    // Category Filter
                    if (currentCategoryId !== 'all' && s.category_id !== currentCategoryId) {
                        return false;
                    }
                    // Status Filter
                    if (currentStatus !== 'all' && s.status !== currentStatus) {
                        return false;
                    }
                    // Search term filter
                    if (currentSearchTerm) {
                        const term = currentSearchTerm.toLowerCase();
                        const matchName = s.name && s.name.toLowerCase().includes(term);
                        const matchDesc = s.description && s.description.toLowerCase().includes(term);
                        const matchCategory = s.category_name && s.category_name.toLowerCase().includes(term);
                        const matchCity = s.city && s.city.toLowerCase().includes(term);
                        const matchArea = s.area && s.area.toLowerCase().includes(term);
                        const matchAddress = s.address && s.address.toLowerCase().includes(term);
                        if (!matchName && !matchDesc && !matchCategory && !matchCity && !matchArea && !matchAddress) {
                            return false;
                        }
                    }
                    return true;
                }).map(s => {
                    // Calculate distance if user location available
                    let distKm = null;
                    if (userCoords && window.WenAlKhedmaGeo) {
                        distKm = window.WenAlKhedmaGeo.calculateDistance(userCoords.lat, userCoords.lng, s.lat, s.lng);
                    }
                    return { ...s, distKm: distKm };
                }).sort((a, b) => {
                    // If distance is available, sort closest first
                    if (a.distKm !== null && b.distKm !== null) {
                        return a.distKm - b.distKm;
                    }
                    return 0;
                });
            }

            // Render markers and sidebar items
            function renderServices() {
                markersLayer.clearLayers();
                markersMap.clear();

                const filtered = getFilteredServices();

                // Update counters
                const countEls = [document.getElementById('visible-count'), document.getElementById('sidebar-count')];
                countEls.forEach(el => {
                    if (el) el.textContent = `${filtered.length} خدمة`;
                });

                // Populate Sidebar
                const sidebarList = document.getElementById('services-sidebar-list');
                if (sidebarList) {
                    sidebarList.innerHTML = '';
                    if (filtered.length === 0) {
                        sidebarList.innerHTML = `
                            <div class="text-center py-12 text-slate-400 space-y-2">
                                <i class="fa-solid fa-magnifying-glass text-3xl"></i>
                                <p class="font-bold text-xs">لا توجد خدمات مطابقة للفلاتر</p>
                                <p class="text-[11px]">جرّب اختيار تصنيف آخر أو مسح كلمة البحث.</p>
                            </div>
                        `;
                    } else {
                        filtered.forEach(service => {
                            const st = statusLabels[service.status] || statusLabels.unknown;
                            const distanceText = (service.distKm !== null && window.WenAlKhedmaGeo) 
                                ? window.WenAlKhedmaGeo.formatDistance(service.distKm) 
                                : null;

                            const item = document.createElement('div');
                            item.className = 'group p-3 rounded-2xl border border-slate-100 bg-slate-50/70 hover:bg-emerald-50/80 hover:border-emerald-200 transition-all cursor-pointer space-y-2';
                            item.innerHTML = `
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="font-black text-slate-900 text-xs group-hover:text-emerald-700 transition-colors line-clamp-1">
                                        ${service.name}
                                    </h4>
                                    <span style="background-color: ${st.bg}; color: ${st.color}; font-size: 10px;" class="px-2 py-0.5 rounded-full font-bold whitespace-nowrap">
                                        ${st.text}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500">
                                    <span class="truncate">
                                        <i class="fa-solid fa-map-pin text-rose-500 ml-1"></i>
                                        ${service.city || 'غزة'} ${service.area ? '(' + service.area + ')' : ''}
                                    </span>
                                    ${distanceText ? `
                                        <span class="font-extrabold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-lg flex items-center gap-1">
                                            <i class="fa-solid fa-person-walking text-emerald-600"></i>
                                            ${distanceText}
                                        </span>
                                    ` : ''}
                                </div>
                            `;

                            item.addEventListener('click', () => {
                                focusOnService(service.id);
                            });

                            sidebarList.appendChild(item);
                        });
                    }
                }

                // Add Markers to Map
                const boundsGroup = [];

                filtered.forEach(service => {
                    const icon = createCustomMarkerIcon(service.category_name, service.status);
                    const marker = L.marker([service.lat, service.lng], { icon: icon });

                    const st = statusLabels[service.status] || statusLabels.unknown;
                    const formattedDist = (service.distKm !== null && window.WenAlKhedmaGeo)
                        ? window.WenAlKhedmaGeo.formatDistance(service.distKm)
                        : null;

                    const popupContent = `
                        <div style="min-width: 250px; max-width: 290px;">
                            <div style="position: relative; height: 110px; background-color: #f1f5f9; overflow: hidden;">
                                <img src="${service.image_url}" alt="${service.name}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='/default-service.png'">
                                <span style="position: absolute; top: 8px; right: 8px; background: rgba(255,255,255,0.9); font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 9999px; color: #0f172a;">
                                    ${service.category_name}
                                </span>
                            </div>
                            <div style="padding: 12px 14px; display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <span style="background-color: ${st.bg}; color: ${st.color}; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 9999px;">
                                        ${st.text}
                                    </span>
                                    ${formattedDist ? `
                                        <span style="background-color: #ecfdf5; color: #065f46; font-size: 11px; font-weight: 900; padding: 2px 8px; border-radius: 8px; border: 1px solid #a7f3d0;">
                                            <i class="fa-solid fa-person-walking"></i> تبعد ${formattedDist}
                                        </span>
                                    ` : ''}
                                </div>
                                <h4 style="font-size: 14px; font-weight: 900; color: #0f172a; margin: 0;">${service.name}</h4>
                                <p style="font-size: 11px; color: #64748b; margin: 0;">
                                    <i class="fa-solid fa-location-dot" style="color: #ef4444;"></i>
                                    ${service.address || (service.city + ' - ' + service.area)}
                                </p>
                                ${service.phone ? `
                                    <p style="font-size: 11px; color: #334155; margin: 0; direction: ltr; text-align: right;">
                                        <i class="fa-solid fa-phone" style="color: #059669;"></i> ${service.phone}
                                    </p>
                                ` : ''}
                                <div style="display: flex; gap: 6px; padding-top: 6px; border-top: 1px solid #f1f5f9;">
                                    <a href="${service.show_url}" style="flex: 1; text-align: center; background-color: #059669; color: white; font-size: 11px; font-weight: 800; padding: 7px 10px; border-radius: 10px; text-decoration: none;">
                                        تفاصيل الخدمة
                                    </a>
                                    <a href="https://www.google.com/maps/dir/?api=1&destination=${service.lat},${service.lng}" target="_blank" style="text-align: center; background-color: #f1f5f9; color: #334155; font-size: 11px; font-weight: 800; padding: 7px 10px; border-radius: 10px; text-decoration: none;" title="الاتجاهات">
                                        <i class="fa-solid fa-diamond-turn-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                    markersLayer.addLayer(marker);
                    markersMap.set(service.id, marker);
                    boundsGroup.push([service.lat, service.lng]);
                });

                // Auto fit bounds if user has searched or filtered and points exist
                if ((currentSearchTerm || currentCategoryId !== 'all') && boundsGroup.length > 0) {
                    map.fitBounds(boundsGroup, { padding: [40, 40], maxZoom: 15 });
                }
            }

            // Focus on a service marker
            function focusOnService(serviceId) {
                const marker = markersMap.get(serviceId);
                if (marker) {
                    const latLng = marker.getLatLng();
                    map.setView(latLng, 16, { animate: true });
                    marker.openPopup();
                }
            }

            // Set user location on map
            function setUserLocation(lat, lng, zoom = true) {
                userCoords = { lat, lng };

                if (userMarker) map.removeLayer(userMarker);
                if (userCircle) map.removeLayer(userCircle);

                // User Pulsing Pin
                const userIcon = L.divIcon({
                    html: `
                        <div style="position: relative; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                            <div style="position: absolute; width: 28px; height: 28px; border-radius: 50%; background-color: rgba(37, 99, 235, 0.35);" class="user-pulse-marker"></div>
                            <div style="width: 16px; height: 16px; border-radius: 50%; background-color: #2563eb; border: 3px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.4);"></div>
                        </div>
                    `,
                    className: 'user-location-marker',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });

                userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);
                userMarker.bindPopup(`
                    <div style="padding: 10px; font-weight: 800; font-size: 12px; color: #1e3a8a; text-align: center;">
                        <i class="fa-solid fa-street-view text-blue-600 ml-1"></i> موقعك الحالي هنا
                    </div>
                `);

                userCircle = L.circle([lat, lng], {
                    radius: 300,
                    color: '#3b82f6',
                    fillColor: '#60a5fa',
                    fillOpacity: 0.1,
                    weight: 1
                }).addTo(map);

                if (zoom) {
                    map.setView([lat, lng], 14, { animate: true });
                }

                // Show banner
                const banner = document.getElementById('user-location-banner');
                if (banner) banner.classList.remove('hidden');

                const prompt = document.getElementById('sidebar-loc-prompt');
                if (prompt) prompt.classList.add('hidden');

                const locateBtn = document.getElementById('btn-locate-text');
                if (locateBtn) locateBtn.textContent = 'تم تحديد موقعك (تحديث)';

                // Re-render services to show distances
                renderServices();
            }

            // Category Filter Click Handler
            document.querySelectorAll('.filter-category-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-category-btn').forEach(b => {
                        b.classList.remove('bg-emerald-600', 'text-white', 'shadow-sm', 'shadow-emerald-600/20');
                        b.classList.add('bg-slate-100', 'text-slate-700');
                    });
                    this.classList.remove('bg-slate-100', 'text-slate-700');
                    this.classList.add('bg-emerald-600', 'text-white', 'shadow-sm', 'shadow-emerald-600/20');

                    currentCategoryId = this.getAttribute('data-filter-category');
                    renderServices();
                });
            });

            // Status Filter Click Handler
            document.querySelectorAll('.filter-status-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-status-btn').forEach(b => {
                        b.classList.remove('bg-slate-800', 'text-white');
                        b.classList.add('bg-slate-100', 'text-slate-600');
                    });
                    this.classList.remove('bg-slate-100', 'text-slate-600');
                    this.classList.add('bg-slate-800', 'text-white');

                    currentStatus = this.getAttribute('data-filter-status');
                    renderServices();
                });
            });

            // Live Search Input Handler
            const searchInput = document.getElementById('map-search-input');
            const clearSearchBtn = document.getElementById('clear-search-btn');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    currentSearchTerm = this.value.trim();
                    if (clearSearchBtn) {
                        clearSearchBtn.classList.toggle('hidden', !currentSearchTerm);
                    }
                    renderServices();
                });
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                        currentSearchTerm = '';
                        clearSearchBtn.classList.add('hidden');
                        renderServices();
                    }
                });
            }

            // Recenter Map Handler
            const recenterBtn = document.getElementById('btn-recenter');
            if (recenterBtn) {
                recenterBtn.addEventListener('click', function () {
                    if (userCoords) {
                        map.setView([userCoords.lat, userCoords.lng], 14, { animate: true });
                    } else {
                        map.setView([31.5000, 34.4667], 13, { animate: true });
                    }
                });
            }

            // Dismiss Location Banner Handler
            const dismissBtn = document.getElementById('btn-dismiss-banner');
            if (dismissBtn) {
                dismissBtn.addEventListener('click', function () {
                    const banner = document.getElementById('user-location-banner');
                    if (banner) banner.classList.add('hidden');
                });
            }

            // Locate Me Buttons Handler
            function triggerLocate() {
                const locateBtn = document.getElementById('btn-locate-text');
                if (locateBtn) locateBtn.textContent = 'جاري تحديد موقعك...';

                if (window.WenAlKhedmaGeo) {
                    window.WenAlKhedmaGeo.requestLocation(
                        function (coords) {
                            setUserLocation(coords.lat, coords.lng, true);
                        },
                        function (err) {
                            alert('تعذر الوصول إلى موقعك الحالي عبر المتصفح. تأكد من تفعيل إذن الموقع الجغرافي.');
                            if (locateBtn) locateBtn.textContent = 'تحديد موقعي لحساب المسافات';
                        }
                    );
                }
            }

            const mapLocateBtn = document.getElementById('btn-map-locate');
            if (mapLocateBtn) mapLocateBtn.addEventListener('click', triggerLocate);

            const sidebarLocateBtn = document.getElementById('btn-sidebar-locate');
            if (sidebarLocateBtn) sidebarLocateBtn.addEventListener('click', triggerLocate);

            // Listen to global location updates
            window.addEventListener('wenalkhedma:location-updated', function (e) {
                if (e.detail && e.detail.lat && e.detail.lng) {
                    setUserLocation(e.detail.lat, e.detail.lng, false);
                }
            });

            // Initialize the map!
            initMap();
        });
    </script>
@endpush
