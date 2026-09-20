@extends('layouts.app')

@section('title', 'تعديل بيانات الخدمة - وين الخدمة')

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-10 space-y-8">
        
        <div class="border-b border-slate-100 pb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900">تعديل بيانات الخدمة</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">تعديل المعلومات وتحديث حالة العمل الفورية</p>
            </div>
            <a href="{{ route('services.mine') }}" class="text-xs text-slate-500 hover:text-slate-900 font-bold">
                <i class="fa-solid fa-arrow-right ml-1"></i> العودة لخدماتي
            </a>
        </div>

        <form action="{{ route('services.update', $service->service_id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">اسم الخدمة *</label>
                    <input type="text" name="name" value="{{ old('name', $service->name) }}" required class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-cyan-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">التصنيف *</label>
                    <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-cyan-600">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ old('category_id', $service->category_id) == $cat->category_id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">حالة الخدمة الآن *</label>
                    <select name="current_status" required class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-cyan-600">
                        <option value="open" {{ old('current_status', $service->current_status) == 'open' ? 'selected' : '' }}>متاحة / مفتوحة</option>
                        <option value="crowded" {{ old('current_status', $service->current_status) == 'crowded' ? 'selected' : '' }}>مزدحمة جداً</option>
                        <option value="closed" {{ old('current_status', $service->current_status) == 'closed' ? 'selected' : '' }}>مغلقة / غير متاحة</option>
                        <option value="unknown" {{ old('current_status', $service->current_status) == 'unknown' ? 'selected' : '' }}>غير محدد</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">رقم الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone', $service->phone) }}" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-cyan-600 dir-ltr">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-cyan-600">{{ old('description', $service->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">المحافظة</label>
                    <input type="text" name="governorate" value="{{ old('governorate', $service->location->governorate ?? 'غزة') }}" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">المدينة</label>
                    <input type="text" name="city" value="{{ old('city', $service->location->city ?? 'غزة') }}" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">المنطقة</label>
                    <input type="text" name="area" value="{{ old('area', $service->location->area ?? '') }}" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">العنوان بالتفصيل</label>
                <input type="text" name="address_text" value="{{ old('address_text', $service->location->address_text ?? '') }}" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5">
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-md">
                    حفظ التعديلات
                </button>
            </div>
        </form>

    </div>

</div>

@endsection
