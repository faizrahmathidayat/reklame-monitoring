<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Reklame;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tglDari      = $request->input('tgl_dari');
        $tglSampai    = $request->input('tgl_sampai');
        $wilayahId    = $request->input('wilayah_id');
        $brandId      = $request->input('brand_id');
        $statusFilter = $request->input('status');

        $withFilter = function ($q) use ($tglDari, $tglSampai, $wilayahId, $brandId, $statusFilter) {
            return $q
                ->when($tglDari,      function ($q) use ($tglDari)      { return $q->where('tgl_spk', '>=', $tglDari); })
                ->when($tglSampai,    function ($q) use ($tglSampai)    { return $q->where('tgl_spk', '<=', $tglSampai); })
                ->when($wilayahId,    function ($q) use ($wilayahId)    { return $q->where('wilayah_id', $wilayahId); })
                ->when($brandId,      function ($q) use ($brandId)      { return $q->where('brand_id', $brandId); })
                ->when($statusFilter, function ($q) use ($statusFilter) { return $q->where('status', $statusFilter); });
        };

        $statuses    = Reklame::allStatuses();
        $allWilayahs = Wilayah::active()->orderBy('nama_wilayah')->get();
        $allBrands   = Brand::active()->orderBy('nama_brand')->get();

        // Wilayahs to include in matrix (filtered if wilayahId selected)
        $wilayahs = $wilayahId
            ? $allWilayahs->filter(function ($w) use ($wilayahId) { return $w->id == $wilayahId; })->values()
            : $allWilayahs;

        $brandMatrix = $this->buildBrandMatrix($withFilter, $wilayahs, $statuses);

        $colTotals  = array_fill_keys($statuses, 0);
        $grandTotal = 0;
        foreach ($brandMatrix as $group) {
            foreach ($statuses as $s) {
                $colTotals[$s] += $group['subtotal'][$s];
            }
            $grandTotal += $group['total'];
        }

        // Summary cards (same filter)
        $totalSpk     = $withFilter(Reklame::query())->count();
        $totalSelesai = $withFilter(Reklame::query())->whereIn('status', Reklame::GROUP_SELESAI)->count();
        $totalCancel  = $withFilter(Reklame::query())->where('status', Reklame::STATUS_CANCEL)->count();
        $totalProses  = $totalSpk - $totalSelesai - $totalCancel;

        if ($request->input('export') === 'csv') {
            return $this->exportCsv(
                $this->buildBrandMatrix($withFilter, $wilayahs, $statuses),
                $statuses, $tglDari, $tglSampai
            );
        }

        return view('report.index', compact(
            'brandMatrix', 'statuses', 'colTotals', 'grandTotal',
            'totalSpk', 'totalSelesai', 'totalCancel', 'totalProses',
            'allWilayahs', 'allBrands', 'tglDari', 'tglSampai', 'wilayahId', 'brandId', 'statusFilter'
        ));
    }

    public function cetak(Request $request)
    {
        $tglDari      = $request->input('tgl_dari');
        $tglSampai    = $request->input('tgl_sampai');
        $brandId      = $request->input('brand_id');
        $wilayahId    = $request->input('wilayah_id');
        $statusFilter = $request->input('status');

        $withFilter = function ($q) use ($tglDari, $tglSampai, $brandId, $wilayahId, $statusFilter) {
            return $q
                ->when($tglDari,      function ($q) use ($tglDari)      { return $q->where('tgl_spk', '>=', $tglDari); })
                ->when($tglSampai,    function ($q) use ($tglSampai)    { return $q->where('tgl_spk', '<=', $tglSampai); })
                ->when($brandId,      function ($q) use ($brandId)      { return $q->where('brand_id', $brandId); })
                ->when($wilayahId,    function ($q) use ($wilayahId)    { return $q->where('wilayah_id', $wilayahId); })
                ->when($statusFilter, function ($q) use ($statusFilter) { return $q->where('status', $statusFilter); });
        };

        $statusGroups = [
            'Proses Status Internal'                     => Reklame::GROUP_PROSES_INTERNAL,
            'Proses Input Website sd Verif'              => [Reklame::STATUS_MENUNGGU_IPR],
            'Siap Dibayarkan'                            => [Reklame::STATUS_PROSES_PEMBAYARAN],
            'Proses Terbit SKPD/IPR (Sudah dibayarkan)' => [Reklame::STATUS_SELESAI_TERBIT_SKPD],
            'Proses Invoice'                             => [Reklame::STATUS_PROSES_INVOICE],
            'Invoice Terkirim'                           => [Reklame::STATUS_INVOICE_TERKIRIM],
            'Sudah Dibayarkan'                           => [Reklame::STATUS_SELESAI, Reklame::STATUS_SELESAI_INV_TERKIRIM],
            'Cancel'                                     => [Reklame::STATUS_CANCEL],
        ];

        $allBrands  = Brand::active()->orderBy('nama_brand')->get();
        $brands     = $brandId ? $allBrands->filter(function ($b) use ($brandId) { return $b->id == $brandId; })->values() : $allBrands;
        $allWilayahs = Wilayah::active()->orderBy('nama_wilayah')->get();
        $wilayahs   = $wilayahId ? $allWilayahs->filter(function ($w) use ($wilayahId) { return $w->id == $wilayahId; })->values() : $allWilayahs;

        // Totals keyed by brand_id → wilayah_id
        $totalsRaw = $withFilter(Reklame::query())
            ->select(
                'brand_id', 'wilayah_id',
                DB::raw('COUNT(DISTINCT spk_id) as spk_count'),
                DB::raw('COUNT(DISTINCT toko_id) as toko_count'),
                DB::raw('COALESCE(SUM(jumlah_objek), 0) as objek_count')
            )
            ->groupBy('brand_id', 'wilayah_id')
            ->get()
            ->groupBy('brand_id')
            ->map(function ($items) { return $items->keyBy('wilayah_id'); });

        // Status counts keyed by brand_id → wilayah_id → collection
        $statusRaw = $withFilter(Reklame::query())
            ->select('brand_id', 'wilayah_id', 'status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('brand_id', 'wilayah_id', 'status')
            ->get()
            ->groupBy('brand_id')
            ->map(function ($items) { return $items->groupBy('wilayah_id'); });

        $groupLabels = array_keys($statusGroups);

        $report = $brands->map(function ($brand) use ($wilayahs, $totalsRaw, $statusRaw, $statusGroups) {
            $brandTotals = $totalsRaw->get($brand->id, collect());
            $brandStatus = $statusRaw->get($brand->id, collect());

            $wilayahRows = $wilayahs->map(function ($w) use ($brandTotals, $brandStatus, $statusGroups) {
                $t = $brandTotals->get($w->id);
                if (!$t) return null;

                $rows   = $brandStatus->get($w->id, collect());
                $groups = [];
                foreach ($statusGroups as $label => $statuses) {
                    $groups[$label] = (int) $rows->filter(function ($r) use ($statuses) {
                        return in_array($r->status, $statuses);
                    })->sum('jumlah');
                }

                return [
                    'wilayah'     => $w,
                    'spk_count'   => (int) $t->spk_count,
                    'toko_count'  => (int) $t->toko_count,
                    'objek_count' => (int) $t->objek_count,
                    'groups'      => $groups,
                ];
            })->filter()->values();

            if ($wilayahRows->isEmpty()) return null;

            return [
                'brand'       => $brand,
                'wilayahs'    => $wilayahRows,
                'total_spk'   => $wilayahRows->sum('spk_count'),
                'total_toko'  => $wilayahRows->sum('toko_count'),
                'total_objek' => $wilayahRows->sum('objek_count'),
            ];
        })->filter()->values();

        return view('report.cetak', [
            'report'       => $report,
            'groupLabels'  => $groupLabels,
            'allBrands'    => $allBrands,
            'allWilayahs'  => $allWilayahs,
            'allStatuses'  => Reklame::allStatuses(),
            'tglDari'      => $tglDari,
            'tglSampai'    => $tglSampai,
            'brandId'      => $brandId,
            'wilayahId'    => $wilayahId,
            'statusFilter' => $statusFilter,
            'companyName'  => 'PT. Sinar Vinito Jaya',
            'reportTitle'  => 'Progress Izin Pajak Reklame',
        ]);
    }

    private function buildBrandMatrix(callable $withFilter, $wilayahs, array $statuses): array
    {
        $raw = $withFilter(Reklame::query())
            ->select('brand_id', 'wilayah_id', 'status', DB::raw('count(*) as jumlah'))
            ->groupBy('brand_id', 'wilayah_id', 'status')
            ->get()
            ->groupBy('brand_id');

        $brands      = Brand::active()->orderBy('nama_brand')->get();
        $brandMatrix = [];

        foreach ($brands as $brand) {
            $byWilayah    = $raw->get($brand->id, collect())->groupBy('wilayah_id');
            $brandWilayahs = [];
            $brandSub     = array_fill_keys($statuses, 0);
            $brandTotal   = 0;

            foreach ($wilayahs as $w) {
                $rows     = $byWilayah->get($w->id, collect());
                $rowData  = [];
                $rowTotal = 0;
                foreach ($statuses as $s) {
                    $count       = (int) $rows->filter(function ($r) use ($s) { return $r->status === $s; })->sum('jumlah');
                    $rowData[$s] = $count;
                    $brandSub[$s] += $count;
                    $rowTotal    += $count;
                }
                if ($rowTotal > 0) {
                    $brandWilayahs[] = ['wilayah' => $w, 'data' => $rowData, 'total' => $rowTotal];
                    $brandTotal      += $rowTotal;
                }
            }

            if (!empty($brandWilayahs)) {
                $brandMatrix[] = [
                    'brand'    => $brand,
                    'wilayahs' => $brandWilayahs,
                    'subtotal' => $brandSub,
                    'total'    => $brandTotal,
                ];
            }
        }

        return $brandMatrix;
    }

    private function exportCsv(array $brandMatrix, array $statuses, $tglDari, $tglSampai)
    {
        $filename = 'laporan-reklame-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($brandMatrix, $statuses, $tglDari, $tglSampai) {
            $f = fopen('php://output', 'w');
            fputs($f, "\xEF\xBB\xBF"); // BOM for Excel UTF-8

            fputcsv($f, ['Laporan Monitoring Reklame']);
            fputcsv($f, ['Periode', ($tglDari ?: '-') . ' s/d ' . ($tglSampai ?: '-')]);
            fputcsv($f, ['Generated', date('d/m/Y H:i')]);
            fputcsv($f, []);

            // Header
            $header = ['Brand', 'DC / Wilayah'];
            foreach ($statuses as $s) { $header[] = $s; }
            $header[] = 'TOTAL';
            fputcsv($f, $header);

            $grandSub   = array_fill_keys($statuses, 0);
            $grandTotal = 0;

            foreach ($brandMatrix as $group) {
                // Rows per wilayah
                foreach ($group['wilayahs'] as $row) {
                    $line = [$group['brand']->nama_brand, $row['wilayah']->nama_wilayah];
                    foreach ($statuses as $s) { $line[] = $row['data'][$s]; }
                    $line[] = $row['total'];
                    fputcsv($f, $line);
                }
                // Brand subtotal
                $subRow = ['SUBTOTAL ' . strtoupper($group['brand']->nama_brand), ''];
                foreach ($statuses as $s) {
                    $subRow[]       = $group['subtotal'][$s];
                    $grandSub[$s]  += $group['subtotal'][$s];
                }
                $subRow[]    = $group['total'];
                $grandTotal += $group['total'];
                fputcsv($f, $subRow);
                fputcsv($f, []); // blank row between brands
            }

            // Grand total
            $totalRow = ['GRAND TOTAL', ''];
            foreach ($statuses as $s) { $totalRow[] = $grandSub[$s]; }
            $totalRow[] = $grandTotal;
            fputcsv($f, $totalRow);

            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}
