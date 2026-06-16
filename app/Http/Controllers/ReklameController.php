<?php

namespace App\Http\Controllers;

use App\Models\Reklame;
use App\Models\Spk;
use App\Models\Toko;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReklameController extends Controller
{
    public function index(Request $request)
    {
        $query = Reklame::with(['wilayah', 'toko', 'brand'])
            ->orderBy('tgl_spk', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_spk', 'like', "%{$q}%")
                    ->orWhere('kode_toko', 'like', "%{$q}%")
                    ->orWhereHas('toko', fn ($t) => $t->where('nama_toko', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->wilayah_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_dari')) {
            $query->where('tgl_spk', '>=', $request->tgl_dari);
        }

        if ($request->filled('tgl_sampai')) {
            $query->where('tgl_spk', '<=', $request->tgl_sampai);
        }

        $data     = $query->paginate(15)->withQueryString();
        $wilayahs = Wilayah::active()->orderBy('nama_wilayah')->get();
        $statuses = Reklame::allStatuses();

        return view('reklame.index', compact('data', 'wilayahs', 'statuses'));
    }

    public function create(Request $request)
    {
        return view('reklame.create', array_merge($this->formData(), [
            'reklame'      => null,
            'defaultSpkId' => $request->input('spk_id'),
        ]));
    }

    public function store(Request $request)
    {
        $validated = $this->validateReklame($request);

        $spk  = Spk::findOrFail($validated['spk_id']);
        $toko = Toko::find($validated['toko_id']);

        $validated['no_spk']             = $spk->no_spk;
        $validated['tgl_spk']            = $spk->tgl_spk->format('Y-m-d');
        $validated['deadline']           = optional($spk->deadline)->format('Y-m-d');
        $validated['wilayah_id']         = $spk->wilayah_id;
        $validated['brand_id']           = $spk->brand_id;
        $validated['cabang_id']          = $spk->cabang_id ?? optional($toko)->cabang_id;
        $validated['pic_id']             = $spk->pic_id;
        if (empty($validated['mulai_tanggal_input'])) {
            $validated['mulai_tanggal_input'] = optional($spk->mulai_tanggal_input)->format('Y-m-d');
        }
        $validated['kode_toko']  = optional($toko)->kode_toko;
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Reklame::create($validated);

        $fromSpkId = $request->input('_from_spk_id');
        if ($fromSpkId) {
            return redirect()->route('spk.show', $fromSpkId)
                ->with('success', 'Toko berhasil ditambahkan ke SPK.');
        }

        return redirect()->route('reklame.index')
            ->with('success', 'Data reklame berhasil ditambahkan.');
    }

    public function show(Reklame $reklame)
    {
        $reklame->load(['wilayah', 'toko', 'brand', 'cabang', 'pic', 'createdBy', 'updatedBy']);
        return view('reklame.show', compact('reklame'));
    }

    public function edit(Reklame $reklame)
    {
        return view('reklame.edit', array_merge($this->formData(), compact('reklame')));
    }

    public function update(Request $request, Reklame $reklame)
    {
        $user = Auth::user();

        if ($user->isFinance() && !$user->isSuperadmin()) {
            $validated = $request->validate([
                'nominal'              => 'nullable|numeric|min:0',
                'tgl_terbit_skpd_baru' => 'nullable|date',
                'nomor_bayar'          => 'nullable|string|max:100',
                'jatuh_tempo'          => 'nullable|date',
            ]);
            $reklame->update(['updated_by' => $user->id] + $validated);
        } else {
            $validated = $this->validateReklame($request, $reklame->id);

            $spk  = Spk::findOrFail($validated['spk_id']);
            $toko = Toko::find($validated['toko_id']);

            $validated['no_spk']    = $spk->no_spk;
            $validated['tgl_spk']   = $spk->tgl_spk->format('Y-m-d');
            $validated['deadline']  = optional($spk->deadline)->format('Y-m-d');
            $validated['wilayah_id']= $spk->wilayah_id;
            $validated['brand_id']  = $spk->brand_id;
            $validated['cabang_id'] = $spk->cabang_id ?? optional($toko)->cabang_id;
            $validated['pic_id']    = $spk->pic_id;
            if (empty($validated['mulai_tanggal_input'])) {
                $validated['mulai_tanggal_input'] = optional($spk->mulai_tanggal_input)->format('Y-m-d');
            }
            $validated['kode_toko']  = optional($toko)->kode_toko;
            $validated['updated_by'] = $user->id;

            $reklame->update($validated);
        }

        return redirect()->route('reklame.show', $reklame)
            ->with('success', 'Data reklame berhasil diperbarui.');
    }

    public function destroy(Reklame $reklame)
    {
        $reklame->delete();
        return redirect()->route('reklame.index')
            ->with('success', 'Data reklame berhasil dihapus.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function formData(): array
    {
        return [
            'spks'     => Spk::with(['wilayah', 'brand'])->orderBy('tgl_spk', 'desc')->get(),
            'tokos'    => Toko::active()->orderBy('nama_toko')->get(),
            'statuses' => Reklame::allStatuses(),
        ];
    }

    private function validateReklame(Request $request, $ignoreReklameId = null): array
    {
        $spkId = $request->input('spk_id');

        $rules = [
            'spk_id'  => 'required|exists:spks,id',
            'toko_id' => [
                'required',
                'exists:tokos,id',
                Rule::unique('reklames', 'toko_id')
                    ->where(function ($q) use ($spkId) {
                        return $q->where('spk_id', $spkId)->whereNull('deleted_at');
                    })
                    ->ignore($ignoreReklameId),
            ],
            'status'                  => 'required|in:' . implode(',', Reklame::allStatuses()),
            'ukuran_reklame'          => 'nullable|string|max:100',
            'jumlah_objek'            => 'nullable|integer|min:1|max:9999',
            'tanggal_awal'            => 'nullable|date',
            'tanggal_awal_toko_baru'  => 'nullable|date',
            'tanggal_akhir_toko_baru' => 'nullable|date',
            'mulai_tanggal_input'     => 'nullable|date',
            'tanggal_update'          => 'nullable|date',
            'di_tolak'                => 'nullable|string|max:255',
            'tgl_pengajuan_ulang'     => 'nullable|date',
            'keterangan'              => 'nullable|string|max:500',
            'note'                    => 'nullable|string|max:500',
        ];

        if (Auth::user()->hasRole(['superadmin', 'finance'])) {
            $rules += [
                'nominal'              => 'nullable|numeric|min:0',
                'tgl_terbit_skpd_baru' => 'nullable|date',
                'nomor_bayar'          => 'nullable|string|max:100',
                'jatuh_tempo'          => 'nullable|date',
            ];
        }

        return $request->validate($rules, [
            'toko_id.unique' => 'Toko ini sudah terdaftar dalam SPK yang dipilih.',
        ]);
    }
}
