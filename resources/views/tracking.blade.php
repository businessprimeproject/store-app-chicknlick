<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking - Chick N Lick</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,600,0,0" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #fff9f5; color: #171411; }
        .yellow { background: #ffd21f; }
        .red { color: #bd160c; }
    </style>
</head>
<body class="min-h-screen pb-24">
    <header class="border-b border-[#eadfd8] bg-[#fff9f5] px-5 py-4">
        <div class="mx-auto flex max-w-6xl items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="material-symbols-outlined red">arrow_back</span>
                <span class="flex h-11 w-11 items-center justify-center rounded-full yellow text-2xl">🍴</span>
                <span class="text-xl font-bold red">Chick N Lick</span>
            </a>
            <span class="material-symbols-outlined text-[#604b42]">notifications</span>
        </div>
    </header>

    <main class="mx-auto max-w-6xl space-y-5 px-5 py-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700">Delivery &amp; Logistics</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight">Track Orders</h1>
            <p class="mt-1 text-sm text-[#765f56]">Monitor your commissary deliveries and order status.</p>
        </div>

        @if ($trackingOrder)
        @php
            $trackingStatus = $trackingOrder['status'] ?? 'Pending';
            $statusStages = ['Pending', 'Order Accepted', 'Preparing Order', 'Ready for Dispatch', 'In Transit', 'Delivered'];
            $stageIndex = array_search($trackingStatus, $statusStages, true);
            $progress = $stageIndex === false ? 0 : max(8, (($stageIndex + 1) / count($statusStages)) * 100);
        @endphp
        <section class="rounded-2xl border border-[#bde8d0] bg-white px-6 py-8 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#e8faf2] text-emerald-700"><span class="material-symbols-outlined text-3xl">check_circle</span></div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">Purchase Order Submitted</p>
                    <h2 class="mt-1 text-2xl font-bold">{{ $trackingOrder['number'] ?? 'Purchase Order' }}</h2>
                    <p class="mt-1 text-sm text-[#765f56]">Sent to Commissary Dispatch Engine • {{ $trackingOrder['submitted_at'] ?? '' }}</p>
                </div>
            </div>
            <div class="mt-6 rounded-xl bg-[#fff7e8] p-4">
                <div class="flex items-center justify-between text-sm font-bold"><span>Order status</span><span class="rounded-full bg-[#e8faf2] px-3 py-1 text-xs text-emerald-700">{{ $trackingStatus }}</span></div>
                <div class="mt-4 h-2 rounded-full bg-[#eadfd8]"><div class="h-full rounded-full bg-emerald-500" style="width: {{ $progress }}%"></div></div>
                <p class="mt-2 text-xs text-[#765f56]">Latest update: {{ $trackingStatus }}.</p>
            </div>
            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('tracking.receipt') }}" class="yellow inline-flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-[#171411] px-4 py-3 text-sm font-bold shadow-[2px_2px_0_#171411]"><span class="material-symbols-outlined text-base">receipt_long</span>View Receipt</a>
                <button type="button" onclick="window.print()" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-[#171411] px-4 py-3 text-sm font-bold"><span class="material-symbols-outlined text-base">picture_as_pdf</span>Save as PDF</button>
            </div>
            <p class="mt-3 text-center text-xs text-[#765f56]">Thank you for your order! We’ll keep you updated as it moves through dispatch.</p>
        </section>
        @else
        <section class="rounded-2xl border border-dashed border-[#d9c7bb] bg-white px-6 py-14 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#fff1dc] text-amber-700">
                <span class="material-symbols-outlined text-4xl">local_shipping</span>
            </div>
            <h2 class="mt-5 text-xl font-bold">No orders yet</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#765f56]">Your delivery tracking will appear here after you submit a purchase order.</p>
            <a href="{{ route('orders') }}" class="yellow mt-6 inline-flex items-center gap-2 rounded-xl border-2 border-[#171411] px-5 py-3 text-sm font-bold shadow-[2px_2px_0_#171411]">
                Order Stock <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </section>
        @endif

        <section class="rounded-2xl border border-[#eadfd8] bg-white p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined rounded-lg bg-[#e8faf2] p-2 text-emerald-700">verified</span>
                <div>
                    <h2 class="font-bold">Live tracking is ready</h2>
                    <p class="mt-1 text-sm leading-6 text-[#765f56]">Once your order is accepted by the commissary, carrier updates and delivery milestones will be shown on this page.</p>
                </div>
            </div>
        </section>
    </main>

    <x-bottom-nav :email="$email" />
</body>
</html>


