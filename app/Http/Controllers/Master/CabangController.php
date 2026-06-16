<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index(Request $request)
    {
        $query = Cabang::with('wilayah');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_cabang', 'like', "%$search%")
                  ->orWhere('nama_cabang', 'like', "%$search%");
            });
        }

        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->wilayah_id);
        }

        $data     = $query->latest()->paginate(15)->withQueryString();
        $wilayahs = Wilayah::active()->orderBy('nama_wilayah')->get();

        return view('master.cabang.index', compact('data', 'wilayahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_cabang' => 'required|string|max:20|unique:cabangs,kode_cabang',
            'nama_cabang' => 'required|string|max:100',
            'wilayah_id'  => 'nullable|exists:wilayahs,id',
        ], [
            'kode_cabang.unique' => 'Kode cabang sudah digunakan.',
            'wilayah_id.exists'  => 'Wilayah tidak ditemukan.',
        ]);

        Cabang::create($request->only('kode_cabang', 'nama_cabang', 'wilayah_id') + ['is_active' => true]);

        return redirect()->route('master.cabang.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Cabang $cabang)
    {
        $request->validate([
            'kode_cabang' => 'required|string|max:20|unique:cabangs,kode_cabang,' . $cabang->id,
            'nama_cabang' => 'required|string|max:100',
            'wilayah_id'  => 'nullable|exists:wilayahs,id',
        ], [
            'kode_cabang.unique' => 'Kode cabang sudah digunakan.',
        ]);

        $cabang->update($request->only('kode_cabang', 'nama_cabang', 'wilayah_id'));

        return redirect()->route('master.cabang.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Cabang $cabang)
    {
        if ($cabang->reklames()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat dihapus, cabang masih digunakan pada data reklame.');
        }

        $cabang->delete();

        return redirect()->route('master.cabang.index')->with('success', 'Cabang berhasil dihapus.');
    }

    public function toggle(Cabang $cabang)
    {
        $cabang->update(['is_active' => !$cabang->is_active]);
        $status = $cabang->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Cabang berhasil $status.");
    }
}
