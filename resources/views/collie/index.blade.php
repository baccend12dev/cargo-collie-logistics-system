@extends('layouts.app')

@section('title', 'Daftar Data Collie (Packing List)')

@section('content')
<div class="flex-1 flex flex-col space-y-2 min-h-0">

    <!-- Notifikasi Sukses / Pesan -->
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
    <!-- CARD TOOLBAR & FILTER (MIRROR DARI HALAMAN 1 VB6)              -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- CARD TOOLBAR & FILTER (COMPACT SINGLE ROW)                     -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 p-2.5 px-3">
        <div class="flex flex-wrap items-center justify-between gap-2.5 text-xs">
            
            <!-- Sisi Kiri: Tombol Tambah Collie, Print, & Filter Cek SPPB -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tombol Tambah Collie -->
                <button type="button" 
                        onclick="openModalInput()"
                        class="px-3.5 py-1.5 bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-500 hover:to-blue-600 text-white rounded-lg font-bold text-xs shadow-sm flex items-center space-x-1.5 transition active:scale-95">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>TAMBAH COLLIE</span>
                </button>

                <!-- Tombol Print (Cetak PL) -->
                <button type="button" 
                        onclick="openPrintPreview()"
                        class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-bold text-xs shadow-sm flex items-center space-x-1.5 transition active:scale-95">
                    <i class="fa-solid fa-print text-xs text-sky-400"></i>
                    <span>PRINT (CETAK PL)</span>
                </button>

                <!-- Tombol Edit Collie (Aktif saat baris dipilih pada tabel) -->
                <button type="button" 
                        id="btn-edit-collie-toolbar"
                        onclick="triggerEditFromToolbar()"
                        class="px-3.5 py-1.5 bg-slate-100 text-slate-400 rounded-lg font-bold text-xs shadow-xs flex items-center space-x-1.5 transition border border-slate-200 cursor-not-allowed select-none"
                        title="Pilih salah satu baris di tabel untuk mengedit Packing List secara inline">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    <span id="btn-edit-collie-text">EDIT COLLIE</span>
                </button>

                <!-- Checkbox Cek SPPB -->
                <form method="GET" action="{{ route('collie.index') }}" id="filter-sppb-form" class="inline">
                    <label class="flex items-center space-x-1.5 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 cursor-pointer text-xs font-semibold text-slate-700 select-none">
                        <input type="checkbox" name="cek_sppb" value="1" {{ request('cek_sppb') == '1' ? 'checked' : '' }} onchange="document.getElementById('filter-sppb-form').submit()" class="w-3.5 h-3.5 rounded text-sky-600 focus:ring-sky-500 border-slate-300">
                        <span>Hanya Ber-SPPB</span>
                    </label>
                </form>

                <!-- Refresh -->
                <a href="{{ route('collie.index') }}" title="Refresh Halaman" class="p-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold flex items-center transition border border-slate-200">
                    <i class="fa-solid fa-rotate text-slate-500"></i>
                </a>
            </div>

            <!-- Sisi Kanan: Form Filter, Cari & Record -->
            <form method="GET" action="{{ route('collie.index') }}" class="flex flex-wrap items-center gap-2">
                <!-- Filter Kata Kunci -->
                <div class="flex items-center space-x-1">
                    <label class="text-slate-600 font-medium">Filter:</label>
                    <input type="text" name="filter_val" value="{{ request('filter_val') }}" placeholder="Kode Obat / No PL..." class="px-2 py-1 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none w-40 bg-slate-50 focus:bg-white text-xs">
                    <button type="submit" class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-semibold transition">Filter</button>
                </div>

                <!-- Cari Cepat (Find) -->
                <div class="flex items-center space-x-1">
                    <label class="text-slate-600 font-medium">Cari:</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. PL/SM/Kode..." class="px-2 py-1 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none w-36 bg-slate-50 focus:bg-white text-xs">
                    <button type="submit" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold transition flex items-center space-x-1">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                        <span>Find</span>
                    </button>
                </div>

                <!-- Badge Total Record -->
                <div class="flex items-center space-x-1.5 bg-sky-50 border border-sky-200 px-2.5 py-1 rounded-lg">
                    <span class="text-sky-700 font-medium text-[11px]">Record:</span>
                    <span class="font-black text-sky-900 text-xs">{{ $totalRecords }}</span>
                </div>
            </form>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- TABEL UTAMA COLLIE (GRID DENSE DATA OPERASIONAL)               -->
    <!-- ============================================================== -->
    <div class="flex-1 bg-white rounded-xl shadow-sm border border-slate-200/90 flex flex-col min-h-0 overflow-hidden">
        
        <!-- Table Responsive Container -->
        <div class="flex-1 overflow-auto">
            <table class="w-full text-left border-collapse table-condensed text-[12px]">
                <thead class="sticky top-0 bg-slate-800 text-white z-10 shadow-sm text-[11px] uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-2.5 px-3 border-r border-slate-700 w-10 text-center">#</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 font-bold text-sky-300">NO_PL</th>
                        <th class="py-2.5 px-3 border-r border-slate-700">NO_SM</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 font-bold text-amber-300">NO_COL</th>
                        <th class="py-2.5 px-3 border-r border-slate-700">TGL_COL</th>
                        <th class="py-2.5 px-3 border-r border-slate-700">CABANG</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 font-bold text-emerald-300">CODE</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 font-semibold text-sky-200">NAMA BARANG</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-right">QTY</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-right">QTY1</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-right">QTY2</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-right font-bold text-amber-300">QTYR (KG)</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-center">AD</th>
                        <th class="py-2.5 px-3 border-r border-slate-700 text-center">STATUS SPPB</th>
                        <th class="py-2.5 px-3 text-center w-20">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium font-mono text-slate-700">
                    
                    @forelse($collies as $index => $item)
                    <tr class="collie-main-row hover:bg-sky-50/70 transition-colors cursor-pointer group"
                        data-id="{{ $item->id }}"
                        data-no-pl="{{ $item->no_pl }}"
                        data-tgl-col="{{ $item->tgl_col ? $item->tgl_col->format('Y-m-d') : '' }}"
                        onclick="selectCollieRow(this, '{{ $item->no_pl }}', '{{ $item->tgl_col ? $item->tgl_col->format('Y-m-d') : '' }}')"
                        ondblclick="openEditGridModal('{{ $item->no_pl }}', '{{ $item->tgl_col ? $item->tgl_col->format('Y-m-d') : '' }}')">
                        <td class="px-3 py-1 text-center text-slate-400 group-hover:text-sky-600 font-sans">
                            {{ $collies->firstItem() + $index }}
                        </td>
                        <td class="px-3 py-1 font-bold text-sky-700">{{ $item->no_pl ?? '-' }}</td>
                        <td class="px-3 py-1 font-sans {{ $item->no_sm ? 'text-slate-900 font-bold' : 'text-slate-400 italic' }}">
                            {{ $item->no_sm ?? '-' }}
                        </td>
                        <td class="px-3 py-1 font-bold text-slate-900 bg-amber-50/50">{{ $item->no_col }}</td>
                        <td class="px-3 py-1 font-sans">{{ $item->tgl_col ? $item->tgl_col->format('d/m/Y') : '-' }}</td>
                        <td class="px-3 py-1 font-sans font-bold">{{ $item->cabang }}</td>
                        <td class="px-3 py-1 font-bold text-emerald-700">{{ $item->code }}</td>
                        <td class="px-3 py-1 font-sans font-medium text-slate-800 text-[11px] max-w-xs truncate" title="{{ $item->nama_barang ?? '' }}">
                            {{ $item->nama_barang ?? '-' }}
                        </td>
                        <td class="px-3 py-1 text-right font-bold text-slate-900">{{ number_format($item->qty ?? 0) }}</td>
                        <td class="px-3 py-1 text-right">{{ $item->qty1 }}</td>
                        <td class="px-3 py-1 text-right">{{ $item->qty2 }}</td>
                        <td class="px-3 py-1 text-right font-bold text-amber-700">{{ number_format($item->qtyr ?? 0, 1) }}</td>
                        <td class="px-3 py-1 text-center">{{ $item->ad }}</td>
                        
                        <!-- Status SPPB -->
                        <td class="px-3 py-1 text-center font-sans">
                            @if($item->no_sppb)
                                <a href="{{ route('sppb.print', ['no_pl' => $item->no_pl, 'tgl_col' => $item->tgl_col ? $item->tgl_col->format('Y-m-d') : '']) }}" target="_blank" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 hover:bg-emerald-200 text-emerald-800 border border-emerald-300 transition" title="Klik untuk Cetak SPPB {{ $item->no_sppb }}">
                                    <i class="fa-solid fa-check text-[9px] mr-1"></i> {{ $item->no_sppb }}
                                </a>
                            @else
                                <a href="{{ route('sppb.index', ['search' => $item->no_pl]) }}" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition" title="Klik untuk Terbitkan SPPB">
                                    <i class="fa-regular fa-clock text-[9px] mr-1"></i> Terbitkan SPPB
                                </a>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-3 py-1 text-center font-sans">
                            <form action="{{ route('collie.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus baris koli {{ $item->no_col }} ({{ $item->code }})?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 p-1" title="Hapus"><i class="fa-regular fa-trash-can"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" class="px-4 py-8 text-center text-slate-400 font-sans">
                            <i class="fa-solid fa-box-open text-3xl mb-2 block text-slate-300"></i>
                            Tidak ada data collie yang sesuai dengan filter.
                        </td>
                    </tr>
                    @endforelse


                </tbody>
            </table>
        </div>

        <!-- ============================================================== -->
        <!-- ACTION BAR BAWAH & PAGINATION CONTROLS                         -->
        <!-- ============================================================== -->
        <div class="bg-slate-50 border-t border-slate-200 px-3 py-2 flex flex-wrap items-center justify-between gap-2">
            
            <div class="flex items-center space-x-3 text-xs text-slate-500">
                <span>Total Record: <strong class="text-slate-800 font-bold">{{ number_format($totalRecords) }}</strong></span>
                <span class="text-slate-300">|</span>
                <span>Total QTY: <strong class="text-slate-800 font-bold font-mono">{{ number_format($totalQty ?? 0) }}</strong></span>
                <span class="text-slate-300">|</span>
                <span>Total Berat: <strong class="text-slate-800 font-bold font-mono">{{ number_format($totalQtyr ?? 0, 1) }} kg</strong></span>
            </div>

            <!-- Pagination Links -->
            <div class="flex items-center space-x-2 text-xs">
                @if($collies->hasPages())
                    <span class="text-slate-500 font-medium">Halaman {{ $collies->currentPage() }} dari {{ $collies->lastPage() }}</span>
                    {{ $collies->links('pagination::simple-tailwind') }}
                @else
                    <span class="text-slate-500 font-medium">Menampilkan {{ $collies->count() }} dari {{ $totalRecords }} data</span>
                @endif
            </div>

        </div>

    </div>

</div>

<!-- ============================================================== -->
<!-- MODAL FORM INPUT COLLIE (CONTINUOUS ENTRY DENGAN LIST DI BAWAH) -->
<!-- (PERSIS SEPERTI input data.png PADA SISTEM ASLINYA)             -->
<!-- ============================================================== -->
<div id="modal-input-collie" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
    <div class="bg-gradient-to-b from-sky-900 via-sky-800 to-slate-900 text-white rounded-2xl shadow-2xl border-2 border-sky-500/50 w-full max-w-5xl overflow-hidden animate-in fade-in duration-200 flex flex-col my-auto max-h-[92vh]">
        
        <!-- Modal Header -->
        <div class="px-5 py-3 bg-sky-950/90 border-b border-sky-700/60 flex items-center justify-between select-none shrink-0">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-300">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base tracking-wide text-white">INPUT COLLIE (PACKING LIST)</h3>
                    <p class="text-[11px] text-sky-300">Input koli bertahap & langsung tampil di tabel daftar bawah</p>
                </div>
            </div>
            <button type="button" onclick="closeModalInput()" class="text-slate-400 hover:text-white text-lg font-bold p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body (Scrollable jika tabel panjang) -->
        <div class="p-5 overflow-y-auto space-y-4">
            
            <form id="form-ajax-collie" onsubmit="submitCollieAjax(event)">
                @csrf
                
                <!-- HEADER TRANSAKSI GRID -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 text-xs font-semibold bg-sky-950/40 p-3.5 rounded-xl border border-sky-600/30">
                    
                    <!-- No. Packing List -->
                    <div class="md:col-span-3 flex items-center space-x-2">
                        <label class="w-24 text-sky-200">No. Packing List:</label>
                        <div class="flex items-center space-x-1 flex-1">
                            <input type="text" name="no_pl" id="modal_no_pl" placeholder="001" value="001/PL" required onchange="onHeaderCollieChange()" class="w-24 px-2.5 py-1.5 rounded-lg bg-white text-slate-900 font-bold font-mono focus:ring-2 focus:ring-sky-400 focus:outline-none">
                        </div>
                    </div>

                    <!-- No. SM -->
                    <div class="md:col-span-4 flex items-center space-x-2">
                        <label class="w-16 text-sky-200">No. SM :</label>
                        <input type="text" name="no_sm" id="modal_no_sm" placeholder="Contoh: 2601027/JKT/OE/00" class="flex-1 px-2.5 py-1.5 rounded-lg bg-white text-slate-900 font-semibold focus:ring-2 focus:ring-sky-400 focus:outline-none">
                    </div>

                    <!-- Tgl Collie -->
                    <div class="md:col-span-3 flex items-center space-x-2">
                        <label class="w-20 text-sky-200">Tgl. Collie :</label>
                        <input type="date" name="tgl_col" id="modal_tgl_col" value="{{ date('Y-m-d') }}" onchange="onHeaderCollieChange()" class="flex-1 px-2 py-1.5 rounded-lg bg-white text-slate-900 font-semibold focus:ring-2 focus:ring-sky-400 focus:outline-none">
                    </div>

                    <!-- Cabang -->
                    <div class="md:col-span-2 flex items-center space-x-1.5">
                        <label class="w-14 text-sky-200">Cabang:</label>
                        <input type="text" name="cabang" id="modal_cabang" value="JKT0" class="w-full px-2 py-1.5 rounded-lg bg-white text-slate-900 font-bold uppercase focus:ring-2 focus:ring-sky-400 focus:outline-none">
                    </div>

                </div>

                <!-- BARIS INPUT ITEM BARANG (MIRROR DARI input data.png / VB6) -->
                <div class="bg-sky-950/60 p-4 rounded-xl border border-sky-500/40 space-y-3 mt-3">
                    <div class="flex flex-wrap justify-between items-center text-[11px] font-bold text-sky-200 uppercase tracking-wider pb-1.5 border-b border-sky-800/60">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Input Rincian Koli & Barang:</span>
                        </div>
                        <span class="text-amber-300 font-mono font-normal normal-case text-xs">
                            💡 Masukkan <strong>Code</strong> lalu <strong>Tekan Enter ↵</strong> untuk menampilkan Nama Barang otomatis (seperti VB)
                        </span>
                    </div>

                    <!-- Baris 1: Kode Barang & Nama Barang (Mirip persis VB) -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end text-xs font-semibold">
                        <!-- Code -->
                        <div class="md:col-span-3 relative">
                            <label class="block text-sky-200 mb-1 text-[11px] uppercase tracking-wide">
                                Code <span class="text-amber-300 font-mono font-bold">(Enter ↵)</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="text" 
                                       name="code" 
                                       id="modal_item_code" 
                                       placeholder="INV095" 
                                       autocomplete="off"
                                       required 
                                       onkeydown="handleCodeKeydown(event)"
                                       onblur="handleCodeBlur()"
                                       oninput="handleCodeInput(event)"
                                       class="w-full px-3 py-2 pr-9 rounded-lg bg-white text-slate-900 font-black uppercase text-sm tracking-wider focus:ring-2 focus:ring-sky-400 focus:outline-none shadow-sm">
                                <button type="button" 
                                        onclick="lookupItemCode(true)" 
                                        title="Cari / Cek Kode Barang (Enter)" 
                                        class="absolute right-1 px-2 py-1 bg-sky-600 hover:bg-sky-500 text-white rounded text-xs font-bold transition flex items-center">
                                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                                </button>
                            </div>
                            <!-- Dropdown Saran / Autocomplete Master Stock -->
                            <div id="autocomplete-suggestions" class="absolute left-0 right-0 top-full mt-1 bg-slate-900 border border-sky-500/50 rounded-lg shadow-2xl z-50 max-h-48 overflow-y-auto hidden text-xs">
                                <!-- Diisi via JS -->
                            </div>
                        </div>

                        <!-- Nama Barang Display (Diambil dari otto_master_stock_v) -->
                        <div class="md:col-span-9">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-sky-200 text-[11px] uppercase tracking-wide">Nama Barang (otto_master_stock_v):</label>
                                <div class="flex items-center space-x-2">
                                    <span id="modal_item_uom" class="hidden px-2 py-0.5 rounded text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-300/30 font-mono"></span>
                                    <span id="modal_item_status" class="hidden px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30"></span>
                                </div>
                            </div>
                            <input type="text" 
                                   id="modal_item_desc" 
                                   readonly 
                                   placeholder="Ketik Code lalu tekan ENTER untuk menampilkan nama barang..." 
                                   class="w-full px-3 py-2 rounded-lg bg-slate-900/90 text-sky-100 font-bold text-xs border border-sky-600/50 focus:outline-none select-none tracking-wide shadow-inner">
                        </div>
                    </div>

                    <!-- Baris 2: No Collie (Otomatis), Qty1, Qty2, Total Qty, Berat, dan Simpan -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 items-end text-xs font-semibold pt-1">
                        <!-- Tampilan No. Collie (Otomatis dari Qty1) -->
                        <div class="md:col-span-3">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-sky-200 text-[11px] font-bold">No. Collie</label>
                                <span class="text-[10px] text-amber-300 font-mono font-bold" id="modal_collie_calc_info">(Auto dari Qty1)</span>
                            </div>
                            <div class="relative flex items-center">
                                <input type="text" 
                                       name="no_col" 
                                       id="modal_input_no_col" 
                                       placeholder="1-80" 
                                       readonly 
                                       tabindex="-1"
                                       title="Nomor Collie otomatis dihitung: (No. Terakhir + 1) s/d (Awal + Qty1 - 1)"
                                       class="w-full px-3 py-1.5 rounded-lg bg-slate-950/80 text-amber-300 font-black text-center text-sm tracking-wider border border-amber-400/50 shadow-inner select-none font-mono">
                                <!-- Hidden input untuk nilai ad dan jucol terhitung -->
                                <input type="hidden" name="ad" id="modal_input_ad" value="1">
                                <input type="hidden" name="jucol" id="modal_input_jucol" value="1">
                            </div>
                        </div>

                        <!-- Qty1 -->
                        <div class="md:col-span-2">
                            <label class="block text-sky-200 mb-1 text-[11px] font-bold">
                                Qty1 <span class="text-amber-300">(Koli)</span>
                            </label>
                            <input type="number" 
                                   name="qty1" 
                                   id="modal_input_qty1" 
                                   placeholder="1" 
                                   min="1"
                                   oninput="onModalQty1Change()" 
                                   onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('modal_input_qty2').focus();document.getElementById('modal_input_qty2').select();}"
                                   required 
                                   class="w-full px-2 py-1.5 rounded-lg bg-white text-slate-900 font-bold text-center focus:ring-2 focus:ring-sky-400">
                        </div>

                        <!-- x -->
                        <div class="hidden md:flex md:col-span-1 items-center justify-center font-bold text-sky-300 pb-2 text-base">
                            &times;
                        </div>

                        <!-- Qty2 -->
                        <div class="md:col-span-2">
                            <label class="block text-sky-200 mb-1 text-[11px]">Qty2</label>
                            <input type="number" 
                                   name="qty2" 
                                   id="modal_input_qty2" 
                                   placeholder="12" 
                                   oninput="kalkulasiTotalQtyModal()" 
                                   onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('modal_item_qtyr').focus();document.getElementById('modal_item_qtyr').select();}"
                                   required 
                                   class="w-full px-2 py-1.5 rounded-lg bg-white text-slate-900 font-bold text-center focus:ring-2 focus:ring-sky-400">
                        </div>

                        <!-- Total Qty -->
                        <div class="md:col-span-1">
                            <label class="block text-sky-200 mb-1 text-[11px]">Total Qty</label>
                            <input type="number" 
                                   name="qty" 
                                   id="modal_input_total_qty" 
                                   placeholder="144" 
                                   readonly 
                                   class="w-full px-1 py-1.5 rounded-lg bg-slate-300 text-slate-900 font-black text-center select-none">
                        </div>

                        <!-- Berat Qtyr -->
                        <div class="md:col-span-1">
                            <label class="block text-sky-200 mb-1 text-[11px]">Berat (Kg)</label>
                            <input type="number" 
                                   step="0.01" 
                                   name="qtyr" 
                                   id="modal_item_qtyr" 
                                   placeholder="5" 
                                   onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('btn-modal-simpan').click();}"
                                   class="w-full px-2 py-1.5 rounded-lg bg-white text-slate-900 font-bold focus:ring-2 focus:ring-sky-400">
                        </div>

                        <!-- Tombol Simpan Baris -->
                        <div class="md:col-span-2">
                            <button type="submit" 
                                    id="btn-modal-simpan" 
                                    class="w-full py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white rounded-lg font-bold text-xs shadow-md transition flex items-center justify-center space-x-1.5 active:scale-95">
                                <i class="fa-solid fa-floppy-disk text-xs"></i>
                                <span>Simpan</span>
                            </button>
                        </div>
                    </div>
                </div>

            </form>

            <!-- TABEL ITEM SEMENTARA (MIRROR FORM BAWAH input data.png) -->
            <div class="bg-slate-950/80 rounded-xl border border-sky-500/40 overflow-hidden shadow-lg mt-3">
                <div class="px-4 py-2 bg-sky-950 border-b border-sky-600/40 flex flex-wrap items-center justify-between text-xs font-bold text-sky-200 gap-2">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-list-check text-sky-400"></i>
                        <span>DAFTAR COLLIE TERSIMPAN PADA NO. PL INI:</span>
                    </div>
                    <div class="flex items-center space-x-4 text-[11px] font-mono">
                        <span>Total Koli: <strong id="modal_counter_koli" class="text-amber-300">0</strong> Box</span>
                        <span>Total Berat: <strong id="modal_counter_berat" class="text-amber-300">0.0</strong> Kg</span>
                    </div>
                </div>

                <div class="max-h-56 overflow-y-auto">
                    <table class="w-full text-left text-xs font-mono">
                        <thead class="bg-sky-950/90 text-sky-200 uppercase text-[10px] sticky top-0 border-b border-sky-700/50">
                            <tr>
                                <th class="py-2 px-3 border-r border-sky-800/60 w-8 text-center">#</th>
                                <th class="py-2 px-3 border-r border-sky-800/60 text-emerald-300">CODE</th>
                                <th class="py-2 px-3 border-r border-sky-800/60 text-sky-200">NAMA BARANG</th>
                                <th class="py-2 px-3 border-r border-sky-800/60 text-amber-300 font-bold">NO_COL</th>
                                <th class="py-2 px-3 border-r border-sky-800/60 text-center">QTY1</th>
                                <th class="py-2 px-3 border-r border-sky-800/60 text-center">QTY2</th>
                                <th class="py-2 px-3 border-r border-sky-800/60 text-right">QTY</th>
                                <th class="py-2 px-3 border-r border-sky-800/60 text-right font-bold text-amber-300">QTYR (KG)</th>
                                <th class="py-2 px-3 border-r border-sky-800/60 text-center">AD</th>
                                <th class="py-2 px-3 border-r border-sky-800/60">NO_SM</th>
                                <th class="py-2 px-2 text-center w-12">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="modal_items_tbody" class="divide-y divide-sky-900/60 text-sky-100">
                            <tr>
                                <td colspan="11" class="py-4 text-center text-slate-400 font-sans italic">
                                    Belum ada baris collie untuk No. PL ini. Silakan input pada form di atas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3 bg-sky-950/90 border-t border-sky-700/60 flex justify-between items-center shrink-0">
            <span class="text-xs text-sky-300 font-medium">
                💡 Data otomatis tersimpan ke PostgreSQL setiap tombol <strong>Simpan Baris</strong> ditekan.
            </span>
            <button type="button" onclick="closeModalInput()" class="px-6 py-2 bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-500 hover:to-blue-600 text-white rounded-xl font-bold text-xs transition shadow-md flex items-center space-x-1.5">
                <i class="fa-solid fa-check"></i>
                <span>Selesai / Close</span>
            </button>
        </div>

    </div>
</div>

<!-- ============================================================== -->
<!-- PREVIEW PRINT MODAL (FORMAT FM.LG.C-P.001.001.01)              -->
<!-- ============================================================== -->
<div id="modal-print-preview" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-300 w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
        
        <div class="px-5 py-3.5 bg-slate-800 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-print text-sky-400"></i>
                <span class="font-bold text-sm">Preview Dokumen Packing List (FM.LG.C-P.001.001.01)</span>
            </div>
            <button onclick="closePrintPreview()" class="text-slate-400 hover:text-white font-bold text-lg">&times;</button>
        </div>

        <div class="p-8 overflow-y-auto flex-1 font-serif text-slate-900 bg-white" id="printable-area">
            <div class="flex justify-between items-start border-b-2 border-slate-900 pb-3 mb-4">
                <div>
                    <h2 class="font-bold text-base tracking-wider uppercase">PT. OTTO PHARMACEUTICAL INDUSTRIES LTD</h2>
                    <h3 class="font-semibold text-sm tracking-widest uppercase">BANDUNG</h3>
                </div>
                <div class="text-right">
                    <span class="text-xs font-mono font-semibold">FM.LG.C-P.001.001.01</span>
                </div>
            </div>

            <div class="text-center my-4">
                <h1 class="text-lg font-black tracking-widest uppercase">PACKING LIST</h1>
                <p class="text-xs font-bold mt-1">001/PL</p>
                <p class="text-xs">Cabang: <strong>JAKARTA</strong> &bull; Tanggal: <strong>{{ date('d-m-Y') }}</strong></p>
            </div>

            <table class="w-full text-xs border border-slate-900 mb-6">
                <thead class="bg-slate-100 font-bold border-b border-slate-900">
                    <tr>
                        <th class="p-2 border-r border-slate-900 text-left">No. Collie</th>
                        <th class="p-2 border-r border-slate-900 text-left">Kode</th>
                        <th class="p-2 border-r border-slate-900 text-right">Qty (Box)</th>
                        <th class="p-2 border-r border-slate-900 text-center">Perhitungan (Qty1 X Qty2)</th>
                        <th class="p-2 text-right">Berat (KG)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @foreach($collies->take(6) as $c)
                    <tr>
                        <td class="p-2 border-r border-slate-900 font-mono font-bold">{{ $c->no_col }}</td>
                        <td class="p-2 border-r border-slate-900 font-mono">{{ $c->code }}</td>
                        <td class="p-2 border-r border-slate-900 text-right font-mono font-bold">{{ number_format($c->qty ?? 0) }}</td>
                        <td class="p-2 border-r border-slate-900 text-center font-mono">{{ $c->qty1 }} X {{ $c->qty2 }} =</td>
                        <td class="p-2 text-right font-mono font-bold">{{ number_format($c->qtyr ?? 0, 1) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 bg-slate-100 border-t border-slate-200 flex justify-end space-x-2">
            <button onclick="closePrintPreview()" class="px-4 py-2 bg-slate-600 text-white text-xs font-bold rounded-lg">Tutup</button>
            <button onclick="window.print()" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-lg flex items-center space-x-1">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Dokumen</span>
            </button>
        </div>

    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL INLINE GRID EDIT COLLIE (PERSIS VB6 edit collie.png)      -->
<!-- ============================================================== -->
<div id="modal-edit-grid-collie" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 overflow-hidden">
    <div class="bg-gradient-to-b from-sky-950 via-slate-900 to-sky-950 text-white rounded-2xl shadow-2xl border-2 border-sky-400/60 w-full max-w-[98vw] 2xl:max-w-[1550px] overflow-hidden animate-in fade-in duration-200 flex flex-col my-auto max-h-[96vh]">
        
        <!-- HEADER TOOLBAR MODAL DENGAN TOMBOL SAVE PERSIS SEPERTI GAMBAR (edit collie.png) -->
        <div class="px-5 py-3 bg-sky-900/90 border-b border-sky-600/50 flex flex-wrap items-center justify-between gap-3 shrink-0 select-none">
            
            <!-- Info No PL & Tanggal di Kiri -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-sky-400/20 border border-sky-300/40 flex items-center justify-center text-sky-200">
                    <i class="fa-solid fa-table-cells text-sm"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold text-sm tracking-wide text-white">EDIT PACKING LIST (INLINE GRID)</span>
                        <span id="edit_grid_badge_no_pl" class="px-2.5 py-0.5 rounded text-xs font-mono font-bold bg-amber-400 text-slate-950 shadow-xs">-</span>
                        <span id="edit_grid_badge_tgl" class="px-2 py-0.5 rounded text-[11px] font-sans font-semibold bg-sky-950 text-sky-200 border border-sky-500/30">-</span>
                    </div>
                    <p class="text-[11px] text-sky-300">Edit data baris secara langsung (spreadsheet grid) seperti sistem VB6 aslinya</p>
                </div>
            </div>

            <!-- Tombol SAVE PERSIS SEPERTI VB6 edit collie.png (Ditengah / Menonjol) -->
            <div class="flex items-center space-x-2">
                <button type="button" 
                        id="btn-edit-grid-save"
                        onclick="saveEditGridBatch()"
                        class="px-7 py-2 bg-white hover:bg-slate-100 text-slate-900 rounded-lg border-2 border-sky-400 shadow-lg font-extrabold text-xs flex items-center space-x-2 transition active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-sky-600 text-sm"></i>
                    <span class="tracking-wide">Save</span>
                </button>

                <button type="button" 
                        onclick="addEmptyRowToEditGrid()"
                        class="px-3.5 py-2 bg-sky-800 hover:bg-sky-700 text-sky-100 hover:text-white rounded-lg border border-sky-500/40 text-xs font-bold transition flex items-center space-x-1.5"
                        title="Tambah baris koli baru di bawah No. PL ini">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    <span>+ Baris</span>
                </button>

                <button type="button" 
                        onclick="closeEditGridModal()"
                        class="px-3.5 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-xs font-bold transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    <span>Tutup</span>
                </button>
            </div>

        </div>

        <!-- CONTAINER SPREADSHEET GRID INLINE EDIT -->
        <div class="flex-1 overflow-auto bg-slate-950 p-2">
            <div class="border border-slate-700/80 rounded-xl overflow-hidden shadow-inner bg-white">
                <table class="w-full border-collapse text-left text-xs" id="edit_grid_table">
                    <thead class="sticky top-0 bg-slate-200 text-slate-800 text-[11px] font-bold uppercase tracking-wider border-b-2 border-slate-300 select-none z-10">
                        <tr>
                            <th class="py-2 px-2 border-r border-slate-300 w-10 text-center">#</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-24 text-center">NO_PL</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-36 text-center">NO_SM</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-24 text-center text-amber-800 font-extrabold">NO_COL</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-28 text-center">TGL_COL</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-20 text-center">CABANG</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-24 text-center text-emerald-800 font-extrabold">CODE</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-44">NAMA BARANG</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-16 text-right">QTY</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-16 text-right">QTY1</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-16 text-right">QTY2</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-20 text-right text-amber-800">QTYR</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-16 text-center">AD</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-16 text-center">JUCOL</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-36">EKSPEDISI</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-28 text-center">NO_SPPB</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-28 text-center">TGL_SPPB</th>
                            <th class="py-2 px-2 border-r border-slate-300 w-48">CUSTOMER_NAME</th>
                            <th class="py-2 px-2 w-12 text-center">HAPUS</th>
                        </tr>
                    </thead>
                    <tbody id="edit_grid_tbody" class="divide-y divide-slate-200 text-slate-800 font-mono">
                        <tr>
                            <td colspan="19" class="py-10 text-center text-slate-500 font-sans italic">
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data Packing List untuk diedit...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FOOTER SUMMARY STRIP GRID -->
        <div class="px-5 py-2.5 bg-sky-950 border-t border-sky-600/50 flex flex-wrap items-center justify-between text-xs font-bold gap-3 shrink-0">
            <div class="flex items-center space-x-3 text-sky-200 font-sans">
                <span>Total Baris: <strong id="edit_grid_total_rows" class="text-white font-mono">0</strong> baris</span>
                <span>&bull;</span>
                <span>Total Koli (Qty1): <strong id="edit_grid_total_koli" class="text-amber-300 font-mono">0</strong> box</span>
                <span>&bull;</span>
                <span>Total Qty: <strong id="edit_grid_total_qty" class="text-sky-300 font-mono">0</strong></span>
                <span>&bull;</span>
                <span>Total Berat (Qtyr): <strong id="edit_grid_total_berat" class="text-amber-300 font-mono">0.0</strong> Kg</span>
            </div>
            <div class="text-[11px] text-sky-400 font-normal">
                Tips: Tekan tombol <strong>Save</strong> di atas untuk menyimpan seluruh baris sekaligus.
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    let hasAddedNewItem = false;
    let searchTimeout = null;
    let currentPlLastJucol = 0;

    function openModalInput() {
        document.getElementById('modal-input-collie').classList.remove('hidden');
        hasAddedNewItem = false;
        currentPlLastJucol = 0;
        const currentPl = document.getElementById('modal_no_pl').value.trim();
        const currentTgl = document.getElementById('modal_tgl_col').value;
        if (currentPl) {
            loadExistingCollies(currentPl, currentTgl);
        }
        // Auto fokus ke input code saat modal dibuka
        setTimeout(() => {
            const codeInput = document.getElementById('modal_item_code');
            if (codeInput) {
                codeInput.focus();
                codeInput.select();
            }
        }, 100);
    }

    function onHeaderCollieChange() {
        const currentPl = document.getElementById('modal_no_pl').value.trim();
        const currentTgl = document.getElementById('modal_tgl_col').value;
        loadExistingCollies(currentPl, currentTgl);
    }

    function closeModalInput() {
        document.getElementById('modal-input-collie').classList.add('hidden');
        // Jika ada penambahan koli baru selama modal dibuka, reload halaman agar tabel luar tersinkronisasi
        if (hasAddedNewItem) {
            location.reload();
        }
    }

    function openPrintPreview() {
        document.getElementById('modal-print-preview').classList.remove('hidden');
    }

    function closePrintPreview() {
        document.getElementById('modal-print-preview').classList.add('hidden');
    }

    // Dipanggil saat input Qty1 berubah di modal
    function onModalQty1Change() {
        kalkulasiNoCollieModal();
        kalkulasiTotalQtyModal();
    }

    // Perhitungan otomatis No. Collie (AD s/d JUCOL) berdasarkan QTY1 dan nomor koli terakhir
    function kalkulasiNoCollieModal() {
        const q1Input = document.getElementById('modal_input_qty1');
        const q1 = q1Input ? parseInt(q1Input.value) : 0;
        const startNo = (currentPlLastJucol > 0) ? (currentPlLastJucol + 1) : 1;

        const adInput = document.getElementById('modal_input_ad');
        const jucolInput = document.getElementById('modal_input_jucol');
        const noColInput = document.getElementById('modal_input_no_col');
        const infoEl = document.getElementById('modal_collie_calc_info');

        if (q1 > 0) {
            const ad = startNo;
            const jucol = ad + q1 - 1;
            const noColStr = (ad === jucol) ? String(ad) : `${ad}-${jucol}`;

            if (adInput) adInput.value = ad;
            if (jucolInput) jucolInput.value = jucol;
            if (noColInput) noColInput.value = noColStr;
            if (infoEl) infoEl.textContent = (ad === jucol) ? `(Koli #${ad})` : `(#${ad} s/d #${jucol})`;
        } else {
            // Belum isi Qty1, preview nomor koli awal berikutnya
            if (adInput) adInput.value = startNo;
            if (jucolInput) jucolInput.value = startNo;
            if (noColInput) noColInput.value = String(startNo);
            if (infoEl) infoEl.textContent = `(Mulai dari #${startNo})`;
        }
    }

    function kalkulasiTotalQtyModal() {
        const q1 = parseInt(document.getElementById('modal_input_qty1').value) || 0;
        const q2 = parseInt(document.getElementById('modal_input_qty2').value) || 0;
        document.getElementById('modal_input_total_qty').value = q1 * q2;
    }

    // =========================================================================
    // FITUR LOOKUP KODE BARANG (MIRIP VB6: KETIK CODE LALU ENTER -> NAMA BARANG)
    // =========================================================================

    /**
     * Handler saat tombol keyboard ditekan pada input Code
     */
    function handleCodeKeydown(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Jangan langsung submit form!
            closeAutocomplete();
            lookupItemCode(true); // true = pindahkan fokus ke input berikutnya seperti di VB
        } else if (e.key === 'Escape') {
            closeAutocomplete();
        }
    }

    /**
     * Handler saat input Code kehilangan fokus (blur)
     */
    function handleCodeBlur() {
        setTimeout(() => {
            closeAutocomplete();
            const desc = document.getElementById('modal_item_desc').value.trim();
            const code = document.getElementById('modal_item_code').value.trim();
            // Jika belum di-lookup dan kode ada
            if (code && (!desc || desc.startsWith('❌'))) {
                lookupItemCode(false);
            }
        }, 250);
    }

    /**
     * Autocomplete live search saat mengetik kode atau nama barang
     */
    function handleCodeInput(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        const suggBox = document.getElementById('autocomplete-suggestions');

        if (query.length < 2) {
            closeAutocomplete();
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('api.master-stock.search') }}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.items && data.items.length > 0) {
                        let html = '';
                        data.items.forEach(it => {
                            html += `
                                <div onclick="selectSuggestion('${it.item_code}', '${it.item_desc.replace(/'/g, "\\'")}', '${it.uom || ''}')" 
                                     class="px-3 py-2 hover:bg-sky-800/80 cursor-pointer border-b border-sky-800/40 transition flex items-center justify-between text-[11px]">
                                    <div class="truncate mr-2">
                                        <span class="font-bold text-amber-300">${it.item_code}</span>
                                        <span class="text-white ml-1.5">${it.item_desc}</span>
                                    </div>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-mono bg-sky-950 text-sky-300 shrink-0">${it.uom || it.ext || 'FG'}</span>
                                </div>
                            `;
                        });
                        suggBox.innerHTML = html;
                        suggBox.classList.remove('hidden');
                    } else {
                        closeAutocomplete();
                    }
                })
                .catch(() => closeAutocomplete());
        }, 300);
    }

    function closeAutocomplete() {
        const box = document.getElementById('autocomplete-suggestions');
        if (box) box.classList.add('hidden');
    }

    function selectSuggestion(code, desc, uom) {
        document.getElementById('modal_item_code').value = code;
        document.getElementById('modal_item_desc').value = desc;
        document.getElementById('modal_item_desc').classList.remove('text-rose-400');
        document.getElementById('modal_item_desc').classList.add('text-sky-100');

        const uomBadge = document.getElementById('modal_item_uom');
        if (uom && uomBadge) {
            uomBadge.textContent = 'UOM: ' + uom;
            uomBadge.classList.remove('hidden');
        }
        closeAutocomplete();

        // Pindahkan fokus ke input berikutnya (AD atau Qty1)
        focusNextField();
    }

    /**
     * Mengambil detail barang dari API berdasarkan item_code
     * @param {boolean} moveFocusToNext Jika true, kursor pindah ke field berikutnya (perilaku khas VB saat Enter)
     */
    function lookupItemCode(moveFocusToNext = false) {
        const codeInput = document.getElementById('modal_item_code');
        const descInput = document.getElementById('modal_item_desc');
        const uomBadge  = document.getElementById('modal_item_uom');
        const statusBadge = document.getElementById('modal_item_status');

        const code = codeInput.value.trim().toUpperCase();
        if (!code) {
            descInput.value = '';
            if (uomBadge) uomBadge.classList.add('hidden');
            if (statusBadge) statusBadge.classList.add('hidden');
            return;
        }

        codeInput.value = code; // Normalisasi ke uppercase
        descInput.value = 'Mencari nama barang di Master Stock...';
        descInput.classList.remove('text-rose-400');
        descInput.classList.add('text-sky-300');

        fetch(`{{ url('/api/item-stock') }}/${encodeURIComponent(code)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.item) {
                    descInput.value = data.item.item_desc;
                    descInput.classList.remove('text-sky-300', 'text-rose-400');
                    descInput.classList.add('text-sky-100');

                    if (uomBadge) {
                        uomBadge.textContent = data.item.uom ? `UOM: ${data.item.uom}` : (data.item.ext || '');
                        uomBadge.classList.remove('hidden');
                    }

                    if (statusBadge && data.item.jenis_bhn) {
                        statusBadge.textContent = data.item.jenis_bhn;
                        statusBadge.classList.remove('hidden');
                    }

                    // Pindah fokus ke input berikutnya seperti di VB (AD atau Qty1)
                    if (moveFocusToNext) {
                        focusNextField();
                    }
                } else {
                    descInput.value = `❌ Kode '${code}' tidak ditemukan di Master Stock (otto_master_stock_v)`;
                    descInput.classList.remove('text-sky-300', 'text-sky-100');
                    descInput.classList.add('text-rose-400');

                    if (uomBadge) uomBadge.classList.add('hidden');
                    if (statusBadge) statusBadge.classList.add('hidden');

                    codeInput.focus();
                    codeInput.select();
                }
            })
            .catch(err => {
                console.error(err);
                descInput.value = '❌ Terjadi kesalahan saat memeriksa kode barang.';
                descInput.classList.add('text-rose-400');
            });
    }

    /**
     * Memindahkan fokus ke field input berikutnya setelah kode barang ditemukan
     */
    function focusNextField() {
        const qty1Input = document.getElementById('modal_input_qty1');
        if (qty1Input) {
            qty1Input.focus();
            qty1Input.select();
        }
    }

    // =========================================================================
    // LOAD & RENDER TABEL MODAL
    // =========================================================================

    // Memuat daftar koli yang sudah ada di database untuk No. PL dan Tanggal tertentu
    function loadExistingCollies(noPl, tglCol) {
        if (!noPl) {
            renderModalTable([]);
            document.getElementById('modal_counter_koli').textContent = '0';
            document.getElementById('modal_counter_berat').textContent = '0.0';
            currentPlLastJucol = 0;
            kalkulasiNoCollieModal();
            return;
        }

        if (typeof tglCol === 'undefined' || tglCol === null) {
            tglCol = document.getElementById('modal_tgl_col')?.value || '';
        }

        let url = `{{ route('api.pl-collies') }}?no_pl=${encodeURIComponent(noPl)}`;
        if (tglCol) {
            url += `&tgl_col=${encodeURIComponent(tglCol)}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderModalTable(data.items);
                    document.getElementById('modal_counter_koli').textContent = parseNum(data.total_koli).toLocaleString();
                    document.getElementById('modal_counter_berat').textContent = parseNum(data.total_berat).toLocaleString(undefined, {minimumFractionDigits: 1, maximumFractionDigits: 1});

                    // Ambil nomor jucol tertinggi yang sudah diinput untuk No. PL ini
                    let maxJucol = 0;
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(it => {
                            let j = parseInt(it.jucol) || 0;
                            if (!j && it.no_col) {
                                const parts = String(it.no_col).split('-');
                                j = parseInt(parts[parts.length - 1]) || 0;
                            }
                            if (j > maxJucol) maxJucol = j;
                        });
                    }
                    if (typeof data.last_jucol !== 'undefined' && data.last_jucol !== null) {
                        currentPlLastJucol = Math.max(parseInt(data.last_jucol) || 0, maxJucol);
                    } else {
                        currentPlLastJucol = maxJucol;
                    }

                    // Sinkronkan tampilan nomor koli awal/preview
                    kalkulasiNoCollieModal();
                }
            })
            .catch(err => console.error(err));
    }

    function parseNum(val) {
        if (val === null || val === undefined || val === '') return 0;
        if (typeof val === 'number') return val;
        const clean = String(val).replace(/,/g, '');
        const num = parseFloat(clean);
        return isNaN(num) ? 0 : num;
    }

    // Render baris ke tabel di bawah modal (menyertakan kolom NAMA BARANG)
    function renderModalTable(items) {
        const tbody = document.getElementById('modal_items_tbody');
        if (!items || items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="11" class="py-4 text-center text-slate-400 font-sans italic">Belum ada baris collie untuk No. PL ini pada tanggal yang dipilih. Silakan input pada form di atas.</td></tr>`;
            return;
        }

        let html = '';
        items.forEach((it, idx) => {
            html += `
                <tr class="hover:bg-sky-900/40 transition">
                    <td class="py-1.5 px-3 text-center text-slate-400 font-sans">${idx + 1}</td>
                    <td class="py-1.5 px-3 font-bold text-emerald-300 font-mono">${it.code || '-'}</td>
                    <td class="py-1.5 px-3 font-sans text-sky-100 text-[11px] max-w-xs truncate" title="${it.nama || ''}">
                        ${it.nama || '-'}
                    </td>
                    <td class="py-1.5 px-3 font-bold text-amber-300 bg-sky-950/50">${it.no_col || '-'}</td>
                    <td class="py-1.5 px-3 text-center">${it.qty1 ?? 0}</td>
                    <td class="py-1.5 px-3 text-center">${it.qty2 ?? 0}</td>
                    <td class="py-1.5 px-3 text-right font-bold text-white">${parseNum(it.qty).toLocaleString()}</td>
                    <td class="py-1.5 px-3 text-right font-bold text-amber-300">${parseNum(it.qtyr).toLocaleString(undefined, {minimumFractionDigits: 1, maximumFractionDigits: 1})}</td>
                    <td class="py-1.5 px-3 text-center font-bold text-sky-200">${it.ad || it.no_col || '-'}</td>
                    <td class="py-1.5 px-3 text-slate-300 font-sans text-[11px]">${it.no_sm || '-'}</td>
                    <td class="py-1.5 px-2 text-center">
                        <button type="button" onclick="hapusCollieAjax(${it.id})" class="text-rose-400 hover:text-rose-300 p-1" title="Hapus"><i class="fa-regular fa-trash-can"></i></button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    // Submit form koli via AJAX (Menyimpan langsung tanpa reload & menambah baris ke tabel bawah)
    function submitCollieAjax(e) {
        e.preventDefault();
        const form = document.getElementById('form-ajax-collie');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('btn-modal-simpan');

        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-xs"></i> Menyimpan...`;

        fetch(`{{ route('collie.store') }}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="fa-solid fa-floppy-disk text-xs"></i> Simpan`;

            if (data.success) {
                hasAddedNewItem = true;
                
                // Muat ulang daftar koli di No. PL dan Tanggal ini agar counter dan list langsung terupdate
                const noPl = document.getElementById('modal_no_pl').value;
                const tglCol = document.getElementById('modal_tgl_col').value;
                loadExistingCollies(noPl, tglCol);

                // Bersihkan field item & nama barang agar siap input koli berikutnya
                document.getElementById('modal_item_code').value = '';
                document.getElementById('modal_item_desc').value = '';
                const uomBadge = document.getElementById('modal_item_uom');
                if (uomBadge) uomBadge.classList.add('hidden');
                const statusBadge = document.getElementById('modal_item_status');
                if (statusBadge) statusBadge.classList.add('hidden');

                document.getElementById('modal_input_qty1').value = '';
                document.getElementById('modal_input_qty2').value = '';
                document.getElementById('modal_input_total_qty').value = '';
                document.getElementById('modal_item_qtyr').value = '';

                // Fokus kembali ke input kode obat untuk input koli berikutnya (kecepatan entry data)
                document.getElementById('modal_item_code').focus();
            } else {
                alert('Gagal menyimpan: ' + (data.message || 'Periksa kelengkapan input'));
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="fa-solid fa-floppy-disk text-xs"></i> Simpan`;
            console.error(err);
            alert('Terjadi kesalahan saat menyimpan data koli.');
        });
    }

    // Hapus koli via AJAX dari dalam modal
    function hapusCollieAjax(id) {
        if (!confirm('Hapus baris koli ini?')) return;

        fetch(`{{ url('/collie') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                hasAddedNewItem = true;
                const noPl = document.getElementById('modal_no_pl').value;
                const tglCol = document.getElementById('modal_tgl_col').value;
                loadExistingCollies(noPl, tglCol);
            }
        })
        .catch(err => console.error(err));
    }

    // =========================================================================
    // FITUR INLINE GRID EDIT COLLIE (PERSIS VB6 edit collie.png)
    // =========================================================================
    let selectedCollieRow = null;
    let selectedNoPl = '';
    let selectedTglCol = '';

    // Memilih baris di tabel utama (single selection)
    function selectCollieRow(tr, noPl, tglCol) {
        // Lepas highlight dari baris sebelumnya
        if (selectedCollieRow) {
            selectedCollieRow.classList.remove('bg-amber-100/90', 'font-semibold', 'ring-2', 'ring-amber-400/80');
        }

        // Highlight baris baru terpilih
        selectedCollieRow = tr;
        selectedNoPl = noPl;
        selectedTglCol = tglCol;
        tr.classList.add('bg-amber-100/90', 'font-semibold', 'ring-2', 'ring-amber-400/80');

        // Aktifkan tombol Edit di toolbar atas
        const editBtn = document.getElementById('btn-edit-collie-toolbar');
        const editText = document.getElementById('btn-edit-collie-text');
        if (editBtn) {
            editBtn.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed', 'border-slate-200');
            editBtn.classList.add('bg-gradient-to-r', 'from-amber-500', 'to-amber-600', 'hover:from-amber-400', 'hover:to-amber-500', 'text-white', 'shadow-md', 'cursor-pointer');
            editText.textContent = `EDIT COLLIE (${noPl || 'PL'})`;
            editBtn.title = `Klik untuk edit semua baris No. PL ${noPl} secara inline`;
        }
    }

    // Klik tombol Edit di toolbar atas
    function triggerEditFromToolbar() {
        if (!selectedNoPl) {
            alert('Silakan klik salah satu baris data pada tabel terlebih dahulu untuk memilih Packing List yang akan diedit.');
            return;
        }
        openEditGridModal(selectedNoPl, selectedTglCol);
    }

    // Buka Modal Spreadsheet Inline Edit
    function openEditGridModal(noPl, tglCol = '') {
        if (!noPl) return;
        selectedNoPl = noPl;
        selectedTglCol = tglCol;

        document.getElementById('edit_grid_badge_no_pl').textContent = noPl;
        document.getElementById('edit_grid_badge_tgl').textContent = tglCol ? formatDateIndo(tglCol) : '-';
        document.getElementById('modal-edit-grid-collie').classList.remove('hidden');

        const tbody = document.getElementById('edit_grid_tbody');
        tbody.innerHTML = `<tr><td colspan="19" class="py-10 text-center text-slate-500 font-sans italic"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data baris Packing List ${noPl}...</td></tr>`;

        let url = `{{ route('api.pl-collies') }}?no_pl=${encodeURIComponent(noPl)}`;
        if (tglCol) {
            url += `&tgl_col=${encodeURIComponent(tglCol)}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderEditGridRows(data.items, noPl, tglCol);
                } else {
                    tbody.innerHTML = `<tr><td colspan="19" class="py-6 text-center text-rose-500 font-sans">Gagal memuat data: ${data.message || 'Terjadi kesalahan'}</td></tr>`;
                }
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="19" class="py-6 text-center text-rose-500 font-sans">Terjadi kesalahan saat memuat data.</td></tr>`;
            });
    }

    function closeEditGridModal() {
        document.getElementById('modal-edit-grid-collie').classList.add('hidden');
    }

    // Render baris ke dalam Spreadsheet Grid Edit
    function renderEditGridRows(items, defaultPl, defaultTgl) {
        const tbody = document.getElementById('edit_grid_tbody');
        if (!items || items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="19" class="py-8 text-center text-slate-400 font-sans italic">Belum ada baris data untuk Packing List ini. Klik tombol <strong>+ Baris</strong> untuk menambah baris baru.</td></tr>`;
            recalcGridSummary();
            return;
        }

        let html = '';
        items.forEach((it, idx) => {
            html += createGridRowHtml(it, idx + 1);
        });
        tbody.innerHTML = html;
        recalcGridSummary();
    }

    // Template HTML untuk setiap baris di spreadsheet edit
    function createGridRowHtml(it, rowNum) {
        const idVal = it.id || '';
        return `
            <tr class="grid-edit-row hover:bg-sky-50 transition border-b border-slate-200" data-id="${idVal}">
                <td class="p-1 text-center text-slate-400 font-sans text-[11px] row-number select-none">${rowNum}</td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="text" data-field="no_pl" value="${it.no_pl || selectedNoPl}" class="w-full px-1.5 py-1 text-xs font-mono font-bold text-sky-800 bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="text" data-field="no_sm" value="${it.no_sm || ''}" placeholder="NO_SM..." class="w-full px-1.5 py-1 text-xs font-mono bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="text" data-field="no_col" value="${it.no_col || ''}" class="w-full px-1.5 py-1 text-xs font-mono font-bold text-slate-900 bg-amber-50/70 focus:bg-white focus:ring-1 focus:ring-amber-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="date" data-field="tgl_col" value="${it.tgl_col || selectedTglCol}" class="w-full px-1 py-1 text-xs font-sans bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="text" data-field="cabang" value="${it.cabang || 'JKT0'}" class="w-full px-1.5 py-1 text-xs font-mono font-bold uppercase text-center bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="text" data-field="code" value="${it.code || ''}" onchange="onRowCodeChange(this)" class="w-full px-1.5 py-1 text-xs font-mono font-bold text-emerald-700 bg-transparent focus:bg-white focus:ring-1 focus:ring-emerald-500 rounded border border-transparent hover:border-slate-300 uppercase">
                </td>
                <td class="p-0.5 border-r border-slate-200 font-sans text-[11px] text-slate-700 max-w-[180px] truncate px-1.5 row-item-name" title="${it.nama || ''}">
                    ${it.nama || '-'}
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="number" data-field="qty" value="${it.qty ?? 0}" class="w-full px-1.5 py-1 text-xs font-mono font-bold text-right text-slate-900 bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300" oninput="recalcGridSummary()">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="number" data-field="qty1" value="${it.qty1 ?? 0}" oninput="onRowQtyChange(this)" class="w-full px-1.5 py-1 text-xs font-mono text-right bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="number" data-field="qty2" value="${it.qty2 ?? 0}" oninput="onRowQtyChange(this)" class="w-full px-1.5 py-1 text-xs font-mono text-right bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="number" step="0.1" data-field="qtyr" value="${it.qtyr ?? 0}" oninput="recalcGridSummary()" class="w-full px-1.5 py-1 text-xs font-mono font-bold text-right text-amber-700 bg-transparent focus:bg-white focus:ring-1 focus:ring-amber-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="number" data-field="ad" value="${it.ad || ''}" oninput="onRowAdJucolChange(this)" class="w-full px-1.5 py-1 text-xs font-mono text-center bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="number" data-field="jucol" value="${it.jucol || ''}" oninput="onRowAdJucolChange(this)" class="w-full px-1.5 py-1 text-xs font-mono text-center bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="text" data-field="ekspedisi" value="${it.ekspedisi || ''}" placeholder="Ekspedisi..." class="w-full px-1.5 py-1 text-xs font-sans bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="text" data-field="no_sppb" value="${it.no_sppb || ''}" placeholder="SPPB..." class="w-full px-1.5 py-1 text-xs font-mono text-center bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="date" data-field="tgl_sppb" value="${it.tgl_sppb || ''}" class="w-full px-1 py-1 text-xs font-sans bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-0.5 border-r border-slate-200">
                    <input type="text" data-field="customer_name" value="${it.customer_name || ''}" placeholder="Customer..." class="w-full px-1.5 py-1 text-xs font-sans bg-transparent focus:bg-white focus:ring-1 focus:ring-sky-500 rounded border border-transparent hover:border-slate-300">
                </td>
                <td class="p-1 text-center">
                    <button type="button" onclick="deleteGridRow(this, '${idVal}')" class="text-rose-500 hover:text-rose-700 p-1" title="Hapus baris ini">
                        <i class="fa-regular fa-trash-can text-xs"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    // Tambah baris kosong baru ke tabel grid edit
    function addEmptyRowToEditGrid() {
        const tbody = document.getElementById('edit_grid_tbody');
        const rows = tbody.querySelectorAll('tr.grid-edit-row');
        const nextNum = rows.length + 1;

        // Ambil data acuan dari baris terakhir (misal nomor AD berikutnya, tanggal, cabang)
        let lastAd = 0;
        let lastJucol = 0;
        let lastEkspedisi = '';
        let lastSppb = '';
        let lastTglSppb = '';
        let lastCust = '';

        if (rows.length > 0) {
            const lastRow = rows[rows.length - 1];
            lastAd = parseInt(lastRow.querySelector('[data-field="ad"]')?.value) || 0;
            lastJucol = parseInt(lastRow.querySelector('[data-field="jucol"]')?.value) || 0;
            lastEkspedisi = lastRow.querySelector('[data-field="ekspedisi"]')?.value || '';
            lastSppb = lastRow.querySelector('[data-field="no_sppb"]')?.value || '';
            lastTglSppb = lastRow.querySelector('[data-field="tgl_sppb"]')?.value || '';
            lastCust = lastRow.querySelector('[data-field="customer_name"]')?.value || '';
        }

        const nextAd = (lastJucol > 0) ? (lastJucol + 1) : (lastAd > 0 ? (lastAd + 1) : 1);

        const newRowItem = {
            id: '',
            no_pl: selectedNoPl,
            no_sm: '',
            no_col: String(nextAd),
            tgl_col: selectedTglCol || new Date().toISOString().split('T')[0],
            cabang: 'JKT0',
            code: '',
            nama: '-',
            qty: 0,
            qty1: 1,
            qty2: 1,
            qtyr: 0,
            ad: nextAd,
            jucol: nextAd,
            ekspedisi: lastEkspedisi,
            no_sppb: lastSppb,
            tgl_sppb: lastTglSppb,
            customer_name: lastCust,
        };

        const tempTr = document.createElement('tbody');
        tempTr.innerHTML = createGridRowHtml(newRowItem, nextNum);
        const newTr = tempTr.firstElementChild;
        tbody.appendChild(newTr);

        recalcGridSummary();

        // Fokus ke input code pada baris baru
        newTr.querySelector('[data-field="code"]')?.focus();
    }

    // Hapus baris dari grid
    function deleteGridRow(btn, id) {
        if (!confirm('Hapus baris koli ini?')) return;
        const row = btn.closest('tr');

        if (id && !String(id).startsWith('new_')) {
            // Hapus di server via AJAX
            fetch(`{{ url('/collie') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    renumberGridRows();
                    recalcGridSummary();
                } else {
                    alert('Gagal menghapus baris di server');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat menghapus data.');
            });
        } else {
            // Baris baru yang belum tersimpan di DB
            row.remove();
            renumberGridRows();
            recalcGridSummary();
        }
    }

    // Nomor urut baris diperbarui
    function renumberGridRows() {
        const rows = document.querySelectorAll('#edit_grid_tbody tr.grid-edit-row');
        rows.forEach((r, i) => {
            const numEl = r.querySelector('.row-number');
            if (numEl) numEl.textContent = i + 1;
        });
    }

    // Perhitungan otomatis saat AD / JUCOL berubah pada baris
    function onRowAdJucolChange(input) {
        const row = input.closest('tr');
        const ad = parseInt(row.querySelector('[data-field="ad"]')?.value) || 0;
        const jucol = parseInt(row.querySelector('[data-field="jucol"]')?.value) || 0;

        if (ad > 0 && jucol >= ad) {
            const countBox = jucol - ad + 1;
            const noColStr = (ad === jucol) ? String(ad) : `${ad}-${jucol}`;
            
            const noColInput = row.querySelector('[data-field="no_col"]');
            if (noColInput) noColInput.value = noColStr;

            const qty1Input = row.querySelector('[data-field="qty1"]');
            if (qty1Input) qty1Input.value = countBox;

            // Recalculate Total QTY = QTY1 * QTY2
            const qty2 = parseInt(row.querySelector('[data-field="qty2"]')?.value) || 0;
            const qtyInput = row.querySelector('[data-field="qty"]');
            if (qtyInput) qtyInput.value = countBox * qty2;
        }

        recalcGridSummary();
    }

    // Perhitungan otomatis saat QTY1 atau QTY2 berubah
    function onRowQtyChange(input) {
        const row = input.closest('tr');
        const q1 = parseInt(row.querySelector('[data-field="qty1"]')?.value) || 0;
        const q2 = parseInt(row.querySelector('[data-field="qty2"]')?.value) || 0;

        const qtyInput = row.querySelector('[data-field="qty"]');
        if (qtyInput) qtyInput.value = q1 * q2;

        // Jika perubahan terjadi pada QTY1, sesuaikan JUCOL dan NO_COL baris tersebut secara otomatis
        if (input.getAttribute('data-field') === 'qty1' && q1 > 0) {
            const adInput = row.querySelector('[data-field="ad"]');
            const jucolInput = row.querySelector('[data-field="jucol"]');
            const noColInput = row.querySelector('[data-field="no_col"]');
            
            let ad = parseInt(adInput?.value) || 0;
            if (ad <= 0) {
                const prevRow = row.previousElementSibling;
                const prevJucol = prevRow ? (parseInt(prevRow.querySelector('[data-field="jucol"]')?.value) || 0) : 0;
                ad = prevJucol + 1;
                if (adInput) adInput.value = ad;
            }
            const jucol = ad + q1 - 1;
            if (jucolInput) jucolInput.value = jucol;
            if (noColInput) noColInput.value = (ad === jucol) ? String(ad) : `${ad}-${jucol}`;
        }

        recalcGridSummary();
    }

    // Lookup nama barang saat CODE diubah di grid
    function onRowCodeChange(input) {
        const row = input.closest('tr');
        const code = input.value.trim().toUpperCase();
        input.value = code;
        const nameCell = row.querySelector('.row-item-name');

        if (!code) {
            if (nameCell) nameCell.textContent = '-';
            return;
        }

        if (nameCell) nameCell.textContent = 'Mencari...';

        fetch(`{{ url('/api/item-stock') }}/${encodeURIComponent(code)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.item) {
                    if (nameCell) {
                        nameCell.textContent = data.item.item_desc;
                        nameCell.title = data.item.item_desc;
                    }
                } else {
                    if (nameCell) nameCell.textContent = `❌ Tidak ditemukan (${code})`;
                }
            })
            .catch(() => {
                if (nameCell) nameCell.textContent = '-';
            });
    }

    // Hitung ulang total footer baris pada grid
    function recalcGridSummary() {
        const rows = document.querySelectorAll('#edit_grid_tbody tr.grid-edit-row');
        let totalRows = rows.length;
        let totalKoli = 0;
        let totalQty = 0;
        let totalBerat = 0;

        rows.forEach(r => {
            const q1 = parseInt(r.querySelector('[data-field="qty1"]')?.value) || 0;
            const q = parseInt(r.querySelector('[data-field="qty"]')?.value) || 0;
            const qr = parseFloat(r.querySelector('[data-field="qtyr"]')?.value) || 0;

            totalKoli += q1;
            totalQty += q;
            totalBerat += qr;
        });

        document.getElementById('edit_grid_total_rows').textContent = totalRows.toLocaleString();
        document.getElementById('edit_grid_total_koli').textContent = totalKoli.toLocaleString();
        document.getElementById('edit_grid_total_qty').textContent = totalQty.toLocaleString();
        document.getElementById('edit_grid_total_berat').textContent = totalBerat.toLocaleString(undefined, {minimumFractionDigits: 1, maximumFractionDigits: 1});
    }

    // Simpan semua baris sekaligus ke server (Batch Update)
    function saveEditGridBatch() {
        const rows = document.querySelectorAll('#edit_grid_tbody tr.grid-edit-row');
        if (rows.length === 0) {
            alert('Tidak ada baris data untuk disimpan.');
            return;
        }

        const items = [];
        rows.forEach(tr => {
            const getVal = (name) => {
                const el = tr.querySelector(`[data-field="${name}"]`);
                return el ? el.value.trim() : '';
            };

            items.push({
                id: tr.dataset.id || null,
                no_pl: getVal('no_pl'),
                no_sm: getVal('no_sm'),
                no_col: getVal('no_col'),
                tgl_col: getVal('tgl_col'),
                cabang: getVal('cabang'),
                code: getVal('code'),
                qty: getVal('qty'),
                qty1: getVal('qty1'),
                qty2: getVal('qty2'),
                qtyr: getVal('qtyr'),
                ad: getVal('ad'),
                jucol: getVal('jucol'),
                ekspedisi: getVal('ekspedisi'),
                no_sppb: getVal('no_sppb'),
                tgl_sppb: getVal('tgl_sppb'),
                customer_name: getVal('customer_name'),
            });
        });

        const saveBtn = document.getElementById('btn-edit-grid-save');
        saveBtn.disabled = true;
        saveBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-sky-600 text-sm"></i> <span>Menyimpan...</span>`;

        fetch(`{{ route('collie.batch-update') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items: items })
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `<i class="fa-solid fa-floppy-disk text-sky-600 text-sm"></i> <span>Save</span>`;

            if (data.success) {
                alert('Data Packing List berhasil disimpan!');
                closeEditGridModal();
                location.reload();
            } else {
                alert('Gagal menyimpan: ' + (data.message || 'Periksa kelengkapan input'));
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `<i class="fa-solid fa-floppy-disk text-sky-600 text-sm"></i> <span>Save</span>`;
            console.error(err);
            alert('Terjadi kesalahan koneksi saat menyimpan perubahan.');
        });
    }

    // Helper format tanggal DD/MM/YYYY
    function formatDateIndo(dateStr) {
        if (!dateStr) return '-';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        return dateStr;
    }
</script>


@endpush

