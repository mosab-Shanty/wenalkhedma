@extends('layouts.app')

@section('title', 'خدماتي المضافة - وين الخدمة')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إدارة خدماتي المضافة</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">عرض جميع الخدمات والمشاريع الحيوية التي قمت بإضافتها لمساعدة المجتمع</p>
        </div>
        <a href="{{ route('services.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-md flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i> إضافة خدمة جديدة
        </a>
    </div>

    @if($services->count() > 0)
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-4">اسم الخدمة</th>
                            <th class="p-4">التصنيف</th>
                            <th class="p-4">حالة الاعتماد</th>
                            <th class="p-4">الحالة التشغيلية</th>
                            <th class="p-4">تاريخ الإضافة</th>
                            <th class="p-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @foreach($services as $service)
                            <tr class="hover:bg-slate-50">
                                <td class="p-4 font-black text-slate-900 text-sm">
                                    {{ $service->name }}
                                </td>
                                <td class="p-4 text-slate-500">
                                    {{ $service->category->name ?? '-' }}
                                </td>
                                <td class="p-4">
                                    @if($service->approval_status === 'approved')
                                        <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full border border-emerald-200 text-[11px] font-bold">
                                            <i class="fa-solid fa-check-circle"></i> معتمدة ومنشورة
                                        </span>
                                    @elseif($service->approval_status === 'pending')
                                        <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full border border-amber-200 text-[11px] font-bold">
                                            <i class="fa-solid fa-clock"></i> قيد التقييم
                                        </span>
                                    @else
                                        <span class="bg-rose-50 text-rose-700 px-2.5 py-1 rounded-full border border-rose-200 text-[11px] font-bold">
                                            <i class="fa-solid fa-xmark-circle"></i> مرفوضة
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @include('components.status-badge', ['status' => $service->current_status])
                                </td>
                                <td class="p-4 text-slate-400 font-normal">
                                    {{ $service->created_at->format('Y-m-d') }}
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('services.show', $service->service_id) }}" class="p-2 text-slate-500 hover:text-emerald-600 bg-slate-100 rounded-xl" title="عرض العامة">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('services.edit', $service->service_id) }}" class="p-2 text-slate-500 hover:text-amber-600 bg-slate-100 rounded-xl" title="تعديل">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 p-8 space-y-4">
            <i class="fa-solid fa-folder-open text-4xl text-slate-300"></i>
            <h3 class="font-extrabold text-slate-800 text-lg">لم تقم بإضافة أي خدمة بعد</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                شارِك بالخير وساهم في إضافة الصيدليات والمخابز ومراكز الخدمات لمساعدة أفراد المجتمع.
            </p>
            <a href="{{ route('services.create') }}" class="inline-block bg-emerald-600 text-white font-bold text-xs px-6 py-3 rounded-2xl shadow-md">
                إضافة أول خدمة الآن
            </a>
        </div>
    @endif

</div>

@endsection
