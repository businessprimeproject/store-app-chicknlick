@props(['number', 'status', 'amount', 'description', 'meta', 'action', 'statusClass' => ''])
<div class="rounded-xl border border-[#e0d7c6] bg-white p-3 shadow-sm">
    <div class="mb-1 flex items-center justify-between"><div class="flex items-center gap-2"><span class="font-display text-sm font-bold">#{{ $number }}</span><span class="rounded-full border px-2 py-0.5 text-[10px] font-bold {{ $statusClass }}">{{ $status }}</span></div><span class="font-display text-sm font-bold">{{ $amount }}</span></div>
    <p class="text-xs leading-4 text-muted">{{ $description }}</p>
    <div class="mt-3 flex items-center justify-between border-t border-cream pt-2"><span class="flex items-center gap-1 text-[11px] font-semibold text-muted"><span class="material-symbols-outlined text-sm">{{ $status === 'In Transit' ? 'schedule' : 'done_all' }}</span>{{ $meta }}</span><button class="flex items-center gap-1 rounded-lg bg-cream px-3 py-1 text-[11px] font-bold"><span class="material-symbols-outlined text-sm">{{ $action === 'Track' ? 'near_me' : ($action === 'Reorder' ? 'replay' : 'receipt') }}</span>{{ $action }}</button></div>
</div>

