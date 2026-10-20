<?php

namespace App\Http\Controllers;

use App\Models\CollieSppb;
use App\Models\OttoMasterStock;
use Illuminate\Http\Request;

class CollieController extends Controller
{
    /**
     * Menampilkan daftar data Collie (Packing List)
     */
    public function index(Request $request)
    {
        $query = CollieSppb::query();

        // Filter Pencarian Cepat (Cari / Find)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pl', 'like', "%{$search}%")
                  ->orWhere('no_sm', 'like', "%{$search}%")
                  ->orWhere('no_col', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('cabang', 'like', "%{$search}%");
            });
        }

        // Filter Kata Kunci Spesiﬁk
        if ($request->filled('filter_val')) {
            $filterVal = $request->filter_val;
            $query->where(function ($q) use ($filterVal) {
                $q->where('code', 'like', "%{$filterVal}%")
                  ->orWhere('no_pl', 'like', "%{$filterVal}%");
            });
        }

        // Filter Cek SPPB (hanya yang sudah ber-SPPB atau sebaliknya)
        if ($request->has('cek_sppb') && $request->cek_sppb == '1') {
            $query->whereNotNull('no_sppb')->where('no_sppb', '!=', '');
        }

        // Filter Periode Bulan
        if ($request->filled('period')) {
            // format: MM-YYYY (misal 10-2026)
            $parts = explode('-', $request->period);
            if (count($parts) === 2) {
                $month = $parts[0];
                $year  = $parts[1];
                $query->whereMonth('tgl_col', $month)->whereYear('tgl_col', $year);
            }
        }

        $totalRecords = (clone $query)->count();
        $totalQty = (clone $query)->sum('qty');
        $totalQtyr = (clone $query)->sum('qtyr');

        $collies = $query->orderByRaw('tgl_col DESC NULLS LAST')
                         ->orderByRaw('no_pl DESC NULLS LAST')
                         ->orderBy('id', 'asc')
                         ->paginate(25)
                         ->withQueryString();

        // Ambil nama barang dari Master Stock (otto_master_stock_v) sekaligus untuk tampilan tabel
        $codes = $collies->pluck('code')->filter()->unique()->map(fn($c) => strtoupper(trim($c)));
        if ($codes->isNotEmpty()) {
            $stockMap = OttoMasterStock::whereIn('item_code', $codes)
                ->orderByRaw("CASE WHEN jenis_bhn = 'FG' THEN 1 ELSE 2 END")
                ->orderByRaw("CASE WHEN anm = 'Active' THEN 1 ELSE 2 END")
                ->get()
                ->groupBy(fn($item) => strtoupper(trim($item->item_code)))
                ->map(fn($group) => $group->first());

            foreach ($collies as $c) {
                $stock = $stockMap->get(strtoupper(trim($c->code)));
                $c->nama_barang = $stock ? $stock->item_desc : ($c->code ?: '-');
                $c->uom_barang  = $stock?->uom ?? '';
            }
        }

        return view('collie.index', compact('collies', 'totalRecords', 'totalQty', 'totalQtyr'));
    }

    /**
     * API untuk mencari data barang berdasarkan item_code dari otto_master_stock_v
     * Seperti pada VB: ketika input data memasukkan kode lalu enter, menampilkan nama barang.
     */
    public function getItemStock(Request $request, $code = null)
    {
        $code = $code ?: $request->query('code');
        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Kode barang harus diisi.'
            ], 400);
        }

        $stock = OttoMasterStock::findByCode($code);

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => "Kode barang '{$code}' tidak ditemukan di master stock."
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item'    => [
                'item_code' => $stock->item_code,
                'item_desc' => $stock->item_desc,
                'ext'       => $stock->ext,
                'uom'       => $stock->uom,
                'anm'       => $stock->anm,
                'jenis_bhn' => $stock->jenis_bhn,
            ]
        ]);
    }

    /**
     * API pencarian / autocomplete daftar barang dari otto_master_stock_v
     */
    public function searchItemStock(Request $request)
    {
        $query = trim($request->query('q', ''));
        if (strlen($query) < 1) {
            return response()->json(['success' => true, 'items' => []]);
        }

        $items = OttoMasterStock::where(function ($q) use ($query) {
                $q->where('item_code', 'ilike', "%{$query}%")
                  ->orWhere('item_desc', 'ilike', "%{$query}%");
            })
            ->orderByRaw("CASE WHEN jenis_bhn = 'FG' THEN 1 ELSE 2 END")
            ->orderByRaw("CASE WHEN anm = 'Active' THEN 1 ELSE 2 END")
            ->limit(15)
            ->get(['item_code', 'ext', 'item_desc', 'uom', 'anm', 'jenis_bhn']);

        return response()->json([
            'success' => true,
            'items'   => $items
        ]);
    }

    /**
     * Menyimpan data Collie baru (Tahap 1: Belum ada data SPPB)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pl'   => 'nullable|string|max:50',
            'no_sm'   => 'nullable|string|max:100',
            'no_col'  => 'nullable|string|max:50',
            'tgl_col' => 'nullable|date',
            'cabang'  => 'nullable|string|max:50',
            'code'    => 'nullable|string|max:50',
            'qty1'    => 'nullable|integer',
            'qty2'    => 'nullable|integer',
            'qty'     => 'nullable|integer',
            'qtyr'    => 'nullable|numeric',
            'ad'      => 'nullable|integer',
            'jucol'   => 'nullable|integer',
        ]);

        // Perhitungan QTY otomatis jika tidak diisi manual
        $qty1 = (int)($validated['qty1'] ?? 1);
        $qty2 = (int)($validated['qty2'] ?? 1);
        $validated['qty'] = $validated['qty'] ?? ($qty1 * $qty2);

        $noPl = $validated['no_pl'] ?? null;
        if (empty($validated['ad']) || empty($validated['jucol'])) {
            // Hitung otomatis nomor collie berikutnya berdasarkan nomor koli terakhir (last jucol)
            $existingCollies = CollieSppb::where('no_pl', $noPl)->get();
            $maxJucol = 0;
            foreach ($existingCollies as $item) {
                if (!empty($item->jucol) && (int)$item->jucol > $maxJucol) {
                    $maxJucol = (int)$item->jucol;
                } elseif (!empty($item->no_col)) {
                    $parts = explode('-', (string)$item->no_col);
                    $val = (int)end($parts);
                    if ($val > $maxJucol) $maxJucol = $val;
                }
            }
            $ad = $maxJucol + 1;
            $jucol = $ad + max(1, $qty1) - 1;
            $validated['ad'] = $ad;
            $validated['jucol'] = $jucol;
            $validated['no_col'] = ($ad == $jucol) ? (string)$ad : "{$ad}-{$jucol}";
        } else {
            $ad = (int)$validated['ad'];
            $jucol = (int)($validated['jucol'] ?? $ad);
            $validated['no_col'] = ($ad == $jucol) ? (string)$ad : "{$ad}-{$jucol}";
        }

        // creation_date default sekarang
        $validated['creation_date'] = now();

        $collie = CollieSppb::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            $stock = OttoMasterStock::findByCode($collie->code);

            return response()->json([
                'success' => true,
                'message' => 'Data Collie berhasil ditambahkan!',
                'item'    => [
                    'id'      => $collie->id,
                    'code'    => $collie->code,
                    'nama'    => $stock ? $stock->item_desc : ($collie->code ?: '-'),
                    'uom'     => $stock?->uom ?? '',
                    'no_col'  => $collie->no_col,
                    'qty1'    => $collie->qty1,
                    'qty2'    => $collie->qty2,
                    'qty'     => $collie->qty,
                    'qtyr'    => round((float)($collie->qtyr ?? 0), 2),
                    'ad'      => $collie->ad ?? $collie->no_col,
                    'no_pl'   => $collie->no_pl,
                    'no_sm'   => $collie->no_sm,
                ]
            ]);
        }

        return redirect()->route('collie.index')->with('success', 'Data Collie berhasil ditambahkan!');
    }

    /**
     * Mengambil daftar koli yang sudah di-input pada No. PL dan Tanggal tertentu (untuk modal)
     */
    public function getPlCollies(Request $request)
    {
        $noPl = $request->query('no_pl');
        $tglCol = $request->query('tgl_col');

        if (!$noPl) {
            return response()->json(['success' => true, 'items' => []]);
        }

        $query = CollieSppb::where('no_pl', $noPl);
        if (!empty($tglCol)) {
            $query->whereDate('tgl_col', $tglCol);
        }

        $items = $query->orderBy('id', 'asc')->get();

        // Ambil data nama barang dari Master Stock (otto_master_stock_v) sekaligus
        $codes = $items->pluck('code')->filter()->unique()->map(fn($c) => strtoupper(trim($c)));
        $stockMap = collect();
        if ($codes->isNotEmpty()) {
            $stockMap = OttoMasterStock::whereIn('item_code', $codes)
                ->orderByRaw("CASE WHEN jenis_bhn = 'FG' THEN 1 ELSE 2 END")
                ->orderByRaw("CASE WHEN anm = 'Active' THEN 1 ELSE 2 END")
                ->get()
                ->groupBy(fn($item) => strtoupper(trim($item->item_code)))
                ->map(fn($group) => $group->first());
        }

        // Hitung nomor collie tertinggi yang sudah ada pada No. PL ini
        $maxJucol = 0;
        foreach ($items as $c) {
            if (!empty($c->jucol) && (int)$c->jucol > $maxJucol) {
                $maxJucol = (int)$c->jucol;
            } elseif (!empty($c->no_col)) {
                $parts = explode('-', (string)$c->no_col);
                $val = (int)end($parts);
                if ($val > $maxJucol) $maxJucol = $val;
            }
        }

        return response()->json([
            'success'     => true,
            'no_pl'       => $noPl,
            'tgl_col'     => $tglCol,
            'total_koli'  => $items->sum('qty1'),
            'total_berat' => round($items->sum('qtyr'), 1),
            'last_jucol'  => $maxJucol,
            'items'       => $items->map(function ($c) use ($stockMap) {
                $stock = $stockMap->get(strtoupper(trim($c->code)));
                return [
                    'id'            => $c->id,
                    'no_pl'         => $c->no_pl ?? '',
                    'no_sm'         => $c->no_sm ?? '',
                    'no_col'        => $c->no_col ?? '',
                    'tgl_col'       => $c->tgl_col ? $c->tgl_col->format('Y-m-d') : '',
                    'cabang'        => $c->cabang ?? '',
                    'code'          => $c->code ?? '',
                    'nama'          => $stock ? $stock->item_desc : ($c->code ?: '-'),
                    'uom'           => $stock?->uom ?? '',
                    'qty'           => $c->qty ?? 0,
                    'qty1'          => $c->qty1 ?? 0,
                    'qty2'          => $c->qty2 ?? 0,
                    'qtyr'          => round((float)($c->qtyr ?? 0), 2),
                    'ad'            => $c->ad ?? '',
                    'jucol'         => $c->jucol ?? '',
                    'ekspedisi'     => $c->ekspedisi ?? '',
                    'no_sppb'       => $c->no_sppb ?? '',
                    'tgl_sppb'      => $c->tgl_sppb ? $c->tgl_sppb->format('Y-m-d') : '',
                    'customer_name' => $c->customer_name ?? '',
                ];
            })
        ]);
    }

    /**
     * Memperbarui massal seluruh baris Collie (Inline Grid Edit)
     */
    public function batchUpdate(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        \DB::beginTransaction();
        try {
            foreach ($request->items as $row) {
                $id = $row['id'] ?? null;
                if (!empty($id) && is_numeric($id)) {
                    $collie = CollieSppb::find($id);
                } else {
                    $collie = new CollieSppb();
                    $collie->creation_date = now();
                }

                if (!$collie) {
                    continue;
                }

                $collie->no_pl         = !empty($row['no_pl']) ? trim($row['no_pl']) : null;
                $collie->no_sm         = !empty($row['no_sm']) ? trim($row['no_sm']) : null;
                $collie->no_col        = !empty($row['no_col']) ? trim($row['no_col']) : null;
                $collie->tgl_col       = !empty($row['tgl_col']) ? $row['tgl_col'] : null;
                $collie->cabang        = !empty($row['cabang']) ? trim($row['cabang']) : null;
                $collie->code          = !empty($row['code']) ? strtoupper(trim($row['code'])) : null;
                
                $qty1                  = isset($row['qty1']) && $row['qty1'] !== '' ? intval($row['qty1']) : 0;
                $qty2                  = isset($row['qty2']) && $row['qty2'] !== '' ? intval($row['qty2']) : 0;
                $collie->qty1          = $qty1;
                $collie->qty2          = $qty2;
                $collie->qty           = isset($row['qty']) && $row['qty'] !== '' ? intval($row['qty']) : ($qty1 * $qty2);
                $collie->qtyr          = isset($row['qtyr']) && $row['qtyr'] !== '' ? floatval($row['qtyr']) : 0;
                $collie->ad            = isset($row['ad']) && $row['ad'] !== '' ? intval($row['ad']) : null;
                $collie->jucol         = isset($row['jucol']) && $row['jucol'] !== '' ? intval($row['jucol']) : null;

                // Format no_col otomatis jika ad & jucol terisi tapi no_col kosong
                if (empty($collie->no_col) && !empty($collie->ad)) {
                    $ad = $collie->ad;
                    $jucol = $collie->jucol ?? $ad;
                    $collie->no_col = ($ad == $jucol) ? (string)$ad : "{$ad}-{$jucol}";
                }

                $collie->ekspedisi     = !empty($row['ekspedisi']) ? trim($row['ekspedisi']) : null;
                $collie->no_sppb       = !empty($row['no_sppb']) ? trim($row['no_sppb']) : null;
                $collie->tgl_sppb      = !empty($row['tgl_sppb']) ? $row['tgl_sppb'] : null;
                $collie->customer_name = !empty($row['customer_name']) ? trim($row['customer_name']) : null;

                $collie->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Semua baris data Packing List berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus baris data Collie
     */
    public function destroy($id)
    {
        $collie = CollieSppb::findOrFail($id);
        $collie->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data koli dihapus!']);
        }

        return redirect()->route('collie.index')->with('success', 'Data Collie berhasil dihapus!');
    }
}

