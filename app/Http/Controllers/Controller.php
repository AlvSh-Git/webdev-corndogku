<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

abstract class Controller
{
    protected function currentRole(): string
    {
        $segment = request()->segment(1);
        return in_array($segment, ['owner', 'cashier']) ? $segment : 'owner';
    }

    /** Real-time store status: checks manual override first, then today's schedule. */
    protected function calcStoreStatus(): array
    {
        $override = Cache::get('manual_override');

        if ($override && ($override['is_closed'] ?? false)) {
            return [
                'is_open'     => false,
                'reason'      => 'manual',
                'reopen_day'  => $override['reopen_day']  ?? '',
                'reopen_time' => $override['reopen_time'] ?? '',
            ];
        }

        $schedule = Cache::get('jadwal_operasional');

        // No schedule configured yet — treat as open
        if (empty($schedule)) {
            return ['is_open' => true, 'reason' => 'schedule', 'reopen_day' => '', 'reopen_time' => ''];
        }

        $now    = Carbon::now('Asia/Jakarta');
        $dayMap = [
            'Sunday'    => 'minggu',
            'Monday'    => 'senin',
            'Tuesday'   => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday'  => 'kamis',
            'Friday'    => 'jumat',
            'Saturday'  => 'sabtu',
        ];

        $todayKey   = $dayMap[$now->format('l')];
        $todayEntry = collect($schedule)->firstWhere('key', $todayKey);

        if ($todayEntry && ($todayEntry['open'] ?? false)
            && ($todayEntry['buka'] ?? '') && ($todayEntry['tutup'] ?? '')) {

            $buka  = Carbon::parse($now->toDateString() . ' ' . $todayEntry['buka']);
            $tutup = Carbon::parse($now->toDateString() . ' ' . $todayEntry['tutup']);

            if ($now->between($buka, $tutup)) {
                return ['is_open' => true, 'reason' => 'schedule', 'reopen_day' => '', 'reopen_time' => ''];
            }

            // Before today's opening time
            if ($now->lt($buka)) {
                return [
                    'is_open'     => false,
                    'reason'      => 'schedule',
                    'reopen_day'  => $todayEntry['label'] ?? $todayKey,
                    'reopen_time' => $todayEntry['buka'],
                ];
            }
        }

        // Today closed / after closing — find the next open day
        [$nextDay, $nextTime] = $this->nextOpenSlot($schedule, $todayKey);
        return [
            'is_open'     => false,
            'reason'      => 'schedule',
            'reopen_day'  => $nextDay,
            'reopen_time' => $nextTime,
        ];
    }

    private function nextOpenSlot(array $schedule, string $todayKey): array
    {
        $dayOrder = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $labelMap = [
            'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu',
            'kamis' => 'Kamis', 'jumat'  => 'Jumat',  'sabtu' => 'Sabtu', 'minggu' => 'Minggu',
        ];

        $schedByKey = collect($schedule)->keyBy('key');
        $todayIdx   = array_search($todayKey, $dayOrder);
        if ($todayIdx === false) {
            $todayIdx = 0;
        }

        for ($offset = 1; $offset <= 7; $offset++) {
            $nextKey   = $dayOrder[($todayIdx + $offset) % 7];
            $nextEntry = $schedByKey->get($nextKey);

            if ($nextEntry && ($nextEntry['open'] ?? false) && ($nextEntry['buka'] ?? '')) {
                $label = $offset === 1 ? 'Besok' : ($labelMap[$nextKey] ?? $nextKey);
                return [$label, $nextEntry['buka']];
            }
        }

        return ['', ''];
    }
}
