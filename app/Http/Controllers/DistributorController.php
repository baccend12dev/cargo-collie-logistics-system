<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kota', 'like', "%{$search}%")
                  ->orWhere('alamat1', 'like', "%{$search}%")
                  ->orWhere('alamat2', 'like', "%{$search}%")
                  ->orWhere('alamat3', 'like', "%{$search}%")
                  ->orWhere('search_key', 'like', "%{$search}%")
                  ->orWhere('telp', 'like', "%{$search}%");
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

        $validated['searchkey'] = $validated['search_key'] ?? null;

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

        $validated['searchkey'] = $validated['search_key'] ?? null;

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
