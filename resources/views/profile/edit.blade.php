@extends('layouts.app')

@section('title', 'Pengaturan Profil Pengguna')

@section('content')
<div class="flex-1 flex flex-col space-y-4 min-h-0 overflow-y-auto pr-1 max-w-5xl mx-auto w-full">

    <!-- ============================================================== -->
    <!-- HEADER BAR: JUDUL & NAVIGASI KEMBALI                           -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/90 p-4 sm:p-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-sky-500/20 text-lg">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-slate-800 tracking-tight leading-snug">PENGATURAN PROFIL & AKUN</h1>
                <p class="text-xs text-slate-500 font-medium">Perbarui Informasi Akun, Foto Profil, dan Kata Sandi</p>
            </div>
        </div>

        <div>
            <a href="{{ route('dashboard') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition flex items-center space-x-1.5 shadow-xs">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- GRID FORM: BIODATA & UPLOAD AVATAR VS GANTI PASSWORD           -->
    <!-- ============================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        
        <!-- ========================================== -->
        <!-- KOLOM KIRI (7 KOLOM): BIODATA & FOTO AVATAR -->
        <!-- ========================================== -->
        <div class="lg:col-span-7 bg-white rounded-2xl shadow-xs border border-slate-200/90 p-5 flex flex-col">
            <div class="pb-3 mb-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                    <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Data Profil & Foto Avatar</h2>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-50 text-sky-700">Akun Terdaftar</span>
            </div>

            <!-- Notifikasi Sukses Update Profil -->
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-3.5 py-2.5 rounded-xl text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4 flex-1 flex flex-col justify-between">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Bagian Foto Profil & Pratinjau (Live Preview) -->
                    <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row items-center gap-4">
                        <div class="relative shrink-0">
                            <img id="avatar-preview" 
                                 src="{{ $user->avatar_url }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-20 h-20 rounded-full object-cover ring-4 ring-white shadow-md border border-slate-200">
                            <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white" title="Aktif"></span>
                        </div>

                        <div class="space-y-1.5 text-center sm:text-left flex-1">
                            <label class="block text-xs font-bold text-slate-700">Foto Profil (Avatar)</label>
                            <p class="text-[11px] text-slate-500">Unggah foto format JPG, PNG, atau WEBP (Maksimal 2MB).</p>
                            
                            <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                <label for="avatar-input" class="px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-bold cursor-pointer transition shadow-xs flex items-center space-x-1.5 active:scale-95">
                                    <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                                    <span>Pilih Foto Baru</span>
                                </label>
                                <input type="file" 
                                       name="avatar" 
                                       id="avatar-input" 
                                       accept="image/png,image/jpeg,image/webp" 
                                       onchange="previewAvatar(event)" 
                                       class="hidden">
                                
                                <span id="file-chosen-text" class="text-[11px] text-slate-500 italic">Belum ada foto baru dipilih</span>
                            </div>
                            @error('avatar')
                                <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Input Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required 
                                   placeholder="Nama lengkap Anda..." 
                                   class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                        </div>
                        @error('name')
                            <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required 
                                   placeholder="nama@ottopharm.com" 
                                   class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                        </div>
                        @error('email')
                            <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Jabatan / Role -->
                    <div>
                        <label for="role" class="block text-xs font-bold text-slate-700 mb-1">Jabatan / Bagian</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-id-badge"></i>
                            </span>
                            <input type="text" 
                                   name="role" 
                                   id="role" 
                                   value="{{ old('role', $user->role ?? 'Logistik & Gudang') }}" 
                                   placeholder="Contoh: Logistik & Gudang, Staff Operasional..." 
                                   class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                        </div>
                        @error('role')
                            <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-500 hover:to-blue-600 text-white rounded-xl font-bold text-xs shadow-md shadow-sky-500/20 transition flex items-center space-x-1.5 active:scale-95">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN (5 KOLOM): GANTI KATA SANDI    -->
        <!-- ========================================== -->
        <div class="lg:col-span-5 bg-white rounded-2xl shadow-xs border border-slate-200/90 p-5 flex flex-col">
            <div class="pb-3 mb-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Ganti Kata Sandi</h2>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">Keamanan</span>
            </div>

            <!-- Notifikasi Sukses Ganti Password -->
            @if(session('success_password'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-3.5 py-2.5 rounded-xl text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
                    <span class="font-semibold">{{ session('success_password') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4 flex-1 flex flex-col justify-between">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Password Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password" 
                                   required 
                                   placeholder="••••••••" 
                                   class="w-full pl-9 pr-9 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                            <button type="button" onclick="togglePasswordVisibility('current_password', 'eye-curr')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                <i id="eye-curr" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Baru</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-key"></i>
                            </span>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required 
                                   placeholder="Minimal 6 karakter..." 
                                   class="w-full pl-9 pr-9 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                            <button type="button" onclick="togglePasswordVisibility('password', 'eye-new')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                <i id="eye-new" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-check-double"></i>
                            </span>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   required 
                                   placeholder="Ulangi kata sandi baru..." 
                                   class="w-full pl-9 pr-9 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eye-conf')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                <i id="eye-conf" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl font-bold text-xs shadow-md shadow-amber-500/20 transition flex items-center space-x-1.5 active:scale-95">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                        <span>Perbarui Kata Sandi</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Preview gambar avatar saat dipilih pengguna
    function previewAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                if (preview) {
                    preview.src = e.target.result;
                }
                const label = document.getElementById('file-chosen-text');
                if (label) {
                    label.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                    label.className = 'text-[11px] text-emerald-600 font-bold';
                }
            };

            reader.readAsDataURL(file);
        }
    }

    // Toggle lihat kata sandi
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
