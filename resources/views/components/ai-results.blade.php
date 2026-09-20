<div class="space-y-4 text-slate-800">
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-xs leading-relaxed space-y-1">
        <div class="font-bold text-emerald-900 flex items-center gap-2">
            <i class="fa-solid fa-robot text-emerald-600"></i> نتيجة فهم استفسارك:
        </div>
        <p class="text-emerald-800 font-semibold">
            "{{ $prompt }}"
        </p>
        <div class="flex flex-wrap gap-2 pt-1 text-[11px]">
            @if($detectedCategory)
                <span class="bg-white px-2 py-0.5 rounded-md border border-emerald-200 font-bold text-emerald-700">
                    التصنيف: {{ $detectedCategory->name }}
                </span>
            @endif
            @if($statusFilter)
                <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md font-bold">
                    الحالة: متاحة الآن
                </span>
            @endif
            @if($locationFilter)
                <span class="bg-white px-2 py-0.5 rounded-md border border-emerald-200 font-bold text-emerald-700">
                    الموقع: {{ $locationFilter }}
                </span>
            @endif
        </div>
    </div>

    @if($results->count() > 0)
        <div class="space-y-3">
            <h4 class="text-xs font-bold text-slate-500">الخدمات المقترحة لك ({{ $results->count() }}):</h4>
            @foreach($results as $service)
                <div class="flex items-center justify-between bg-slate-50 hover:bg-slate-100 p-3 rounded-2xl border border-slate-200 transition-colors">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm text-slate-900">{{ $service->name }}</span>
                            @include('components.status-badge', ['status' => $service->current_status])
                        </div>
                        <p class="text-xs text-slate-500">
                            <i class="fa-solid fa-location-dot text-rose-500"></i> {{ $service->location->city ?? 'غزة' }} - {{ $service->location->area ?? '' }}
                        </p>
                    </div>
                    <a href="{{ route('services.show', $service->service_id) }}" class="bg-emerald-600 text-white font-bold text-xs px-3 py-1.5 rounded-xl shadow-sm hover:bg-emerald-700 transition-all">
                        عرض التفاصيل
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 space-y-2">
            <i class="fa-solid fa-circle-question text-3xl text-slate-400"></i>
            <p class="font-bold text-sm text-slate-700">لم يتم العثور على مطابقة دقيقة لاستفسارك</p>
            <p class="text-xs text-slate-500 max-w-xs mx-auto">
                لم أتمكن من فهم طلبك بشكل كامل، جرب مثل: <br>
                <span class="font-bold text-emerald-700">"أقرب صيدلية متاحة"</span> أو <span class="font-bold text-emerald-700">"أقرب مخبز"</span> أو <span class="font-bold text-emerald-700">"محطات مياه غزة"</span>
            </p>
        </div>
    @endif
</div>
