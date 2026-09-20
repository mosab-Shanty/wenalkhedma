@extends('layouts.app')

@section('title', 'بلاغاتي - وين الخدمة')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

    <div class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900">سجل بلاغاتي المُقدمة</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">متابعة حالة البلاغات والتحديثات التي قمت بإرسالها للمشرفين</p>
        </div>
        <a href="{{ route('reports.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-md flex items-center gap-2">
            <i class="fa-solid fa-flag text-xs"></i> تقديم بلاغ جديد
        </a>
    </div>

    @if($reports->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($reports as $rep)
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <span class="font-extrabold text-sm text-slate-900">
                            {{ $rep->service->name ?? 'خدمة' }}
                        </span>
                        
                        @if($rep->report_status === 'approved')
                            <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full border border-emerald-200 text-xs font-bold">
                                <i class="fa-solid fa-check"></i> مقبول وتم التحديث
                            </span>
                        @elseif($rep->report_status === 'pending')
                            <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full border border-amber-200 text-xs font-bold">
                                <i class="fa-solid fa-clock"></i> قيد المراجعة
                            </span>
                        @else
                            <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-full border border-rose-200 text-xs font-bold">
                                <i class="fa-solid fa-xmark"></i> مرفوض
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-700 font-normal leading-relaxed">
                        {{ $rep->note }}
                    </p>

                    <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-50">
                        <span>نوع التحديث: {{ $rep->reported_status }}</span>
                        <span>{{ $rep->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 p-8 space-y-3">
            <i class="fa-solid fa-flag text-4xl text-slate-300"></i>
            <h3 class="font-bold text-slate-800 text-base">لا توجد بلاغات مُسجلة باسمك</h3>
            <p class="text-xs text-slate-500">شكراً لاهتمامك بمساعدة المجتمع والحفاظ على دقة البيانات.</p>
        </div>
    @endif

</div>

@endsection
