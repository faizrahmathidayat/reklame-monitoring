<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $query = Wilayah::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_wilayah', 'like', "%$search%")
                  ->orWhere('nama_wilayah', 'like', "%$search%");
            });
        }

        $data = $query->latest()->paginate(15)->withQueryString();

        return view('master.wilayah.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_wilayah' => 'required|string|max:20|unique:wilayahs,kode_wilayah',
            'nama_wilayah' => 'required|string|max:100',
            'keterangan'   => 'nullable|string|max:255',
        ], [
            'kode_wilayah.unique' => 'Kode wilayah sudah digunakan.',
        ]);

        Wilayah::create($request->only('kode_wilayah', 'nama_wilayah', 'keterangan') + ['is_active' => true]);

        return redirect()->route('master.wilayah.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function update(Request $request, Wilayah $wilayah)
    {
        $request->validate([
            'kode_wilayah' => 'required|string|max:20|unique:wilayahs,kode_wilayah,' . $wilayah->id,
            'nama_wilayah' => 'required|string|max:100',
            'keterangan'   => 'nullable|string|max:255',
        ], [
            'kode_wilayah.unique' => 'Kode wilayah sudah digunakan.',
        ]);

        $wilayah->update($request->only('kode_wilayah', 'nama_wilayah', 'keterangan'));

        return redirect()->route('master.wilayah.index')->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(Wilayah $wilayah)
    {
        if ($wilayah->reklames()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat dihapus, wilayah masih digunakan pada data reklame.');
        }

        $wilayah->delete();

        return redirect()->route('master.wilayah.index')->with('success', 'Wilayah berhasil dihapus.');
    }

    public function toggle(Wilayah $wilayah)
    {
        $wilayah->update(['is_active' => !$wilayah->is_active]);
        $status = $wilayah->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Wilayah berhasil $status.");
    }
}
