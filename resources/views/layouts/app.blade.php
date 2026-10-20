<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Collie & SPPB') - PT OTTO Pharmaceutical Industries</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS (Vite / Fallback CDN) -->
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script>
            // Suppress Tailwind Play CDN production warning in console
            (function() {
                const _w = console.warn;
                console.warn = function(...args) {
                    if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com')) return;
                    _w.apply(console, args);
                };
            })();
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                            mono: ['"JetBrains Mono"', 'monospace'],
                        },
                        colors: {
                            otto: {
                                50: '#f0f7ff',
                                100: '#e0effe',
                                500: '#0284c7',
                                600: '#0369a1',
                                700: '#075985',
                                800: '#0c4a6e',
                                900: '#082f49',
                            }
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .table-condensed th {
            padding-top: 0.35rem;
            padding-bottom: 0.35rem;
        }
        .table-condensed td {
            padding-top: 0.2rem;
            padding-bottom: 0.2rem;
        }
        /* Custom scrollbar for dense data table */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col text-slate-800 antialiased selection:bg-sky-500 selection:text-white">

    <!-- ============================================================== -->
    <!-- TOP BAR / HEADER UTAMA (FULL-WIDTH COMPACT ENTERPRISE NAVBAR)  -->
    <!-- ============================================================== -->
    <header class="bg-gradient-to-r from-slate-900 via-sky-950 to-slate-900 text-white shadow-lg sticky top-0 z-40 border-b border-sky-800/40">
        <div class="w-full px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                
                <!-- Sisi Kiri: Identitas PT OTTO & Sistem -->
                <div class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center shadow-md shadow-sky-500/20 text-white font-black text-lg ring-2 ring-sky-300/30">
                        <i class="fa-solid fa-boxes-packing text-sky-100"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-extrabold tracking-tight text-white text-base">PT. OTTO PHARMACEUTICAL INDUSTRIES</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide uppercase bg-sky-500/20 text-sky-300 border border-sky-400/30">Bandung</span>
                        </div>
                        <p class="text-xs text-sky-200/80 font-medium">Sistem Manajemen Collie & SPPB (Packing List Logistik)</p>
                    </div>
                </div>

                <!-- Bagian Tengah: Mode Switcher (DASHBOARD - COLLIE - SPPB - REPORT) -->
                <nav class="hidden md:flex items-center space-x-1.5 bg-slate-950/50 p-1.5 rounded-xl border border-sky-800/50 shadow-inner">
                    <!-- 1. DASHBOARD (Kiri) -->
                    <a href="{{ route('dashboard') }}" 
                       class="px-4 py-2 rounded-lg font-bold text-xs flex items-center space-x-2 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-md shadow-sky-500/30 ring-1 ring-sky-300/40' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        <i class="fa-solid fa-chart-pie text-sm"></i>
                        <span class="tracking-wider uppercase">DASHBOARD</span>
                    </a>

                    <!-- 2. COLLIE (Tengah-Kiri) -->
                    <a href="{{ route('collie.index') }}" 
                       class="px-4 py-2 rounded-lg font-bold text-xs flex items-center space-x-2 transition-all duration-200 {{ request()->routeIs('collie.*') ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-md shadow-sky-500/30 ring-1 ring-sky-300/40' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        <i class="fa-solid fa-box-open text-sm"></i>
                        <span class="tracking-wider uppercase">COLLIE</span>
                    </a>
                    
                    <!-- 3. SPPB (Tengah-Kanan) -->
                    <a href="{{ route('sppb.index') }}" 
                       class="px-4 py-2 rounded-lg font-bold text-xs flex items-center space-x-2 transition-all duration-200 {{ request()->routeIs('sppb.*') ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-md shadow-sky-500/30 ring-1 ring-sky-300/40' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        <i class="fa-solid fa-file-invoice text-sm"></i>
                        <span class="tracking-wider uppercase">SPPB</span>
                    </a>

                    <!-- 4. REPORT (Kanan) -->
                    <a href="{{ route('report.index') }}" 
                       class="px-4 py-2 rounded-lg font-bold text-xs flex items-center space-x-2 transition-all duration-200 {{ request()->routeIs('report.*') ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-md shadow-sky-500/30 ring-1 ring-sky-300/40' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        <i class="fa-solid fa-chart-column text-sm"></i>
                        <span class="tracking-wider uppercase">REPORT</span>
                    </a>

                    <!-- 5. DISTRIBUTOR (Master Data) -->
                    <a href="{{ route('distributor.index') }}" 
                       class="px-4 py-2 rounded-lg font-bold text-xs flex items-center space-x-2 transition-all duration-200 {{ request()->routeIs('distributor.*') ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-md shadow-sky-500/30 ring-1 ring-sky-300/40' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        <i class="fa-solid fa-address-book text-sm"></i>
                        <span class="tracking-wider uppercase">DISTRIBUTOR</span>
                    </a>
                </nav>

                <!-- Sisi Kanan: Filter Periode Bulan & Akun -->
                <div class="flex items-center space-x-4">
                    <!-- Profil Pengguna / Foto User & Logout Dropdown -->
                    @auth
                    <div class="relative" id="user-menu-container">
                        <button type="button" 
                                onclick="toggleUserDropdown()" 
                                id="user-menu-button"
                                class="flex items-center space-x-2.5 p-1 sm:px-2.5 sm:py-1 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/80 text-white transition focus:outline-none focus:ring-2 focus:ring-sky-400 group cursor-pointer">
                            
                            <!-- Foto / Avatar User -->
                            <div class="relative shrink-0">
                                <img src="{{ Auth::user()->avatar_url }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="w-8 h-8 rounded-full object-cover ring-2 ring-sky-400/50 shadow-sm">
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-slate-900"></span>
                            </div>

                            <!-- Nama & Role User -->
                            <div class="hidden sm:flex flex-col text-left leading-tight">
                                <span class="text-xs font-bold text-slate-100 group-hover:text-sky-300 transition truncate max-w-[130px]">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-slate-400 font-medium truncate max-w-[130px]">{{ Auth::user()->role ?? 'Logistik & Gudang' }}</span>
                            </div>

                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-hover:text-slate-200 transition"></i>
                        </button>

                        <!-- Dropdown Menu Logout -->
                        <div id="user-dropdown-menu" 
                             class="hidden absolute right-0 mt-2 w-56 rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl shadow-black/80 p-2 z-50 text-xs animate-in fade-in duration-150">
                            <div class="px-3 py-2.5 border-b border-slate-800/90 mb-1">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Login Sebagai:</p>
                                <p class="font-bold text-slate-100 truncate mt-0.5">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-sky-400 font-mono truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="space-y-1 mb-1">
                                <a href="{{ route('profile.edit') }}" 
                                   class="w-full flex items-center space-x-2.5 px-3 py-2 rounded-xl text-slate-200 hover:text-white hover:bg-slate-800 transition text-left font-semibold">
                                    <i class="fa-solid fa-user-gear text-sky-400"></i>
                                    <span>Pengaturan Profil</span>
                                </a>
                            </div>

                            <div class="h-px bg-slate-800/80 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')"
                                        class="w-full flex items-center space-x-2.5 px-3 py-2 rounded-xl text-rose-300 hover:text-white hover:bg-rose-600/80 transition text-left font-semibold cursor-pointer">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-rose-400"></i>
                                    <span>Keluar (Logout)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold text-xs shadow-sm flex items-center space-x-1.5 transition">
                        <i class="fa-solid fa-right-to-bracket text-xs"></i>
                        <span>Login</span>
                    </a>
                    @endauth
                </div>

            </div>
        </div>
    </header>

    <!-- ============================================================== -->
    <!-- CONTENT SLOT UTAMA (FULL-WIDTH WORKSPACE)                      -->
    <!-- ============================================================== -->
    <main class="flex-1 w-full p-2.5 sm:p-3 flex flex-col overflow-hidden">
        @yield('content')
    </main>

    <!-- Footer Status Bar -->
    <footer class="bg-white border-t border-slate-200 text-slate-500 px-4 py-2 text-xs flex flex-wrap items-center justify-between shadow-sm">
        <div class="flex items-center space-x-4">
            <span class="flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-medium text-slate-700">Status Server: Online</span>
            </span>
            <span class="text-slate-300">|</span>
            <span>Dokumen Standar: <strong class="text-slate-700">FM.LG.C-P.001.001.01</strong></span>
            <span class="text-slate-300 hidden md:inline">|</span>
            <span class="hidden md:inline">Platform: <strong>Laravel 12 / PHP 8.2</strong></span>
        </div>
        <div class="text-slate-400 font-medium">
            &copy; 2026 PT. OTTO Pharmaceutical Industries Ltd &bull; Divisi Logistik & Gudang
        </div>
    </footer>

    <script>
        function toggleUserDropdown() {
            const menu = document.getElementById('user-dropdown-menu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('user-menu-container');
            const menu = document.getElementById('user-dropdown-menu');
            if (container && menu && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
