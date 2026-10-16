@extends('layouts.app')

@section('title', 'Dashboard & Analitik Logistik')

@section('content')
<div class="flex-1 flex flex-col space-y-4 min-h-0 overflow-y-auto pr-1">

    <!-- ============================================================== -->
    <!-- HEADER BAR: JUDUL & FILTER TAHUN                               -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-sky-500/20 text-lg">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-slate-800 tracking-tight leading-snug">DASHBOARD & MONITORING LOGISTIK</h1>
                <p class="text-xs text-slate-500 font-medium">Ringkasan Pengiriman Barang, Tren Koli/Collie Bulanan, & Distribusi SPPB</p>
            </div>
        </div>

        <!-- Filter Tahun & Quick Actions -->
        <div class="flex items-center space-x-2.5">
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-2">
                <label for="year" class="text-xs font-semibold text-slate-600">Tahun:</label>
                <div class="relative">
                    <select name="year" id="year" onchange="this.form.submit()" class="appearance-none bg-slate-50 hover:bg-slate-100 border border-slate-300 font-bold text-slate-800 text-xs rounded-xl px-3 py-2 pr-8 focus:ring-2 focus:ring-sky-500 focus:outline-none cursor-pointer">
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </form>

            <a href="{{ route('collie.index') }}" class="px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center space-x-1.5">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Input Collie</span>
            </a>

            <a href="{{ route('sppb.index') }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center space-x-1.5">
                <i class="fa-solid fa-file-signature text-xs"></i>
                <span>Terbitkan SPPB</span>
            </a>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 4 KPI METRIC CARDS                                             -->
    <!-- ============================================================== -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        
        <!-- Card 1: Total Packing List -->
        <div class="bg-white rounded-xl p-3 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-sky-300 transition-all">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase truncate block">Total Packing List (PL)</span>
                    <div class="flex items-baseline space-x-1.5 mt-0.5">
                        <h3 class="text-lg sm:text-xl font-black text-slate-800">{{ number_format($kpi->total_pl ?? 0) }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium">Thn {{ $selectedYear }}</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-sm shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                <span>All time:</span>
                <strong class="text-slate-700 font-bold">{{ number_format($totalAllPl) }} PL</strong>
            </div>
        </div>

        <!-- Card 2: Status SPPB -->
        <div class="bg-white rounded-xl p-3 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-emerald-300 transition-all">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase truncate block">Realisasi SPPB</span>
                    <div class="flex items-baseline space-x-1.5 mt-0.5">
                        <h3 class="text-lg sm:text-xl font-black text-emerald-600">{{ number_format($kpi->pl_sudah_sppb ?? 0) }}</h3>
                        <span class="text-[10px] text-emerald-600/80 font-medium">/ {{ number_format($kpi->total_pl ?? 0) }} PL</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px]">
                <span class="text-amber-600 font-medium flex items-center space-x-1">
                    <i class="fa-solid fa-clock text-[9px]"></i>
                    <span>Belum SPPB:</span>
                </span>
                <strong class="text-amber-700 font-bold">{{ number_format($kpi->pl_belum_sppb ?? 0) }} PL</strong>
            </div>
        </div>

        <!-- Card 3: Total Koli / Box -->
        <div class="bg-white rounded-xl p-3 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-indigo-300 transition-all">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase truncate block">Volume Muatan</span>
                    <div class="flex items-baseline space-x-1.5 mt-0.5">
                        <h3 class="text-lg sm:text-xl font-black text-indigo-600">{{ number_format($kpi->total_collie ?? 0) }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium">Box</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-dolly"></i>
                </div>
            </div>
            <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                <span>Rata-rata/PL:</span>
                <strong class="text-slate-700 font-bold">
                    {{ $kpi->total_pl ? number_format(round($kpi->total_collie / $kpi->total_pl)) : 0 }} Box
                </strong>
            </div>
        </div>

        <!-- Card 4: Total Berat (Kg) -->
        <div class="bg-white rounded-xl p-3 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-violet-300 transition-all">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase truncate block">Tonase Pengiriman</span>
                    <div class="flex items-baseline space-x-1.5 mt-0.5">
                        <h3 class="text-lg sm:text-xl font-black text-violet-700">{{ number_format($kpi->total_berat ?? 0, 1) }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium">Kg</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center text-sm shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-weight-hanging"></i>
                </div>
            </div>
            <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                <span>Setara:</span>
                <strong class="text-slate-700 font-bold">{{ number_format(round(($kpi->total_berat ?? 0) / 1000, 2), 2) }} Ton</strong>
            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- GRAFIK UTAMA: TREN PER BULAN & BREAKDOWN                       -->
    <!-- ============================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        
        <!-- Grafik Tren Bulanan (8 Kolom) -->
        <div class="lg:col-span-8 bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col">
            <div class="flex flex-wrap items-center justify-between pb-3 mb-2 border-b border-slate-100 gap-2">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                    <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Tren Pengiriman Per Bulan (Tahun {{ $selectedYear }})</h2>
                </div>
                <div class="flex items-center space-x-3 text-xs">
                    <div class="flex items-center space-x-1.5">
                        <span class="w-3 h-3 rounded-sm bg-sky-500 inline-block"></span>
                        <span class="text-slate-600 font-semibold">Total Koli (Box)</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="w-3 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                        <span class="text-slate-600 font-semibold">Berat (Kg)</span>
                    </div>
                </div>
            </div>

            <!-- Canvas Chart.js -->
            <div class="relative w-full h-80 sm:h-96">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Distribusi Ekspedisi & Cabang (4 Kolom) -->
        <div class="lg:col-span-4 flex flex-col space-y-4">
            
            <!-- Card Porsi Ekspedisi -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex-1 flex flex-col">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Distribusi Ekspedisi</h2>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">Top 5</span>
                </div>

                <div class="relative h-44 mb-3">
                    <canvas id="expeditionPieChart"></canvas>
                </div>

                <!-- List Ekspedisi -->
                <div class="space-y-2 text-xs overflow-y-auto max-h-40 pr-1">
                    @forelse($expeditionData as $eks)
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg hover:bg-slate-50 transition border border-transparent hover:border-slate-100">
                            <div class="flex items-center space-x-2 truncate">
                                <i class="fa-solid fa-truck text-slate-400 text-[10px]"></i>
                                <span class="font-semibold text-slate-700 truncate" title="{{ $eks->nama_ekspedisi }}">{{ $eks->nama_ekspedisi }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-extrabold text-slate-900">{{ number_format($eks->total_collie) }}</span>
                                <span class="text-[10px] text-slate-400">box</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs">Belum ada data ekspedisi pada tahun ini</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- BAGIAN BAWAH: AKTIVITAS TRANSAKSI TERAKHIR                     -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Aktivitas Packing List & SPPB Terkini</h2>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('sppb.index') }}" class="text-xs text-sky-600 hover:text-sky-800 font-bold flex items-center space-x-1">
                    <span>Lihat Semua SPPB</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 font-extrabold text-[11px] uppercase tracking-wider">
                        <th class="py-2.5 px-3">No. Packing List</th>
                        <th class="py-2.5 px-3">Tanggal PL</th>
                        <th class="py-2.5 px-3">Cabang</th>
                        <th class="py-2.5 px-3">No. SPPB</th>
                        <th class="py-2.5 px-3">Via Ekspedisi</th>
                        <th class="py-2.5 px-3">Customer / Penerima</th>
                        <th class="py-2.5 px-3 text-right">Total Koli</th>
                        <th class="py-2.5 px-3 text-right">Berat (Kg)</th>
                        <th class="py-2.5 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($recentPl as $row)
                        <tr class="hover:bg-sky-50/40 transition">
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-900">
                                <a href="{{ route('collie.index') }}?no_pl={{ urlencode($row->no_pl) }}" class="hover:text-sky-600 hover:underline">
                                    {{ $row->no_pl }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3 font-mono text-slate-600">
                                {{ $row->tgl_col ? date('d/m/Y', strtotime($row->tgl_col)) : '-' }}
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $row->cabang ?: 'JKT0' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-mono font-bold text-sky-700">
                                {{ $row->no_sppb ?: '-' }}
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="font-semibold text-slate-800">{{ $row->ekspedisi ?: '-' }}</span>
                            </td>
                            <td class="py-2.5 px-3 truncate max-w-xs" title="{{ $row->customer_name }}">
                                {{ $row->customer_name ?: '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-bold text-slate-900">
                                {{ number_format($row->total_collie) }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-700">
                                {{ number_format($row->total_berat, 1) }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if(!empty($row->no_sppb))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-check mr-1"></i> SPPB Terbit
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-clock mr-1"></i> Draft PL
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-slate-400">Belum ada riwayat aktivitas data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. SETUP GRAFIK TREN BULANAN ---
    const ctxMonthly = document.getElementById('monthlyTrendChart').getContext('2d');
    
    const monthlyLabels = @json($chartMonthlyLabels);
    const dataCollie = @json($chartMonthlyCollie);
    const dataBerat = @json($chartMonthlyBerat);
    const dataPl = @json($chartMonthlyPl);

    const monthlyChart = new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [
                {
                    label: 'Total Koli (Box)',
                    data: dataCollie,
                    backgroundColor: 'rgba(14, 165, 233, 0.75)', // Sky 500
                    borderColor: 'rgb(2, 132, 199)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    yAxisID: 'yKoli',
                    order: 2,
                },
                {
                    label: 'Total Berat (Kg)',
                    data: dataBerat,
                    type: 'line',
                    borderColor: '#f59e0b', // Amber 500
                    backgroundColor: 'rgba(245, 158, 11, 0.15)',
                    borderWidth: 3,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.35,
                    yAxisID: 'yBerat',
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += Number(context.parsed.y).toLocaleString() + (context.datasetIndex === 0 ? ' Box' : ' Kg');
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11
                        }
                    }
                },
                yKoli: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Volume Koli (Box)',
                        font: { size: 10, weight: 'bold' }
                    },
                    grid: {
                        color: 'rgba(226, 232, 240, 0.8)'
                    },
                    ticks: {
                        callback: function(val) { return Number(val).toLocaleString(); }
                    }
                },
                yBerat: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Berat (Kg)',
                        font: { size: 10, weight: 'bold' }
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: function(val) { return Number(val).toLocaleString() + ' kg'; }
                    }
                }
            }
        }
    });

    // --- 2. SETUP GRAFIK PORSI EKSPEDISI (DONUT) ---
    const ctxExp = document.getElementById('expeditionPieChart').getContext('2d');
    
    const expDataRaw = @json($expeditionData);
    const expLabels = expDataRaw.map(it => it.nama_ekspedisi);
    const expCounts = expDataRaw.map(it => parseInt(it.total_collie));

    const expPalette = [
        '#0284c7', // Sky 600
        '#4f46e5', // Indigo 600
        '#10b981', // Emerald 500
        '#f59e0b', // Amber 500
        '#8b5cf6', // Violet 500
        '#ec4899', // Pink 500
        '#94a3b8'  // Slate 400
    ];

    const expChart = new Chart(ctxExp, {
        type: 'doughnut',
        data: {
            labels: expLabels.length ? expLabels : ['Belum Ada Data'],
            datasets: [{
                data: expCounts.length ? expCounts : [1],
                backgroundColor: expPalette.slice(0, expLabels.length || 1),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.label}: ${Number(context.parsed).toLocaleString()} Box`;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });

});
</script>
@endpush
