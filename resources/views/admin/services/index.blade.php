@extends('layouts.app')

@section('title', 'إدارة خدمات المنصة - الأدمن')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إدارة واعتماد الخدمات</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">مراجعة الخدمات المدخلة من المستخدمين والتحقق من تفاصيلها وقبولها</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.services.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-md' : 'bg-slate-100 text-slate-700' }}">
                قيد التقييم المعلقة
            </a>
            <a href="{{ route('admin.services.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-700' }}">
                المعتمدة والمفتوحة
            </a>
            <a href="{{ route('admin.services.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-md' : 'bg-slate-100 text-slate-700' }}">
                المرفوضة
            </a>
        </div>
    </div>

    @if($services->count() > 0)
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-4">اسم الخدمة</th>
                            <th class="p-4">صاحب الحساب</th>
                            <th class="p-4">التصنيف</th>
                            <th class="p-4">المدينة / المحافظة</th>
                            <th class="p-4">الحالة التشغيلية</th>
                            <th class="p-4">التاريخ</th>
                            <th class="p-4 text-center">القرارات والتحكم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @foreach($services as $srv)
                            <tr class="hover:bg-slate-50">
                                <td class="p-4">
                                    <a href="{{ route('services.show', $srv->service_id) }}" target="_blank" class="font-black text-slate-900 text-sm hover:text-cyan-600">
                                        {{ $srv->name }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
                                    </a>
                                </td>
                                <td class="p-4 text-slate-600">{{ $srv->owner->full_name ?? '-' }} ({{ $srv->owner->phone ?? 'لا يوجد هاتف' }})</td>
                                <td class="p-4 text-slate-500">{{ $srv->category->name ?? '-' }}</td>
                                <td class="p-4 text-slate-500">{{ $srv->location->city ?? 'غزة' }} ({{ $srv->location->area ?? '-' }})</td>
                                <td class="p-4">
                                    @include('components.status-badge', ['status' => $srv->current_status])
                                </td>
                                <td class="p-4 text-slate-400 font-normal">{{ $srv->created_at->format('Y-m-d') }}</td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($srv->approval_status !== 'approved')
                                            <form action="{{ route('admin.services.approve', $srv->service_id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3 py-1.5 rounded-xl shadow-sm">
                                                    اعتماد ونشر
                                                </button>
                                            </form>
                                        @endif

                                        @if($srv->approval_status !== 'rejected')
                                            <form action="{{ route('admin.services.reject', $srv->service_id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs px-3 py-1.5 rounded-xl border border-amber-200">
                                                    رفض
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.services.destroy', $srv->service_id) }}" method="POST" onsubmit="return confirm('هل أنت تأكد من نقل أو حذف هذه الخدمة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 bg-rose-50 rounded-xl">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-4">
            {{ $services->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 p-8 space-y-2">
            <i class="fa-solid fa-inbox text-4xl text-slate-300"></i>
            <h3 class="font-extrabold text-slate-800 text-base">لا توجد خدمات مطابقة بالحالة المحددة</h3>
        </div>
    @endif

</div>

@endsection
