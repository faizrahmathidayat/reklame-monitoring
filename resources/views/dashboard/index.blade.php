@extends($isMobile ? 'layouts.mobile' : 'layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

@if($isMobile)
{{-- ═══════════════════════════════════════ MOBILE DASHBOARD ═══ --}}

{{-- Stat Cards 2x2 --}}
<div class="row g-2 mb-2">
    <div class="col-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,0.15)">
                <i class="fa-solid fa-file-contract" style="color:#818cf8"></i>
            </div>
            <div class="stat-value">{{ number_format($totalSpk) }}</div>
            <div class="stat-label">Total SPK</div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(251,146,60,0.15)">
                <i class="fa-solid fa-spinner" style="color:#fb923c"></i>
            </div>
            <div class="stat-value">{{ number_format($totalProses) }}</div>
            <div class="stat-label">Proses Aktif</div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(34,197,94,0.15)">
                <i class="fa-solid fa-circle-check" style="color:#4ade80"></i>
            </div>
            <div class="stat-value">{{ number_format($totalSelesai) }}</div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,0.15)">
                <i class="fa-solid fa-ban" style="color:#f87171"></i>
            </div>
            <div class="stat-value">{{ number_format($totalCancel) }}</div>
            <div class="stat-label">Cancel</div>
        </div>
    </div>
</div>

{{-- Brand Breakdown --}}
@if($brandBreakdown->isNotEmpty())
<div class="m-section-title">Ringkasan per Brand</div>
@foreach($brandBreakdown as $row)
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-1">
        <span style="font-weight:600;font-size:0.875rem">{{ $row['brand']->nama_brand }}</span>
        <span style="font-weight:700;color:var(--accent);font-size:0.875rem">{{ $row['total'] }}</span>
    </div>
    <div class="d-flex gap-2" style="font-size:0.72rem;color:var(--text-dim)">
        <span><i class="fa-solid fa-spinner me-1" style="color:#fb923c"></i>{{ $row['proses'] }} Proses</span>
        <span><i class="fa-solid fa-check me-1" style="color:#4ade80"></i>{{ $row['selesai'] }} Selesai</span>
        @if($row['cancel'] > 0)
        <span><i class="fa-solid fa-ban me-1" style="color:#f87171"></i>{{ $row['cancel'] }} Cancel</span>
        @endif
    </div>
</div>
@endforeach
@endif

{{-- Wilayah Breakdown --}}
@php $activeWilayahs = $wilayahBreakdown->filter(function($r){ return $r['total'] > 0; }); @endphp
@if($activeWilayahs->isNotEmpty())
<div class="m-section-title">Ringkasan per DC / Wilayah</div>
@foreach($activeWilayahs as $row)
<div class="m-card">
    <div class="d-flex align-items-center justify-content-between mb-1">
        <div>
            <div style="font-weight:600;font-size:0.85rem">{{ $row['wilayah']->nama_wilayah }}</div>
            <div style="font-size:0.68rem;color:var(--text-dim);font-family:monospace">{{ $row['wilayah']->kode_wilayah }}</div>
        </div>
        <span style="font-weight:700;color:var(--accent)">{{ $row['total'] }}</span>
    </div>
    <div class="d-flex gap-2" style="font-size:0.72rem">
        <span style="color:#fb923c">{{ $row['proses'] }} Proses</span>
        <span style="color:#4ade80">{{ $row['selesai'] }} Selesai</span>
        @if($row['cancel'] > 0)<span style="color:#f87171">{{ $row['cancel'] }} Cancel</span>@endif
    </div>
</div>
@endforeach
@endif

{{-- Deadline Near --}}
@if($deadlineNear->isNotEmpty())
<div class="m-section-title"><i class="fa-solid fa-triangle-exclamation me-1" style="color:#f59e0b"></i>Deadline ≤ 14 Hari</div>
@foreach($deadlineNear as $r)
@php $daysLeft = (int) now()->diffInDays($r->deadline, false); @endphp
<a href="{{ route('reklame.show', $r->id) }}" class="m-card d-block text-decoration-none">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div style="font-weight:600;font-size:0.82rem;color:var(--text-primary)">{{ $r->no_spk ?? '—' }}</div>
            <div style="font-size:0.72rem;color:var(--text-dim)">{{ optional($r->toko)->nama_toko ?? '-' }}</div>
        </div>
        <div class="text-end">
            <div style="font-size:0.78rem;font-weight:600;color:{{ $daysLeft <= 3 ? '#ef4444' : ($daysLeft <= 7 ? '#f59e0b' : '#94a3b8') }}">
                {{ $r->deadline->format('d/m/Y') }}
            </div>
            <div style="font-size:0.65rem;color:var(--text-dim)">{{ $daysLeft }} hari lagi</div>
        </div>
    </div>
    <div class="mt-1">
        <span class="status-badge {{ $r->statusBadgeClass() }}" style="font-size:0.6rem">{{ $r->status }}</span>
    </div>
</a>
@endforeach
@endif

{{-- Latest --}}
<div class="m-section-title">5 Data Terbaru</div>
@forelse($latestReklames as $r)
<a href="{{ route('reklame.show', $r->id) }}" class="m-card d-block text-decoration-none">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div style="font-weight:600;font-size:0.82rem;color:var(--text-primary)">{{ $r->no_spk ?? '—' }}</div>
            <div style="font-size:0.72rem;color:var(--text-dim)">{{ optional($r->toko)->nama_toko ?? '-' }}</div>
        </div>
        <div class="text-end">
            <span class="status-badge {{ $r->statusBadgeClass() }}" style="font-size:0.6rem">{{ $r->status }}</span>
            <div style="font-size:0.65rem;color:var(--text-dim);margin-top:3px">{{ $r->tgl_spk ? $r->tgl_spk->format('d/m/Y') : '-' }}</div>
        </div>
    </div>
</a>
@empty
<div class="m-card text-center" style="color:var(--text-dim);font-size:0.82rem">Belum ada data.</div>
@endforelse

@else
{{-- ═══════════════════════════════════════ DESKTOP DASHBOARD ═══ --}}

{{-- Page Header --}}
<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-gauge me-2" style="color:var(--accent)"></i>Dashboard</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Ringkasan Data Reklame</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('report') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-chart-bar me-1"></i> Laporan Lengkap
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card-dark mb-4">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('dashboard') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-auto">
                    <label class="form-label mb-1" style="font-size:0.8rem;color:var(--text-dim)">Dari Tgl SPK</label>
                    <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ $tglDari }}" style="width:150px">
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="form-label mb-1" style="font-size:0.8rem;color:var(--text-dim)">Sampai</label>
                    <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ $tglSampai }}" style="width:150px">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-filter me-1"></i>Terapkan
                    </button>
                    @if($tglDari || $tglSampai)
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </div>
                @if($tglDari || $tglSampai)
                <div class="col-auto ms-auto">
                    <span style="font-size:0.78rem;color:var(--accent)">
                        <i class="fa-solid fa-calendar-check me-1"></i>
                        Filter aktif:
                        {{ $tglDari ? \Carbon\Carbon::parse($tglDari)->format('d/m/Y') : '...' }}
                        –
                        {{ $tglSampai ? \Carbon\Carbon::parse($tglSampai)->format('d/m/Y') : '...' }}
                    </span>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,0.15);">
                <i class="fa-solid fa-file-contract" style="color:#818cf8;"></i>
            </div>
            <div class="stat-value">{{ number_format($totalSpk) }}</div>
            <div class="stat-label">Total SPK</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(251,146,60,0.15);">
                <i class="fa-solid fa-spinner" style="color:#fb923c;"></i>
            </div>
            <div class="stat-value">{{ number_format($totalProses) }}</div>
            <div class="stat-label">Proses Aktif</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(34,197,94,0.15);">
                <i class="fa-solid fa-circle-check" style="color:#4ade80;"></i>
            </div>
            <div class="stat-value">{{ number_format($totalSelesai) }}</div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,0.15);">
                <i class="fa-solid fa-ban" style="color:#f87171;"></i>
            </div>
            <div class="stat-value">{{ number_format($totalCancel) }}</div>
            <div class="stat-label">Cancel</div>
        </div>
    </div>
</div>

{{-- Chart + Wilayah Breakdown --}}
<div class="row g-3 mb-4">

    {{-- Donut Chart --}}
    <div class="col-12 col-lg-5">
        <div class="card-dark h-100">
            <div class="card-header">
                <h6 class="card-title">
                    <i class="fa-solid fa-chart-pie me-2" style="color:var(--accent)"></i>Distribusi Status
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height:260px">
                @if($totalSpk > 0)
                    <canvas id="statusChart" style="max-height:240px"></canvas>
                @else
                    <div class="text-center" style="color:var(--text-dim)">
                        <i class="fa-solid fa-chart-pie fa-2x mb-2 d-block"></i>
                        <span style="font-size:0.85rem">Belum ada data</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Per-Wilayah Breakdown Table --}}
    <div class="col-12 col-lg-7">
        <div class="card-dark h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title">
                    <i class="fa-solid fa-map-location-dot me-2" style="color:var(--accent)"></i>Ringkasan per DC / Wilayah
                </h6>
                <span style="color:var(--text-dim);font-size:0.75rem">{{ now()->format('d/m/Y H:i') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>DC / Wilayah</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Proses</th>
                                <th class="text-center">Selesai</th>
                                <th class="text-center">Cancel</th>
                                <th class="text-end" width="60">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wilayahBreakdown as $row)
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:0.85rem">{{ $row['wilayah']->nama_wilayah }}</div>
                                    <div style="color:var(--text-dim);font-size:0.7rem;font-family:monospace">{{ $row['wilayah']->kode_wilayah }}</div>
                                </td>
                                <td class="text-center">
                                    <span style="font-weight:700;color:var(--text-primary)">{{ $row['total'] }}</span>
                                </td>
                                <td class="text-center">
                                    @if($row['proses'] > 0)
                                        <span class="status-badge status-proses-bayar">{{ $row['proses'] }}</span>
                                    @else
                                        <span style="color:var(--text-dim)">–</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row['selesai'] > 0)
                                        <span class="status-badge status-selesai">{{ $row['selesai'] }}</span>
                                    @else
                                        <span style="color:var(--text-dim)">–</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row['cancel'] > 0)
                                        <span class="status-badge status-cancel">{{ $row['cancel'] }}</span>
                                    @else
                                        <span style="color:var(--text-dim)">–</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('reklame.index', ['wilayah_id' => $row['wilayah']->id]) }}"
                                       class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem" title="Lihat Data">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @if($wilayahBreakdown->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center py-4" style="color:var(--text-dim)">
                                    <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>Belum ada data wilayah.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Brand Breakdown --}}
<div class="card-dark mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title">
            <i class="fa-solid fa-tag me-2" style="color:var(--accent)"></i>Ringkasan per Brand
        </h6>
        <span style="color:var(--text-dim);font-size:0.75rem">{{ now()->format('d/m/Y H:i') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>Brand</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Proses</th>
                        <th class="text-center">Selesai</th>
                        <th class="text-center">Cancel</th>
                        <th class="text-end" width="60">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brandBreakdown as $row)
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:0.85rem">{{ $row['brand']->nama_brand }}</div>
                        </td>
                        <td class="text-center">
                            <span style="font-weight:700;color:var(--text-primary)">{{ $row['total'] }}</span>
                        </td>
                        <td class="text-center">
                            @if($row['proses'] > 0)
                                <span class="status-badge status-proses-bayar">{{ $row['proses'] }}</span>
                            @else
                                <span style="color:var(--text-dim)">–</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['selesai'] > 0)
                                <span class="status-badge status-selesai">{{ $row['selesai'] }}</span>
                            @else
                                <span style="color:var(--text-dim)">–</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['cancel'] > 0)
                                <span class="status-badge status-cancel">{{ $row['cancel'] }}</span>
                            @else
                                <span style="color:var(--text-dim)">–</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('report') }}"
                               class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem" title="Lihat Laporan">
                                <i class="fa-solid fa-chart-bar"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4" style="color:var(--text-dim)">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>Belum ada data brand.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Bottom Row: Deadline Near + Latest --}}
<div class="row g-3">

    {{-- Deadline Approaching --}}
    <div class="col-12 col-md-6">
        <div class="card-dark">
            <div class="card-header d-flex align-items-center gap-2">
                <h6 class="card-title">
                    <i class="fa-solid fa-triangle-exclamation me-2" style="color:#f59e0b"></i>Deadline ≤ 14 Hari
                </h6>
                <span class="ms-auto" style="color:var(--text-dim);font-size:0.72rem">status aktif saja</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>No SPK / Toko</th>
                                <th class="text-center">Deadline</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deadlineNear as $r)
                            @php $daysLeft = (int) now()->diffInDays($r->deadline, false); @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('reklame.show', $r->id) }}"
                                       style="font-size:0.8rem;font-weight:500;color:var(--text-primary);text-decoration:none">
                                        {{ $r->no_spk ?? '—' }}
                                    </a>
                                    <div style="font-size:0.72rem;color:var(--text-dim)">
                                        {{ optional($r->toko)->nama_toko ?? '-' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div style="font-size:0.8rem;font-weight:600;
                                        color:{{ $daysLeft <= 3 ? '#ef4444' : ($daysLeft <= 7 ? '#f59e0b' : '#94a3b8') }}">
                                        {{ $r->deadline ? $r->deadline->format('d/m/Y') : '-' }}
                                    </div>
                                    <small style="color:var(--text-dim);font-size:0.68rem">{{ $daysLeft }} hari lagi</small>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge {{ $r->statusBadgeClass() }}" style="font-size:0.63rem;white-space:nowrap">
                                        {{ $r->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4" style="color:var(--text-dim)">
                                    <i class="fa-solid fa-calendar-check fa-2x mb-2 d-block" style="color:#22c55e"></i>
                                    <span style="font-size:0.82rem">Tidak ada deadline mendekat</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Latest 5 Reklames --}}
    <div class="col-12 col-md-6">
        <div class="card-dark">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title">
                    <i class="fa-solid fa-clock-rotate-left me-2" style="color:var(--accent)"></i>5 Data Terbaru
                </h6>
                <a href="{{ route('reklame.index') }}"
                   style="font-size:0.75rem;color:var(--accent);text-decoration:none">
                    Lihat semua <i class="fa-solid fa-arrow-right fa-xs"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>No SPK / Toko</th>
                                <th class="text-center">Tgl SPK</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestReklames as $r)
                            <tr>
                                <td>
                                    <a href="{{ route('reklame.show', $r->id) }}"
                                       style="font-size:0.8rem;font-weight:500;color:var(--text-primary);text-decoration:none">
                                        {{ $r->no_spk ?? '—' }}
                                    </a>
                                    <div style="font-size:0.72rem;color:var(--text-dim)">
                                        {{ optional($r->toko)->nama_toko ?? '-' }}
                                    </div>
                                </td>
                                <td class="text-center" style="font-size:0.8rem;color:var(--text-muted)">
                                    {{ $r->tgl_spk ? $r->tgl_spk->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="status-badge {{ $r->statusBadgeClass() }}" style="font-size:0.63rem;white-space:nowrap">
                                        {{ $r->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4" style="color:var(--text-dim)">
                                    <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>Belum ada data reklame.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endif
{{-- ═════════════════════════════════════════════════════════════ --}}

@endsection

@if(!$isMobile)
@php
    $chartLabelsJson = json_encode($chartLabels);
    $chartDataJson   = json_encode($chartData);
    $chartColorsJson = json_encode($chartColors);
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
@if($totalSpk > 0)
(function () {
    var ctx    = document.getElementById('statusChart').getContext('2d');
    var labels = {!! $chartLabelsJson !!};
    var data   = {!! $chartDataJson !!};
    var colors = {!! $chartColorsJson !!};

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderColor: '#1e293b',
                borderWidth: 2,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: '#94a3b8',
                        font: { size: 11, family: 'Inter' },
                        boxWidth: 12,
                        padding: 8,
                        filter: function (item, chart) {
                            return chart.datasets[0].data[item.index] > 0;
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            var total = ctx.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                            var pct   = total > 0 ? Math.round(ctx.raw / total * 100) : 0;
                            return ' ' + ctx.label + ': ' + ctx.raw + ' SPK (' + pct + '%)';
                        }
                    }
                }
            },
            cutout: '65%',
        }
    });
})();
@endif
</script>
@endpush
@endif
