@extends('layouts.app')

@section('title', 'إدارة المستخدمين - الأدمن')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إدارة المستخدمين والحسابات</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">تعديل أدوار المستخدمين وتغيير حالة الحسابات (نشط / حظر)</p>
        </div>
        <span class="text-xs font-bold text-slate-500">إجمالي الحسابات: ({{ $users->total() }})</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">الاسم الكامل</th>
                        <th class="p-4">البريد الإلكتروني</th>
                        <th class="p-4">رقم الهاتف</th>
                        <th class="p-4">الدور الصلاحية</th>
                        <th class="p-4">الحالة</th>
                        <th class="p-4 text-center">التحكم الحسابي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @foreach($users as $usr)
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-black text-slate-900 text-sm">{{ $usr->full_name }}</td>
                            <td class="p-4 text-slate-500">{{ $usr->email }}</td>
                            <td class="p-4 text-slate-500 dir-ltr text-right">{{ $usr->phone ?? '-' }}</td>
                            <td class="p-4">
                                <form action="{{ route('admin.users.update_role', $usr->user_id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold p-1">
                                        <option value="user" {{ $usr->role === 'user' ? 'selected' : '' }}>مستخدم عادي</option>
                                        <option value="moderator" {{ $usr->role === 'moderator' ? 'selected' : '' }}>مشرف Moderator</option>
                                        <option value="admin" {{ $usr->role === 'admin' ? 'selected' : '' }}>مدير Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td class="p-4">
                                @if($usr->status === 'active')
                                    <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full text-[11px] font-bold border border-emerald-200">نشط</span>
                                @else
                                    <span class="bg-rose-50 text-rose-700 px-2.5 py-1 rounded-full text-[11px] font-bold border border-rose-200">محظور</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.users.toggle_status', $usr->user_id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl font-bold text-xs {{ $usr->status === 'active' ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                        {{ $usr->status === 'active' ? 'حظر الحساب' : 'إلغاء الحظر' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-4">
        {{ $users->links() }}
    </div>

</div>

@endsection
