<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * 1. إنشاء حساب جديد للمستخدم العادي[cite: 3]
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role' => 'user', // افتراضياً دور مستخدم عادي[cite: 3]
            'status' => 'active',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    /**
     * 2. تسجيل الدخول (لكافة الأدوار: user, moderator, admin)[cite: 3]
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'يرجى إدخال البريد وكلمة المرور بشكل صحيح',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات الاعتماد غير صحيحة'
            ], 401);
        }

        if ($user->status !== 'active') {

            $message = match ($user->status) {
                'blocked' => 'هذا الحساب محظور، يرجى التواصل مع الإدارة',
                'pending' => 'هذا الحساب قيد المراجعة ولم يتم تفعيله بعد',
                default => 'هذا الحساب غير متاح حالياً',
            };

            return response()->json([
                'status' => false,
                'message' => $message
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role, // مهم لمعرفة هل هو أدمن، مشرف، أم مستخدم[cite: 3]
                'status' => $user->status,
            ]
        ], 200);
    }

    /**
     * 3. جلب بيانات الملف الشخصي للمستخدم الحالي
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * 4. تسجيل الخروج وإلغاء الـ Token
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }
}
