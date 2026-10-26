@props(['label', 'value', 'detail', 'icon', 'footer', 'footerValue', 'iconClass' => 'text-ink', 'valueClass' => 'text-ink'])
<div class="flex flex-col justify-between rounded-xl border border-[#e0d7c6] bg-white p-3 shadow-sm">
    <div class="flex items-center justify-between"><span class="text-[11px] font-bold uppercase tracking-wider text-[#926f69]">{{ $label }}</span><div class="flex h-7 w-7 items-center justify-center rounded-lg bg-cream {{ $iconClass }}"><span class="material-symbols-outlined text-base">{{ $icon }}</span></div></div>
    <div class="mt-2"><div class="font-display text-xl font-bold leading-6 {{ $valueClass }}">{{ $value }}</div><p class="mt-0.5 text-xs font-bold text-amber-700">{{ $detail }}</p></div>
    <div class="mt-2 flex items-center justify-between border-t border-cream pt-2 text-[11px] text-muted"><span>{{ $footer }}</span><span class="font-bold text-ink">{{ $footerValue }}</span></div>
</div>

