<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Toko;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    public function index(Request $request)
    {
        $query = Toko::with(['wilayah', 'cabang']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_toko', 'like', "%$search%")
                  ->orWhere('nama_toko', 'like', "%$search%");
            });
        }

        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->wilayah_id);
        }

        $data     = $query->latest()->paginate(20)->withQueryString();
        $wilayahs = Wilayah::active()->orderBy('nama_wilayah')->get();
        $cabangs  = Cabang::active()->with('wilayah')->orderBy('nama_cabang')->get();

        return view('master.toko.index', compact('data', 'wilayahs', 'cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_toko'  => 'required|string|max:50|unique:tokos,kode_toko',
            'nama_toko'  => 'required|string|max:150',
            'wilayah_id' => 'nullable|exists:wilayahs,id',
            'cabang_id'  => 'nullable|exists:cabangs,id',
            'alamat'     => 'nullable|string|max:255',
        ], [
            'kode_toko.unique' => 'Kode toko sudah digunakan.',
        ]);

        Toko::create($request->only('kode_toko', 'nama_toko', 'wilayah_id', 'cabang_id', 'alamat') + ['is_active' => true]);

        return redirect()->route('master.toko.index')->with('success', 'Toko berhasil ditambahkan.');
    }

    public function update(Request $request, Toko $toko)
    {
        $request->validate([
            'kode_toko'  => 'required|string|max:50|unique:tokos,kode_toko,' . $toko->id,
            'nama_toko'  => 'required|string|max:150',
            'wilayah_id' => 'nullable|exists:wilayahs,id',
            'cabang_id'  => 'nullable|exists:cabangs,id',
            'alamat'     => 'nullable|string|max:255',
        ], [
            'kode_toko.unique' => 'Kode toko sudah digunakan.',
        ]);

        $toko->update($request->only('kode_toko', 'nama_toko', 'wilayah_id', 'cabang_id', 'alamat'));

        return redirect()->route('master.toko.index')->with('success', 'Toko berhasil diperbarui.');
    }

    public function destroy(Toko $toko)
    {
        if ($toko->reklames()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat dihapus, toko masih digunakan pada data reklame.');
        }

        $toko->delete();

        return redirect()->route('master.toko.index')->with('success', 'Toko berhasil dihapus.');
    }

    public function toggle(Toko $toko)
    {
        $toko->update(['is_active' => !$toko->is_active]);
        $status = $toko->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Toko berhasil $status.");
    }

    // Endpoint untuk mengambil daftar cabang berdasarkan wilayah (dipakai di form reklame)
    public function getCabangsByWilayah(Request $request)
    {
        $cabangs = Cabang::active()
            ->where('wilayah_id', $request->wilayah_id)
            ->orderBy('nama_cabang')
            ->get(['id', 'kode_cabang', 'nama_cabang']);

        return response()->json($cabangs);
    }
}
