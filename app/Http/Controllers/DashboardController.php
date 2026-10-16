<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CollieSppb;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Daftar tahun yang tersedia dari data
        $availableYears = DB::table('collie_sppbs')
            ->selectRaw('DISTINCT EXTRACT(YEAR FROM tgl_col)::int as year')
            ->whereNotNull('tgl_col')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [intval(date('Y'))];
        }

        // Tahun terpilih
        $selectedYear = intval($request->get('year', $availableYears[0]));

        // --- 1. KPI UTAMA (TAHUN TERPILIH) ---
        // Catatan: Satu transaksi Packing List (PL) unik diidentifikasi oleh pasangan (no_pl, tgl_col)
        // karena nomor PL (001/PL, 002/PL, dst) dipakai berulang setiap periode/tanggal pengiriman.
        $kpi = DB::table('collie_sppbs')
            ->whereRaw('EXTRACT(YEAR FROM tgl_col) = ?', [$selectedYear])
            ->whereNotNull('no_pl')
            ->selectRaw('
                COUNT(DISTINCT no_pl || \'_\' || tgl_col) as total_pl,
                COUNT(DISTINCT CASE WHEN no_sppb IS NOT NULL AND no_sppb != \'\' THEN no_pl || \'_\' || tgl_col END) as pl_sudah_sppb,
                COUNT(DISTINCT CASE WHEN no_sppb IS NULL OR no_sppb = \'\' THEN no_pl || \'_\' || tgl_col END) as pl_belum_sppb,
                COALESCE(SUM(qty1), 0) as total_collie,
                COALESCE(ROUND(SUM(qtyr)::numeric, 1), 0) as total_berat
            ')
            ->first();

        // Total keseluruhan all-time untuk perbandingan
        $totalAllPl = DB::table('collie_sppbs')
            ->whereNotNull('no_pl')
            ->selectRaw('COUNT(DISTINCT no_pl || \'_\' || tgl_col) as total')
            ->value('total') ?? 0;

        // --- 2. GRAFIK TREN PER BULAN (1 - 12) ---
        $monthlyRaw = DB::table('collie_sppbs')
            ->whereRaw('EXTRACT(YEAR FROM tgl_col) = ?', [$selectedYear])
            ->whereNotNull('no_pl')
            ->selectRaw('
                EXTRACT(MONTH FROM tgl_col)::int as month,
                COUNT(DISTINCT no_pl || \'_\' || tgl_col) as total_pl,
                COALESCE(SUM(qty1), 0) as total_collie,
                COALESCE(ROUND(SUM(qtyr)::numeric, 1), 0) as total_berat,
                COUNT(DISTINCT CASE WHEN no_sppb IS NOT NULL AND no_sppb != \'\' THEN no_pl || \'_\' || tgl_col END) as sppb_terbit
            ')
            ->groupByRaw('EXTRACT(MONTH FROM tgl_col)')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $chartMonthlyLabels = [];
        $chartMonthlyCollie = [];
        $chartMonthlyBerat = [];
        $chartMonthlyPl = [];
        $chartMonthlySppb = [];

        for ($m = 1; $m <= 12; $m++) {
            $chartMonthlyLabels[] = $monthNames[$m];
            $row = $monthlyRaw->get($m);
            $chartMonthlyCollie[] = $row ? intval($row->total_collie) : 0;
            $chartMonthlyBerat[] = $row ? floatval($row->total_berat) : 0;
            $chartMonthlyPl[] = $row ? intval($row->total_pl) : 0;
            $chartMonthlySppb[] = $row ? intval($row->sppb_terbit) : 0;
        }

        // --- 3. GRAFIK DISTRIBUSI EKSPEDISI ---
        $expeditionData = DB::table('collie_sppbs')
            ->whereRaw('EXTRACT(YEAR FROM tgl_col) = ?', [$selectedYear])
            ->selectRaw('
                COALESCE(NULLIF(ekspedisi, \'\'), \'Belum Ditentukan\') as nama_ekspedisi,
                COUNT(DISTINCT no_pl || \'_\' || tgl_col) as total_pl,
                COALESCE(SUM(qty1), 0) as total_collie,
                COALESCE(ROUND(SUM(qtyr)::numeric, 1), 0) as total_berat
            ')
            ->groupByRaw('1')
            ->orderByDesc('total_collie')
            ->limit(7)
            ->get();

        // --- 4. DISTRIBUSI CABANG ---
        $branchData = DB::table('collie_sppbs')
            ->whereRaw('EXTRACT(YEAR FROM tgl_col) = ?', [$selectedYear])
            ->selectRaw('
                COALESCE(NULLIF(cabang, \'\'), \'Lainnya\') as nama_cabang,
                COUNT(DISTINCT no_pl || \'_\' || tgl_col) as total_pl,
                COALESCE(SUM(qty1), 0) as total_collie
            ')
            ->groupByRaw('1')
            ->orderByDesc('total_collie')
            ->get();

        // --- 5. AKTIVITAS TERBARU (5 PL & 5 SPPB TERAKHIR) ---
        $recentPl = DB::table('collie_sppbs')
            ->select(
                'no_pl',
                'tgl_col',
                DB::raw('MAX(cabang) as cabang'),
                DB::raw('MAX(no_sppb) as no_sppb'),
                DB::raw('MAX(ekspedisi) as ekspedisi'),
                DB::raw('MAX(customer_name) as customer_name'),
                DB::raw('COALESCE(MAX(jumlah), SUM(qty1)) as total_collie'),
                DB::raw('COALESCE(MAX(berat), SUM(qtyr)) as total_berat')
            )
            ->whereNotNull('no_pl')
            ->groupBy('no_pl', 'tgl_col')
            ->orderByRaw('tgl_col DESC NULLS LAST')
            ->orderByRaw('no_pl DESC NULLS LAST')
            ->limit(6)
            ->get();

        return view('dashboard.index', compact(
            'availableYears',
            'selectedYear',
            'kpi',
            'totalAllPl',
            'chartMonthlyLabels',
            'chartMonthlyCollie',
            'chartMonthlyBerat',
            'chartMonthlyPl',
            'chartMonthlySppb',
            'expeditionData',
            'branchData',
            'recentPl'
        ));
    }
}
