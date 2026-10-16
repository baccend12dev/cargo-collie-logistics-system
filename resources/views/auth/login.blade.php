<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Collie & SPPB | PT OTTO Pharmaceutical Industries</title>
    
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
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-radial from-slate-900 via-slate-950 to-black text-slate-100 selection:bg-sky-500 selection:text-white relative overflow-hidden">

    <!-- Background Ambient Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-sky-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        
        <!-- Header Branding -->
        <div class="text-center mb-6">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-500 to-blue-600 items-center justify-center shadow-lg shadow-sky-500/25 ring-4 ring-sky-400/20 mb-3 text-white text-2xl">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <h1 class="text-lg font-black tracking-tight text-white uppercase">PT. OTTO PHARMACEUTICAL INDUSTRIES</h1>
            <p class="text-xs text-sky-400 font-semibold tracking-wider uppercase mt-0.5">Sistem Manajemen Collie & SPPB Logistik</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-black/60 relative">
            
            <div class="mb-5 pb-3 border-b border-slate-800/80">
                <h2 class="text-base font-bold text-white">Masuk ke Akun Anda</h2>
                <p class="text-xs text-slate-400 mt-0.5">Masukkan email dan kata sandi untuk mengakses sistem.</p>
            </div>

            <!-- Notifikasi Error -->
            @if ($errors->any())
                <div class="mb-4 bg-rose-500/15 border border-rose-500/30 text-rose-300 px-3.5 py-2.5 rounded-xl text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Notifikasi Sukses / Logout -->
            @if (session('success'))
                <div class="mb-4 bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 px-3.5 py-2.5 rounded-xl text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-xs">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', 'admin@ottopharm.com') }}" 
                               required 
                               autofocus
                               placeholder="nama@ottopharm.com" 
                               class="w-full pl-9 pr-3.5 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-xs">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               value="admin123"
                               required 
                               placeholder="••••••••" 
                               class="w-full pl-9 pr-10 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 text-xs">
                            <i id="eye-icon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-sky-600 focus:ring-sky-500">
                        <span class="text-xs text-slate-400">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full mt-2 py-2.5 px-4 bg-gradient-to-r from-sky-500 via-blue-600 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-500/25 transition active:scale-[0.98] flex items-center justify-center space-x-2">
                    <span>Masuk ke Sistem</span>
                    <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                </button>
            </form>

            <!-- Info Demo Credentials -->
            <div class="mt-5 p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-[11px] text-slate-400">
                <div class="flex items-center space-x-1.5 text-sky-400 font-bold mb-1">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Akun Default Logistik:</span>
                </div>
                <div class="flex justify-between font-mono text-[10px] text-slate-300">
                    <span>Email: <strong>admin@ottopharm.com</strong></span>
                    <span>Pass: <strong>admin123</strong></span>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-[11px] text-slate-500">
            &copy; 2026 PT. OTTO Pharmaceutical Industries Ltd &bull; Divisi Logistik
        </div>

    </div>

    <script>
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
