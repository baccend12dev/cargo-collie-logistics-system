@extends('layouts.app')

@section('title', 'Pencetakkan Surat Pengantar Pengiriman Barang (SPPB)')

@section('content')
<div class="flex-1 flex flex-col space-y-3 min-h-0">

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2.5 rounded-xl text-xs flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- CARD TOOLBAR & FILTER MONITORING PL & SPPB                     -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 p-2.5 px-3">
        <div class="flex flex-wrap items-center justify-between gap-2.5 text-xs">
            
            <!-- Sisi Kiri: Judul Modul -->
            <div class="flex items-center space-x-2.5">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold shrink-0">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </span>
                <div>
                    <h1 class="text-sm font-bold text-slate-800 leading-tight">PENCETAKKAN SURAT PENGANTAR PENGIRIMAN BARANG (SPPB)</h1>
                    <span class="text-[11px] text-slate-500 font-medium">Pengelolaan Surat Jalan Pengiriman Berdasarkan Packing List & Surat Muat</span>
                </div>
            </div>

            <!-- Sisi Kanan: Filter Status, Cabang, Cari Cepat & Refresh -->
            <form method="GET" action="{{ route('sppb.index') }}" class="flex flex-wrap items-center gap-2">
                
                <!-- Filter Status SPPB -->
                <div class="flex items-center space-x-1.5">
                    <label class="text-slate-600 font-medium">Status :</label>
                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 border border-slate-300 rounded-lg bg-slate-50 focus:bg-white text-xs font-medium">
                        <option value="">Semua Status</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>⏳ Belum Terbit SPPB</option>
                        <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>✅ Sudah Terbit SPPB</option>
                    </select>
                </div>

                <!-- Filter Cabang -->
                <div class="flex items-center space-x-1.5">
                    <label class="text-slate-600 font-medium">Cabang :</label>
                    <select name="cabang" onchange="this.form.submit()" class="px-2 py-1 border border-slate-300 rounded-lg bg-slate-50 focus:bg-white text-xs font-medium">
                        <option value="">Semua Cabang</option>
                        <option value="JKT0" {{ request('cabang') == 'JKT0' ? 'selected' : '' }}>JKT0 - Jakarta</option>
                        <option value="SBY0" {{ request('cabang') == 'SBY0' ? 'selected' : '' }}>SBY0 - Surabaya</option>
                    </select>
                </div>

                <!-- Cari Cepat -->
                <div class="flex items-center space-x-1">
                    <label class="text-slate-600 font-medium">Cari :</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. PL / SPPB / Ekspedisi..." class="px-2 py-1 border border-slate-300 rounded-lg w-44 bg-slate-50 focus:bg-white text-xs">
                    <button type="submit" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold transition" title="Cari">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                    </button>
                </div>

                <!-- Refresh -->
                <a href="{{ route('sppb.index') }}" title="Refresh Halaman" class="p-1 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold flex items-center transition border border-slate-200">
                    <i class="fa-solid fa-rotate text-slate-500"></i>
                </a>

            </form>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- TABEL MONITORING PACKING LIST & SPPB                           -->
    <!-- ============================================================== -->
    <div class="flex-1 bg-white rounded-xl shadow-sm border border-slate-200/90 flex flex-col min-h-0 overflow-hidden">
        <div class="flex-1 overflow-auto">
            <table class="w-full text-left border-collapse table-condensed text-[12px]">
                <thead class="sticky top-0 bg-slate-800 text-white z-10 shadow-sm text-[11px] uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-2.5 px-3 border-r border-slate-700 w-10 text-center">#</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 font-bold text-sky-300">NO_PL</th>
                        <th class="py-2.5 px-3 border-r border-slate-700">TGL_PL</th>
                        <th class="py-2.5 px-3 border-r border-slate-700">CABANG</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-center font-bold text-amber-300">STATUS SPPB</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 font-bold text-emerald-300">KEPADA (CUSTOMER)</th>
                        <th class="py-2.5 px-3 border-r border-slate-700">EKSPEDISI</th>
                        <th class="py-2.5 px-3 border-r border-slate-700">SUPIR / NO. POL</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-center">TOTAL SM</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-right">JUMLAH KOLI</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-right font-bold text-amber-300">BERAT (KG)</th>
                        <th class="py-2.5 px-3 text-center w-36">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium font-mono text-slate-700">
                    @forelse($plList as $idx => $pl)
                    <tr class="hover:bg-sky-50/70 transition-colors cursor-pointer group">
                        <td class="px-3 py-1.5 text-center text-slate-400 font-sans">
                            {{ $plList->firstItem() + $idx }}
                        </td>
                        <td class="px-3 py-1.5 font-bold text-sky-700 text-sm">
                            <button type="button" 
                                    onclick="openCollieDetailModal('{{ $pl->no_pl }}', '{{ $pl->tgl_pl ? \Carbon\Carbon::parse($pl->tgl_pl)->format('Y-m-d') : '' }}', '{{ $pl->tgl_pl ? \Carbon\Carbon::parse($pl->tgl_pl)->format('d/m/Y') : '-' }}')"
                                    class="text-sky-700 hover:text-sky-900 hover:underline inline-flex items-center gap-1.5 font-mono group/btn text-left"
                                    title="Klik untuk melihat rincian collie {{ $pl->no_pl }} ({{ $pl->tgl_pl ? \Carbon\Carbon::parse($pl->tgl_pl)->format('d/m/Y') : '' }})">
                                <span>{{ $pl->no_pl }}</span>
                                <i class="fa-solid fa-circle-info text-[11px] text-sky-400 group-hover/btn:text-sky-600 opacity-70 group-hover/btn:opacity-100 transition"></i>
                            </button>
                        </td>
                        <td class="px-3 py-1.5 font-sans">{{ $pl->tgl_pl ? \Carbon\Carbon::parse($pl->tgl_pl)->format('d/m/Y') : '-' }}</td>
                        <td class="px-3 py-1.5 font-sans font-bold">{{ $pl->cab ?: '' }}</td>
                        
                        <!-- Status SPPB Badge -->
                        <td class="px-3 py-1.5 text-center font-sans">
                            @if($pl->no_sppb)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-xs">
                                    <i class="fa-solid fa-circle-check text-emerald-600 mr-1.5"></i>
                                    {{ $pl->no_sppb }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300 shadow-xs">
                                    <i class="fa-solid fa-clock text-amber-600 mr-1.5"></i>
                                    Belum Terbit SPPB
                                </span>
                            @endif
                        </td>

                        <td class="px-3 py-1.5 font-sans font-semibold text-slate-900 truncate max-w-xs" title="{{ $pl->customer_name }}">
                            {{ $pl->customer_name ?: '-' }}
                        </td>
                        <td class="px-3 py-1.5 font-sans text-slate-700">{{ $pl->ekspedisi ?: '-' }}</td>
                        <td class="px-3 py-1.5 font-sans text-slate-600 text-[11px]">{{ $pl->supirnopol ?: '-' }}</td>
                        <td class="px-3 py-1.5 text-center font-sans font-bold text-sky-700">{{ $pl->total_sm }} SM</td>
                        <td class="px-3 py-1.5 text-right font-bold text-slate-900">{{ number_format($pl->jumlah_collie) }}</td>
                        <td class="px-3 py-1.5 text-right font-bold text-amber-700">{{ number_format($pl->berat_total, 1) }}</td>
                        
                        <!-- Tombol Aksi -->
                        <td class="px-3 py-1.5 text-center font-sans space-x-1">
                            <!-- Buka Dialog SPPB (Mirip VB6) -->
                            <button type="button" 
                                    onclick="openSppbModal('{{ $pl->no_pl }}', '{{ $pl->tgl_pl ? \Carbon\Carbon::parse($pl->tgl_pl)->format('Y-m-d') : '' }}')"
                                    class="px-2.5 py-1 bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-500 hover:to-blue-600 text-white rounded-lg text-xs font-bold shadow-xs transition active:scale-95"
                                    title="Buka Form SPPB untuk {{ $pl->no_pl }} ({{ $pl->tgl_pl ? \Carbon\Carbon::parse($pl->tgl_pl)->format('d/m/Y') : '' }})">
                                <i class="fa-solid fa-file-pen text-[10px] mr-1"></i>
                                <span>SPPB</span>
                            </button>

                            <!-- Tombol Cetak Dokumen -->
                            @if($pl->no_sppb)
                                <a href="{{ route('sppb.print', ['no_pl' => $pl->no_pl, 'tgl_col' => $pl->tgl_pl ? \Carbon\Carbon::parse($pl->tgl_pl)->format('Y-m-d') : '']) }}" target="_blank"
                                   class="px-2 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold shadow-xs inline-flex items-center"
                                   title="Cetak Dokumen SPPB">
                                    <i class="fa-solid fa-print text-[10px]"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-4 py-10 text-center text-slate-400 font-sans">
                            <i class="fa-solid fa-box-archive text-3xl mb-2 block text-slate-300"></i>
                            Tidak ada data Packing List yang sesuai dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Action Bar Bawah & Ringkasan Statistik Status (Sesuai Layout Collie) -->
        <div class="bg-slate-50 border-t border-slate-200 px-3 py-2 flex flex-wrap items-center justify-between gap-2">
            
            <div class="flex items-center space-x-3 text-xs text-slate-500">
                <span>Total PL: <strong class="text-slate-800 font-bold font-mono">{{ number_format($totalPl) }}</strong></span>
                <span class="text-slate-300">|</span>
                <span>Sudah SPPB: <strong class="text-emerald-700 font-bold font-mono">{{ number_format($plSudahSppb) }}</strong></span>
                <span class="text-slate-300">|</span>
                <span>Belum SPPB: <strong class="text-amber-700 font-bold font-mono">{{ number_format($plBelumSppb) }}</strong></span>
            </div>

            <!-- Pagination Links -->
            <div class="flex items-center space-x-2 text-xs">
                @if($plList->hasPages())
                    <span class="text-slate-500 font-medium">Halaman {{ $plList->currentPage() }} dari {{ $plList->lastPage() }}</span>
                    {{ $plList->links('pagination::simple-tailwind') }}
                @else
                    <span class="text-slate-500 font-medium">Menampilkan {{ $plList->count() }} dari {{ $totalPl }} data</span>
                @endif
            </div>

        </div>
    </div>

</div>

<!-- ============================================================== -->
<!-- MODAL FORM UTAMA: PENCETAKKAN SURAT PENGANTAR PENGIRIMAN BARANG -->
<!-- (DESAIN IDENTIK DENGAN VB6 PADA input sppb.png & input sppb new.png) -->
<!-- ============================================================== -->
<div id="modal-sppb-vb6" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
    
    <!-- Window Frame Bergaya Enterprise Blue VB6 Modern -->
    <div class="bg-gradient-to-br from-sky-800 via-sky-700 to-blue-900 text-white rounded-2xl shadow-2xl border-2 border-sky-400/50 w-full max-w-5xl overflow-hidden animate-in fade-in duration-200 flex flex-col my-auto">
        
        <form action="{{ route('sppb.store') }}" method="POST" id="form-sppb-vb6">
            @csrf
            <input type="hidden" name="no_pl" id="vb6_no_pl_hidden">
            <input type="hidden" name="tgl_col" id="vb6_tgl_col_hidden">

            <!-- WINDOW TITLE BAR (Persis Judul VB6) -->
            <div class="px-5 py-3 bg-gradient-to-r from-sky-950 via-slate-900 to-sky-950 border-b border-sky-500/40 flex items-center justify-between select-none">
                <div class="flex items-center space-x-2.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-rose-500 inline-block ring-2 ring-rose-400/30"></span>
                    <h2 class="font-extrabold text-sm sm:text-base tracking-wide text-white">
                        Pencetakkan Surat Pengantar Pengiriman Barang (<span id="vb6_title_no_pl" class="text-sky-300"></span> - <span id="vb6_display_tgl_col" class="text-amber-300"></span>)
                    </h2>
                </div>
                <div class="flex items-center space-x-2">
                    <span id="badge-status-sppb" class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-sky-500/30 border border-sky-400/40 text-sky-200">
                        MEMUAT...
                    </span>
                    <button type="button" onclick="closeSppbModal()" class="text-slate-300 hover:text-white text-lg font-bold p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- FORM BODY (Grid Layout Identik Gambar VB6) -->
            <div class="p-5 sm:p-6 space-y-4">
                
                <!-- ROW 1: HEADER INFORMASI UTAMA -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start text-xs font-semibold">
                    
                    <!-- Kolom Kiri: No, Ekspedisi, Kepada -->
                    <div class="md:col-span-5 space-y-3">
                        <!-- No. SPPB -->
                        <div class="flex items-center space-x-2">
                            <label class="w-20 text-sky-100">No</label>
                            <input type="text" name="no_sppb" id="vb6_no_sppb" required class="flex-1 px-3 py-1.5 rounded-lg bg-white text-slate-900 font-bold font-mono focus:ring-2 focus:ring-sky-300 focus:outline-none">
                        </div>

                        <!-- Ekspedisi -->
                        <div class="flex items-center space-x-2">
                            <label class="w-20 text-sky-100">Ekspedisi</label>
                            <div class="flex-1">
                                <input type="text" list="ekspedisi-options" name="ekspedisi" id="vb6_ekspedisi" placeholder="Pilih / ketik ekspedisi..." class="w-full px-3 py-1.5 rounded-lg bg-white text-slate-900 font-semibold focus:ring-2 focus:ring-sky-300">
                                <datalist id="ekspedisi-options">
                                    @foreach($ekspedisiList as $eks)
                                        <option value="{{ $eks }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <!-- Kepada (Distributor / Customer) via Dropdown -->
                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center space-x-2">
                                <label for="vb6_select_distributor" class="w-20 text-sky-100 flex items-center space-x-1">
                                    <span>Kepada</span>
                                    <span class="text-amber-400 font-bold">*</span>
                                </label>
                                <div class="flex-1 flex items-center space-x-1.5">
                                    <select id="vb6_select_distributor" onchange="onSelectDistributor(this)" class="flex-1 px-2.5 py-1.5 rounded-lg bg-white text-slate-900 font-semibold text-xs border border-slate-300 focus:ring-2 focus:ring-sky-300 shadow-xs">
                                        <option value="">-- Pilih Distributor --</option>
                                        @foreach($distributorList as $dist)
                                            @php
                                                $alamatParts = array_filter([
                                                    trim($dist->alamat1 ?? ''),
                                                    trim($dist->alamat2 ?? ''),
                                                    trim($dist->alamat3 ?? ''),
                                                ]);
                                                $fullAlamat = implode(' ', $alamatParts);
                                            @endphp
                                            <option value="{{ $dist->id }}" 
                                                data-nama="{{ $dist->nama }}" 
                                                data-alamat="{{ $fullAlamat }}" 
                                                data-kota="{{ $dist->kota }}">
                                                {{ $dist->nama }}{{ !empty($dist->kota) ? ' (' . $dist->kota . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('distributor.index') }}" target="_blank" title="Buka Master Distributor (Tambah / Edit Data)" class="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-500 text-white rounded-lg text-xs font-bold transition shadow-xs flex items-center space-x-1 shrink-0">
                                        <i class="fa-solid fa-address-book"></i>
                                        <span class="hidden sm:inline">Master</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Area Tampilan / Input Customer (Otomatis Terisi & Bisa Diedit) -->
                            <div class="bg-sky-950/60 p-2.5 rounded-xl border border-sky-400/30 space-y-1.5">
                                <input type="text" name="customer_name" id="vb6_customer_name" placeholder="Nama Customer / PT (Otomatis dari Distributor)..." class="w-full px-2 py-1 rounded bg-white text-slate-900 font-bold text-xs focus:ring-1 focus:ring-sky-400">
                                <textarea name="customer_address" id="vb6_customer_address" rows="2" placeholder="Alamat Customer (Otomatis dari Distributor)..." class="w-full px-2 py-1 rounded bg-white text-slate-800 font-normal text-xs leading-relaxed focus:ring-1 focus:ring-sky-400"></textarea>
                            </div>
                        </div>

                    </div>

                    <!-- Kolom Tengah: Tanggal & Cabang -->
                    <div class="md:col-span-3 space-y-3">
                        <!-- Tanggal SPPB -->
                        <div class="flex items-center space-x-2">
                            <label class="w-16 text-sky-100">Tanggal</label>
                            <input type="date" name="tgl_sppb" id="vb6_tgl_sppb" required class="flex-1 px-2.5 py-1.5 rounded-lg bg-white text-slate-900 font-semibold focus:ring-2 focus:ring-sky-300">
                        </div>

                        <!-- Cabang -->
                        <div class="flex items-center space-x-2 pt-1">
                            <label class="w-16 text-sky-100">Cabang :</label>
                            <input type="text" name="cabang" id="vb6_cabang" class="flex-1 px-2.5 py-1.5 rounded-lg bg-sky-950 text-white font-bold uppercase border border-sky-500/40">
                        </div>
                    </div>

                    <!-- Kolom Kanan: Supir / No. Pol & Catatan -->
                    <div class="md:col-span-4 space-y-3">
                        <!-- Supir / No. Pol -->
                        <div class="flex items-center space-x-2">
                            <label class="w-24 text-sky-100">Supir / No. Pol :</label>
                            <input type="text" name="supirnopol" id="vb6_supirnopol" placeholder="Contoh: NANANG/B 9266 PCM/7779" class="flex-1 px-2.5 py-1.5 rounded-lg bg-white text-slate-900 font-semibold focus:ring-2 focus:ring-sky-300">
                        </div>

                        <!-- Catatan -->
                        <div class="space-y-1">
                            <label class="block text-sky-100">Catatan :</label>
                            <textarea name="cat" id="vb6_cat" rows="4" placeholder="Keterangan / instruksi khusus..." class="w-full p-2 rounded-lg bg-white text-slate-900 text-xs font-normal leading-relaxed focus:ring-2 focus:ring-sky-300"></textarea>
                        </div>
                    </div>

                </div>

                <!-- ROW 2: SUMMARY STRIP (No. Packing List, Jumlah Collie, Berat Kg) -->
                <div class="bg-sky-950/70 p-3 rounded-xl border border-sky-400/40 flex flex-wrap items-center justify-between text-xs font-bold gap-3 shadow-inner">
                    <div class="flex items-center space-x-2">
                        <span class="text-sky-300">No. Packing List :</span>
                        <span id="vb6_display_no_pl" class="text-white text-sm font-mono tracking-wider bg-sky-900/80 px-2.5 py-0.5 rounded border border-sky-500/30">-</span>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-2">
                            <span class="text-sky-300">Jumlah Collie :</span>
                            <span id="vb6_display_jumlah" class="text-amber-300 text-sm font-mono">-</span>
                            <input type="hidden" name="jumlah" id="vb6_input_jumlah">
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="text-sky-300">Berat (Kg) :</span>
                            <span id="vb6_display_berat" class="text-amber-300 text-sm font-mono">-</span>
                            <input type="hidden" name="berat" id="vb6_input_berat">
                        </div>
                    </div>
                </div>

                <!-- ROW 3: TABEL GRID SURAT MUAT (Kiri Bawah) & ILUSTRASI FORKLIFT (Kanan Bawah) -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    
                    <!-- Tabel List NO_SM (Kolom 8) -->
                    <div class="md:col-span-8 bg-slate-900/80 rounded-xl border border-sky-500/40 overflow-hidden shadow-md">
                        <div class="px-3 py-1.5 bg-sky-950 border-b border-sky-600/40 flex justify-between items-center text-[11px] font-bold text-sky-200">
                            <span>DAFTAR SURAT MUAT (NO_SM) PADA PACKING LIST INI:</span>
                            <span class="text-amber-300 font-mono" id="vb6_sm_count">0 SM</span>
                        </div>

                        <div class="max-h-44 overflow-y-auto">
                            <table class="w-full text-left text-xs font-mono">
                                <thead class="bg-sky-950/90 text-sky-200 uppercase text-[10px] sticky top-0 border-b border-sky-700/50">
                                    <tr>
                                        <th class="py-1.5 px-3 w-8 text-center">
                                            <input type="checkbox" id="check-all-sm" checked onchange="toggleSelectAllSm(this.checked)" class="rounded text-sky-600">
                                        </th>
                                        <th class="py-1.5 px-3">NO_SM</th>
                                        <th class="py-1.5 px-3 text-center">TGL_SM</th>
                                        <th class="py-1.5 px-3 text-right">KOLI</th>
                                        <th class="py-1.5 px-3 text-right">BERAT (KG)</th>
                                        <th class="py-1.5 px-2 w-10 text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="vb6_sm_tbody" class="divide-y divide-sky-900/60 text-sky-100">
                                    <!-- Diisi via AJAX getPlDetail -->
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-slate-400 font-sans">Memuat data surat muat...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Ilustrasi Forklift / Logistik (Kolom 4) -->
                    <div class="md:col-span-4 flex flex-col items-center justify-center p-3 text-center">
                        <div class="w-24 h-24 rounded-2xl bg-sky-900/40 border border-sky-400/30 flex items-center justify-center shadow-lg text-amber-400 mb-1">
                            <i class="fa-solid fa-dolly text-5xl"></i>
                        </div>
                        <span class="text-[11px] font-semibold text-sky-300">Logistik & Distribusi PT. OTTO</span>
                        <span class="text-[10px] text-sky-400">Siap Diberangkatkan</span>
                    </div>

                </div>

            </div>

            <!-- MODAL ACTION FOOTER (Pilih SM, Cancel, Save, Print) -->
            <div class="px-5 py-3.5 bg-sky-950/90 border-t border-sky-500/40 flex flex-wrap items-center justify-between gap-2">
                
                <!-- Tombol Pilih SM di Kiri Bawah (Buka Dialog Picker dari Oracle) -->
                <button type="button" 
                        onclick="openSmOraclePicker()"
                        class="px-4 py-2 bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-500 hover:to-blue-600 text-white rounded-xl border border-sky-400/50 text-xs font-bold transition flex items-center space-x-1.5 shadow-md active:scale-95">
                    <i class="fa-solid fa-file-circle-check text-amber-300 text-xs"></i>
                    <span>Pilih SM</span>
                </button>

                <!-- Tombol Aksi di Kanan Bawah -->
                <div class="flex items-center space-x-2.5">
                    <!-- Cancel -->
                    <button type="button" 
                            onclick="closeSppbModal()"
                            class="px-5 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-xl text-xs font-bold transition shadow-sm">
                        Cancel
                    </button>

                    <!-- Save -->
                    <button type="submit" 
                            class="px-5 py-2 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Save</span>
                    </button>

                    <!-- Print -->
                    <button type="button" 
                            id="btn-print-from-modal"
                            onclick="printCurrentPl()"
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-print text-xs"></i>
                        <span>Print</span>
                    </button>
                </div>

            </div>

        </form>

    </div>
</div>

<!-- ============================================================== -->
<!-- SUB-MODAL: PILIH SURAT MUAT (NO_SM) DARI ORACLE VIEW           -->
<!-- ============================================================== -->
<div id="modal-picker-sm-oracle" style="z-index: 70;" class="fixed inset-0 z-[70] hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
    <div class="bg-gradient-to-b from-sky-900 via-slate-900 to-sky-950 text-white rounded-2xl shadow-2xl border-2 border-sky-400/70 w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150 flex flex-col my-auto max-h-[90vh]">
        
        <!-- Header Dialog -->
        <div class="px-4 py-3 bg-sky-950 border-b border-sky-600/50 flex items-center justify-between select-none">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block animate-ping"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                <h3 class="font-extrabold text-xs sm:text-sm tracking-wide text-white">
                    PILIH SURAT MUAT (NO_SM) DARI ORACLE
                </h3>
            </div>
            <button type="button" onclick="closeSmOraclePicker()" class="text-slate-400 hover:text-white text-lg font-bold p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Toolbar Pencarian & Filter Cepat -->
        <div class="p-3 bg-sky-950/60 border-b border-sky-700/40 flex items-center gap-2">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-sky-400 text-xs"></i>
                <input type="text" 
                       id="sm_oracle_search_input"
                       oninput="onSmOracleSearchInput(this.value)"
                       placeholder="Cari No. SM (misal: 2601085) atau Customer..." 
                       class="w-full pl-8 pr-3 py-1.5 rounded-lg bg-white text-slate-900 text-xs font-semibold placeholder:text-slate-400 focus:ring-2 focus:ring-sky-400 outline-none">
            </div>
            <button type="button" 
                    onclick="loadSmOracleList('', true)" 
                    class="px-3 py-1.5 bg-sky-800 hover:bg-sky-700 text-sky-200 hover:text-white rounded-lg text-xs font-bold border border-sky-500/40 transition flex items-center space-x-1"
                    title="Refresh data langsung dari Oracle (Abaikan Cache)">
                <i class="fa-solid fa-arrows-rotate text-xs"></i>
                <span>Refresh</span>
            </button>
        </div>

        <!-- Tabel List SM dari Oracle -->
        <div class="flex-1 overflow-y-auto max-h-[50vh] p-2 bg-slate-950/50">
            <table class="w-full text-left text-xs font-mono">
                <thead class="sticky top-0 bg-sky-950 text-sky-200 uppercase text-[10px] border-b border-sky-600/40 select-none">
                    <tr>
                        <th class="py-2 px-3 w-8 text-center">
                            <input type="checkbox" id="check-all-picker-sm" onchange="toggleSelectAllPickerSm(this.checked)" class="rounded text-sky-600">
                        </th>
                        <th class="py-2 px-3 font-bold text-amber-300">NO_SM</th>
                        <th class="py-2 px-3 text-center">TGL_SM</th>
                        <th class="py-2 px-3">CUSTOMER</th>
                        <th class="py-2 px-3 text-right">TOTAL QTY</th>
                    </tr>
                </thead>
                <tbody id="sm_oracle_tbody" class="divide-y divide-sky-900/60 text-sky-100">
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400 font-sans italic">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data Surat Muat dari Oracle...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Action Picker -->
        <div class="px-4 py-3 bg-sky-950 border-t border-sky-600/50 flex items-center justify-between gap-2">
            <span class="text-[11px] text-sky-300 font-sans">
                Terpilih: <strong id="picker_sm_selected_count" class="text-amber-300 font-mono">0</strong> SM
            </span>
            <div class="flex items-center space-x-2">
                <button type="button" 
                        onclick="closeSmOraclePicker()"
                        class="px-4 py-1.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-xs font-bold transition">
                    Batal
                </button>
                <button type="button" 
                        onclick="applySelectedSmOracle()"
                        class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition flex items-center space-x-1.5 shadow-sm active:scale-95">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambahkan SM Terpilih</span>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: DETAIL RINCIAN COLLIE (DARI COLLIE_SPPBS SESUAI TANGGAL) -->
<!-- ============================================================== -->
<div id="modal-collie-detail" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-5xl overflow-hidden animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 via-sky-950 to-slate-900 text-white px-5 py-3.5 flex items-center justify-between border-b border-sky-800/40">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-400">
                    <i class="fa-solid fa-boxes-stacked text-base"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-base text-white tracking-wide">Rincian Informasi Collie</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-sky-500/20 text-sky-300 border border-sky-400/30" id="modal_collie_badge_pl">-</span>
                    </div>
                    <div class="text-xs text-slate-300 flex items-center gap-2 mt-0.5">
                        <span>Tanggal: <strong class="text-amber-300 font-mono" id="modal_collie_badge_tgl">-</strong></span>
                        <span>&bull;</span>
                        <span>Tabel Sumber: <code class="text-sky-300 font-mono text-[11px]">collie_sppbs</code></span>
                    </div>
                </div>
            </div>

            <button type="button" 
                    onclick="closeCollieDetailModal()"
                    class="text-slate-400 hover:text-white hover:bg-white/10 w-8 h-8 rounded-lg flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Body: Tabel Data Collie -->
        <div class="p-4 overflow-y-auto flex-1">
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800 text-white font-semibold font-mono text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-12 border-r border-slate-700">No.</th>
                            <th class="py-2.5 px-3 border-r border-slate-700">No. SM</th>
                            <th class="py-2.5 px-3 border-r border-slate-700 text-center font-bold text-sky-300">No. Col</th>
                            <th class="py-2.5 px-3 border-r border-slate-700 text-center">AD - JUCOL</th>
                            <th class="py-2.5 px-3 border-r border-slate-700">Kode</th>
                            <th class="py-2.5 px-3 border-r border-slate-700">Nama Produk / Barang</th>
                            <th class="py-2.5 px-3 border-r border-slate-700 text-right">Qty</th>
                            <th class="py-2.5 px-3 border-r border-slate-700 text-right">Qty1</th>
                            <th class="py-2.5 px-3 border-r border-slate-700 text-right">Qty2</th>
                            <th class="py-2.5 px-3 text-right font-bold text-amber-300">QTYR (Kg)</th>
                        </tr>
                    </thead>
                    <tbody id="modal_collie_tbody" class="divide-y divide-slate-200 font-mono font-medium text-slate-700">
                        <!-- Dimuat via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-slate-50 border-t border-slate-200 px-5 py-3 flex items-center justify-between text-xs">
            <div class="flex items-center gap-4 text-slate-600 font-medium">
                <span>Total Baris: <strong class="text-slate-900 font-mono" id="modal_collie_total_rows">0</strong></span>
                <span class="text-slate-300">|</span>
                <span>Total Koli: <strong class="text-sky-700 font-mono font-bold" id="modal_collie_total_koli">0</strong></span>
                <span class="text-slate-300">|</span>
                <span>Total Berat: <strong class="text-amber-700 font-mono font-bold" id="modal_collie_total_berat">0 Kg</strong></span>
            </div>
            <div class="flex items-center gap-2">
                <a id="modal_collie_link_edit" href="#" target="_blank" class="px-3 py-1.5 bg-sky-50 text-sky-700 border border-sky-300 hover:bg-sky-100 rounded-lg font-bold flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    <span>Buka di Menu Collie</span>
                </a>
                <button type="button" 
                        onclick="closeCollieDetailModal()"
                        class="px-4 py-1.5 bg-slate-700 hover:bg-slate-800 text-white rounded-lg font-bold transition">
                    Tutup
                </button>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentSelectedNoPl = '';
    let currentSelectedTglCol = '';

    // Buka Modal SPPB untuk No. PL & Tanggal tertentu
    function openSppbModal(noPl, tglCol = '') {
        currentSelectedNoPl = noPl;
        currentSelectedTglCol = tglCol;
        document.getElementById('vb6_no_pl_hidden').value = noPl;
        document.getElementById('vb6_tgl_col_hidden').value = tglCol;
        if (document.getElementById('vb6_title_no_pl')) {
            document.getElementById('vb6_title_no_pl').textContent = noPl;
        }
        if (document.getElementById('vb6_display_no_pl')) {
            document.getElementById('vb6_display_no_pl').textContent = noPl;
        }
        document.getElementById('vb6_display_tgl_col').textContent = tglCol ? formatDateIndo(tglCol) : '-';
        document.getElementById('badge-status-sppb').textContent = 'MEMUAT...';
        document.getElementById('modal-sppb-vb6').classList.remove('hidden');

        // Fetch detail Packing List via AJAX
        let url = `{{ route('api.pl-detail') }}?no_pl=${encodeURIComponent(noPl)}`;
        if (tglCol) {
            url += `&tgl_col=${encodeURIComponent(tglCol)}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.no_pl) {
                        if (document.getElementById('vb6_title_no_pl')) {
                            document.getElementById('vb6_title_no_pl').textContent = data.no_pl;
                        }
                        if (document.getElementById('vb6_display_no_pl')) {
                            document.getElementById('vb6_display_no_pl').textContent = data.no_pl;
                        }
                    }
                    if (data.tgl_col_formatted) {
                        document.getElementById('vb6_display_tgl_col').textContent = data.tgl_col_formatted;
                    }
                    if (data.tgl_col) {
                        document.getElementById('vb6_tgl_col_hidden').value = data.tgl_col;
                        currentSelectedTglCol = data.tgl_col;
                    }

                    // Isi form fields
                    document.getElementById('vb6_no_sppb').value = data.no_sppb;
                    document.getElementById('vb6_tgl_sppb').value = data.tgl_sppb;
                    document.getElementById('vb6_cabang').value = data.cabang;
                    document.getElementById('vb6_ekspedisi').value = data.ekspedisi;
                    document.getElementById('vb6_customer_name').value = data.customer_name;
                    document.getElementById('vb6_customer_address').value = data.customer_address;
                    document.getElementById('vb6_supirnopol').value = data.supirnopol;
                    document.getElementById('vb6_cat').value = data.cat;

                    // Sinkronisasi dropdown distributor dengan data.customer_name dan data.cabang
                    const distSelect = document.getElementById('vb6_select_distributor');
                    if (distSelect) {
                        distSelect.value = '';
                        if (data.customer_name) {
                            let matched = false;
                            if (data.cabang) {
                                for (let i = 0; i < distSelect.options.length; i++) {
                                    const opt = distSelect.options[i];
                                    if (opt.getAttribute('data-nama') === data.customer_name && 
                                        opt.getAttribute('data-kota')?.trim().toUpperCase() === data.cabang.trim().toUpperCase()) {
                                        distSelect.selectedIndex = i;
                                        matched = true;
                                        break;
                                    }
                                }
                            }
                            if (!matched) {
                                for (let i = 0; i < distSelect.options.length; i++) {
                                    if (distSelect.options[i].getAttribute('data-nama') === data.customer_name) {
                                        distSelect.selectedIndex = i;
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    // Summary
                    document.getElementById('vb6_display_jumlah').textContent = Number(data.jumlah_collie).toLocaleString() + ' Box';
                    document.getElementById('vb6_input_jumlah').value = data.jumlah_collie;
                    document.getElementById('vb6_display_berat').textContent = Number(data.berat_kg).toLocaleString(undefined, {minimumFractionDigits: 1}) + ' Kg';
                    document.getElementById('vb6_input_berat').value = data.berat_kg;

                    // Badge status
                    const badge = document.getElementById('badge-status-sppb');
                    if (data.is_new) {
                        badge.textContent = 'DRAFT BARU (BELUM SIMPAN)';
                        badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-500/30 border border-amber-400/40 text-amber-200';
                    } else {
                        badge.textContent = 'TERDAFTAR: ' + data.no_sppb;
                        badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/30 border border-emerald-400/40 text-emerald-200';
                    }

                    // Render Surat Muat (SM) Table
                    renderSmTable(data.sm_list);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat rincian Packing List');
            });
    }

    function formatDateIndo(dateStr) {
        if (!dateStr) return '-';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        return dateStr;
    }

    let currentSmList = [];
    let smOracleSearchTimeout = null;

    function renderSmTable(smList) {
        currentSmList = smList || [];
        const tbody = document.getElementById('vb6_sm_tbody');
        document.getElementById('vb6_sm_count').textContent = currentSmList.length + ' SM';

        if (!currentSmList || currentSmList.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="py-3 text-center text-slate-400 font-sans italic">Tidak ada Nomor SM di bawah Packing List ini. Klik <strong>Pilih SM</strong> untuk menambahkan.</td></tr>`;
            return;
        }

        let html = '';
        currentSmList.forEach((sm, idx) => {
            html += `
                <tr class="hover:bg-sky-900/50 transition">
                    <td class="py-1.5 px-3 text-center">
                        <input type="checkbox" name="selected_sms[]" value="${sm.no_sm}" checked class="sm-checkbox rounded text-sky-600 focus:ring-0">
                    </td>
                    <td class="py-1.5 px-3 font-bold text-white font-mono">${sm.no_sm}</td>
                    <td class="py-1.5 px-3 text-center text-sky-200 font-sans">${sm.tgl_sm || '-'}</td>
                    <td class="py-1.5 px-3 text-right font-bold text-sky-200">${sm.total_koli && !isNaN(sm.total_koli) ? Number(sm.total_koli).toLocaleString() : '-'}</td>
                    <td class="py-1.5 px-3 text-right font-bold text-amber-300">${sm.total_berat && !isNaN(sm.total_berat) ? Number(sm.total_berat).toFixed(1) : '-'}</td>
                    <td class="py-1.5 px-2 text-center">
                        <button type="button" onclick="removeSmFromList('${sm.no_sm}')" class="text-rose-400 hover:text-rose-300 p-1" title="Hapus SM ini dari daftar">
                            <i class="fa-regular fa-trash-can text-xs"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function removeSmFromList(noSm) {
        currentSmList = currentSmList.filter(s => s.no_sm !== noSm);
        renderSmTable(currentSmList);
    }

    function toggleSelectAllSm(checked) {
        const checkboxes = document.querySelectorAll('.sm-checkbox');
        checkboxes.forEach(cb => cb.checked = checked);
    }

    // =========================================================================
    // SUB-MODAL PICKER: PILIH SURAT MUAT (NO_SM) DARI ORACLE
    // =========================================================================
    function openSmOraclePicker() {
        document.getElementById('modal-picker-sm-oracle').classList.remove('hidden');
        document.getElementById('sm_oracle_search_input').value = '';
        loadSmOracleList('');
    }

    function closeSmOraclePicker() {
        document.getElementById('modal-picker-sm-oracle').classList.add('hidden');
    }

    function onSmOracleSearchInput(val) {
        clearTimeout(smOracleSearchTimeout);
        smOracleSearchTimeout = setTimeout(() => {
            loadSmOracleList(val.trim());
        }, 300);
    }

    function loadSmOracleList(query = '', forceRefresh = false) {
        const tbody = document.getElementById('sm_oracle_tbody');
        tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-slate-400 font-sans italic"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data Surat Muat dari Oracle...</td></tr>`;

        let url = `{{ route('api.sm-oracle') }}`;
        const params = new URLSearchParams();
        if (query) params.append('q', query);
        if (forceRefresh) params.append('refresh', '1');
        const qs = params.toString();
        if (qs) url += `?${qs}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderSmOraclePickerTable(data.items);
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-rose-400 font-sans">Gagal memuat data: ${data.message || 'Terjadi kesalahan'}</td></tr>`;
                }
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-rose-400 font-sans">Terjadi kesalahan koneksi saat memuat data dari Oracle.</td></tr>`;
            });
    }

    function renderSmOraclePickerTable(items) {
        const tbody = document.getElementById('sm_oracle_tbody');
        document.getElementById('check-all-picker-sm').checked = false;
        document.getElementById('picker_sm_selected_count').textContent = '0';

        if (!items || items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-slate-400 font-sans italic">Tidak ada Surat Muat yang cocok dengan pencarian.</td></tr>`;
            return;
        }

        let html = '';
        items.forEach(it => {
            // Cek apakah SM ini sudah ada di daftar SPPB saat ini
            const alreadyInList = currentSmList.some(s => s.no_sm === it.no_sm);
            const isChecked = alreadyInList ? 'checked disabled' : '';
            const rowBadge = alreadyInList ? '<span class="text-[9px] bg-sky-950 text-sky-400 px-1 py-0.5 rounded ml-1 border border-sky-600/30">Sudah Ada</span>' : '';

            html += `
                <tr class="hover:bg-sky-900/60 transition cursor-pointer" onclick="togglePickerRowCheckbox(this, event)">
                    <td class="py-2 px-3 text-center">
                        <input type="checkbox" 
                               value="${it.no_sm}" 
                               data-tgl-sm="${it.tgl_sm || ''}"
                               data-customer-name="${it.customer_name || ''}"
                               data-customer-address="${it.customer_address || ''}"
                               data-total-qty="${it.total_qty || ''}"
                               ${isChecked}
                               onchange="updatePickerSelectedCount()"
                               class="picker-sm-checkbox rounded text-emerald-500 focus:ring-0 cursor-pointer">
                    </td>
                    <td class="py-2 px-3 font-bold text-amber-300 font-mono">
                        ${it.no_sm}
                        ${rowBadge}
                    </td>
                    <td class="py-2 px-3 text-center text-sky-200 font-sans text-[11px]">${it.tgl_sm || '-'}</td>
                    <td class="py-2 px-3 text-sky-100 font-sans text-[11px] max-w-[200px] truncate" title="${it.customer_name || ''}">
                        ${it.customer_name || '-'}
                    </td>
                    <td class="py-2 px-3 text-right font-bold text-white font-mono">${it.total_qty ? Number(it.total_qty).toLocaleString() : '-'}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
        updatePickerSelectedCount();
    }

    function togglePickerRowCheckbox(tr, e) {
        if (e.target.tagName.toLowerCase() === 'input') return;
        const cb = tr.querySelector('.picker-sm-checkbox');
        if (cb && !cb.disabled) {
            cb.checked = !cb.checked;
            updatePickerSelectedCount();
        }
    }

    function toggleSelectAllPickerSm(checked) {
        const checkboxes = document.querySelectorAll('.picker-sm-checkbox:not(:disabled)');
        checkboxes.forEach(cb => cb.checked = checked);
        updatePickerSelectedCount();
    }

    function updatePickerSelectedCount() {
        const checked = document.querySelectorAll('.picker-sm-checkbox:checked:not(:disabled)');
        document.getElementById('picker_sm_selected_count').textContent = checked.length;
    }

    function applySelectedSmOracle() {
        const checkedBoxes = document.querySelectorAll('.picker-sm-checkbox:checked:not(:disabled)');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang minimal satu Surat Muat (SM) terlebih dahulu.');
            return;
        }

        let lastCustomerName = '';
        let lastCustomerAddr = '';

        checkedBoxes.forEach(cb => {
            const noSm = cb.value;
            const tglSm = cb.dataset.tglSm || '';
            const custName = cb.dataset.customerName || '';
            const custAddr = cb.dataset.customerAddress || '';
            const totalQty = cb.dataset.totalQty || '';

            if (custName) lastCustomerName = custName;
            if (custAddr) lastCustomerAddr = custAddr;

            // Tambahkan jika belum ada di list
            const exists = currentSmList.some(s => s.no_sm === noSm);
            if (!exists) {
                currentSmList.push({
                    no_sm: noSm,
                    tgl_sm: tglSm,
                    total_koli: '-',
                    total_berat: totalQty,
                });
            }
        });

        // Jika form Customer di SPPB masih kosong, otomatis isi dengan customer dari SM terpilih
        const custNameInput = document.getElementById('vb6_customer_name');
        const custAddrInput = document.getElementById('vb6_customer_address');
        if (custNameInput && !custNameInput.value.trim() && lastCustomerName) {
            custNameInput.value = lastCustomerName;
        }
        if (custAddrInput && !custAddrInput.value.trim() && lastCustomerAddr) {
            custAddrInput.value = lastCustomerAddr;
        }

        renderSmTable(currentSmList);
        closeSmOraclePicker();
    }

    function closeSppbModal() {
        document.getElementById('modal-sppb-vb6').classList.add('hidden');
    }

    function printCurrentPl() {
        if (!currentSelectedNoPl) return;
        let url = `{{ url('/sppb/print') }}/${encodeURIComponent(currentSelectedNoPl)}`;
        if (currentSelectedTglCol) {
            url += `?tgl_col=${encodeURIComponent(currentSelectedTglCol)}`;
        }
        window.open(url, '_blank');
    }

    function onSelectDistributor(selectElem) {
        if (!selectElem) return;
        const opt = selectElem.options[selectElem.selectedIndex];
        if (!opt || !opt.value) return;

        const nama = opt.getAttribute('data-nama') || '';
        const alamat = opt.getAttribute('data-alamat') || '';
        const kota = opt.getAttribute('data-kota') || '';

        const nameInput = document.getElementById('vb6_customer_name');
        const addrInput = document.getElementById('vb6_customer_address');
        const cabangInput = document.getElementById('vb6_cabang');

        if (nameInput) nameInput.value = nama;
        if (addrInput) addrInput.value = alamat;
        if (cabangInput && kota) cabangInput.value = kota;
    }

    // ==============================================================
    // DETAIL RINCIAN COLLIE MODAL (DARI COLLIE_SPPBS PER NO_PL & TGL)
    // ==============================================================
    function openCollieDetailModal(noPl, tglCol = '', tglColFormatted = '') {
        if (!noPl) return;
        document.getElementById('modal_collie_badge_pl').textContent = noPl;
        document.getElementById('modal_collie_badge_tgl').textContent = tglColFormatted || tglCol || '-';
        document.getElementById('modal_collie_link_edit').href = `{{ url('/collie') }}?search=${encodeURIComponent(noPl)}`;
        document.getElementById('modal-collie-detail').classList.remove('hidden');

        const tbody = document.getElementById('modal_collie_tbody');
        tbody.innerHTML = `<tr><td colspan="10" class="py-8 text-center text-slate-400 font-sans italic"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data rincian collie ${noPl}...</td></tr>`;

        let url = `{{ route('api.pl-collies') }}?no_pl=${encodeURIComponent(noPl)}`;
        if (tglCol) {
            url += `&tgl_col=${encodeURIComponent(tglCol)}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.items && data.items.length > 0) {
                    renderCollieDetailRows(data.items);
                    document.getElementById('modal_collie_total_rows').textContent = data.items.length;
                    document.getElementById('modal_collie_total_koli').textContent = Number(data.total_koli || 0).toLocaleString('id-ID');
                    document.getElementById('modal_collie_total_berat').textContent = Number(data.total_berat || 0).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' Kg';
                } else {
                    tbody.innerHTML = `<tr><td colspan="10" class="py-8 text-center text-slate-400 font-sans italic">Tidak ada rincian collie untuk Packing List ${noPl} pada tanggal ini.</td></tr>`;
                    document.getElementById('modal_collie_total_rows').textContent = '0';
                    document.getElementById('modal_collie_total_koli').textContent = '0';
                    document.getElementById('modal_collie_total_berat').textContent = '0 Kg';
                }
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="10" class="py-8 text-center text-rose-500 font-sans">Gagal memuat rincian data collie.</td></tr>`;
            });
    }

    function renderCollieDetailRows(items) {
        const tbody = document.getElementById('modal_collie_tbody');
        tbody.innerHTML = '';
        items.forEach((item, idx) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-sky-50/70 transition-colors';
            const adJucol = (item.ad !== null && item.ad !== '' && item.jucol !== null && item.jucol !== '') ? `${item.ad} - ${item.jucol}` : '-';
            tr.innerHTML = `
                <td class="py-1.5 px-3 text-center text-slate-400 font-sans">${idx + 1}</td>
                <td class="py-1.5 px-3 font-sans ${item.no_sm ? 'text-slate-900 font-bold' : 'text-slate-400 italic'}">${item.no_sm || '-'}</td>
                <td class="py-1.5 px-3 text-center font-bold text-slate-900 bg-amber-50/50">${item.no_col || '-'}</td>
                <td class="py-1.5 px-3 text-center text-slate-600 font-mono">${adJucol}</td>
                <td class="py-1.5 px-3 font-bold text-emerald-700">${item.code || '-'}</td>
                <td class="py-1.5 px-3 font-sans text-slate-800 text-[11px] max-w-xs truncate" title="${item.nama || ''}">${item.nama || '-'}</td>
                <td class="py-1.5 px-3 text-right font-bold text-slate-900">${Number(item.qty || 0).toLocaleString('id-ID')}</td>
                <td class="py-1.5 px-3 text-right">${item.qty1 || 0}</td>
                <td class="py-1.5 px-3 text-right">${item.qty2 || 0}</td>
                <td class="py-1.5 px-3 text-right font-bold text-amber-700">${Number(item.qtyr || 0).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 })}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function closeCollieDetailModal() {
        document.getElementById('modal-collie-detail').classList.add('hidden');
    }
</script>
@endpush
