<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chick N Lick - Franchisee Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { ink: '#1e1b18', paper: '#fff8f4', cream: '#faf2ed', red: '#b00d00', yellow: '#ffd91f', muted: '#5d3f3a' },
            fontFamily: { display: ['Space Grotesk', 'sans-serif'], body: ['Plus Jakarta Sans', 'sans-serif'] }
        } } };
    </script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>.material-symbols-outlined{font-family:'Material Symbols Outlined';font-weight:normal;font-style:normal;font-size:20px;line-height:1;display:inline-block;white-space:nowrap;-webkit-font-feature-settings:'liga';-webkit-font-smoothing:antialiased}</style>
</head>
<body class="min-h-screen bg-paper font-body text-ink antialiased">
    <div class="mx-auto flex min-h-screen w-full max-w-[480px] flex-col bg-paper shadow-2xl">
        <header class="relative overflow-hidden bg-ink text-white">
            <div class="absolute inset-0 scale-105 bg-cover bg-center opacity-40 mix-blend-luminosity" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuBmbFw8egGA_7sPxjGuVIGe0UOSaJ4vN_bl_9TbGcee9m82K4Je19sDKq9LSTvoJsZhLuMK-t_zHU_zFwShSVNjbywgQYIk25yMRz5yiw1hzbb5-xKXcAvav3YSDACALG786WljWjc5Mp1pP73r5X8JNbb3smC8wVqaXKQ9MTmSImeMtmdDzDXtM56uuH2FA-BmzLsR-HmMjb_pAi3mEHwUGanQX7Pv9yefA2JExuozWmjPKgO5squ5KFwDhrFdk1OuG8W5uhaMTbGyEg');"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-ink/90 via-ink/85 to-ink"></div>
            <div class="relative z-10 flex flex-col items-center px-5 pb-7 pt-8 text-center">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-yellow bg-yellow px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-[#101010]"><span class="h-2 w-2 animate-pulse rounded-full bg-[#101010]"></span>Commissary Network Online</div>
                <img alt="Chick N Lick Supply Portal Logo" class="mb-2 h-28 w-auto object-contain drop-shadow-xl" src="https://lh3.googleusercontent.com/aida-public/AB6AXuABnqNpLLnCAPhThio_e5yK6cS02h7qUeZ-IWl4JsLqLgvPy5EDU9SwkYFDx_DuY-G2ayd8NRIe8XhYecbU-OPsa_gkruXvWC9-HDRMwghiLUEUDaVS2JMAigew_fRYbEmt8DDhu814Y9mHdUuQrP1fwnfHZKGj_dJQ6Ssh0HTGGwsSnM-we0LAp0oY882O8cN7zWo34U3NxDdqX3qwcU_3alOZnyOMiIc9Mpf-ttg75yjJ7RPWzLukgIDoHQgKN06-mprCgFblc4Q2hg">
                <p class="mt-1 text-xs font-medium tracking-wide text-white/80">Official Franchisee Commissary &amp; Inventory Portal</p>
                <div class="mt-4 flex w-full items-center justify-center gap-3 border-t border-white/10 pt-3 text-xs font-medium text-white/90"><span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-yellow">verified</span>HQ Verified</span><span class="text-white/30">•</span><span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-yellow">bolt</span>Rush Hours Par-Stock</span><span class="text-white/30">•</span><span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-yellow">lock</span>Tier-1 Secure</span></div>
            </div>
        </header>

        <main class="relative z-20 -mt-3 flex-1 space-y-4 px-4">
            <section class="relative overflow-hidden rounded-xl border border-[#ebe5d8] bg-white p-5 shadow-sm">
                <div class="relative z-10 mb-4 flex items-center gap-2 border-b border-cream pb-3"><div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cream text-red"><span class="material-symbols-outlined">storefront</span></div><div><h1 class="font-display text-lg font-bold tracking-tight">Franchisee Sign-In</h1><p class="text-xs text-muted">Store Operations Credentials</p></div></div>
                <form action="{{ route('login') }}" method="POST" class="relative z-10 space-y-4" id="loginForm">
                    @csrf
                    <div><label for="email" class="mb-1.5 block text-xs font-bold">Franchisee Email Address</label><div class="relative"><span class="material-symbols-outlined pointer-events-none absolute inset-y-0 left-3 flex items-center text-muted">alternate_email</span><input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="store-id@chicknlickstores.com" class="w-full rounded-lg border border-[#d5cbb9] bg-paper py-2.5 pl-10 pr-3 text-sm font-medium outline-none transition focus:border-red focus:ring-1 focus:ring-red"></div>@error('email')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror</div>
                    <div><div class="mb-1.5 flex items-center justify-between gap-2"><label for="password" class="text-xs font-bold">Password</label><a href="#" class="text-[11px] font-bold text-[#a83900] hover:underline">Forgot password?</a></div><div class="relative"><span class="material-symbols-outlined pointer-events-none absolute inset-y-0 left-3 flex items-center text-muted">lock</span><input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••••••" class="w-full rounded-lg border border-[#d5cbb9] bg-paper py-2.5 pl-10 pr-10 text-sm font-semibold tracking-widest outline-none transition focus:border-red focus:ring-1 focus:ring-red"><button type="button" id="togglePassword" class="absolute inset-y-0 right-3 text-muted" title="Toggle password visibility" aria-label="Toggle password visibility"><span class="material-symbols-outlined">visibility</span></button></div>@error('password')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror</div>
                    <label class="flex cursor-pointer select-none items-center gap-2 pt-1 text-xs font-medium"><input name="remember" type="checkbox" class="h-4 w-4 rounded border-[#d5cbb9] accent-red focus:ring-red">Remember this store device</label>
                    <button type="submit" id="loginButton" class="flex h-12 w-full items-center justify-center gap-2 rounded-lg border border-[#e8c400] bg-yellow font-bold tracking-wide text-[#101010] shadow-md transition hover:bg-[#e8c400] active:scale-[.98]"><span class="material-symbols-outlined">login</span><span>Sign In to Portal</span></button>
                </form>
                <p class="relative z-10 mt-4 text-center text-xs text-muted">New franchisee? <a href="{{ route('register') }}" class="font-bold text-[#a83900] hover:underline">Create an account</a></p>
            </section>
        </main>

        <footer class="mt-8 space-y-2 border-t border-[#e9e1dc] px-5 pb-4 pt-4 text-center"><p class="text-xs text-muted">24/7 Commissary Logistics Hotline</p><p class="flex items-center justify-center gap-1.5 text-xs text-muted"><span class="inline-flex items-center gap-1 rounded-full bg-yellow px-2 py-0.5 text-[11px] font-bold text-[#101010]"><span class="h-1.5 w-1.5 rounded-full bg-[#101010]"></span>Operational</span>System Status: <strong class="text-ink">All Hubs On-Time</strong></p><p class="pt-2 text-[10px] text-[#926f69]">Chick N Lick Franchise Operations LLC © 2024 • Version 4.12.0</p></footer>
    </div>
    <script src="{{ asset('js/app.js') }}"></script><script>document.getElementById('loginForm')?.addEventListener('submit',function(){const button=document.getElementById('loginButton');if(button){button.disabled=true;button.classList.add('opacity-70');button.querySelector('span:last-child').textContent='Signing in…';}});</script>
</body>
</html>

