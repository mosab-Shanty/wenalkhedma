@extends('layouts.app')

@section('title', 'إدارة التصنيفات - الأدمن')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إدارة تصنيفات الخدمات</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">إضافة، تعديل وحذف الأقسام والتصنيفات المتاحة بالمنصة</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Add Category Form -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4 h-fit">
            <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3">إضافة تصنيف جديد</h3>
            
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">اسم التصنيف *</label>
                    <input type="text" name="name" required placeholder="مثال: مراكز الإيواء" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 focus:outline-none focus:border-cyan-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">الوصف والتوضيح</label>
                    <textarea name="description" rows="3" placeholder="وصف قصير للخدمات المدرجة تحت هذا التصنيف..." class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 focus:outline-none focus:border-cyan-600"></textarea>
                </div>

                <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs py-3 rounded-xl shadow-md">
                    حفظ التصنيف
                </button>
            </form>
        </div>

        <!-- Categories List -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-slate-100 font-extrabold text-sm text-slate-900">
                التصنيفات المتاحة حالياً ({{ $categories->count() }})
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($categories as $cat)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50">
                        <div class="space-y-1">
                            <h4 class="font-black text-sm text-slate-900">{{ $cat->name }}</h4>
                            <p class="text-xs text-slate-500 font-normal">{{ $cat->description ?? 'لا يوجد وصف' }}</p>
                            <span class="text-[11px] text-cyan-600 font-bold">عدد الخدمات المرتبطة: ({{ $cat->services_count }})</span>
                        </div>

                        <form action="{{ route('admin.categories.destroy', $cat->category_id) }}" method="POST" onsubmit="return confirm('حذف هذا التصنيف قد يؤثر على الخدمات المرتبطة به. هل تريد المتابعة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 bg-rose-50 rounded-xl" title="حذف">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

@endsection
