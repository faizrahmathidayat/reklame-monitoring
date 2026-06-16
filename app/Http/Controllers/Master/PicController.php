<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Pic;
use Illuminate\Http\Request;

class PicController extends Controller
{
    public function index(Request $request)
    {
        $query = Pic::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pic', 'like', "%$search%")
                  ->orWhere('jabatan', 'like', "%$search%");
            });
        }

        $data = $query->latest()->paginate(15)->withQueryString();

        return view('master.pic.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pic' => 'required|string|max:100',
            'jabatan'  => 'nullable|string|max:100',
            'telepon'  => 'nullable|string|max:20',
        ]);

        Pic::create($request->only('nama_pic', 'jabatan', 'telepon') + ['is_active' => true]);

        return redirect()->route('master.pic.index')->with('success', 'PIC berhasil ditambahkan.');
    }

    public function update(Request $request, Pic $pic)
    {
        $request->validate([
            'nama_pic' => 'required|string|max:100',
            'jabatan'  => 'nullable|string|max:100',
            'telepon'  => 'nullable|string|max:20',
        ]);

        $pic->update($request->only('nama_pic', 'jabatan', 'telepon'));

        return redirect()->route('master.pic.index')->with('success', 'PIC berhasil diperbarui.');
    }

    public function destroy(Pic $pic)
    {
        if ($pic->reklames()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat dihapus, PIC masih digunakan pada data reklame.');
        }

        $pic->delete();

        return redirect()->route('master.pic.index')->with('success', 'PIC berhasil dihapus.');
    }

    public function toggle(Pic $pic)
    {
        $pic->update(['is_active' => !$pic->is_active]);
        $status = $pic->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "PIC berhasil $status.");
    }
}
