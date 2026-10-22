@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi Pengiriman Logistik')

@push('styles')
<style>
    @media print {
        header, footer, .no-print {
            display: none !important;
        }
        body {
            background: white !important;
            color: black !important;
            font-size: 10pt;
        }
        main {
            padding: 0 !important;
            margin: 0 !important;
            overflow: visible !important;
        }
        .print-only {
            display: block !important;
        }
        .table-print th, .table-print td {
            border: 1px solid #333 !important;
            padding: 4px 6px !important;
            white-space: normal !important;
        }
    }
    .print-only {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="flex-1 flex flex-col space-y-2 min-h-0">

    <!-- ============================================================== -->
    <!-- HEADER KHUSUS CETAK DOKUMEN RESMI (HANYA MUNCUL SAAT PRINT)    -->
    <!-- ============================================================== -->
    <div class="print-only mb-3 pb-2 border-b-2 border-slate-900">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-base font-extrabold uppercase">PT. OTTO PHARMACEUTICAL INDUSTRIES LTD BANDUNG</h1>
                <h2 class="text-sm font-bold text-slate-800">LAPORAN REKAPITULASI PACKING LIST & SPPB (LOGISTIK)</h2>
                <p class="text-[11px] text-slate-600">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}</p>
            </div>
            <div class="text-right text-[10px] text-slate-500">
                <p>Dokumen: FM.LG.C-P.001.001.01</p>
                <p>Dicetak: {{ date('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- CARD TOOLBAR & FILTER LAPORAN                                  -->
    <!-- ============================================================== -->
    <div class="no-print bg-white rounded-xl shadow-sm border border-slate-200/90 p-2.5 px-3 shrink-0">
        
        <!-- Baris Atas: Judul, Ringkasan Badge, dan Tombol Cetak / Export -->
        <div class="flex flex-wrap items-center justify-between pb-2 mb-2 border-b border-slate-100 gap-2">
            
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-sky-600 text-white flex items-center justify-center shadow-xs text-xs">
                    <i class="fa-solid fa-chart-column"></i>
                </div>
                <div>
                    <h1 class="text-sm font-extrabold text-slate-800 tracking-tight leading-none">LAPORAN & REKAPITULASI LOGISTIK</h1>
                    <span class="text-[10px] text-slate-400 font-medium">Rekapitulasi Pengiriman Packing List & SPPB</span>
                </div>
            </div>

            <!-- Ringkasan Statistik Hasil Filter (Inline Badge) -->
            <div class="flex flex-wrap items-center gap-1.5 text-xs">
                <div class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded-lg text-slate-700">
                    <span class="text-slate-500">Total:</span>
                    <strong class="text-slate-900 ml-1">{{ number_format($summary->total_pl ?? 0) }}</strong> PL
                </div>
                <div class="px-2.5 py-1 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800">
                    <span class="text-emerald-600">SPPB Terbit:</span>
                    <strong class="text-emerald-950 ml-1">{{ number_format($summary->total_sppb_terbit ?? 0) }}</strong>
                </div>
                <div class="px-2.5 py-1 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-800">
                    <span class="text-indigo-600">Koli:</span>
                    <strong class="text-indigo-950 ml-1">{{ number_format($summary->grand_total_collie ?? 0) }}</strong> Box
                </div>
                <div class="px-2.5 py-1 bg-violet-50 border border-violet-200 rounded-lg text-violet-800">
                    <span class="text-violet-600">Tonase:</span>
                    <strong class="text-violet-950 ml-1">{{ number_format($summary->grand_total_berat ?? 0, 1) }}</strong> Kg
                </div>
            </div>

            <!-- Tombol Cetak & Export -->
            <div class="flex items-center space-x-1.5">
                <button type="button" onclick="window.print()" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center space-x-1 active:scale-95">
                    <i class="fa-solid fa-print text-[11px] text-sky-400"></i>
                    <span>Cetak</span>
                </button>

                <a href="{{ route('report.export-excel', request()->query()) }}" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center space-x-1.5 active:scale-95" title="Export Seluruh Data Hasil Filter ke Format Microsoft Excel (.xlsx)">
                    <i class="fa-solid fa-file-excel text-[12px] text-emerald-200"></i>
                    <span>Export Excel</span>
                </a>
            </div>
        </div>

        <!-- Baris Bawah: Form Filter Flex Terpadu (Anti-Break) -->
        <form method="GET" action="{{ route('report.index') }}" class="flex flex-wrap items-center gap-2 text-xs">
            
            <!-- Periode Tanggal -->
            <div class="flex items-center space-x-1 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
                <label class="text-slate-500 font-semibold text-[11px] shrink-0">Periode:</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-transparent font-medium text-slate-800 text-xs focus:outline-none w-28">
                <span class="text-slate-400 font-bold">-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-transparent font-medium text-slate-800 text-xs focus:outline-none w-28">
            </div>

            <!-- Filter Ekspedisi -->
            <div class="flex items-center space-x-1 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 min-w-0">
                <label class="text-slate-500 font-semibold text-[11px] shrink-0">Ekspedisi:</label>
                <select name="ekspedisi" class="bg-transparent font-semibold text-slate-800 text-xs focus:outline-none max-w-[180px] cursor-pointer">
                    <option value="">Semua Ekspedisi</option>
                    @foreach($ekspedisiOptions as $eks)
                        <option value="{{ $eks }}" {{ $ekspedisi == $eks ? 'selected' : '' }}>{{ $eks }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Cabang -->
            <div class="flex items-center space-x-1 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
                <label class="text-slate-500 font-semibold text-[11px] shrink-0">Cabang:</label>
                <select name="cabang" class="bg-transparent font-semibold text-slate-800 text-xs focus:outline-none cursor-pointer">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangOptions as $cab)
                        <option value="{{ $cab }}" {{ $cabang == $cab ? 'selected' : '' }}>{{ $cab }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="flex items-center space-x-1 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
                <label class="text-slate-500 font-semibold text-[11px] shrink-0">Status:</label>
                <select name="status" class="bg-transparent font-semibold text-slate-800 text-xs focus:outline-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="sudah" {{ $status == 'sudah' ? 'selected' : '' }}>Sudah Terbit SPPB</option>
                    <option value="belum" {{ $status == 'belum' ? 'selected' : '' }}>Belum Terbit SPPB</option>
                </select>
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="flex items-center space-x-1.5 ml-auto">
                <button type="submit" class="px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg text-xs shadow-xs transition flex items-center space-x-1 active:scale-95">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('report.index') }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg border border-slate-200 text-xs font-semibold flex items-center space-x-1" title="Reset Filter">
                    <i class="fa-solid fa-rotate text-[10px]"></i>
                    <span>Reset</span>
                </a>
            </div>

        </form>

    </div>

    <!-- ============================================================== -->
    <!-- TABEL REKAPITULASI DATA LAPORAN (STRUCTURE MIRROR COLLIE/SPPB) -->
    <!-- ============================================================== -->
    <div class="flex-1 bg-white rounded-xl shadow-sm border border-slate-200/90 flex flex-col min-h-0 overflow-hidden">
        
        <!-- Table Scroll Container -->
        <div class="flex-1 overflow-auto">
            <table id="report-table" class="w-full text-left border-collapse table-condensed text-[12px] whitespace-nowrap table-print">
                <thead class="sticky top-0 bg-slate-800 text-white z-10 shadow-sm text-[11px] uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-2 px-3 border-r border-slate-700 w-10 text-center">#</th>
                        <th class="py-2 px-3 border-r border-slate-700 font-bold text-sky-300">NO_PL</th>
                        <th class="py-2 px-3 border-r border-slate-700">TGL_COL</th>
                        <th class="py-2 px-3 border-r border-slate-700 font-bold text-amber-300">NO_SPPB</th>
                        <th class="py-2 px-3 border-r border-slate-700">TGL_SPPB</th>
                        <th class="py-2 px-3 border-r border-slate-700">CABANG</th>
                        <th class="py-2 px-3 border-r border-slate-700">EKSPEDISI</th>
                        <th class="py-2 px-3 border-r border-slate-700">CUSTOMER / PENERIMA</th>
                        <th class="py-2 px-3 border-r border-slate-700 text-center">JML SM</th>
                        <th class="py-2 px-3 border-r border-slate-700 text-right">TOTAL KOLI</th>
                        <th class="py-2 px-3 border-r border-slate-700 text-right font-bold text-amber-300">BERAT (KG)</th>
                        <th class="py-2 px-3 text-center no-print">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium font-mono text-slate-700">
                    @forelse($reportData as $index => $row)
                        <tr class="hover:bg-sky-50/70 transition-colors">
                            <td class="py-1 px-3 text-center text-slate-400 text-[11px]">
                                {{ $reportData->firstItem() + $index }}
                            </td>
                            <td class="py-1 px-3 font-bold text-slate-900">
                                <a href="{{ route('collie.index') }}?no_pl={{ urlencode($row->no_pl) }}" class="hover:text-sky-600 hover:underline">
                                    {{ $row->no_pl }}
                                </a>
                            </td>
                            <td class="py-1 px-3 text-slate-600 text-[11px]">
                                {{ $row->tgl_col ? date('d/m/Y', strtotime($row->tgl_col)) : '-' }}
                            </td>
                            <td class="py-1 px-3 font-bold text-sky-800">
                                @if(!empty($row->no_sppb))
                                    <a href="{{ route('sppb.index') }}?search={{ urlencode($row->no_sppb) }}" class="hover:text-sky-600 hover:underline">
                                        {{ $row->no_sppb }}
                                    </a>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="py-1 px-3 text-slate-600 text-[11px]">
                                {{ $row->tgl_sppb ? date('d/m/Y', strtotime($row->tgl_sppb)) : '-' }}
                            </td>
                            <td class="py-1 px-3 font-semibold text-slate-700 text-[11px]">
                                {{ $row->cabang ?: 'JKT0' }}
                            </td>
                            <td class="py-1 px-3 font-medium text-slate-800 text-[11px]">
                                {{ $row->ekspedisi ?: '-' }}
                            </td>
                            <td class="py-1 px-3 font-sans text-slate-800 truncate max-w-xs" title="{{ $row->customer_name }}">
                                {{ $row->customer_name ?: '-' }}
                            </td>
                            <td class="py-1 px-3 text-center">
                                {{ $row->total_sm }}
                            </td>
                            <td class="py-1 px-3 text-right font-bold text-slate-900">
                                {{ number_format($row->total_collie) }}
                            </td>
                            <td class="py-1 px-3 text-right font-bold text-slate-800">
                                {{ number_format($row->total_berat, 1) }}
                            </td>
                            <td class="py-1 px-3 text-center no-print font-sans">
                                @if(!empty($row->no_sppb))
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        SPPB Terbit
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Draft PL
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-12 text-center text-slate-400 font-sans">
                                <i class="fa-solid fa-filter-circle-xmark text-3xl mb-2 block text-slate-300"></i>
                                Tidak ada data laporan yang sesuai dengan kriteria filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Total Baris (Normal Tfoot tanpa sticky bottom agar tidak melayang saat data sedikit) -->
                @if($reportData->count() > 0)
                <tfoot class="bg-slate-100 text-slate-900 font-extrabold border-t-2 border-slate-300 text-xs">
                    <tr>
                        <td colspan="9" class="py-1.5 px-3 text-right uppercase tracking-wider text-[11px]">
                            Subtotal (Halaman Ini):
                        </td>
                        <td class="py-1.5 px-3 text-right font-bold">
                            {{ number_format($reportData->sum('total_collie')) }} Box
                        </td>
                        <td class="py-1.5 px-3 text-right font-bold">
                            {{ number_format($reportData->sum('total_berat'), 1) }} Kg
                        </td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Pagination Bar (Simple Tailwind Anti-Break) -->
        <div class="no-print bg-slate-50 border-t border-slate-200 px-3 py-2 flex items-center justify-between text-xs shrink-0">
            <span class="text-slate-500 font-medium">
                Menampilkan data ke-<strong>{{ $reportData->firstItem() ?? 0 }}</strong> s/d <strong>{{ $reportData->lastItem() ?? 0 }}</strong> dari total <strong>{{ $reportData->total() }}</strong> Packing List
            </span>
            @if($reportData->hasPages())
                <div>
                    {{ $reportData->links('pagination::simple-tailwind') }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

@push('scripts')
{{-- Script khusus halaman report jika dibutuhkan di masa mendatang --}}
@endpush
