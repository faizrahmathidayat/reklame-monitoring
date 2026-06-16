<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Reklame — Cetak</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            background: #fff;
            color: #000;
        }

        /* ── Screen-only controls ── */
        .screen-only {
            background: #f0f4ff;
            border-bottom: 2px solid #6366f1;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .screen-only a.back {
            color: #4f46e5;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
        }
        .screen-only .filter-form {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            flex: 1;
        }
        .screen-only label { font-size: 0.8rem; color: #374151; }
        .screen-only input[type="date"] {
            font-size: 0.8rem;
            padding: 3px 6px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            outline: none;
        }
        .btn-filter {
            padding: 4px 12px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
        }
        .btn-print {
            padding: 5px 14px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 0.85rem;
            cursor: pointer;
            font-weight: 600;
            white-space: nowrap;
        }

        /* ── Report Content ── */
        .report-body {
            padding: 20px 24px;
            max-width: 680px;
        }

        /* ── Brand Section ── */
        .brand-section {
            margin-bottom: 40px;
        }
        .brand-header {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #333;
            padding-bottom: 3px;
            margin-bottom: 16px;
        }
        .brand-subtotal td {
            background: #d0d0d0;
            font-weight: bold;
        }

        /* ── DC Section ── */
        .dc-section {
            margin-bottom: 24px;
            margin-left: 8px;
        }

        .company-name {
            font-weight: bold;
            font-size: 11pt;
        }
        .report-subtitle {
            color: #1a1aff;
            font-size: 10pt;
            margin-bottom: 2px;
        }
        .dc-name {
            font-style: italic;
            font-size: 9pt;
            color: #333;
            margin-top: 10px;
        }
        .dc-date {
            font-size: 9pt;
            color: #333;
            margin-bottom: 6px;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        td {
            border: 1px solid #888;
            padding: 4px 8px;
            font-size: 9.5pt;
            vertical-align: middle;
        }
        .row-header td {
            background: #b8b8b8;
            font-weight: bold;
        }
        .row-header td.col-val {
            font-weight: bold;
        }
        .col-val {
            text-align: right;
            width: 200px;
            white-space: nowrap;
        }
        .row-selesai td  { background: #d4f5d4; }
        .row-siap td     { background: #d4eef5; }
        .row-cancel td   { background: #f5d4d4; }
        .row-total td {
            background: #c8c8c8;
            font-weight: bold;
        }
        .row-zero td {
            color: #666;
        }

        /* ── Print Styles ── */
        @media print {
            .screen-only  { display: none !important; }
            .report-body  { padding: 8px 12px; max-width: none; }
            .dc-section   { page-break-inside: avoid; }
            .brand-section { page-break-before: auto; }
        }

        @media (max-width: 600px) {
            .screen-only { flex-direction: column; align-items: flex-start; }
            .report-body { padding: 14px; }
        }
    </style>
</head>
<body>

{{-- ── Screen Controls ── --}}
<div class="screen-only">
    <a href="{{ route('report') }}" class="back">
        &#8592; Kembali
    </a>
    <form method="GET" action="{{ route('report.cetak') }}" class="filter-form">
        <label>Dari:</label>
        <input type="date" name="tgl_dari" value="{{ $tglDari }}">
        <label>Sampai:</label>
        <input type="date" name="tgl_sampai" value="{{ $tglSampai }}">
        <select name="brand_id" style="font-size:0.8rem;padding:3px 6px;border:1px solid #d1d5db;border-radius:4px;outline:none">
            <option value="">-- Semua Brand --</option>
            @foreach($allBrands as $b)
                <option value="{{ $b->id }}" {{ $brandId == $b->id ? 'selected' : '' }}>{{ $b->nama_brand }}</option>
            @endforeach
        </select>
        <select name="wilayah_id" style="font-size:0.8rem;padding:3px 6px;border:1px solid #d1d5db;border-radius:4px;outline:none">
            <option value="">-- Semua DC --</option>
            @foreach($allWilayahs as $w)
                <option value="{{ $w->id }}" {{ $wilayahId == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
            @endforeach
        </select>
        <select name="status" style="font-size:0.8rem;padding:3px 6px;border:1px solid #d1d5db;border-radius:4px;outline:none">
            <option value="">-- Semua Status --</option>
            @foreach($allStatuses as $s)
                <option value="{{ $s }}" {{ $statusFilter === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-filter">Filter</button>
        @if($tglDari || $tglSampai || $brandId || $wilayahId || $statusFilter)
            <a href="{{ route('report.cetak') }}" style="font-size:0.8rem;color:#6b7280;text-decoration:none">Reset</a>
        @endif
    </form>
    <button onclick="window.print()" class="btn-print">
        &#128438; Cetak / Simpan PDF
    </button>
</div>

{{-- ── Report Body ── --}}
<div class="report-body">

    @if($tglDari || $tglSampai)
    <p style="font-size:8.5pt;color:#555;margin-bottom:14px">
        Periode: {{ $tglDari ? \Carbon\Carbon::parse($tglDari)->translatedFormat('d F Y') : 'awal' }}
        s/d {{ $tglSampai ? \Carbon\Carbon::parse($tglSampai)->translatedFormat('d F Y') : 'sekarang' }}
    </p>
    @endif

    @forelse($report as $brandGroup)
    <div class="brand-section">

        {{-- Brand header --}}
        <div class="brand-header">{{ $brandGroup['brand']->nama_brand }}</div>

        {{-- Per-DC tables --}}
        @foreach($brandGroup['wilayahs'] as $item)
        <div class="dc-section">

            <div class="company-name">{{ $companyName }}</div>
            <div class="report-subtitle">{{ $reportTitle }}</div>

            <div class="dc-name">{{ $item['wilayah']->nama_wilayah }}</div>
            <div class="dc-date">Update Pertanggal : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>

            <table>
                <tr class="row-header">
                    <td>Total SPK Masuk</td>
                    <td class="col-val">
                        {{ $item['spk_count'] }} SPK
                        ({{ $item['toko_count'] }} TOKO)
                        ({{ $item['objek_count'] }} OBJEK)
                    </td>
                </tr>

                @foreach($groupLabels as $label)
                @php
                    $count        = $item['groups'][$label] ?? 0;
                    $displayLabel = $label;
                    $rowClass     = $count === 0 ? 'row-zero' : '';

                    if ($label === 'Sudah Dibayarkan') {
                        $displayLabel = 'Sudah Dibayarkan Oleh ' . $item['wilayah']->nama_wilayah;
                        $rowClass     = $count > 0 ? 'row-selesai' : 'row-zero';
                    } elseif ($label === 'Siap Dibayarkan') {
                        $rowClass = $count > 0 ? 'row-siap' : 'row-zero';
                    } elseif ($label === 'Cancel') {
                        $rowClass = $count > 0 ? 'row-cancel' : 'row-zero';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $displayLabel }}</td>
                    <td class="col-val">{{ $count > 0 ? $count : '' }}</td>
                </tr>
                @endforeach

                <tr class="row-total">
                    <td>TOTAL KESELURUHAN</td>
                    <td class="col-val">{{ $item['toko_count'] }}</td>
                </tr>
            </table>
        </div>
        @endforeach

        {{-- Brand subtotal --}}
        <table style="margin-left:8px;margin-top:4px">
            <tr class="brand-subtotal">
                <td>SUBTOTAL {{ strtoupper($brandGroup['brand']->nama_brand) }}</td>
                <td class="col-val">
                    {{ $brandGroup['total_spk'] }} SPK
                    ({{ $brandGroup['total_toko'] }} TOKO)
                    ({{ $brandGroup['total_objek'] }} OBJEK)
                </td>
            </tr>
        </table>

    </div>
    @empty
    <div style="text-align:center;padding:60px 0;color:#666;font-size:10pt">
        Tidak ada data reklame untuk ditampilkan.
        @if($tglDari || $tglSampai)
            <br><small>Coba hapus filter tanggal.</small>
        @endif
    </div>
    @endforelse

</div>

</body>
</html>
