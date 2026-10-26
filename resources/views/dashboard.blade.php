<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Franchisee Dashboard | Chick N Lick</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>tailwind.config={theme:{extend:{colors:{ink:'#1e1b18',paper:'#fff8f4',cream:'#faf2ed',red:'#b00d00',yellow:'#ffd91f',muted:'#5d3f3a'},fontFamily:{display:['Space Grotesk','sans-serif'],body:['Plus Jakarta Sans','sans-serif']}}}};</script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .material-symbols-outlined{font-family:'Material Symbols Outlined';font-weight:normal;font-style:normal;font-size:20px;line-height:1;display:inline-block;white-space:nowrap;-webkit-font-feature-settings:'liga';-webkit-font-smoothing:antialiased}
        .no-scrollbar::-webkit-scrollbar{display:none}.no-scrollbar{scrollbar-width:none}
    </style>
</head>
<body class="min-h-screen bg-paper pb-24 font-body text-ink antialiased">
    <div class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col bg-paper shadow-xl">
        <header class="sticky top-0 z-40 border-b-2 border-[#101010] bg-yellow shadow-sm">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-2">
                    <img alt="Chick N Lick Supply Portal" class="h-10 w-auto rounded-lg border border-[#101010] bg-white object-contain p-1 shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBDCD-DGedzzg-V2PWIu5W6gk4zqddssrMc-eynN8KfEOKx60kHALPAhkg-OMysoFPerxowcVdGfZxcGEVV5m0cPLqDsHQqaIbLFFDt9vyOjbtZiEuQwmq3iVBdqARNOnaqEvra2K6JTDZAUpTNv5yY4_20n6UBGjAKWFmOHr27E_86vsr79BzMasM6VyomSexnVxjoINi8kDY-1Ag1F1XemhpasrKCpxmjh33Wuf2eDyhEmVpJvMwW3F2sBRty8_PuTwEH5UtdDBhCNA">
                    <span class="border-l border-[#101010]/30 pl-2 font-display text-[13px] font-bold">Franchisee Portal</span>
                </div>
                <span class="material-symbols-outlined">notifications</span>
            </div>
            <div class="flex items-center justify-end px-4 pb-2.5">
                <div class="flex items-center gap-3"><span class="text-[11px] font-semibold">{{ $email }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="rounded-lg border border-[#101010] bg-white px-2.5 py-1 text-[11px] font-bold shadow-sm">Logout</button></form></div>
            </div>
        </header>

        <main class="relative mx-auto flex-1 w-full max-w-6xl space-y-4 px-4 py-3 sm:px-6 lg:px-8">
            <section class="rounded-r-xl border border-[#ebe5d8] border-l-4 border-l-red bg-cream p-3.5 shadow-sm">
                <div class="flex items-start gap-2.5"><div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red"><span class="material-symbols-outlined">warning</span></div><div class="flex-1"><div class="flex items-center justify-between"><h2 class="font-display text-sm font-bold text-red">Urgent Stock Alert</h2><span class="rounded-full border-2 border-[#101010] bg-yellow px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider">Critical Par</span></div><p class="mt-1 text-xs leading-snug"> <span class="font-semibold text-red">Seasoned Chicken Tenderloin Fillets (10kg)</span> below safety threshold (2 packs remaining, estimated depletion in 14 hours).</p><div class="mt-2.5 flex items-center justify-between"><span class="flex items-center gap-1 text-[11px] font-semibold text-muted"><span class="material-symbols-outlined text-sm">local_shipping</span>Cutoff: 18:00</span><button class="flex items-center gap-1 rounded-xl border-2 border-[#101010] bg-yellow px-3.5 py-1.5 text-xs font-bold shadow-[2px_2px_0px_0px_#101010]">Quick-Order<span class="material-symbols-outlined text-sm">arrow_forward</span></button></div></div></div>
            </section>

            <section class="grid grid-cols-2 gap-2.5 lg:grid-cols-4">
                @if ($hasSubmittedOrder ?? false)
                <x-dashboard-metric label="Open POs" value="{{ $orderCount }} {{ $orderCount === 1 ? 'Order' : 'Orders' }}" detail="{{ $committedTotal }} Committed" icon="receipt_long" footer="Status" footer-value="Submitted" />
                <x-dashboard-metric label="In Transit" value="{{ $orderCount }} {{ $orderCount === 1 ? 'Delivery' : 'Deliveries' }}" detail="Active delivery orders" icon="local_shipping" footer="Latest PO" footer-value="{{ $latestActiveOrder['number'] ?? 'None' }}" icon-class="text-blue-700" />
                <x-dashboard-metric label="Delivered" value="{{ $deliveredCount }} {{ $deliveredCount === 1 ? 'Order' : 'Orders' }}" detail="Successfully delivered" icon="task_alt" footer="Latest PO" footer-value="{{ $latestDeliveredOrder['number'] ?? 'None' }}" icon-class="text-emerald-700" />
                @else
                <x-dashboard-metric label="Open POs" value="0 Orders" detail="₱0.00 Committed" icon="receipt_long" footer="Status" footer-value="No orders yet" />
                <x-dashboard-metric label="In Transit" value="0 Deliveries" detail="No active deliveries" icon="local_shipping" footer="Status" footer-value="No orders yet" icon-class="text-blue-700" />
                <x-dashboard-metric label="Delivered" value="0 Orders" detail="No delivered orders" icon="task_alt" footer="Status" footer-value="No orders yet" icon-class="text-emerald-700" />
                @endif
            </section>

            <section>
                <div class="mb-2 flex items-center justify-between"><h3 class="font-display text-[15px] font-bold">Quick Actions</h3><span class="text-[11px] text-muted">Shift Ergonomics</span></div>
                <div class="no-scrollbar -mx-4 flex gap-2.5 overflow-x-auto px-4 pb-1">
                    <a href="{{ route('orders') }}" class="flex shrink-0 items-center gap-2 rounded-xl border-2 border-[#101010] bg-yellow px-4 py-2.5 text-[13px] font-bold shadow-[2px_2px_0px_0px_#101010]"><span class="material-symbols-outlined">shopping_bag</span>Order Stock</a>
                    <button class="flex shrink-0 items-center gap-1.5 rounded-xl border border-[#e0d7c6] bg-white px-3.5 py-2.5 text-[13px] font-semibold shadow-sm"><span class="material-symbols-outlined text-amber-700">replay</span>Reorder PO-8819</button>
                    <button class="flex shrink-0 items-center gap-1.5 rounded-xl border border-[#e0d7c6] bg-white px-3.5 py-2.5 text-[13px] font-semibold shadow-sm"><span class="material-symbols-outlined text-blue-700">near_me</span>Track Delivery</button>
                    <button class="flex shrink-0 items-center gap-1.5 rounded-xl border border-[#e0d7c6] bg-white px-3.5 py-2.5 text-[13px] font-semibold shadow-sm"><span class="material-symbols-outlined text-emerald-700">qr_code_scanner</span>Receive Stock</button>
                </div>
            </section>

            <section class="rounded-xl border border-[#e0d7c6] bg-gradient-to-r from-white to-cream p-3.5 shadow-sm"><div class="flex items-start gap-3"><div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#ffddb6] text-amber-900"><span class="material-symbols-outlined">campaign</span></div><div class="flex-1"><div class="flex items-center justify-between"><span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">HQ Announcement</span><span class="text-[10px] text-muted">Today</span></div><p class="mt-1 text-xs font-medium leading-snug">New Crispy Smoked Paprika Dry Rub available for seasonal menu trial. Minimum 2 cases per franchisee.</p><button class="mt-2 flex items-center gap-1 text-[11px] font-bold text-amber-700">View Spec Sheet<span class="material-symbols-outlined text-sm">arrow_forward</span></button></div></div></section>

            <section class="space-y-2.5"><div class="flex items-center justify-between"><h3 class="font-display text-base font-bold">Recent Orders</h3><a href="#" class="flex items-center rounded-full border-2 border-[#101010] bg-yellow px-2.5 py-0.5 text-xs font-bold shadow-[1px_1px_0px_0px_#101010]">View All<span class="material-symbols-outlined text-sm">chevron_right</span></a></div>
                <x-dashboard-order number="PO-8821" status="In Transit" amount="₱2,845.00" description="Weekly Core Protein & Fryer Pack" meta="ETA 11:30 AM Today" action="Track" status-class="bg-blue-100 text-blue-700 border-blue-300" />
                <x-dashboard-order number="PO-8819" status="Delivered" amount="₱1,280.50" description="Sauces, Packaging & Dry Spices" meta="Delivered (Yesterday)" action="Reorder" status-class="bg-emerald-100 text-emerald-700 border-emerald-300" />
                <x-dashboard-order number="PO-8810" status="Completed" amount="₱640.00" description="Monthly Deep Clean & Sanitation Supply" meta="Closed PO" action="Invoice" status-class="bg-cream text-muted border-[#e9e1dc]" />
            </section>
        </main>

        <x-bottom-nav :email="$email" />
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const recentOrders = [...document.querySelectorAll('section')].find((section) => section.textContent.includes('Recent Orders'));
            const hasOrder = @json($hasSubmittedOrder ?? false);
            if (recentOrders && !hasOrder) recentOrders.classList.add('hidden');
            if (recentOrders && hasOrder) {
                const submittedOrders = @json($submittedOrders ?? []);
                recentOrders.innerHTML = `<div class="flex items-center justify-between"><h3 class="font-display text-base font-bold">Recent Orders</h3><a href="{{ route('tracking') }}" class="flex items-center rounded-full border-2 border-[#101010] bg-yellow px-2.5 py-0.5 text-xs font-bold shadow-[1px_1px_0px_0px_#101010]">Track<span class="material-symbols-outlined text-sm">chevron_right</span></a></div>${submittedOrders.slice().reverse().map((order) => `<div class="rounded-xl border border-[#e0d7c6] bg-white p-3.5 shadow-sm"><div class="flex items-center justify-between"><div class="flex items-center gap-2"><span class="font-display text-sm font-bold">${order.number}</span><span class="rounded-full border border-emerald-300 bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">${order.status || 'Pending'}</span></div><span class="font-display text-sm font-bold">${order.total}</span></div><p class="mt-1 text-xs text-muted">Commissary order submitted</p><div class="mt-3 flex items-center justify-between border-t border-cream pt-2"><span class="flex items-center gap-1 text-[11px] font-semibold text-muted"><span class="material-symbols-outlined text-sm">schedule</span>${order.status || 'Pending'}</span><span class="text-[11px] font-bold text-amber-700">Track Order</span></div></div>`).join('')}`;
            }

            const stockAlert = [...document.querySelectorAll('section')].find((section) => section.textContent.includes('Urgent Stock Alert'));
            if (stockAlert) stockAlert.remove();

            const announcement = [...document.querySelectorAll('section')].find((section) => section.textContent.includes('HQ Announcement'));
            if (announcement) announcement.remove();

            [...document.querySelectorAll('span')].find((element) => element.textContent.trim() === 'Shift Ergonomics')?.remove();

            ['Reorder PO-8819', 'Track Delivery', 'Receive Stock'].forEach((label) => {
                [...document.querySelectorAll('button')].find((button) => button.textContent.includes(label))?.remove();
            });
        });
    </script>
    <script>document.addEventListener('DOMContentLoaded', () => { const w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT); let n; while (n = w.nextNode()) { if (!n.parentElement.closest('script,style')) n.nodeValue = n.nodeValue.replaceAll('$', '₱'); } });</script>
</body>
</html>



