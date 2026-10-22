@extends('layouts.app')

@section('title', 'Master Data Distributor & Cabang')

@section('content')
<div class="flex-1 flex flex-col space-y-2.5 min-h-0">

    <!-- ============================================================== -->
    <!-- NOTIFIKASI SUKSES / ERROR                                      -->
    <!-- ============================================================== -->
    @if(session('success'))
    <div id="flash-alert" class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-xl flex items-center justify-between text-emerald-800 text-xs shadow-xs animate-in fade-in duration-200">
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="document.getElementById('flash-alert').remove()" class="text-emerald-700 hover:text-emerald-900 transition">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div id="error-alert" class="p-3 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-800 text-xs shadow-xs">
        <div class="flex items-center space-x-2 mb-1">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
            <span class="font-bold">Terjadi kesalahan validasi:</span>
        </div>
        <ul class="list-disc list-inside pl-5 space-y-0.5 text-rose-700 font-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- ============================================================== -->
    <!-- CARD TOOLBAR: HEADER, PENCARIAN & TOMBOL AKSI                 -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200/90 p-3 shrink-0">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            
            <!-- Identitas & Jumlah Data -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-blue-700 text-white flex items-center justify-center shadow-md shadow-sky-500/20 text-lg shrink-0">
                    <i class="fa-solid fa-address-book"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-base font-extrabold text-slate-900 tracking-tight">Master Data Distributor</h1>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800 border border-sky-300">
                            {{ number_format($totalCount) }} Data Terdaftar
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Pengelolaan data alamat cabang, customer tujuan, telepon, dan search key untuk SPPB</p>
                </div>
            </div>

            <!-- Toolbar Sisi Kanan: Form Search & Tombol Tambah -->
            <div class="flex items-center space-x-2">
                <!-- Form Pencarian Cepat -->
                <form method="GET" action="{{ route('distributor.index') }}" class="flex items-center space-x-1.5">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari nama, kota, key..." 
                               class="pl-8 pr-7 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-sky-400 focus:outline-none w-48 sm:w-60 shadow-inner">
                        @if(!empty($search))
                            <a href="{{ route('distributor.index') }}" 
                               title="Reset Pencarian"
                               class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-600 transition">
                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-bold transition shadow-xs">
                        Cari
                    </button>
                </form>

                <!-- Tombol Tambah Distributor (Buka Modal Create) -->
                <button type="button" 
                        onclick="openCreateModal()" 
                        class="px-3.5 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-lg text-xs font-bold transition shadow-md shadow-emerald-600/20 flex items-center space-x-1.5 cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Distributor</span>
                </button>

                <!-- Tombol Shortcut Kembali ke SPPB -->
                <a href="{{ route('sppb.index') }}" 
                   title="Buka Form SPPB" 
                   class="px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-300 rounded-lg text-xs font-bold transition flex items-center space-x-1">
                    <i class="fa-solid fa-file-invoice text-xs"></i>
                    <span class="hidden sm:inline">Form SPPB</span>
                </a>
            </div>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- TABEL DAFTAR DISTRIBUTOR                                       -->
    <!-- ============================================================== -->
    <div class="flex-1 bg-white rounded-xl shadow-xs border border-slate-200/90 flex flex-col min-h-0 overflow-hidden">
        
        <div class="flex-1 overflow-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-gradient-to-r from-slate-900 to-slate-800 text-white font-bold sticky top-0 z-10 uppercase tracking-wider text-[11px] shadow-xs">
                    <tr>
                        <th class="py-2.5 px-3 text-center w-12 border-r border-slate-700">No</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 w-28">Search Key</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 min-w-[220px]">Nama Distributor</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 w-36">Kota / Cabang</th>
                        <th class="py-2.5 px-3 border-r border-slate-700">Alamat Lengkap</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 w-32">Telepon</th>
                        <th class="py-2.5 px-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-800 font-medium">
                    @forelse($distributors as $index => $dist)
                        @php
                            $alamatParts = array_filter([
                                trim($dist->alamat1 ?? ''),
                                trim($dist->alamat2 ?? ''),
                                trim($dist->alamat3 ?? ''),
                            ]);
                            $fullAlamat = implode(', ', $alamatParts);
                        @endphp
                        <tr class="hover:bg-sky-50/70 transition-colors group">
                            <!-- Nomor -->
                            <td class="py-2 px-3 text-center font-mono text-slate-500 font-bold bg-slate-50/50 group-hover:bg-transparent">
                                {{ ($distributors->currentPage() - 1) * $distributors->perPage() + $index + 1 }}
                            </td>

                            <!-- Search Key -->
                            <td class="py-2 px-3">
                                @php $displayKey = $dist->search_key ?: $dist->searchkey; @endphp
                                @if(!empty($displayKey))
                                    <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-[11px] bg-slate-100 text-slate-800 border border-slate-300">
                                        {{ $displayKey }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Nama Distributor -->
                            <td class="py-2 px-3 font-bold text-slate-900">
                                {{ $dist->nama }}
                            </td>

                            <!-- Kota / Cabang -->
                            <td class="py-2 px-3">
                                @if(!empty($dist->kota))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold text-[10px] bg-sky-100 text-sky-800 border border-sky-300 uppercase">
                                        {{ $dist->kota }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Alamat Lengkap -->
                            <td class="py-2 px-3 text-slate-600 text-[11px] leading-relaxed">
                                {{ !empty($fullAlamat) ? $fullAlamat : '-' }}
                            </td>

                            <!-- Telepon -->
                            <td class="py-2 px-3 font-mono text-slate-700 text-[11px]">
                                {{ $dist->telp ?? '-' }}
                            </td>

                            <!-- Aksi (Edit & Hapus) -->
                            <td class="py-2 px-3 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- Tombol Edit -->
                                    <button type="button" 
                                            onclick='openEditModal(@json($dist))' 
                                            title="Edit Data Distributor"
                                            class="w-7 h-7 rounded-lg bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center text-xs transition shadow-xs cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form method="POST" action="{{ route('distributor.destroy', $dist->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus distributor &quot;{{ addslashes($dist->nama) }}&quot;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Data Distributor"
                                                class="w-7 h-7 rounded-lg bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition shadow-xs cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center text-slate-400">
                                <i class="fa-solid fa-address-book text-4xl mb-3 block text-slate-300"></i>
                                <span class="font-bold text-slate-600 text-sm">Tidak ada data distributor yang ditemukan.</span>
                                @if(!empty($search))
                                    <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian yang lain atau bersihkan filter.</p>
                                    <a href="{{ route('distributor.index') }}" class="inline-block mt-3 px-3 py-1 bg-sky-600 text-white text-xs font-bold rounded-lg hover:bg-sky-500 transition">
                                        Tampilkan Semua Data
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <div class="bg-slate-50 border-t border-slate-200 px-3 py-2 flex items-center justify-between text-xs shrink-0">
            <span class="text-slate-500 font-medium">
                Menampilkan <strong>{{ $distributors->firstItem() ?? 0 }}</strong> s/d <strong>{{ $distributors->lastItem() ?? 0 }}</strong> dari total <strong>{{ $distributors->total() }}</strong> Distributor
            </span>
            @if($distributors->hasPages())
                <div>
                    {{ $distributors->links('pagination::simple-tailwind') }}
                </div>
            @endif
        </div>

    </div>

</div>

<!-- ============================================================== -->
<!-- MODAL TERPADU: TAMBAH & EDIT DISTRIBUTOR                       -->
<!-- ============================================================== -->
<div id="distributorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 animate-in fade-in duration-150">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden animate-in zoom-in-95 duration-150">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 via-sky-950 to-slate-900 text-white px-5 py-3.5 flex items-center justify-between border-b border-sky-800/40">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-300 border border-sky-400/30 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-address-book" id="modalIcon"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-white" id="modalTitle">Tambah Distributor Baru</h3>
                    <p class="text-[11px] text-sky-200/80">Lengkapi formulir master distributor di bawah ini</p>
                </div>
            </div>
            <button type="button" onclick="closeDistributorModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form id="distributorForm" method="POST" action="{{ route('distributor.store') }}" class="p-5 space-y-3.5 text-xs">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <!-- Baris 1: Nama Distributor -->
            <div>
                <label for="input_nama" class="block font-bold text-slate-700 mb-1">
                    Nama Distributor / Customer <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="nama" 
                       id="input_nama" 
                       required 
                       placeholder="Contoh: PT. Mensa Bina Sukses - JAKARTA 1" 
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 font-semibold text-slate-900 focus:ring-2 focus:ring-sky-400 focus:outline-none">
            </div>

            <!-- Baris 2: Kota / Cabang & Search Key -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="input_kota" class="block font-bold text-slate-700 mb-1">
                        Kota / Cabang
                    </label>
                    <input type="text" 
                           name="kota" 
                           id="input_kota" 
                           placeholder="Contoh: JAKARTA -1, SURABAYA" 
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 font-semibold text-slate-900 focus:ring-2 focus:ring-sky-400 focus:outline-none uppercase">
                </div>
                <div>
                    <label for="input_search_key" class="block font-bold text-slate-700 mb-1">
                        Search Key / Kode Singkat
                    </label>
                    <input type="text" 
                           name="search_key" 
                           id="input_search_key" 
                           placeholder="Contoh: JKT01, SBY, BDG" 
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 font-mono font-bold text-slate-900 focus:ring-2 focus:ring-sky-400 focus:outline-none uppercase">
                </div>
            </div>

            <!-- Baris 3: Telepon -->
            <div>
                <label for="input_telp" class="block font-bold text-slate-700 mb-1">
                    Nomor Telepon
                </label>
                <input type="text" 
                       name="telp" 
                       id="input_telp" 
                       placeholder="Contoh: 021-4615906" 
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 font-semibold text-slate-900 focus:ring-2 focus:ring-sky-400 focus:outline-none">
            </div>

            <!-- Baris 4: Alamat 1, 2, 3 -->
            <div class="space-y-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                <span class="block font-bold text-slate-700 text-[11px] uppercase tracking-wider">Detail Alamat Lengkap</span>
                
                <div>
                    <input type="text" 
                           name="alamat1" 
                           id="input_alamat1" 
                           placeholder="Alamat Baris 1: Jalan / No. Gedung / Kawasan Industri..." 
                           class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 font-normal text-slate-800 bg-white focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>
                <div>
                    <input type="text" 
                           name="alamat2" 
                           id="input_alamat2" 
                           placeholder="Alamat Baris 2: RT/RW / Kelurahan / Kecamatan..." 
                           class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 font-normal text-slate-800 bg-white focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>
                <div>
                    <input type="text" 
                           name="alamat3" 
                           id="input_alamat3" 
                           placeholder="Alamat Baris 3: Kota Administrasi / Kode Pos..." 
                           class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 font-normal text-slate-800 bg-white focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>
            </div>

            <!-- Modal Footer / Tombol Aksi -->
            <div class="pt-2 flex items-center justify-end space-x-2 border-t border-slate-100">
                <button type="button" 
                        onclick="closeDistributorModal()" 
                        class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        id="modalSubmitBtn"
                        class="px-4 py-2 bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-500 hover:to-blue-500 text-white font-bold rounded-xl transition shadow-md shadow-sky-600/30 flex items-center space-x-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span id="modalSubmitText">Simpan Data</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('distributorModal');
    const form = document.getElementById('distributorForm');
    const formMethod = document.getElementById('formMethod');
    const modalTitle = document.getElementById('modalTitle');
    const modalSubmitText = document.getElementById('modalSubmitText');

    function openCreateModal() {
        form.reset();
        form.action = "{{ route('distributor.store') }}";
        formMethod.value = "POST";
        modalTitle.textContent = "Tambah Distributor Baru";
        modalSubmitText.textContent = "Simpan Distributor";
        
        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('input_nama').focus(), 50);
    }

    function openEditModal(dist) {
        if (!dist) return;
        form.reset();
        form.action = `{{ url('/distributor') }}/${dist.id}`;
        formMethod.value = "PUT";
        modalTitle.textContent = `Edit Distributor: ${dist.nama}`;
        modalSubmitText.textContent = "Perbarui Data";

        document.getElementById('input_nama').value = dist.nama || '';
        document.getElementById('input_kota').value = dist.kota || '';
        document.getElementById('input_search_key').value = dist.search_key || dist.searchkey || '';
        document.getElementById('input_telp').value = dist.telp || '';
        document.getElementById('input_alamat1').value = dist.alamat1 || '';
        document.getElementById('input_alamat2').value = dist.alamat2 || '';
        document.getElementById('input_alamat3').value = dist.alamat3 || '';

        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('input_nama').focus(), 50);
    }

    function closeDistributorModal() {
        modal.classList.add('hidden');
    }

    // Tutup modal jika tombol ESC ditekan
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeDistributorModal();
        }
    });

    // Tutup jika klik di luar area modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeDistributorModal();
        }
    });
</script>
@endpush
