<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function statusInfo(): array
    {
        return $this->calcStoreStatus();
    }

    public function index()
    {
        $schedule  = $this->operationalSchedule();
        $storeInfo = $this->calcStoreStatus($schedule);
        $override  = Cache::get('manual_override');
        $role      = $this->currentRole();

        $todayKey   = $this->scheduleKeyFor(Carbon::now('Asia/Jakarta'));
        $todayEntry = collect($schedule)->firstWhere('key', $todayKey);
        $todayLabel = $todayEntry['label'] ?? '';
        $todayBuka  = $todayEntry['buka']  ?? '';

        return view('owner.jadwal', compact('schedule', 'storeInfo', 'override', 'role', 'todayLabel', 'todayBuka'));
    }

    public function save(Request $request)
    {
        $days   = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $labels = [
            'senin'  => 'Senin',  'selasa' => 'Selasa', 'rabu'   => 'Rabu',
            'kamis'  => 'Kamis',  'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu', 'minggu' => 'Minggu',
        ];

        $schedule = [];
        foreach ($days as $d) {
            $open       = $request->boolean('open_' . $d);
            $schedule[] = [
                'key'   => $d,
                'label' => $labels[$d],
                'open'  => $open,
                'buka'  => $open ? $request->input('buka_'  . $d, '10:00') : '',
                'tutup' => $open ? $request->input('tutup_' . $d, '21:00') : '',
            ];
        }

        Cache::put('jadwal_operasional', $schedule, now()->addDays(365));

        return back()->with('saved', true);
    }

    public function toggleStatus(Request $request)
    {
        $request->validate(['status' => ['required', 'in:available,unavailable']]);

        if ($request->status === 'unavailable') {
            $request->validate([
                'reopen_day'  => ['required', 'string', 'max:30'],
                'reopen_time' => ['required', 'string', 'max:5'],
            ]);

            Cache::put('manual_override', [
                'is_closed'   => true,
                'reopen_day'  => $request->reopen_day,
                'reopen_time' => $request->reopen_time,
            ], now()->addDays(7));
        } else {
            Cache::put('manual_override', ['is_open' => true], now()->addHours(24));
        }

        return response()->json(['success' => true, 'store_info' => $this->calcStoreStatus()]);
    }

    public function getStatus(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->calcStoreStatus());
    }
}
