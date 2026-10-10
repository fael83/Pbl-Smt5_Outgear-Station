@props([
    'title',
    'value',
    'subvalue' => null,
    'icon',
    'badgeColor',
])

<div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $title }}</span>
        <div class="w-8 h-8 rounded-xl {{ $badgeColor }} flex items-center justify-center text-sm">
            {{ $icon }}
        </div>
    </div>
    <div>
        <h3 class="text-2xl font-bold font-heading text-[#1A2024]">{{ $value }}</h3>
        @if($subvalue)
            <p class="text-xs text-gray-500 mt-1">{{ $subvalue }}</p>
        @endif
    </div>
</div>