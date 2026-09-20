@extends('layouts.app')

@section('title', 'مراجعة البلاغات - الأدمن')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900">مراجعة والتعامل مع البلاغات</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">التحقق من بلاغات وتحديثات المستخدمين والموافقة عليها لتعديل الحالة التلقائية للخدمة</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-md' : 'bg-slate-100 text-slate-700' }}">
                البلاغات الجديدة (المعلقة)
            </a>
            <a href="{{ route('admin.reports.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-700' }}">
                البلاغات المقبولة
            </a>
            <a href="{{ route('admin.reports.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-md' : 'bg-slate-100 text-slate-700' }}">
                البلاغات المرفوضة
            </a>
        </div>
    </div>

    @if($reports->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($reports as $rep)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-xs text-slate-400 font-bold block">الخدمة المبلغ عنها:</span>
                            <a href="{{ route('services.show', $rep->service_id) }}" target="_blank" class="font-black text-slate-900 text-base hover:text-cyan-600">
                                {{ $rep->service->name ?? 'خدمة محذوفة' }}
                            </a>
                        </div>

                        <span class="bg-slate-100 text-slate-700 text-xs font-bold px-3 py-1 rounded-full">
                            التحديث المُراد: {{ $rep->reported_status }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs text-slate-700 font-normal leading-relaxed bg-slate-50 p-3 rounded-2xl border border-slate-100">
                            "{{ $rep->note }}"
                        </p>
                        <p class="text-[11px] text-slate-400 font-semibold">
                            بواسطة: {{ $rep->user->full_name ?? 'مستخدم' }} ({{ $rep->user->email ?? '' }})
                        </p>
                    </div>

                    @if($rep->image_url)
                        <div class="pt-2">
                            <a href="{{ $rep->image_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-cyan-700 font-bold hover:underline">
                                <i class="fa-solid fa-paperclip"></i> معاينة الصورة المرفقة بالبلاغ
                            </a>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <span class="text-[11px] text-slate-400 font-normal">{{ $rep->created_at->format('Y-m-d H:i') }}</span>

                        <div class="flex items-center gap-2">
                            @if($rep->report_status !== 'approved')
                                <form action="{{ route('admin.reports.approve', $rep->report_id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm">
                                        قبول البلاغ وتحديث الخدمة
                                    </button>
                                </form>
                            @endif

                            @if($rep->report_status !== 'rejected')
                                <form action="{{ route('admin.reports.reject', $rep->report_id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs px-4 py-2 rounded-xl border border-rose-200">
                                        رفض
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $reports->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 p-8 space-y-2">
            <i class="fa-solid fa-flag text-4xl text-slate-300"></i>
            <h3 class="font-extrabold text-slate-800 text-base">لا توجد بلاغات بهذه الحالة</h3>
        </div>
    @endif

</div>

@endsection
