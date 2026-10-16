<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CollieSppb;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $cabang = $request->get('cabang');
        $ekspedisi = $request->get('ekspedisi');
        $status = $request->get('status');
        $search = $request->get('search');

        $query = DB::table('collie_sppbs')
            ->select(
                'no_pl',
                'tgl_col',
                DB::raw('MAX(cabang) as cabang'),
                DB::raw('MAX(no_sppb) as no_sppb'),
                DB::raw('MAX(tgl_sppb) as tgl_sppb'),
                DB::raw('MAX(ekspedisi) as ekspedisi'),
                DB::raw('MAX(customer_name) as customer_name'),
                DB::raw('MAX(customer_address) as customer_address'),
                DB::raw('MAX(supirnopol) as supirnopol'),
                DB::raw('COUNT(DISTINCT no_sm) as total_sm'),
                DB::raw('COUNT(id) as total_items'),
                DB::raw('COALESCE(MAX(jumlah), SUM(qty1)) as total_collie'),
                DB::raw('COALESCE(MAX(berat), SUM(qtyr)) as total_berat')
            )
            ->whereNotNull('no_pl')
            ->groupBy('no_pl', 'tgl_col');

        if ($startDate && $endDate) {
            $query->whereBetween('tgl_col', [$startDate, $endDate]);
        }

        if (!empty($cabang)) {
            $query->where('cabang', $cabang);
        }

        if (!empty($ekspedisi)) {
            $query->where('ekspedisi', $ekspedisi);
        }

        if ($status === 'sudah') {
            $query->whereNotNull('no_sppb')->where('no_sppb', '!=', '');
        } elseif ($status === 'belum') {
            $query->where(function ($q) {
                $q->whereNull('no_sppb')->orWhere('no_sppb', '');
            });
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('no_pl', 'like', "%{$search}%")
                  ->orWhere('no_sppb', 'like', "%{$search}%")
                  ->orWhere('ekspedisi', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Ambil data untuk tabel dengan pagination
        $reportData = (clone $query)
            ->orderByRaw('tgl_col DESC NULLS LAST')
            ->orderByRaw('no_pl DESC NULLS LAST')
            ->paginate(50)
            ->withQueryString();

        // Rekapitulasi Summary
        $summary = DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query)
            ->selectRaw('
                COUNT(*) as total_pl,
                COALESCE(SUM(total_collie), 0) as grand_total_collie,
                COALESCE(ROUND(SUM(total_berat)::numeric, 1), 0) as grand_total_berat,
                COUNT(CASE WHEN no_sppb IS NOT NULL AND no_sppb != \'\' THEN 1 END) as total_sppb_terbit
            ')
            ->first();

        // Opsi filter
        $ekspedisiOptions = DB::table('ekspedisi')->orderBy('id')->pluck('nama');
        $cabangOptions = DB::table('collie_sppbs')
            ->whereNotNull('cabang')
            ->where('cabang', '!=', '')
            ->distinct()
            ->pluck('cabang');

        return view('report.index', compact(
            'reportData',
            'summary',
            'startDate',
            'endDate',
            'cabang',
            'ekspedisi',
            'status',
            'search',
            'ekspedisiOptions',
            'cabangOptions'
        ));
    }
}
