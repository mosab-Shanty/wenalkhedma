@extends('layouts.app')

@section('title', 'منصة وين الخدمة - الرئيسية')

@section('content')

    <!-- Hero Search Section with Gaza Image Background & Dark Overlay -->
    <section class="relative min-h-[560px] flex items-center text-white py-20 lg:py-24 overflow-hidden">
        
        <!-- Gaza Background Image -->
        <img src="{{ asset('img/gaza-hero.jpg') }}" 
             alt="غزة" 
             class="absolute inset-0 w-full h-full object-cover object-center scale-105 transform">

        <!-- Multi-layer Overlay for High Contrast & Visual Depth -->
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/90 via-slate-950/80 to-slate-950/95"></div>
        <div class="absolute inset-0 bg-emerald-950/30 mix-blend-multiply"></div>
        <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:20px_20px]"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-8 w-full">
            
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/20 text-xs font-bold text-emerald-300">
                <i class="fa-solid fa-sparkles"></i> الدليل التفاعلي المباشر للخدمات الحيوية
            </div>

            <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-normal sm:leading-relaxed max-w-4xl mx-auto">
                ابحث عن أي خدمة تحتاجها <br>
                <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-green-400 bg-clip-text text-transparent inline-block mt-1 sm:mt-2">في غزة والمحافظات فوراً</span>
            </h1>

            <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto font-normal leading-relaxed">
                تصفح حالة الصيدليات، المخابز، نقاط توزيع المياه، والخدمات المتاحة لحظة بلحظة وبدقة عالية دون الحاجة لتسجيل حساب.
            </p>

            <!-- Search Form -->
            <form action="{{ route('services.index') }}" method="GET" class="max-w-3xl mx-auto bg-white p-2 sm:p-3 rounded-3xl shadow-2xl flex flex-col sm:flex-row items-center gap-2">
                <div class="flex items-center gap-3 px-4 w-full text-slate-700">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-lg"></i>
                    <input type="text" 
                           name="q" 
                           placeholder="ابحث باسم الخدمة، المنطقة (مثلاً: صيدلية الرمال)..." 
                           class="w-full bg-transparent py-3 text-sm sm:text-base font-bold text-slate-900 focus:outline-none placeholder-slate-400">
                </div>
                <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3.5 rounded-2xl text-base transition-all shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2">
                    <span>بحث الآن</span>
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </button>
            </form>

            <!-- Quick Stats -->
            <div class="pt-8 grid grid-cols-2 sm:grid-cols-3 gap-4 max-w-2xl mx-auto border-t border-white/10">
                <div class="p-3">
                    <span class="block text-3xl font-black text-white">{{ $totalServices }}</span>
                    <span class="text-xs text-slate-400 font-semibold">إجمالي الخدمات الموثقة</span>
                </div>
                <div class="p-3">
                    <span class="block text-3xl font-black text-emerald-400">{{ $openServices }}</span>
                    <span class="text-xs text-slate-400 font-semibold">متاحة ومفتوحة الآن</span>
                </div>
                <div class="p-3 col-span-2 sm:col-span-1">
                    <span class="block text-3xl font-black text-teal-300">{{ $totalCategories }}</span>
                    <span class="text-xs text-slate-400 font-semibold">تصنيفات رئيسية</span>
                </div>
            </div>

        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900">تصفح الخدمات حسب التصنيف</h2>
                <p class="text-xs text-slate-500 font-medium">اختر المجال للوصول المباشر للخدمات المتوفرة</p>
            </div>
            <a href="{{ route('services.index') }}" class="text-emerald-600 hover:text-emerald-700 font-bold text-sm flex items-center gap-1">
                <span>عرض الكل</span>
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('services.index', ['category_id' => $category->category_id]) }}" 
                   class="bg-white p-5 rounded-3xl border border-slate-200 hover:border-emerald-500 hover:shadow-lg transition-all group flex flex-col items-center text-center space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        @if(str_contains($category->name, 'صيدل'))
                            <i class="fa-solid fa-prescription-bottle-medical"></i>
                        @elseif(str_contains($category->name, 'مخبز'))
                            <i class="fa-solid fa-wheat-wheat"></i>
                        @elseif(str_contains($category->name, 'مياه'))
                            <i class="fa-solid fa-droplet"></i>
                        @elseif(str_contains($category->name, 'عياد') || str_contains($category->name, 'طبي'))
                            <i class="fa-solid fa-stethoscope"></i>
                        @elseif(str_contains($category->name, 'كهرب'))
                            <i class="fa-solid fa-bolt"></i>
                        @else
                            <i class="fa-solid fa-layer-group"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-800 group-hover:text-emerald-600 transition-colors">{{ $category->name }}</h3>
                        <span class="text-[11px] text-slate-400 font-medium">{{ $category->services_count }} خدمة</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- AI Callout Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="bg-gradient-to-r from-emerald-950 via-teal-900 to-slate-900 rounded-3xl p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl relative overflow-hidden">
            <div class="space-y-3 relative z-10">
                <span class="inline-flex items-center gap-1.5 bg-emerald-500/20 text-emerald-300 text-xs font-bold px-3 py-1 rounded-full border border-emerald-400/30">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> المساعد الذكي
                </span>
                <h3 class="text-2xl sm:text-3xl font-black">تحتاج مساعدة سريعة في العثور على أقرب خدمة؟</h3>
                <p class="text-slate-300 text-xs sm:text-sm max-w-xl font-normal">
                    استخدم المساعد الذكي لكتابة استفساراتك باللغة الطبيعية مثل: "وين أقرب صيدلية متاحة الآن؟" وسيقوم النظام فوراً باستخلاص أحدث البيانات لك.
                </p>
            </div>
            <button @click="aiOpen = true" class="relative z-10 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3.5 rounded-2xl shadow-lg shadow-emerald-600/30 text-sm whitespace-nowrap transition-all flex items-center gap-2">
                <i class="fa-solid fa-comments"></i> تجربة المساعد الذكي
            </button>
        </div>
    </section>

    <!-- Featured Services Grid -->
    <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900">أحدث الخدمات المضافة والموثقة</h2>
                <p class="text-xs text-slate-500 font-medium">الخدمات التي تم اعتمادها حديثاً من قبل المشرفين</p>
            </div>
            <a href="{{ route('services.index') }}" class="text-emerald-600 hover:text-emerald-700 font-bold text-sm flex items-center gap-1">
                <span>استكشاف كل الخدمات</span>
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
        </div>

        @if($featuredServices->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($featuredServices as $service)
                    @include('components.service-card', ['service' => $service])
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 p-8 space-y-3">
                <i class="fa-solid fa-inbox text-4xl text-slate-300"></i>
                <p class="font-bold text-slate-700 text-base">لا توجد خدمات موثقة متاحة حالياً</p>
            </div>
        @endif
    </section>

@endsection
