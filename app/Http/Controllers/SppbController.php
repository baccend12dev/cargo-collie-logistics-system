<?php

namespace App\Http\Controllers;

use App\Models\CollieSppb;
use App\Models\SmOracle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SppbController extends Controller
{
    /**
     * Menampilkan daftar Packing List dan Status Dokumen SPPB
     */
    public function index(Request $request)
    {
        $query = CollieSppb::query();

        // Filter Cabang
        if ($request->filled('cabang')) {
            $query->where('cabang', $request->cabang);
        }

        // Filter Status SPPB (sudah / belum)
        if ($request->filled('status')) {
            if ($request->status === 'sudah') {
                $query->whereNotNull('no_sppb')->where('no_sppb', '!=', '');
            } elseif ($request->status === 'belum') {
                $query->where(function ($q) {
                    $q->whereNull('no_sppb')->orWhere('no_sppb', '');
                });
            }
        }

        // Pencarian Cepat (No PL, No SPPB, Ekspedisi, Customer, Supir)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pl', 'like', "%{$search}%")
                  ->orWhere('no_sppb', 'like', "%{$search}%")
                  ->orWhere('no_sm', 'like', "%{$search}%")
                  ->orWhere('ekspedisi', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('supirnopol', 'like', "%{$search}%");
            });
        }

        // Agregasi per No. Packing List & Tanggal Collie (No. PL & TGL_COL)
        $plList = (clone $query)->select(
            'no_pl',
            'tgl_col as tgl_pl',
            DB::raw('MAX(cabang) as cabang'),
            DB::raw('MAX(cab) as cab'),
            DB::raw('MAX(no_sppb) as no_sppb'),
            DB::raw('MAX(tgl_sppb) as tgl_sppb'),
            DB::raw('MAX(ekspedisi) as ekspedisi'),
            DB::raw('MAX(customer_name) as customer_name'),
            DB::raw('MAX(customer_address) as customer_address'),
            DB::raw('MAX(supirnopol) as supirnopol'),
            DB::raw('MAX(cat) as cat'),
            DB::raw('COUNT(DISTINCT no_sm) as total_sm'),
            DB::raw('COUNT(id) as total_items'),
            DB::raw('COALESCE(MAX(jumlah), SUM(qty1)) as jumlah_collie'),
            DB::raw('COALESCE(MAX(berat), SUM(qtyr)) as berat_total')
        )
        ->whereNotNull('no_pl')
        ->groupBy('no_pl', 'tgl_col')
        ->orderByRaw('tgl_col DESC NULLS LAST')
        ->orderByRaw('no_pl DESC NULLS LAST')
        ->paginate(15)
        ->withQueryString();

        // Statistik (dihitung per pasang No. PL & TGL_COL unik)
        $totalPl = DB::table(DB::raw("(SELECT DISTINCT no_pl, tgl_col FROM collie_sppbs WHERE no_pl IS NOT NULL) as t"))->count();
        $plSudahSppb = DB::table(DB::raw("(SELECT DISTINCT no_pl, tgl_col FROM collie_sppbs WHERE no_pl IS NOT NULL AND no_sppb IS NOT NULL AND no_sppb != '') as t"))->count();
        $plBelumSppb = $totalPl - $plSudahSppb;

        // Master Ekspedisi & Customer unik dari data historis untuk kemudahan pilihan
        $ekspedisiList = DB::table('ekspedisi')->orderBy('id')->pluck('nama');
        $customerList = CollieSppb::whereNotNull('customer_name')->where('customer_name', '!=', '')->select('customer_name', 'customer_address')->distinct()->get();
        $distributorList = DB::table('distributor')->orderBy('nama', 'asc')->orderBy('kota', 'asc')->get();

        return view('sppb.index', compact('plList', 'totalPl', 'plSudahSppb', 'plBelumSppb', 'ekspedisiList', 'customerList', 'distributorList'));
    }

    /**
     * API untuk mengambil data lengkap suatu No. PL & Tanggal saat tombol SPPB ditekan
     */
    public function getPlDetail(Request $request)
    {
        $noPl = $request->query('no_pl');
        $tglCol = $request->query('tgl_col');

        if (!$noPl) {
            return response()->json(['success' => false, 'message' => 'No. PL tidak ditemukan'], 400);
        }

        $query = CollieSppb::where('no_pl', $noPl);
        if (!empty($tglCol)) {
            $query->whereDate('tgl_col', $tglCol);
        }

        $items = $query->get();
        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data Packing List tidak ditemukan'], 404);
        }

        $first = $items->first();

        // Hitung total koli dan total berat
        $totalKoli = $first->jumlah ?: $items->sum('qty1');
        $totalBerat = $first->berat ?: $items->sum('qtyr');

        // Daftar Surat Muat (NO_SM) unik di bawah No. PL & tanggal ini
        $smList = $items->whereNotNull('no_sm')->groupBy('no_sm')->map(function ($group, $noSm) {
            $firstGroup = $group->first();
            return [
                'no_sm'       => $noSm,
                'tgl_sm'      => $firstGroup->tgl_col ? $firstGroup->tgl_col->format('d/m/Y') : ($firstGroup->creation_date ? $firstGroup->creation_date->format('d/m/Y') : date('d/m/Y')),
                'total_koli'  => $group->sum('qty1'),
                'total_berat' => round($group->sum('qtyr'), 2),
            ];
        })->values();

        // Generate nomor SPPB otomatis jika belum ada (misal: 0327/10/2026)
        $noSppb = $first->no_sppb;
        if (empty($noSppb)) {
            $lastSppb = CollieSppb::whereNotNull('no_sppb')->where('no_sppb', '!=', '')->orderBy('id', 'desc')->value('no_sppb');
            $counter = 325;
            if ($lastSppb && preg_match('/^(\d+)\//', $lastSppb, $matches)) {
                $counter = intval($matches[1]) + 1;
            }
            $noSppb = sprintf('%04d/%s/%s', $counter, date('m'), date('Y'));
        }

        return response()->json([
            'success'          => true,
            'is_new'           => empty($first->no_sppb),
            'no_pl'            => $noPl,
            'tgl_col'          => $tglCol ?: ($first->tgl_col ? $first->tgl_col->format('Y-m-d') : ''),
            'tgl_col_formatted'=> $first->tgl_col ? $first->tgl_col->format('d/m/Y') : '-',
            'no_sppb'          => $noSppb,
            'tgl_sppb'         => $first->tgl_sppb ? $first->tgl_sppb->format('Y-m-d') : date('Y-m-d'),
            'cabang'           => $first->cab ?: ($first->cabang ?: 'JAKARTA PUSAT'),
            'ekspedisi'        => $first->ekspedisi ?? '',
            'customer_name'    => $first->customer_name ?? '',
            'customer_address' => $first->customer_address ?? '',
            'supirnopol'       => $first->supirnopol ?? '',
            'cat'              => $first->cat ?? '',
            'jumlah_collie'    => $totalKoli,
            'berat_kg'         => round($totalBerat, 1),
            'sm_list'          => $smList,
        ]);
    }

    /**
     * Menyimpan / Memperbarui Dokumen SPPB (Mass Update ke baris koli pada No. PL & TGL_COL terpilih)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pl'            => 'required|string',
            'tgl_col'          => 'nullable|date',
            'no_sppb'          => 'required|string|max:100',
            'tgl_sppb'         => 'required|date',
            'cabang'           => 'nullable|string|max:100',
            'ekspedisi'        => 'nullable|string|max:150',
            'customer_name'    => 'nullable|string|max:200',
            'customer_address' => 'nullable|string',
            'supirnopol'       => 'nullable|string|max:150',
            'cat'              => 'nullable|string',
            'jumlah'           => 'nullable|integer',
            'berat'            => 'nullable|numeric',
            'selected_sms'     => 'nullable|array',
        ]);

        // Seluruh baris di bawah no_pl dan tanggal ini
        $query = CollieSppb::where('no_pl', $validated['no_pl']);
        if (!empty($validated['tgl_col'])) {
            $query->whereDate('tgl_col', $validated['tgl_col']);
        }

        // Kalkulasi total koli & total berat dari SEMUA baris pada no_pl & tanggal tersebut
        $koliSummary = (clone $query)->selectRaw('SUM(qty1) as total_koli, SUM(qtyr) as total_berat')->first();
        $jumlah = $validated['jumlah'] ?? ($koliSummary->total_koli ?? 0);
        $berat  = $validated['berat'] ?? ($koliSummary->total_berat ?? 0);

        // Eksekusi Mass Update
        $updatedRows = $query->update([
            'no_sppb'          => $validated['no_sppb'],
            'tgl_sppb'         => $validated['tgl_sppb'],
            'cab'              => $validated['cabang'] ?? 'JAKARTA PUSAT',
            'ekspedisi'        => $validated['ekspedisi'],
            'customer_name'    => $validated['customer_name'],
            'customer_address' => $validated['customer_address'],
            'supirnopol'       => $validated['supirnopol'],
            'cat'              => $validated['cat'],
            'jumlah'           => $jumlah,
            'berat'            => $berat,
        ]);

        // Update baris-baris koli: masukkan NO_SM mulai dari atas sampai bawah secara berurutan
        if ($request->has('selected_sms')) {
            $collieRows = (clone $query)->orderBy('id', 'asc')->get();
            $selectedSms = array_values(array_filter((array) $request->input('selected_sms', [])));

            foreach ($collieRows as $index => $row) {
                $sm = $selectedSms[$index] ?? null;
                $row->no_sm = $sm;
                $row->save();
            }
        }

        return redirect()->route('sppb.index')
            ->with('success', "Dokumen SPPB {$validated['no_sppb']} untuk Packing List {$validated['no_pl']} berhasil disimpan ({$updatedRows} baris diperbarui)!");
    }

    /**
     * Mengambil daftar Surat Muat (NO_SM) terbaru dari view Oracle (otto_collie_trx_picklist2_v)
     * Dioptimalkan dengan subquery LIMIT pada transaksi terbaru (sales_order_id DESC)
     * agar query FDW Oracle tidak melakukan full table scan seluruh riwayat (dari 14 detik menjadi ~0.5 detik).
     */
    public function getSmOracleList(Request $request)
    {
        $search = trim($request->query('q', ''));
        $forceRefresh = $request->boolean('refresh', false);

        // Cache hasil default (tanpa filter pencarian) selama 2 menit agar pembukaan modal instan
        $cacheKey = 'sm_oracle_recent_list';
        if (empty($search) && !$forceRefresh) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return response()->json([
                    'success' => true,
                    'items'   => $cached,
                    'cached'  => true,
                ]);
            }
        }

        try {
            // Gunakan subquery dengan batas transaksi terbaru (LIMIT 500) agar Oracle langsung
            // memanfaatkan index ORDER BY sales_order_id DESC tanpa membaca jutaan riwayat transaksi lama.
            $items = DB::table(function ($sub) use ($search) {
                $sub->from('otto_collie_trx_picklist2_v')
                    ->whereNotNull('no_bongkar_muat')
                    ->where('no_bongkar_muat', '!=', '');

                if (!empty($search)) {
                    $upperSearch = '%' . strtoupper($search) . '%';
                    $sub->where(function ($q) use ($upperSearch) {
                        $q->whereRaw('UPPER(no_bongkar_muat) LIKE ?', [$upperSearch])
                          ->orWhereRaw('UPPER(customer_name) LIKE ?', [$upperSearch]);
                    });
                }

                $sub->orderByDesc('sales_order_id')
                    ->limit(500);
            }, 'sub')
            ->select(
                'no_bongkar_muat as no_sm',
                'creation_date as tgl_sm',
                'customer_name',
                'customer_address',
                DB::raw('MAX(sales_order_id) as max_so_id'),
                DB::raw('COUNT(item_code) as total_items'),
                DB::raw('SUM(qty_transacted) as total_qty')
            )
            ->groupBy('no_bongkar_muat', 'creation_date', 'customer_name', 'customer_address')
            ->orderByDesc('max_so_id')
            ->limit(15)
            ->get();

            if (empty($search)) {
                Cache::put($cacheKey, $items, now()->addMinutes(2));
            }

            return response()->json([
                'success' => true,
                'items'   => $items
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari Oracle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencetak Dokumen Resmi Surat Pengantar Pengiriman Barang (SPPB)
     */
    public function print(Request $request, $no_pl)
    {
        $tglCol = $request->query('tgl_col');
        $query = CollieSppb::where('no_pl', $no_pl);
        if (!empty($tglCol)) {
            $query->whereDate('tgl_col', $tglCol);
        }

        $items = $query->get();
        if ($items->isEmpty()) {
            abort(404, 'Data Packing List tidak ditemukan');
        }

        $header = $items->first();
        $smList = $items->whereNotNull('no_sm')->groupBy('no_sm')->map(function ($group, $noSm) {
            $firstGroup = $group->first();
            return [
                'no_sm'       => $noSm,
                'tgl_sm'      => $firstGroup->tgl_col ? $firstGroup->tgl_col->format('d/m/Y') : ($firstGroup->creation_date ? $firstGroup->creation_date->format('d/m/Y') : date('d/m/Y')),
                'total_koli'  => $group->sum('qty1'),
                'total_berat' => round($group->sum('qtyr'), 2),
                'items'       => $group,
            ];
        });

        return view('sppb.print', compact('header', 'items', 'smList', 'no_pl', 'tglCol'));
    }
}
