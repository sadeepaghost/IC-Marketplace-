<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SadeepaElectronics — IC Parts Marketplace' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        /* ── Navbar ── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            background: rgba(5, 10, 20, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        /* ── Hero ── */
        .hero {
            min-height: 100vh;
            background:
                linear-gradient(to bottom, rgba(5,10,20,0.75) 0%, rgba(5,10,20,0.55) 50%, rgba(5,10,20,0.92) 100%),
                url('https://images.unsplash.com/photo-1518770660439-4636190af475?w=1800&q=80') center/cover no-repeat;
            display: flex; flex-direction: column; justify-content: center;
        }

        /* ── Animated grid overlay ── */
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(0,180,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,180,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* ── Glowing accent ── */
        .glow-blue {
            position: absolute; width: 600px; height: 600px; border-radius: 50%;
            background: radial-gradient(circle, rgba(0,120,255,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── Stat cards ── */
        .stat-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            backdrop-filter: blur(8px);
            transition: border-color .3s, transform .3s;
        }
        .stat-card:hover { border-color: rgba(0,120,255,0.4); transform: translateY(-3px); }

        /* ── Section ── */
        .section-label {
            font-family: 'Syne', sans-serif;
            font-size: 11px; font-weight: 700;
            letter-spacing: .2em; text-transform: uppercase;
            color: #3b82f6;
        }

        /* ── Feature cards ── */
        .feature-card {
            background: #0d1526;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            transition: border-color .3s, box-shadow .3s;
        }
        .feature-card:hover {
            border-color: rgba(59,130,246,0.3);
            box-shadow: 0 0 30px rgba(59,130,246,0.08);
        }

        /* ── Scroll reveal ── */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        body { background: #060d1a; color: #e2e8f0; }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════════════ --}}
<nav class="navbar">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2.5 group">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
            </div>
            <span class="font-display font-800 text-white text-lg tracking-tight">
                Sadeepa<span class="text-blue-400">Electronics</span>
            </span>
        </a>

        {{-- Nav Links --}}
        <div class="hidden md:flex items-center gap-8">
            <a href="#search"   class="text-sm text-slate-400 hover:text-white transition-colors">Search Parts</a>
            <a href="#features" class="text-sm text-slate-400 hover:text-white transition-colors">Why Us</a>
            <a href="#about"    class="text-sm text-slate-400 hover:text-white transition-colors">About</a>
            <a href="#contact"  class="text-sm text-slate-400 hover:text-white transition-colors">Contact</a>
        </div>

        {{-- CTA --}}
        <a href="#search"
           class="hidden md:flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            Search Parts
        </a>
    </div>
</nav>

{{-- ══════════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════════ --}}
<section class="hero relative pt-16" id="search">
    <div class="glow-blue top-20 -left-40"></div>
    <div class="glow-blue bottom-0 right-0"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-24">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 bg-blue-950/60 border border-blue-800/50 rounded-full px-4 py-1.5 mb-6">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            <span class="text-xs font-semibold text-blue-300 tracking-wide">AI-Powered Procurement — 24hr Quotes</span>
        </div>

        {{-- Headline --}}
        <h1 class="font-display text-5xl md:text-7xl font-800 text-white leading-none tracking-tight mb-4 max-w-3xl">
            Find Any IC Part.<br>
            <span class="text-blue-400">Instantly.</span>
        </h1>
        <p class="text-lg text-slate-400 max-w-xl mb-10 leading-relaxed">
            Search 10,000+ electronic components from 500+ manufacturers.
            Can't find it? Our AI sources it within 24 hours.
        </p>

        {{-- Search Component --}}
        <div class="max-w-3xl">
            @livewire('ic-part-search')
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 max-w-3xl">
            @foreach([
                ['10,000+', 'Components'],
                ['500+',    'Manufacturers'],
                ['24hr',    'Sourcing Time'],
                ['99.2%',   'Fulfillment Rate'],
            ] as $stat)
            <div class="stat-card px-4 py-4 text-center">
                <p class="font-display text-2xl font-800 text-white">{{ $stat[0] }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $stat[1] }}</p>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     FEATURES / WHY US
══════════════════════════════════════════════════════════════ --}}
<section class="py-24 px-6" id="features">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-14 reveal">
            <p class="section-label mb-3">Why SadeepaElectronics</p>
            <h2 class="font-display text-4xl font-700 text-white">Built for Electronics Professionals</h2>
            <p class="text-slate-400 mt-3 max-w-lg mx-auto">From prototype to production — we source, verify, and deliver.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                [
                    'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347A3.999 3.999 0 0114 21H10a4 4 0 01-2.83-1.172l-.346-.347z',
                    'title' => 'AI-Powered Sourcing',
                    'desc'  => 'When a part isn\'t in stock, our AI instantly scans global supplier networks and suggests cross-references — all within seconds.',
                    'color' => 'blue',
                ],
                [
                    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    'title' => 'Verified Authentic Parts',
                    'desc'  => 'Every component is verified against manufacturer datasheets. We maintain full chain-of-custody documentation for traceability.',
                    'color' => 'green',
                ],
                [
                    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                    'title' => '24-Hour Quote Guarantee',
                    'desc'  => 'Submit a sourcing request and receive a competitive, itemized quote within 24 hours — or we expedite at no extra cost.',
                    'color' => 'amber',
                ],
            ] as $i => $f)
            <div class="feature-card p-7 reveal" style="transition-delay: {{ $i * 100 }}ms">
                <div class="w-11 h-11 rounded-xl bg-{{ $f['color'] }}-900/40 border border-{{ $f['color'] }}-800/40 flex items-center justify-center mb-5">
                    <svg class="w-5 h-5 text-{{ $f['color'] }}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-display font-700 text-white text-lg mb-2">{{ $f['title'] }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     ABOUT US
══════════════════════════════════════════════════════════════ --}}
<section class="py-24 px-6 border-t border-white/5" id="about">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 items-center">

        {{-- Text --}}
        <div class="reveal">
            <p class="section-label mb-4">About Us</p>
            <h2 class="font-display text-4xl font-700 text-white leading-tight mb-5">
                A Decade of Sourcing Hard-to-Find Components
            </h2>
            <p class="text-slate-400 leading-relaxed mb-4">
                SadeepaElectronics was founded by electronics engineers who were frustrated by long lead times,
                counterfeit parts, and opaque pricing. We built the marketplace we always wanted — one that
                combines real inventory, AI-assisted procurement, and transparent quotes.
            </p>
            <p class="text-slate-400 leading-relaxed mb-8">
                Today we serve OEMs, contract manufacturers, and hardware startups across our country,
                with a focus on obsolete, hard-to-find, and high-volume ICs.
            </p>

            <div class="flex flex-wrap gap-4">
                @foreach(['RoHS Certified', 'ISO 9001:2015', 'AS9120B Aerospace', 'REACH Compliant'] as $badge)
                <span class="text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-300 px-3 py-1.5 rounded-full">
                    ✓ {{ $badge }}
                </span>
                @endforeach
            </div>
        </div>

        {{-- Image grid --}}
        <div class="grid grid-cols-2 gap-3 reveal">
            <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=600&q=80"
                 class="rounded-xl object-cover h-48 w-full" alt="Electronics lab">
            <img src="https://images.unsplash.com/photo-1563770660941-20978e870e26?w=600&q=80"
                 class="rounded-xl object-cover h-48 w-full mt-8" alt="Circuit board">
            <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&q=80"
                 class="rounded-xl object-cover h-48 w-full -mt-4" alt="IC chips">
            <img src="https://images.unsplash.com/photo-1601524909162-ae8725290836?w=600&q=80"
                 class="rounded-xl object-cover h-48 w-full mt-4" alt="Warehouse">
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     CONTACT
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 px-6 border-t border-white/5" id="contact">
    <div class="max-w-3xl mx-auto text-center reveal">
        <p class="section-label mb-3">Contact</p>
        <h2 class="font-display text-4xl font-700 text-white mb-4">Get In Touch</h2>
        <p class="text-slate-400 mb-10">Have a large order, a custom requirement, or need a bulk quote? Reach out directly.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
            @foreach([
                ['📧', 'Email', 'sadeepaamaranayake@gmail.com'],
                ['📞', 'Phone', '+94754414937'],
                ['🕐', 'Hours', 'Mon–Fri, 8AM–6PM EST'],
            ] as $c)
            <div class="feature-card p-5 text-center">
                <div class="text-2xl mb-2">{{ $c[0] }}</div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1">{{ $c[1] }}</p>
                <p class="text-sm text-slate-300 font-medium">{{ $c[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════════ --}}
<footer class="border-t border-white/5 py-8 px-6">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="font-display font-700 text-white">Sadeepa<span class="text-blue-400">Electronics</span></p>
        <p class="text-xs text-slate-600">© {{ date('Y') }} SadeepaElectronics. All rights reserved.</p>
        <div class="flex gap-6">
            <a href="#" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">Privacy</a>
            <a href="#" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">Terms</a>
            <a href="#" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">Sitemap</a>
        </div>
    </div>
</footer>

@livewireScripts

<script>
// Scroll reveal
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

</body>
</html>
