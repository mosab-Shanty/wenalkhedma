@extends('layouts.app')

@section('title', 'تقديم بلاغ عن خدمة - وين الخدمة')

@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-10 space-y-8">
        
        <div class="border-b border-slate-100 pb-6 text-center sm:text-right">
            <h1 class="text-2xl font-black text-slate-900">تقديم بلاغ أو تحديث حالة خدمة</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">ساهم في تصحيح البيانات والتأكد من وصول المعلومات الصادقة للمستخدمين</p>
        </div>

        <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">الخدمة المبلغ عنها *</label>
                <select name="service_id" required class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-cyan-600">
                    <option value="">اختر الخدمة المعنية</option>
                    @foreach($services as $srv)
                        <option value="{{ $srv->service_id }}" {{ (request('service_id') == $srv->service_id || ($service && $service->service_id == $srv->service_id)) ? 'selected' : '' }}>
                            {{ $srv->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">نوع / موضوع البلاغ *</label>
                <select name="reported_status" required class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-cyan-600">
                    <option value="closed">الخدمة مغلقة / غير متاحة بالواقع</option>
                    <option value="crowded">الخدمة بها ازدحام شديد ونفاد للكميات</option>
                    <option value="open">الخدمة مفتوحة ومتاحة عكس ما هو مدون</option>
                    <option value="wrong_location">الموقع الجغرافي أو البيانات خاطئة</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">ملاحظات البلاغ والتفاصيل *</label>
                <textarea name="note" rows="4" required placeholder="يرجى ذكر التغيرات الفعلية المعاينة ميدانياً لتسهيل التحقق من قِبل المشرفين..." class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-cyan-600">{{ old('note') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">إرفاق صورة موثقة (اختياري)</label>
                <input type="file" name="image" accept="image/*" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl p-3">
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('reports.index') }}" class="text-xs text-slate-500 font-bold">إلغاء</a>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> إرسال البلاغ للمراجعة
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
