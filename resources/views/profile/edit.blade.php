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

    <!-- ============================================================== -->
    <!-- SECTION 3: MANAJEMEN PENGGUNA SISTEM (KHUSUS ROLE SUPER USER)  -->
    <!-- ============================================================== -->
    @if(Auth::user()->isSuperUser())
    <div class="bg-white rounded-2xl shadow-xs border border-purple-200/90 p-5 flex flex-col space-y-4">
        
        <!-- Header Toolbar Manajemen User -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-700 text-white flex items-center justify-center shadow-md shadow-purple-500/20 text-base shrink-0">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">Manajemen Pengguna Sistem</h2>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-purple-100 text-purple-800 border border-purple-300 flex items-center space-x-1">
                            <i class="fa-solid fa-crown text-amber-500 text-[10px]"></i>
                            <span>Khusus Super User</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Kelola akun yang berhak login ke sistem, tentukan hak akses (role), dan reset kata sandi</p>
                </div>
            </div>

            <!-- Tombol Tambah Pengguna Baru -->
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold text-slate-500 hidden md:inline">
                    Total: <strong class="text-slate-800">{{ $allUsers->count() }}</strong> Akun
                </span>
                <button type="button" 
                        onclick="openUserModal('create')" 
                        class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white rounded-xl font-bold text-xs shadow-md shadow-purple-500/20 transition flex items-center space-x-1.5 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>Tambah User Baru</span>
                </button>
            </div>
        </div>

        <!-- Notifikasi Sukses / Error Manajemen User -->
        @if(session('success_user'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3.5 py-2.5 rounded-xl text-xs flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
                    <span class="font-semibold">{{ session('success_user') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        @endif

        @if(session('error_user'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-3.5 py-2.5 rounded-xl text-xs flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-xmark text-rose-500 text-sm shrink-0"></i>
                    <span class="font-semibold">{{ session('error_user') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        @endif

        <!-- Tabel Daftar Pengguna -->
        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-slate-900 text-white font-bold text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-12 border-r border-slate-800">No</th>
                            <th class="py-2.5 px-3 border-r border-slate-800">Nama Pengguna</th>
                            <th class="py-2.5 px-3 border-r border-slate-800">Email Login</th>
                            <th class="py-2.5 px-3 border-r border-slate-800">Role / Hak Akses</th>
                            <th class="py-2.5 px-3 border-r border-slate-800 text-center w-32">Terdaftar</th>
                            <th class="py-2.5 px-3 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-800 font-medium">
                        @forelse($allUsers as $index => $u)
                        <tr class="hover:bg-purple-50/40 transition-colors {{ $u->id === Auth::id() ? 'bg-amber-50/30' : '' }}">
                            <!-- Nomor -->
                            <td class="py-2.5 px-3 text-center font-mono text-slate-500 font-bold">
                                {{ $index + 1 }}
                            </td>

                            <!-- Nama & Avatar -->
                            <td class="py-2.5 px-3">
                                <div class="flex items-center space-x-2.5">
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-200 shrink-0">
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center space-x-1.5">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === Auth::id())
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">Akun Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-2.5 px-3 font-mono text-slate-700">
                                {{ $u->email }}
                            </td>

                            <!-- Role -->
                            <td class="py-2.5 px-3">
                                @if($u->isSuperUser())
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-800 border border-purple-300">
                                        <i class="fa-solid fa-crown text-amber-500 text-[10px]"></i>
                                        <span>{{ $u->role }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                        <i class="fa-solid fa-user-tag text-sky-500 text-[10px]"></i>
                                        <span>{{ $u->role ?? 'Admin Logistik' }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Tanggal Terdaftar -->
                            <td class="py-2.5 px-3 text-center text-slate-500 font-mono text-[11px]">
                                {{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <!-- Aksi (Edit & Hapus) -->
                            <td class="py-2.5 px-3 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- Tombol Edit User -->
                                    <button type="button" 
                                            onclick='openUserModal("edit", @json($u))'
                                            title="Edit Pengguna Ini"
                                            class="w-7 h-7 rounded-lg bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center text-xs transition shadow-xs cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Tombol Hapus User -->
                                    @if($u->id !== Auth::id())
                                    <form method="POST" action="{{ route('users.destroy', $u->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna &quot;{{ addslashes($u->name) }}&quot;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Pengguna Ini"
                                                class="w-7 h-7 rounded-lg bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition shadow-xs cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button" 
                                            disabled 
                                            title="Tidak dapat menghapus akun sendiri yang sedang aktif digunakan"
                                            class="w-7 h-7 rounded-lg bg-slate-200 text-slate-400 flex items-center justify-center text-xs cursor-not-allowed">
                                        <i class="fa-solid fa-lock"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-slate-400">
                                Belum ada data pengguna lainnya yang terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- MODAL TERPADU: TAMBAH & EDIT PENGGUNA (USER MODAL)             -->
    <!-- ============================================================== -->
    <div id="um_modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 animate-in fade-in duration-150">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden animate-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-slate-900 via-purple-950 to-slate-900 text-white px-5 py-3.5 flex items-center justify-between border-b border-purple-800/40">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-300 border border-purple-400/30 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-user-gear" id="um_modal_icon"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-white" id="um_modal_title">Tambah Pengguna Baru</h3>
                        <p class="text-[11px] text-purple-200/80" id="um_modal_subtitle">Lengkapi formulir akun pengguna di bawah ini</p>
                    </div>
                </div>
                <button type="button" onclick="closeUserModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form id="um_form" method="POST" action="{{ route('users.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <input type="hidden" name="_method" id="um_form_method" value="POST">

                <!-- Nama Lengkap -->
                <div>
                    <label for="um_name" class="block font-bold text-slate-700 mb-1">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" 
                               name="name" 
                               id="um_name" 
                               required 
                               placeholder="Nama lengkap staf / operator..." 
                               class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <!-- Alamat Email Login -->
                <div>
                    <label for="um_email" class="block font-bold text-slate-700 mb-1">
                        Alamat Email Login <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" 
                               name="email" 
                               id="um_email" 
                               required 
                               placeholder="staf@ottopharm.com" 
                               class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <!-- Role / Hak Akses -->
                <div>
                    <label for="um_role" class="block font-bold text-slate-700 mb-1">
                        Role / Hak Akses <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <select name="role" id="um_role" required class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="Super User">Super User (Akses Penuh & Manajemen Akun)</option>
                            <option value="Admin Logistik" selected>Admin Logistik (Entri SPPB, Collie & Laporan)</option>
                            <option value="Staff Logistik & Gudang">Staff Logistik & Gudang (Operasional)</option>
                        </select>
                    </div>
                </div>

                <!-- Password Block -->
                <div class="space-y-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Kata Sandi Akun</span>
                        <span id="um_password_hint" class="text-[10px] text-slate-400 italic"></span>
                    </div>

                    <!-- Input Kata Sandi -->
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" 
                                   name="password" 
                                   id="um_password" 
                                   placeholder="Minimal 6 karakter..." 
                                   class="w-full pl-9 pr-9 py-2 border border-slate-300 rounded-xl font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <button type="button" onclick="togglePasswordVisibility('um_password', 'um_eye_pwd')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                <i id="um_eye_pwd" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Kata Sandi -->
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-check-double"></i>
                            </span>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="um_password_confirmation" 
                                   placeholder="Ulangi kata sandi..." 
                                   class="w-full pl-9 pr-9 py-2 border border-slate-300 rounded-xl font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <button type="button" onclick="togglePasswordVisibility('um_password_confirmation', 'um_eye_conf')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                <i id="um_eye_conf" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="pt-2 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" 
                            onclick="closeUserModal()" 
                            class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            id="um_submit_btn"
                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold rounded-xl transition shadow-md shadow-purple-600/30 flex items-center space-x-1.5 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span id="um_submit_text">Simpan Pengguna</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
    @endif

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

    // ==========================================
    // LOGIKA MODAL MANAJEMEN PENGGUNA (SUPER USER)
    // ==========================================
    const umModal = document.getElementById('um_modal');
    const umForm = document.getElementById('um_form');
    const umFormMethod = document.getElementById('um_form_method');
    const umModalTitle = document.getElementById('um_modal_title');
    const umModalSubtitle = document.getElementById('um_modal_subtitle');
    const umSubmitText = document.getElementById('um_submit_text');
    const umPassword = document.getElementById('um_password');
    const umPasswordConf = document.getElementById('um_password_confirmation');
    const umPasswordHint = document.getElementById('um_password_hint');

    function openUserModal(mode, userData = null) {
        if (!umModal) return;
        umForm.reset();

        if (mode === 'create') {
            umForm.action = "{{ route('users.store') }}";
            umFormMethod.value = "POST";
            umModalTitle.textContent = "Tambah Pengguna Baru";
            umModalSubtitle.textContent = "Lengkapi formulir untuk membuat akun login baru";
            umSubmitText.textContent = "Simpan Pengguna";
            
            umPassword.required = true;
            umPasswordConf.required = true;
            umPasswordHint.textContent = "* Wajib diisi (Minimal 6 karakter)";
            umPasswordHint.className = "text-[10px] text-rose-500 font-semibold";
        } else if (mode === 'edit' && userData) {
            umForm.action = `{{ url('/users') }}/${userData.id}`;
            umFormMethod.value = "PUT";
            umModalTitle.textContent = `Edit Pengguna: ${userData.name}`;
            umModalSubtitle.textContent = "Ubah profil atau kosongkan kata sandi jika tidak ingin diganti";
            umSubmitText.textContent = "Perbarui Pengguna";

            document.getElementById('um_name').value = userData.name || '';
            document.getElementById('um_email').value = userData.email || '';
            document.getElementById('um_role').value = userData.role || 'Admin Logistik';

            umPassword.required = false;
            umPasswordConf.required = false;
            umPasswordHint.textContent = "(Kosongkan jika tidak ingin mengubah kata sandi)";
            umPasswordHint.className = "text-[10px] text-slate-400 italic";
        }

        umModal.classList.remove('hidden');
        setTimeout(() => document.getElementById('um_name').focus(), 50);
    }

    function closeUserModal() {
        if (umModal) {
            umModal.classList.add('hidden');
        }
    }

    // Tutup jika tombol ESC ditekan
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && umModal && !umModal.classList.contains('hidden')) {
            closeUserModal();
        }
    });

    // Tutup jika klik area latar belakang
    if (umModal) {
        umModal.addEventListener('click', function(e) {
            if (e.target === umModal) {
                closeUserModal();
            }
        });
    }
</script>
@endpush
