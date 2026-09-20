<footer class="bg-slate-900 text-slate-300 border-t border-slate-800 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            
            <!-- Brand Info -->
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo2.png') }}" alt="وين الخدمة" class="h-10 w-auto">
                    <span class="font-black text-2xl text-white">وين الخدمة</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                    منصة مجتمعية تفاعلية تهدف لمساعدة المواطنين والزوار في الوصول السريع للخدمات والمرافق التموينية والطبية والأساسية المتاحة وتوفير تحديث فوري للحالات.
                </p>
                <div class="flex items-center gap-4 text-slate-400 text-sm">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800 text-emerald-400 text-xs font-semibold">
                        <i class="fa-solid fa-circle text-[8px] animate-pulse"></i> نظام نشط
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="space-y-3">
                <h4 class="text-white font-bold text-base">روابط سريعة</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-emerald-400 transition-colors">الرئيسية</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-emerald-400 transition-colors">تصفح كل الخدمات</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition-colors">تسجيل الدخول</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-emerald-400 transition-colors">إنشاء حساب جديد</a></li>
                </ul>
            </div>

            <!-- Categories Quick View -->
            <div class="space-y-3">
                <h4 class="text-white font-bold text-base">التصنيفات الشائعة</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><i class="fa-solid fa-prescription-bottle-medical text-emerald-500 ml-1"></i> صيدليات ومستلزمات طبية</li>
                    <li><i class="fa-solid fa-wheat-wheat text-amber-500 ml-1"></i> مخابز ومنافذ طحين</li>
                    <li><i class="fa-solid fa-droplet text-blue-500 ml-1"></i> محطات توزيع مياه</li>
                    <li><i class="fa-solid fa-bolt text-yellow-500 ml-1"></i> شحن كهرباء وطاقة</li>
                </ul>
            </div>

        </div>

        <div class="border-t border-slate-800 mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-4">
            <p>© {{ date('Y') }} منصة "وين الخدمة". جميع الحقوق محفوظة.</p>
            <p>Laravel Monolithic Architecture (Simple MVC)</p>
        </div>
    </div>
</footer>
