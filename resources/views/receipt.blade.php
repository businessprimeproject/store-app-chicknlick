<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $order['number'] }} - Chick N Lick</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: Arial, sans-serif; background: #fff9f5; color: #171411; } .receipt-card { position: relative; overflow: hidden; } .receipt-card::before { content: ""; position: absolute; inset: 18% 12%; background: url("/images/chick-n-lick-logo.png") center / 55% auto no-repeat; opacity: .08; pointer-events: none; } .receipt-card > * { position: relative; z-index: 1; } @media print { .no-print { display: none; } body { background: white; } .receipt-card::before { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }</style>
</head>
<body class="min-h-screen px-5 py-8">
    <main class="receipt-card mx-auto max-w-2xl rounded-2xl border border-[#eadfd8] bg-white p-6 shadow-sm sm:p-10">
        <div class="flex items-start justify-between border-b border-[#eadfd8] pb-6">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-700">Chick N Lick</p>
                <h1 class="mt-2 text-3xl font-bold">Purchase Receipt</h1>
                <p class="mt-1 text-sm text-[#765f56]">Commissary B2B Portal</p>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-[#bd160c]">{{ $order['number'] }}</p>
                <p class="mt-1 text-xs text-[#765f56]">{{ $order['submitted_at'] }}</p>
            </div>
        </div>

        <div class="grid gap-4 border-b border-[#eadfd8] py-6 text-sm sm:grid-cols-2">
            <div><p class="text-xs font-bold uppercase text-[#765f56]">Store</p><p class="mt-1 font-semibold"></p></div>
            <div><p class="text-xs font-bold uppercase text-[#765f56]">Submitted by</p><p class="mt-1 font-semibold">{{ $email }}</p></div>
        </div>

        <div class="py-6">
            <h2 class="text-lg font-bold">Ordered Products</h2>
            <div class="mt-4 divide-y divide-[#eadfd8] border-y border-[#eadfd8]">
                @forelse ($order['items'] ?? [] as $item)
                <div class="flex justify-between gap-4 py-3 text-sm"><span>{{ $item['name'] }}<small class="block text-xs text-[#765f56]">{{ $item['quantity'] }} × {{ $item['unit'] ?? 'piece' }} @ ₱{{ number_format((float) ($item['price'] ?? 0), 2) }}</small></span><span class="shrink-0 font-semibold">₱{{ number_format((float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0), 2) }}</span></div>
                @empty
                <div class="flex justify-between py-3 text-sm"><span>Commissary order items</span><span class="font-semibold">Submitted</span></div>
                @endforelse
                <div class="flex justify-between py-3 text-sm"><span>Delivery</span><span>Standard commissary route</span></div>
            </div>
        </div>

        <div class="flex items-center justify-between border-t-2 border-[#171411] pt-4 text-xl font-bold">
            <span>Total Amount</span><span class="text-[#bd160c]">{{ $order['total'] }}</span>
        </div>

        <div class="no-print mt-8 flex flex-col gap-3 sm:flex-row">
            <button type="button" onclick="window.print()" class="flex-1 rounded-xl bg-[#ffd21f] px-4 py-3 font-bold">Print / Save as PDF</button>
            <a href="{{ route('tracking') }}" class="flex-1 rounded-xl border-2 border-[#171411] px-4 py-3 text-center font-bold">Back to Tracking</a>
        </div>
        <p class="mt-8 text-center text-xs text-[#765f56]">Thank you for ordering with Chick N Lick.</p>
    </main>
    <script>document.addEventListener('DOMContentLoaded', () => { const w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT); let n; while (n = w.nextNode()) { if (!n.parentElement.closest('script,style')) n.nodeValue = n.nodeValue.replaceAll('$', '₱'); } });</script>
</body>
</html>


