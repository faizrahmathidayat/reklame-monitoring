<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Cabang;
use App\Models\Pic;
use App\Models\Reklame;
use App\Models\Spk;
use App\Models\Toko;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SpkController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $wilayahId = $request->input('wilayah_id');
        $brandId   = $request->input('brand_id');
        $tglDari   = $request->input('tgl_dari');
        $tglSampai = $request->input('tgl_sampai');

        $data = Spk::with(['wilayah', 'brand', 'pic'])
            ->withCount('reklames as jumlah_toko')
            ->when($search, function ($q) use ($search) {
                return $q->where('no_spk', 'like', "%{$search}%");
            })
            ->when($wilayahId, function ($q) use ($wilayahId) {
                return $q->where('wilayah_id', $wilayahId);
            })
            ->when($brandId, function ($q) use ($brandId) {
                return $q->where('brand_id', $brandId);
            })
            ->when($tglDari, function ($q) use ($tglDari) {
                return $q->where('tgl_spk', '>=', $tglDari);
            })
            ->when($tglSampai, function ($q) use ($tglSampai) {
                return $q->where('tgl_spk', '<=', $tglSampai);
            })
            ->orderBy('tgl_spk', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $wilayahs = Wilayah::active()->orderBy('nama_wilayah')->get();
        $brands   = Brand::active()->orderBy('nama_brand')->get();

        return view('spk.index', compact(
            'data', 'wilayahs', 'brands',
            'search', 'wilayahId', 'brandId', 'tglDari', 'tglSampai'
        ));
    }

    public function create()
    {
        return view('spk.create', array_merge($this->formData(), ['spk' => null]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_spk'              => 'required|string|max:100|unique:spks,no_spk',
            'tgl_spk'             => 'required|date',
            'deadline'            => 'nullable|date',
            'wilayah_id'          => 'required|exists:wilayahs,id',
            'brand_id'            => 'required|exists:brands,id',
            'cabang_id'           => 'nullable|exists:cabangs,id',
            'pic_id'              => 'nullable|exists:pics,id',
            'mulai_tanggal_input' => 'nullable|date',
            'keterangan'          => 'nullable|string|max:500',
            'note'                => 'nullable|string|max:500',
        ]);

        $spk = Spk::create(array_merge(
            $request->only([
                'no_spk', 'tgl_spk', 'deadline', 'wilayah_id', 'brand_id',
                'cabang_id', 'pic_id', 'mulai_tanggal_input', 'keterangan', 'note',
            ]),
            ['created_by' => Auth::id(), 'updated_by' => Auth::id()]
        ));

        return redirect()->route('spk.show', $spk)
            ->with('success', 'SPK "' . $spk->no_spk . '" berhasil dibuat. Silakan tambahkan toko.');
    }

    public function show(Spk $spk)
    {
        $spk->load([
            'wilayah', 'brand', 'cabang', 'pic',
            'createdBy', 'updatedBy',
            'reklames.toko',
        ]);

        $tokosAvail = Toko::active()
            ->where('wilayah_id', $spk->wilayah_id)
            ->orderBy('nama_toko')
            ->get();

        $statuses = Reklame::allStatuses();

        $statusSummary = $spk->reklames
            ->groupBy('status')
            ->map(function ($items) { return $items->count(); });

        $jumlahObjek = $spk->reklames->sum('jumlah_objek');

        return view('spk.show', compact('spk', 'tokosAvail', 'statuses', 'statusSummary', 'jumlahObjek'));
    }

    public function edit(Spk $spk)
    {
        return view('spk.edit', array_merge($this->formData(), compact('spk')));
    }

    public function update(Request $request, Spk $spk)
    {
        $request->validate([
            'no_spk'              => ['required', 'string', 'max:100', Rule::unique('spks')->ignore($spk->id)],
            'tgl_spk'             => 'required|date',
            'deadline'            => 'nullable|date',
            'wilayah_id'          => 'required|exists:wilayahs,id',
            'brand_id'            => 'required|exists:brands,id',
            'cabang_id'           => 'nullable|exists:cabangs,id',
            'pic_id'              => 'nullable|exists:pics,id',
            'mulai_tanggal_input' => 'nullable|date',
            'keterangan'          => 'nullable|string|max:500',
            'note'                => 'nullable|string|max:500',
        ]);

        $spk->update(array_merge(
            $request->only([
                'no_spk', 'tgl_spk', 'deadline', 'wilayah_id', 'brand_id',
                'cabang_id', 'pic_id', 'mulai_tanggal_input', 'keterangan', 'note',
            ]),
            ['updated_by' => Auth::id()]
        ));

        // Sync denormalized fields on child reklames
        Reklame::where('spk_id', $spk->id)->update([
            'no_spk'     => $spk->no_spk,
            'tgl_spk'    => $spk->tgl_spk->format('Y-m-d'),
            'deadline'   => optional($spk->deadline)->format('Y-m-d'),
            'wilayah_id' => $spk->wilayah_id,
            'brand_id'   => $spk->brand_id,
        ]);

        return redirect()->route('spk.show', $spk)
            ->with('success', 'SPK "' . $spk->no_spk . '" berhasil diperbarui.');
    }

    public function destroy(Spk $spk)
    {
        $noSpk = $spk->no_spk;
        $spk->delete();
        return redirect()->route('spk.index')
            ->with('success', 'SPK "' . $noSpk . '" berhasil dihapus.');
    }

    public function destroyToko(Spk $spk, Reklame $reklame)
    {
        if ((int) $reklame->spk_id !== (int) $spk->id) {
            abort(403, 'Toko ini bukan bagian dari SPK ini.');
        }

        $nama = optional($reklame->toko)->nama_toko ?? $reklame->kode_toko;
        $reklame->delete();

        return redirect()->route('spk.show', $spk)
            ->with('success', 'Toko "' . $nama . '" berhasil dihapus dari SPK.');
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function formData(): array
    {
        return [
            'wilayahs' => Wilayah::active()->orderBy('nama_wilayah')->get(),
            'cabangs'  => Cabang::active()->orderBy('nama_cabang')->get(),
            'brands'   => Brand::active()->orderBy('nama_brand')->get(),
            'pics'     => Pic::active()->orderBy('nama_pic')->get(),
            'tokos'    => Toko::active()->orderBy('nama_toko')->get(),
        ];
    }
}
