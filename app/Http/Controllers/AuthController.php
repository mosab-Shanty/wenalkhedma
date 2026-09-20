<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'password.required' => 'يرجى إدخال كلمة المرور',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $user = Auth::user();
            if ($user->status === 'blocked') {
                Auth::logout();
                return back()->withErrors(['email' => 'حسابك محظور حالياً، يرجى التواصل مع الإدارة.']);
            }

            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'مرحباً بك في لوحة التحكم.');
            }

            return redirect()->intended(route('home'))->with('success', 'تم تسجيل الدخول بنجاح.');
        }

        return back()->withErrors([
            'email' => 'البيانات المدخلة غير صحيحة، يرجى المحاولة مرة أخرى.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'full_name.required' => 'يرجى إدخال الاسم الكامل',
            'email.required' => 'يرجى إدخال البريد الإلكتروني',
            'email.unique' => 'البريد الإلكتروني مُسجل بالفعل',
            'password.required' => 'يرجى إدخال كلمة المرور',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 6 أحرف',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password_hash' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'أهلاً بك! تم إنشاء حسابك بنجاح.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'تم تسجيل الخروج بنجاح.');
    }
}
