<header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm" x-data="{ mobileMenu: false, userMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <!-- Logo Only (Text removed as requested) -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center group">
                    <img src="{{ asset('Logo.png') }}" alt="وين الخدمة" class="h-11 w-auto group-hover:scale-105 transition-transform">
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}" class="font-semibold text-slate-700 hover:text-emerald-600 transition-colors {{ request()->routeIs('home') ? 'text-emerald-600 font-bold' : '' }}">
                    <i class="fa-solid fa-house ml-1 text-slate-400"></i> الرئيسية
                </a>
                <a href="{{ route('services.index') }}" class="font-semibold text-slate-700 hover:text-emerald-600 transition-colors {{ request()->routeIs('services.index') ? 'text-emerald-600 font-bold' : '' }}">
                    <i class="fa-solid fa-compass ml-1 text-slate-400"></i> استكشاف الخدمات
                </a>
                <a href="{{ route('services.map') }}" class="font-semibold text-slate-700 hover:text-emerald-600 transition-colors {{ request()->routeIs('services.map') ? 'text-emerald-600 font-bold' : '' }} flex items-center gap-1.5">
                    <i class="fa-solid fa-map-location-dot {{ request()->routeIs('services.map') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                    <span>خريطة الخدمات</span>
                </a>
                @auth
                    <a href="{{ route('favorites.index') }}" class="font-semibold text-slate-700 hover:text-emerald-600 transition-colors {{ request()->routeIs('favorites.index') ? 'text-emerald-600 font-bold' : '' }}">
                        <i class="fa-solid fa-heart ml-1 text-rose-500"></i> المفضلة
                    </a>
                @endauth
            </nav>

            <!-- User / Action Buttons -->
            <div class="hidden md:flex items-center gap-4">
                @auth
                    <!-- Add Service Button -->
                    <a href="{{ route('services.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl text-sm transition-all shadow-md shadow-emerald-600/20 flex items-center gap-2">
                        <i class="fa-solid fa-plus text-xs"></i> إضافة خدمة
                    </a>

                    <!-- User Dropdown Menu -->
                    <div class="relative" @click.away="userMenu = false">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-2 text-sm font-bold text-slate-700 hover:text-emerald-600 bg-slate-100 px-3 py-2 rounded-xl transition-all">
                            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black">
                                {{ mb_substr(Auth::user()->full_name, 0, 1) }}
                            </div>
                            <span>{{ Auth::user()->full_name }}</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>

                        <div x-show="userMenu" x-cloak x-transition 
                             class="absolute left-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs text-slate-400">حساب مسجل</p>
                                <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-amber-600 hover:bg-amber-50">
                                    <i class="fa-solid fa-shield-halved text-amber-500 w-5"></i> لوحة الإدارة
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                            @endif

                            <a href="{{ route('services.mine') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                <i class="fa-solid fa-list-check text-slate-400 w-5"></i> خدماتي المضافة
                            </a>
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                <i class="fa-solid fa-flag text-slate-400 w-5"></i> بلاغاتي
                            </a>
                            <a href="{{ route('favorites.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                <i class="fa-solid fa-heart text-rose-400 w-5"></i> قائمة المفضلة
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-right flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-50">
                                    <i class="fa-solid fa-right-from-bracket w-5"></i> تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Guest Options -->
                    <a href="{{ route('login') }}" class="font-bold text-sm text-slate-700 hover:text-emerald-600 px-3 py-2">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-4 py-2 rounded-xl transition-all shadow-md shadow-emerald-600/20">
                        إنشاء حساب جديد
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="flex items-center md:hidden gap-2">
                <button @click="mobileMenu = !mobileMenu" class="p-2 text-slate-600 hover:text-slate-900 focus:outline-none">
                    <i class="fa-solid" :class="mobileMenu ? 'fa-xmark text-xl' : 'fa-bars text-xl'"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-slate-100 bg-white px-4 pt-3 pb-6 space-y-3">
        <a href="{{ route('home') }}" class="block font-bold text-slate-800 py-2 border-b border-slate-50">الرئيسية</a>
        <a href="{{ route('services.index') }}" class="block font-bold text-slate-800 py-2 border-b border-slate-50">استكشاف الخدمات</a>
        <a href="{{ route('services.map') }}" class="block font-bold text-slate-800 py-2 border-b border-slate-50 flex items-center gap-2 {{ request()->routeIs('services.map') ? 'text-emerald-600' : '' }}">
            <i class="fa-solid fa-map-location-dot text-emerald-600"></i>
            <span>خريطة الخدمات</span>
        </a>
        
        @auth
            <a href="{{ route('services.mine') }}" class="block text-slate-700 py-2">خدماتي المضافة</a>
            <a href="{{ route('favorites.index') }}" class="block text-slate-700 py-2">المفضلة</a>
            <a href="{{ route('reports.index') }}" class="block text-slate-700 py-2">بلاغاتي</a>
            <a href="{{ route('services.create') }}" class="block bg-emerald-600 text-white text-center font-bold py-2.5 rounded-xl">إضافة خدمة جديدة</a>
            
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block text-amber-600 font-bold py-2">لوحة الإدارة</a>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="pt-2">
                @csrf
                <button type="submit" class="w-full text-right font-bold text-rose-600 py-2">تسجيل الخروج</button>
            </form>
        @else
            <div class="pt-2 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="block text-center border border-slate-200 font-bold py-2.5 rounded-xl text-slate-700">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="block text-center bg-emerald-600 text-white font-bold py-2.5 rounded-xl">إنشاء حساب جديد</a>
            </div>
        @endauth
    </div>
</header>
