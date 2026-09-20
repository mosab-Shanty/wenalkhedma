@props(['service'])

@php
    $document = $service->documents->first();
    $imageUrl = $document ? $document->file_url : asset('default-service.png');
    $lat = $service->location->latitude ?? null;
    $lng = $service->location->longitude ?? null;
@endphp

<div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-200 hover:shadow-xl hover:border-slate-300 transition-all duration-300 flex flex-col h-full group service-card"
     data-service-card
     data-lat="{{ $lat }}"
     data-lng="{{ $lng }}"
     data-service-name="{{ $service->name }}">
    
    <!-- Image Header -->
    <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
        <img src="{{ $imageUrl }}" alt="{{ $service->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onError="this.src='{{ asset('default-service.png') }}'">
        
        <!-- Category Badge -->
        <span class="absolute top-3 right-3 bg-white/90 backdrop-blur-md text-slate-800 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
            {{ $service->category->name ?? 'خدمة' }}
        </span>

        <!-- Verified Badge -->
        @if($service->is_verified)
            <span class="absolute top-3 left-3 bg-emerald-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm flex items-center gap-1">
                <i class="fa-solid fa-circle-check"></i> موثقة
            </span>
        @endif

        <!-- Floating Distance Badge on Image -->
        @if($lat && $lng)
            <div class="service-distance-badge hidden absolute bottom-3 right-3 bg-slate-900/85 backdrop-blur-md text-emerald-300 text-xs font-black px-3 py-1 rounded-xl shadow-lg items-center gap-1.5 border border-emerald-500/30">
                <i class="fa-solid fa-person-walking text-emerald-400"></i>
                <span class="distance-value">...</span>
            </div>
        @endif
    </div>

    <!-- Content Body -->
    <div class="p-5 flex flex-col flex-grow justify-between space-y-4">
        <div>
            <!-- Status & Title -->
            <div class="flex items-center justify-between gap-2 mb-2">
                @include('components.status-badge', ['status' => $service->current_status])
                
                @if($service->phone)
                    <span class="text-xs text-slate-500 font-medium dir-ltr inline-flex items-center gap-1">
                        <i class="fa-solid fa-phone text-slate-400"></i> {{ $service->phone }}
                    </span>
                @endif
            </div>

            <h3 class="font-extrabold text-slate-900 text-lg group-hover:text-emerald-600 transition-colors line-clamp-1">
                <a href="{{ route('services.show', $service->service_id) }}">
                    {{ $service->name }}
                </a>
            </h3>

            <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                {{ $service->description ?? 'لا يوجد وصف تفصيلي متوفر حالياً لهذه الخدمة.' }}
            </p>
        </div>

        <!-- Location & Distance in Body -->
        <div class="space-y-3 pt-3 border-t border-slate-100">
            @if($lat && $lng)
                <div class="service-distance-inline hidden items-center justify-between bg-emerald-50/70 border border-emerald-100 text-emerald-800 px-3 py-1.5 rounded-xl text-xs font-bold">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-location-arrow text-emerald-600 text-xs"></i>
                        <span class="distance-inline-value">تبعد عنك ...</span>
                    </span>
                    <span class="text-[10px] text-emerald-600 font-medium">مباشر</span>
                </div>
            @endif

            <!-- Location & Footer -->
            <div class="flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-1 text-slate-600 truncate font-semibold">
                    <i class="fa-solid fa-location-dot text-rose-500"></i>
                    <span>{{ $service->location->city ?? 'غزة' }}</span>
                    @if($service->location->area)
                        <span class="text-slate-400 font-normal">({{ $service->location->area }})</span>
                    @endif
                </div>

                <a href="{{ route('services.show', $service->service_id) }}" 
                   class="bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 font-bold px-3 py-1.5 rounded-xl transition-colors flex items-center gap-1">
                    <span>التفاصيل</span>
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

</div>
