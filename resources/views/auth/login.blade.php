@extends('layouts.app')

@section('title', 'تسجيل الدخول - وين الخدمة')

@section('content')

<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full bg-white rounded-3xl border border-slate-200 shadow-xl p-8 space-y-6">
        
        <div class="text-center space-y-3">
            <img src="{{ asset('Logo.png') }}" alt="وين الخدمة" class="h-14 mx-auto w-auto">
            <h2 class="text-2xl font-black text-slate-900">تسجيل الدخول</h2>
            <p class="text-xs text-slate-500 font-medium">أدخل بيانات حسابك للوصول للخدمات الخاصة والمفضلة</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">البريد الإلكتروني</label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       placeholder="example@win.com" 
                       class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600 dir-ltr text-right">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">كلمة المرور</label>
                <input type="password" 
                       name="password" 
                       required 
                       placeholder="••••••••" 
                       class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 font-bold text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>تذكرني على هذا الجهاز</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-3.5 rounded-xl transition-all shadow-md shadow-emerald-600/20">
                تسجيل الدخول
            </button>
        </form>

        <div class="border-t border-slate-100 pt-6 text-center text-xs font-medium text-slate-500">
            ليس لديك حساب بعد؟ 
            <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:underline">أنشئ حسابك الآن</a>
        </div>

    </div>

</div>

@endsection
