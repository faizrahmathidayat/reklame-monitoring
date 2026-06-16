{{-- Partial: SPK header fields (used in create & edit) --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-file-contract me-2" style="color:var(--accent)"></i>Informasi SPK</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">No. SPK <span class="text-danger">*</span></label>
                <input type="text" name="no_spk" class="form-control form-control-sm"
                    value="{{ old('no_spk', optional($spk)->no_spk) }}"
                    placeholder="Nomor SPK" maxlength="100" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Tgl. SPK <span class="text-danger">*</span></label>
                <input type="date" name="tgl_spk" class="form-control form-control-sm"
                    value="{{ old('tgl_spk', optional(optional($spk)->tgl_spk)->format('Y-m-d')) }}" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Deadline</label>
                <input type="date" name="deadline" class="form-control form-control-sm"
                    value="{{ old('deadline', optional(optional($spk)->deadline)->format('Y-m-d')) }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Mulai Tgl. Input</label>
                <input type="date" name="mulai_tanggal_input" class="form-control form-control-sm"
                    value="{{ old('mulai_tanggal_input', optional(optional($spk)->mulai_tanggal_input)->format('Y-m-d')) }}">
            </div>
        </div>
    </div>
</div>

<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-map-pin me-2" style="color:var(--accent)"></i>Lokasi &amp; Entitas</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">Wilayah / DC <span class="text-danger">*</span></label>
                <select name="wilayah_id" id="s_wilayah" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Wilayah --</option>
                    @foreach($wilayahs as $w)
                    <option value="{{ $w->id }}"
                        {{ old('wilayah_id', optional($spk)->wilayah_id) == $w->id ? 'selected' : '' }}>
                        {{ $w->nama_wilayah }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Cabang</label>
                <select name="cabang_id" id="s_cabang" class="form-select form-select-sm">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabangs as $c)
                    <option value="{{ $c->id }}"
                        data-wilayah="{{ $c->wilayah_id }}"
                        {{ old('cabang_id', optional($spk)->cabang_id) == $c->id ? 'selected' : '' }}>
                        {{ $c->nama_cabang }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Brand <span class="text-danger">*</span></label>
                <select name="brand_id" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Brand --</option>
                    @foreach($brands as $b)
                    <option value="{{ $b->id }}"
                        {{ old('brand_id', optional($spk)->brand_id) == $b->id ? 'selected' : '' }}>
                        {{ $b->nama_brand }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">PIC</label>
                <select name="pic_id" class="form-select form-select-sm">
                    <option value="">-- Pilih PIC --</option>
                    @foreach($pics as $p)
                    <option value="{{ $p->id }}"
                        {{ old('pic_id', optional($spk)->pic_id) == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_pic }}@if($p->jabatan) — {{ $p->jabatan }}@endif
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-note-sticky me-2" style="color:var(--accent)"></i>Catatan</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control form-control-sm" rows="3"
                    maxlength="500" placeholder="Keterangan SPK...">{{ old('keterangan', optional($spk)->keterangan) }}</textarea>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Note Internal</label>
                <textarea name="note" class="form-control form-control-sm" rows="3"
                    maxlength="500" placeholder="Catatan internal...">{{ old('note', optional($spk)->note) }}</textarea>
            </div>
        </div>
    </div>
</div>
