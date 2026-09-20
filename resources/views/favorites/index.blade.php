@extends('layouts.app')

@section('title', 'المفضلة - وين الخدمة')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">الخدمات المحفوظة بالمفضلة</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">وصول سريع ومباشر للخدمات والمرافق الأكثر أهمية بالنسبة لك</p>
        </div>
        <span class="bg-rose-50 text-rose-700 px-3.5 py-1.5 rounded-full text-xs font-extrabold border border-rose-200">
            <i class="fa-solid fa-heart text-rose-500 ml-1"></i> {{ $favorites->count() }} خدمة محفوظة
        </span>
    </div>

    @if($favorites->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($favorites as $fav)
                @if($fav->service)
                    @include('components.service-card', ['service' => $fav->service])
                @endif
            @endforeach
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 p-8 space-y-4">
            <i class="fa-solid fa-heart-crack text-4xl text-slate-300"></i>
            <h3 class="font-extrabold text-slate-800 text-lg">قائمة المفضلة فارغة حالياً</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                يمكنك إضافة أي خدمة إلى مفضلتك عبر الضغط على أيقونة "إضافة للمفضلة" في صفحة تفاصيل الخدمة.
            </p>
            <a href="{{ route('services.index') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-6 py-3 rounded-2xl shadow-md">
                استكشاف الخدمات الآن
            </a>
        </div>
    @endif

</div>

@endsection
