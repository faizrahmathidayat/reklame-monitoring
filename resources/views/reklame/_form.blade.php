@php
    $user          = auth()->user();
    $isFinanceOnly = $user->isFinance() && !$user->isSuperadmin();
    $canFinance    = $user->hasRole(['superadmin', 'finance']);
    $currentReklame = $reklame ?? null;
    $defaultSpkId   = $defaultSpkId ?? null;

    $tokosJson = json_encode(
        $tokos->map(function ($t) {
            return ['id' => $t->id, 'nama' => $t->nama_toko, 'kode' => $t->kode_toko,
                    'wilayah_id' => $t->wilayah_id, 'cabang_id' => $t->cabang_id];
        })->values()->toArray()
    );
    $spksJson = json_encode(
        $spks->map(function ($s) {
            return [
                'id'         => $s->id,
                'no_spk'     => $s->no_spk,
                'wilayah_id' => $s->wilayah_id,
                'wilayah'    => optional($s->wilayah)->nama_wilayah,
                'brand'      => optional($s->brand)->nama_brand,
                'tgl_spk'    => optional($s->tgl_spk)->format('d/m/Y'),
                'deadline'   => optional($s->deadline)->format('d/m/Y'),
            ];
        })->values()->toArray()
    );
    $currentSpkId = old('spk_id', optional($currentReklame)->spk_id ?? $defaultSpkId);
@endphp

@if($errors->any())
<div class="alert mb-4" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:0.625rem;padding:0.75rem 1rem;font-size:0.875rem">
    <i class="fa-solid fa-circle-exclamation me-2"></i><strong>Terdapat kesalahan input:</strong>
    <ul class="mb-0 mt-1 ps-3">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

@if($isFinanceOnly && $currentReklame)
<div class="card-dark mb-3" style="border-color:rgba(99,102,241,0.25)">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-circle-info me-2" style="color:var(--accent)"></i>Info Reklame</h6>
    </div>
    <div class="card-body">
        <div class="row g-2" style="font-size:0.875rem">
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">No. SPK</div>
                <div style="font-weight:600;color:#a5b4fc;font-family:monospace">{{ $currentReklame->no_spk }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Status</div>
                <div><span class="status-badge {{ $currentReklame->statusBadgeClass() }}">{{ $currentReklame->status }}</span></div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Toko</div>
                <div>{{ optional($currentReklame->toko)->nama_toko ?? $currentReklame->kode_toko }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Tgl. SPK</div>
                <div>{{ $currentReklame->tgl_spk ? $currentReklame->tgl_spk->format('d/m/Y') : '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endif

@if(!$isFinanceOnly)

{{-- ═══ SEKSI 1 — Pilih SPK ═══ --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-file-contract me-2" style="color:var(--accent)"></i>Pilih SPK</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Nomor SPK <span class="text-danger">*</span></label>
                <select name="spk_id" id="f_spk" class="form-select form-select-sm" required>
                    <option value="">-- Pilih SPK --</option>
                    @foreach($spks as $s)
                    <option value="{{ $s->id }}" {{ $currentSpkId == $s->id ? 'selected' : '' }}>
                        {{ $s->no_spk }} — {{ optional($s->wilayah)->nama_wilayah }} | {{ optional($s->brand)->nama_brand }}
                    </option>
                    @endforeach
                </select>
                <div style="font-size:0.72rem;color:var(--text-dim);margin-top:0.25rem">
                    Belum ada SPK? <a href="{{ route('spk.create') }}" target="_blank" style="color:var(--accent)">Buat SPK baru</a> dulu.
                </div>
            </div>
        </div>

        {{-- Info SPK (muncul setelah pilih) --}}
        <div id="spk-info" class="mt-3 p-3 rounded" style="background:var(--bg-elevated);{{ $currentSpkId ? '' : 'display:none' }}">
            <div class="row g-2" style="font-size:0.83rem">
                <div class="col-6 col-md-3">
                    <div style="color:var(--text-dim);font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px">DC / Wilayah</div>
                    <div id="info-wilayah" style="font-weight:600">—</div>
                </div>
                <div class="col-6 col-md-3">
                    <div style="color:var(--text-dim);font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px">Brand</div>
                    <div id="info-brand">—</div>
                </div>
                <div class="col-6 col-md-3">
                    <div style="color:var(--text-dim);font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px">Tgl. SPK</div>
                    <div id="info-tgl">—</div>
                </div>
                <div class="col-6 col-md-3">
                    <div style="color:var(--text-dim);font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px">Deadline</div>
                    <div id="info-deadline">—</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ SEKSI 2 — Toko & Status ═══ --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-store me-2" style="color:var(--accent)"></i>Toko &amp; Status</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Toko <span class="text-danger">*</span></label>
                <select name="toko_id" id="f_toko" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Toko --</option>
                    @foreach($tokos as $t)
                    <option value="{{ $t->id }}"
                        data-wilayah="{{ $t->wilayah_id }}"
                        data-kode="{{ $t->kode_toko }}"
                        {{ old('toko_id', optional($currentReklame)->toko_id) == $t->id ? 'selected' : '' }}>
                        {{ $t->kode_toko }} — {{ $t->nama_toko }}
                    </option>
                    @endforeach
                </select>
                <div style="font-size:0.72rem;color:var(--text-dim);margin-top:0.2rem">Otomatis difilter sesuai DC dari SPK</div>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Kode Toko</label>
                <input type="text" id="f_kode_toko" class="form-control form-control-sm" readonly
                    value="{{ old('kode_toko', optional($currentReklame)->kode_toko) }}"
                    style="background:var(--bg-elevated);color:var(--text-dim);cursor:default"
                    placeholder="Otomatis" tabindex="-1">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select form-select-sm" required>
                    <option value="">-- Status --</option>
                    @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ old('status', optional($currentReklame)->status) === $s ? 'selected' : '' }}>
                        {{ $s }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

{{-- ═══ SEKSI 3 — Detail Reklame ═══ --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-rectangle-ad me-2" style="color:var(--accent)"></i>Detail Reklame</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Ukuran Reklame</label>
                <input type="text" name="ukuran_reklame" class="form-control form-control-sm"
                    value="{{ old('ukuran_reklame', optional($currentReklame)->ukuran_reklame) }}"
                    placeholder="Contoh: 3x2 m" maxlength="100">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Jml. Objek</label>
                <input type="number" name="jumlah_objek" class="form-control form-control-sm"
                    value="{{ old('jumlah_objek', optional($currentReklame)->jumlah_objek ?? 1) }}"
                    min="1" max="9999" placeholder="1">
                <div style="font-size:0.72rem;color:var(--text-dim);margin-top:0.2rem">Reklame fisik</div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" class="form-control form-control-sm"
                    value="{{ old('tanggal_awal', optional(optional($currentReklame)->tanggal_awal)->format('Y-m-d')) }}">
            </div>
            <div class="col-12 col-md-2"></div>
            <div class="col-12 col-md-4">
                <label class="form-label">Tgl. Awal Toko Baru</label>
                <input type="date" name="tanggal_awal_toko_baru" class="form-control form-control-sm"
                    value="{{ old('tanggal_awal_toko_baru', optional(optional($currentReklame)->tanggal_awal_toko_baru)->format('Y-m-d')) }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Tgl. Akhir Toko Baru</label>
                <input type="date" name="tanggal_akhir_toko_baru" class="form-control form-control-sm"
                    value="{{ old('tanggal_akhir_toko_baru', optional(optional($currentReklame)->tanggal_akhir_toko_baru)->format('Y-m-d')) }}">
            </div>
        </div>
    </div>
</div>

{{-- ═══ SEKSI 4 — Proses & Timeline ═══ --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-timeline me-2" style="color:var(--accent)"></i>Proses &amp; Timeline</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label">Mulai Tgl. Input</label>
                <input type="date" name="mulai_tanggal_input" class="form-control form-control-sm"
                    value="{{ old('mulai_tanggal_input', optional(optional($currentReklame)->mulai_tanggal_input)->format('Y-m-d')) }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Tgl. Update</label>
                <input type="date" name="tanggal_update" class="form-control form-control-sm"
                    value="{{ old('tanggal_update', optional(optional($currentReklame)->tanggal_update)->format('Y-m-d')) }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Di Tolak</label>
                <input type="text" name="di_tolak" class="form-control form-control-sm"
                    value="{{ old('di_tolak', optional($currentReklame)->di_tolak) }}"
                    placeholder="Keterangan penolakan (jika ada)" maxlength="255">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Tgl. Pengajuan Ulang</label>
                <input type="date" name="tgl_pengajuan_ulang" class="form-control form-control-sm"
                    value="{{ old('tgl_pengajuan_ulang', optional(optional($currentReklame)->tgl_pengajuan_ulang)->format('Y-m-d')) }}">
            </div>
        </div>
    </div>
</div>
@endif {{-- !$isFinanceOnly --}}

@if($canFinance)
{{-- ═══ SEKSI 5 — Data Keuangan ═══ --}}
<div class="card-dark mb-3" style="border-color:rgba(245,158,11,0.35)">
    <div class="card-header" style="border-bottom-color:rgba(245,158,11,0.2)">
        <h6 class="card-title">
            <i class="fa-solid fa-coins me-2" style="color:#f59e0b"></i>Data Keuangan
            <span class="ms-2" style="background:rgba(245,158,11,0.15);color:#fcd34d;font-size:0.65rem;font-weight:600;padding:0.15rem 0.5rem;border-radius:0.3rem;text-transform:uppercase;letter-spacing:0.04em">Finance</span>
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Nominal (Rp)</label>
                <input type="number" name="nominal" class="form-control form-control-sm"
                    value="{{ old('nominal', optional($currentReklame)->nominal) }}"
                    placeholder="0" min="0" step="1">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">No. Bayar</label>
                <input type="text" name="nomor_bayar" class="form-control form-control-sm"
                    value="{{ old('nomor_bayar', optional($currentReklame)->nomor_bayar) }}"
                    placeholder="Nomor bayar" maxlength="100">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Jatuh Tempo</label>
                <input type="date" name="jatuh_tempo" class="form-control form-control-sm"
                    value="{{ old('jatuh_tempo', optional(optional($currentReklame)->jatuh_tempo)->format('Y-m-d')) }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Tgl. Terbit SKPD Baru</label>
                <input type="date" name="tgl_terbit_skpd_baru" class="form-control form-control-sm"
                    value="{{ old('tgl_terbit_skpd_baru', optional(optional($currentReklame)->tgl_terbit_skpd_baru)->format('Y-m-d')) }}">
            </div>
        </div>
    </div>
</div>
@endif

@if(!$isFinanceOnly)
{{-- ═══ SEKSI 6 — Catatan ═══ --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-note-sticky me-2" style="color:var(--accent)"></i>Catatan</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control form-control-sm" rows="3"
                    maxlength="500" placeholder="Keterangan tambahan...">{{ old('keterangan', optional($currentReklame)->keterangan) }}</textarea>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Note Internal</label>
                <textarea name="note" class="form-control form-control-sm" rows="3"
                    maxlength="500" placeholder="Catatan internal...">{{ old('note', optional($currentReklame)->note) }}</textarea>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
(function () {
    var allSpks  = {!! $spksJson !!};
    var allTokos = {!! $tokosJson !!};

    function findSpk(id) {
        for (var i = 0; i < allSpks.length; i++) {
            if (allSpks[i].id == id) return allSpks[i];
        }
        return null;
    }

    function updateSpkInfo(spk) {
        var info = document.getElementById('spk-info');
        if (!info) return;
        if (!spk) { info.style.display = 'none'; return; }
        document.getElementById('info-wilayah').textContent  = spk.wilayah   || '—';
        document.getElementById('info-brand').textContent    = spk.brand      || '—';
        document.getElementById('info-tgl').textContent      = spk.tgl_spk   || '—';
        document.getElementById('info-deadline').textContent = spk.deadline   || '—';
        info.style.display = '';
    }

    function populateTokos(wilayahId, selectedId) {
        var sel = document.getElementById('f_toko');
        if (!sel) return;
        var cur = (selectedId !== undefined ? selectedId : sel.value);
        sel.innerHTML = '<option value="">-- Pilih Toko --</option>';
        var list = wilayahId
            ? allTokos.filter(function (t) { return t.wilayah_id == wilayahId; })
            : allTokos;
        list.forEach(function (t) {
            var opt = new Option(t.kode + ' — ' + t.nama, t.id);
            if (cur && t.id == cur) opt.selected = true;
            sel.appendChild(opt);
        });
        syncKodeToko();
    }

    function syncKodeToko() {
        var sel    = document.getElementById('f_toko');
        var kodeEl = document.getElementById('f_kode_toko');
        if (!sel || !kodeEl) return;
        var toko = null;
        for (var i = 0; i < allTokos.length; i++) {
            if (allTokos[i].id == sel.value) { toko = allTokos[i]; break; }
        }
        kodeEl.value = toko ? toko.kode : '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var spkSel  = document.getElementById('f_spk');
        var tokoSel = document.getElementById('f_toko');
        if (!spkSel) return;

        var initSpkId  = spkSel.value;
        var initTokoId = tokoSel ? tokoSel.value : null;

        if (initSpkId) {
            var spk = findSpk(initSpkId);
            updateSpkInfo(spk);
            populateTokos(spk ? spk.wilayah_id : null, initTokoId);
        }

        spkSel.addEventListener('change', function () {
            var spk = findSpk(this.value);
            updateSpkInfo(spk);
            populateTokos(spk ? spk.wilayah_id : null, null);
        });

        if (tokoSel) {
            tokoSel.addEventListener('change', syncKodeToko);
        }
    });
})();
</script>
@endpush
