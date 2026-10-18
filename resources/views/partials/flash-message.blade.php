@if (session('success'))
    <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800 shadow-sm">
        <span class="mt-0.5 text-sm">✅</span>
        <p class="font-medium leading-relaxed">{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-800 shadow-sm">
        <span class="mt-0.5 text-sm">⚠️</span>
        <p class="font-medium leading-relaxed">{{ session('error') }}</p>
    </div>
@endif

