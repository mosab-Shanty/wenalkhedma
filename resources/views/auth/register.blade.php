@extends('layouts.app')

@section('title', 'إنشاء حساب جديد - وين الخدمة')

@section('content')

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full bg-white rounded-3xl border border-slate-200 shadow-xl p-8 space-y-6">
        
        <div class="text-center space-y-3">
            <img src="{{ asset('Logo.png') }}" alt="وين الخدمة" class="h-14 mx-auto w-auto">
            <h2 class="text-2xl font-black text-slate-900">إنشاء حساب جديد</h2>
            <p class="text-xs text-slate-500 font-medium">قم بالانضمام لمنصة وين الخدمة لإضافة وتحديث الخدمات الحيوية</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">الاسم الكامل *</label>
                <input type="text" 
                       name="full_name" 
                       value="{{ old('full_name') }}" 
                       required 
                       placeholder="مثال: أحمد محمود" 
                       class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">البريد الإلكتروني *</label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       placeholder="name@example.com" 
                       class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600 dir-ltr text-right">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">رقم الهاتف</label>
                <input type="text" 
                       name="phone" 
                       value="{{ old('phone') }}" 
                       placeholder="059xxxxxxx" 
                       class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600 dir-ltr">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">كلمة المرور *</label>
                <input type="password" 
                       name="password" 
                       required 
                       placeholder="6 أحرف على الأقل" 
                       class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">تأكيد كلمة المرور *</label>
                <input type="password" 
                       name="password_confirmation" 
                       required 
                       placeholder="إعادة تأكيد كلمة المرور" 
                       class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3.5 focus:outline-none focus:border-emerald-600">
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-3.5 rounded-xl transition-all shadow-md shadow-emerald-600/20">
                إتمام التسجيل
            </button>
        </form>

        <div class="border-t border-slate-100 pt-6 text-center text-xs font-medium text-slate-500">
            لديك حساب بالفعل؟ 
            <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:underline">تسجيل الدخول</a>
        </div>

    </div>

</div>

@endsection
