<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Distributor;

class DistributorController extends Controller
{
    /**
     * Tampilkan halaman daftar distributor beserta fitur pencarian.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));

        $query = Distributor::query();

        if (!empty($search)) {
            $isPgsql = DB::connection()->getDriverName() === 'pgsql';
            $likeOp = $isPgsql ? 'ilike' : 'like';

            $keywords = array_filter(preg_split('/\s+/', $search));

            $query->where(function ($q) use ($keywords, $likeOp) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word, $likeOp) {
                        $sub->where('nama', $likeOp, "%{$word}%")
                            ->orWhere('kota', $likeOp, "%{$word}%")
                            ->orWhere('alamat1', $likeOp, "%{$word}%")
                            ->orWhere('alamat2', $likeOp, "%{$word}%")
                            ->orWhere('alamat3', $likeOp, "%{$word}%")
                            ->orWhere('search_key', $likeOp, "%{$word}%")
                            ->orWhere('searchkey', $likeOp, "%{$word}%")
                            ->orWhere('telp', $likeOp, "%{$word}%");
                    });
                }
            });
        }

        $distributors = $query->orderBy('nama', 'asc')
                              ->orderBy('kota', 'asc')
                              ->paginate(15)
                              ->withQueryString();

        $totalCount = Distributor::count();

        return view('distributor.index', compact('distributors', 'search', 'totalCount'));
    }

    /**
     * Simpan distributor baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'kota'       => 'nullable|string|max:100',
            'alamat1'    => 'nullable|string|max:255',
            'alamat2'    => 'nullable|string|max:255',
            'alamat3'    => 'nullable|string|max:255',
            'telp'       => 'nullable|string|max:50',
            'search_key' => 'nullable|string|max:50',
        ]);

        $searchKeyVal = $validated['search_key'] ?? null;
        $validated['search_key'] = $searchKeyVal;
        $validated['searchkey']  = $searchKeyVal;

        Distributor::create($validated);

        return redirect()->route('distributor.index')
                         ->with('success', 'Distributor baru berhasil ditambahkan.');
    }

    /**
     * Perbarui data distributor yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $distributor = Distributor::findOrFail($id);

        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'kota'       => 'nullable|string|max:100',
            'alamat1'    => 'nullable|string|max:255',
            'alamat2'    => 'nullable|string|max:255',
            'alamat3'    => 'nullable|string|max:255',
            'telp'       => 'nullable|string|max:50',
            'search_key' => 'nullable|string|max:50',
        ]);

        $searchKeyVal = $validated['search_key'] ?? null;
        $validated['search_key'] = $searchKeyVal;
        $validated['searchkey']  = $searchKeyVal;

        $distributor->update($validated);

        return redirect()->route('distributor.index')
                         ->with('success', 'Data distributor berhasil diperbarui.');
    }

    /**
     * Hapus data distributor.
     */
    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $nama = $distributor->nama;
        $distributor->delete();

        return redirect()->route('distributor.index')
                         ->with('success', "Distributor \"{$nama}\" berhasil dihapus.");
    }
}
