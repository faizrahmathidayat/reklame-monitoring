<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('search')) {
            $query->where('nama_brand', 'like', '%' . $request->search . '%');
        }

        $data = $query->latest()->paginate(15)->withQueryString();

        return view('master.brand.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_brand' => 'required|string|max:100|unique:brands,nama_brand',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'nama_brand.unique' => 'Nama brand sudah terdaftar.',
        ]);

        Brand::create($request->only('nama_brand', 'keterangan') + ['is_active' => true]);

        return redirect()->route('master.brand.index')->with('success', 'Brand berhasil ditambahkan.');
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'nama_brand' => 'required|string|max:100|unique:brands,nama_brand,' . $brand->id,
            'keterangan' => 'nullable|string|max:255',
        ], [
            'nama_brand.unique' => 'Nama brand sudah terdaftar.',
        ]);

        $brand->update($request->only('nama_brand', 'keterangan'));

        return redirect()->route('master.brand.index')->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->reklames()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat dihapus, brand masih digunakan pada data reklame.');
        }

        $brand->delete();

        return redirect()->route('master.brand.index')->with('success', 'Brand berhasil dihapus.');
    }

    public function toggle(Brand $brand)
    {
        $brand->update(['is_active' => !$brand->is_active]);
        $status = $brand->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Brand berhasil $status.");
    }
}
