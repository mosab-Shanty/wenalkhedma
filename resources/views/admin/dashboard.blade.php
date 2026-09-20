@extends('layouts.app')

@section('title', 'لوحة تحكم الإدارة - وين الخدمة')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 to-cyan-950 text-white p-6 sm:p-8 rounded-3xl shadow-lg">
        <div>
            <span class="inline-flex items-center gap-1.5 bg-amber-500/20 text-amber-300 text-xs font-bold px-3 py-1 rounded-full border border-amber-400/30 mb-2">
                <i class="fa-solid fa-shield-halved"></i> لوحة الإشراف والإدارة
            </span>
            <h1 class="text-2xl sm:text-3xl font-black">إدارة منصة "وين الخدمة"</h1>
            <p class="text-xs text-slate-300 font-medium mt-1">مراجعة الخدمات الجديدة، متابعة البلاغات، وإدارة التصنيفات والمستخدمين</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.services.index') }}" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md">
                <i class="fa-solid fa-list-check ml-1"></i> مراجعة الخدمات
            </a>
            <a href="{{ route('admin.reports.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md">
                <i class="fa-solid fa-flag ml-1"></i> البلاغات المعلقة
            </a>
            <a href="{{ route('admin.categories.index') }}" class="bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl border border-slate-700">
                <i class="fa-solid fa-layer-group ml-1"></i> التصنيفات
            </a>
            <a href="{{ route('admin.users.index') }}" class="bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl border border-slate-700">
                <i class="fa-solid fa-users ml-1"></i> المستخدمون
            </a>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-2">
            <span class="text-xs text-slate-400 font-bold block">إجمالي المستخدمين</span>
            <span class="text-3xl font-black text-slate-900 block">{{ $stats['total_users'] }}</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-2">
            <span class="text-xs text-slate-400 font-bold block">إجمالي الخدمات</span>
            <span class="text-3xl font-black text-slate-900 block">{{ $stats['total_services'] }}</span>
        </div>

        <div class="bg-amber-50 p-5 rounded-3xl border border-amber-200 shadow-sm space-y-2">
            <span class="text-xs text-amber-700 font-bold block">خدمات تنتظر الاعتماد</span>
            <span class="text-3xl font-black text-amber-800 block">{{ $stats['pending_services'] }}</span>
        </div>

        <div class="bg-blue-50 p-5 rounded-3xl border border-blue-200 shadow-sm space-y-2">
            <span class="text-xs text-blue-700 font-bold block">خدمات موثقة</span>
            <span class="text-3xl font-black text-blue-800 block">{{ $stats['verified_services'] }}</span>
        </div>

        <div class="bg-rose-50 p-5 rounded-3xl border border-rose-200 shadow-sm space-y-2">
            <span class="text-xs text-rose-700 font-bold block">بلاغات قيد المراجعة</span>
            <span class="text-3xl font-black text-rose-800 block">{{ $stats['pending_reports'] }}</span>
        </div>

        <div class="bg-emerald-50 p-5 rounded-3xl border border-emerald-200 shadow-sm space-y-2">
            <span class="text-xs text-emerald-700 font-bold block">التصنيفات النشطة</span>
            <span class="text-3xl font-black text-emerald-800 block">{{ $stats['total_categories'] }}</span>
        </div>

    </div>

    <!-- Recent Services Approval Section -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-extrabold text-slate-900 text-lg">أحدث طلبات إضافة الخدمات</h3>
                <p class="text-xs text-slate-500 font-medium">الخدمات المُضافة حديثاً والتي تحتاج قرار الاعتماد</p>
            </div>
            <a href="{{ route('admin.services.index') }}" class="text-xs font-bold text-cyan-600 hover:underline">عرض الكل</a>
        </div>

        @if($recentServices->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3">اسم الخدمة</th>
                            <th class="p-3">المقدم</th>
                            <th class="p-3">التصنيف</th>
                            <th class="p-3">الموقع</th>
                            <th class="p-3">حالة الاعتماد</th>
                            <th class="p-3 text-center">القرار والإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @foreach($recentServices as $srv)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-extrabold text-slate-900">{{ $srv->name }}</td>
                                <td class="p-3 text-slate-500">{{ $srv->owner->full_name ?? '-' }}</td>
                                <td class="p-3 text-slate-500">{{ $srv->category->name ?? '-' }}</td>
                                <td class="p-3 text-slate-500">{{ $srv->location->city ?? 'غزة' }}</td>
                                <td class="p-3">
                                    @if($srv->approval_status === 'approved')
                                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full text-[11px] font-bold border border-emerald-200">معتمدة</span>
                                    @elseif($srv->approval_status === 'pending')
                                        <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full text-[11px] font-bold border border-amber-200">معلقة</span>
                                    @else
                                        <span class="bg-rose-50 text-rose-700 px-2 py-0.5 rounded-full text-[11px] font-bold border border-rose-200">مرفوضة</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($srv->approval_status !== 'approved')
                                            <form action="{{ route('admin.services.approve', $srv->service_id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] px-3 py-1.5 rounded-xl shadow-sm">
                                                    قبول ونشر
                                                </button>
                                            </form>
                                        @endif

                                        @if($srv->approval_status !== 'rejected')
                                            <form action="{{ route('admin.services.reject', $srv->service_id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] px-3 py-1.5 rounded-xl border border-rose-200">
                                                    رفض
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-slate-400 text-center py-6">لا توجد خدمات مُضافة مؤخراً.</p>
        @endif
    </div>

</div>

@endsection
