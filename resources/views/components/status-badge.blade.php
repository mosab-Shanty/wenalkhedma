@props(['status'])

@php
    $statusMap = [
        'open' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'fa-circle-check', 'text' => 'متاحة الآن'],
        'available' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'fa-circle-check', 'text' => 'متاحة الآن'],
        'crowded' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'icon' => 'fa-users', 'text' => 'مزدحم جداً'],
        'closed' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'icon' => 'fa-circle-xmark', 'text' => 'مغلق حالياً'],
        'unavailable' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'icon' => 'fa-circle-xmark', 'text' => 'غير متاح'],
        'unknown' => ['bg' => 'bg-slate-100 text-slate-600 border-slate-200', 'icon' => 'fa-circle-question', 'text' => 'غير محدد'],
    ];

    $config = $statusMap[$status] ?? $statusMap['unknown'];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $config['bg'] }}">
    <i class="fa-solid {{ $config['icon'] }}"></i>
    {{ $config['text'] }}
</span>
