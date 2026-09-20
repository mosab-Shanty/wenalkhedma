<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 mb-4 text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl text-emerald-600"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 mb-4 text-rose-800 bg-rose-50 border border-rose-200 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-xl text-rose-600"></i>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-rose-500 hover:text-rose-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if (session('info'))
        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 mb-4 text-cyan-800 bg-cyan-50 border border-cyan-200 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-info text-xl text-cyan-600"></i>
                <span class="font-bold text-sm">{{ session('info') }}</span>
            </div>
            <button @click="show = false" class="text-cyan-500 hover:text-cyan-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" class="p-4 mb-4 text-rose-800 bg-rose-50 border border-rose-200 rounded-2xl shadow-sm space-y-1">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2 font-bold text-sm text-rose-700">
                    <i class="fa-solid fa-circle-xmark"></i> يرجى تصحيح الأخطاء التالية:
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 text-rose-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
