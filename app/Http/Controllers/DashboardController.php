<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Reklame;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tglDari   = $request->input('tgl_dari');
        $tglSampai = $request->input('tgl_sampai');

        $withFilter = function ($q) use ($tglDari, $tglSampai) {
            return $q
                ->when($tglDari,   function ($q) use ($tglDari)   { return $q->where('tgl_spk', '>=', $tglDari); })
                ->when($tglSampai, function ($q) use ($tglSampai) { return $q->where('tgl_spk', '<=', $tglSampai); });
        };

        // Stat cards
        $totalSpk     = $withFilter(Reklame::query())->count();
        $totalSelesai = $withFilter(Reklame::query())->whereIn('status', Reklame::GROUP_SELESAI)->count();
        $totalCancel  = $withFilter(Reklame::query())->where('status', Reklame::STATUS_CANCEL)->count();
        $totalProses  = $totalSpk - $totalSelesai - $totalCancel;

        // Status counts for chart
        $rawStatusCounts = $withFilter(Reklame::query())
            ->select('status', DB::raw('count(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        // Chart: 7 grouped categories
        $chartGroups = [
            'Input/Pemberkasan' => ['INPUT WEB', 'PEMBERKASAN', 'PETUGAS PELAYANAN'],
            'Menunggu IPR'      => ['MENUNGGU IPR'],
            'Proses Invoice'    => ['PROSES INVOICE'],
            'Inv Terkirim'      => ['INVOICE TERKIRIM', 'SELESAI INV TERKIRIM'],
            'Proses Pembayaran' => ['PROSES PEMBAYARAN'],
            'Selesai'           => ['SELESAI', 'SELESAI TERBIT SKPD'],
            'Cancel'            => ['CANCEL'],
        ];
        $groupColors = ['#6366f1', '#f59e0b', '#f97316', '#06b6d4', '#3b82f6', '#22c55e', '#ef4444'];

        $chartLabels = [];
        $chartData   = [];
        $chartColors = [];
        $idx = 0;
        foreach ($chartGroups as $label => $statuses) {
            $count = 0;
            foreach ($statuses as $s) {
                $count += (int) $rawStatusCounts->get($s, 0);
            }
            $chartLabels[] = $label;
            $chartData[]   = $count;
            $chartColors[] = $groupColors[$idx++];
        }

        // Per-wilayah breakdown (single query, grouped in PHP)
        $rawBreakdown = $withFilter(Reklame::query())
            ->select('wilayah_id', 'status', DB::raw('count(*) as jumlah'))
            ->groupBy('wilayah_id', 'status')
            ->get()
            ->groupBy('wilayah_id');

        $wilayahs = Wilayah::active()->orderBy('nama_wilayah')->get();

        $wilayahBreakdown = $wilayahs->map(function ($w) use ($rawBreakdown) {
            $rows    = $rawBreakdown->get($w->id, collect());
            $total   = (int) $rows->sum('jumlah');
            $selesai = (int) $rows->filter(function ($r) {
                return in_array($r->status, Reklame::GROUP_SELESAI);
            })->sum('jumlah');
            $cancel = (int) $rows->filter(function ($r) {
                return $r->status === Reklame::STATUS_CANCEL;
            })->sum('jumlah');
            $proses = $total - $selesai - $cancel;

            return [
                'wilayah' => $w,
                'total'   => $total,
                'proses'  => $proses,
                'selesai' => $selesai,
                'cancel'  => $cancel,
            ];
        });

        // Per-brand breakdown
        $rawBrandBreakdown = $withFilter(Reklame::query())
            ->select('brand_id', 'status', DB::raw('count(*) as jumlah'))
            ->groupBy('brand_id', 'status')
            ->get()
            ->groupBy('brand_id');

        $brands = Brand::active()->orderBy('nama_brand')->get();

        $brandBreakdown = $brands->map(function ($b) use ($rawBrandBreakdown) {
            $rows    = $rawBrandBreakdown->get($b->id, collect());
            $total   = (int) $rows->sum('jumlah');
            if ($total === 0) return null;
            $selesai = (int) $rows->filter(function ($r) {
                return in_array($r->status, Reklame::GROUP_SELESAI);
            })->sum('jumlah');
            $cancel = (int) $rows->filter(function ($r) {
                return $r->status === Reklame::STATUS_CANCEL;
            })->sum('jumlah');
            return [
                'brand'   => $b,
                'total'   => $total,
                'proses'  => $total - $selesai - $cancel,
                'selesai' => $selesai,
                'cancel'  => $cancel,
            ];
        })->filter()->values();

        // Deadline approaching: next 14 days, active status
        $deadlineNear = Reklame::with(['toko', 'wilayah'])
            ->where('deadline', '>=', now()->format('Y-m-d'))
            ->where('deadline', '<=', now()->addDays(14)->format('Y-m-d'))
            ->whereNotIn('status', Reklame::GROUP_SELESAI)
            ->where('status', '!=', Reklame::STATUS_CANCEL)
            ->orderBy('deadline')
            ->take(5)
            ->get();

        // Latest 5 regardless of date filter
        $latestReklames = Reklame::with(['toko', 'wilayah'])
            ->latest('tgl_spk')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalSpk', 'totalSelesai', 'totalCancel', 'totalProses',
            'chartLabels', 'chartData', 'chartColors',
            'wilayahBreakdown', 'brandBreakdown', 'latestReklames', 'deadlineNear',
            'tglDari', 'tglSampai'
        ));
    }
}
