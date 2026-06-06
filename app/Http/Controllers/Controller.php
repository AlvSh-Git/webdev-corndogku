<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

// Base controller with shared helpers (roles, order lists, store status).
abstract class Controller
{
    // Get the active role from the URL's first segment.
    protected function currentRole(): string
    {
        $segment = request()->segment(1);
        return in_array($segment, ['owner', 'cashier']) ? $segment : 'owner';
    }

    /** Single source of truth for the store address (configurable via .env). */
    protected function storeAddress(): string
    {
        return (string) config('store.address');
    }

    /**
     * Order statuses that are still "in progress". These orders stay visible in
     * the cashier/owner order lists across day boundaries so an unfinished order
     * never silently disappears when the date rolls over.
     */
    protected function activeOrderStatuses(): array
    {
        return ['Pending', 'Preparing', 'Ready'];
    }

    /**
     * Constrain an Order query to a given day PLUS any still-active order from
     * earlier days — but ONLY when viewing today's board, so an unfinished order
     * never disappears on the date rollover. A deliberately selected past date
     * stays strictly scoped to that day for accurate historical review.
     * Used for the order LIST only — financial stats/charts stay date-scoped.
     */
    protected function ordersForDay($query, string $date)
    {
        $isToday = $date === today()->toDateString();

        return $query->where(function ($q) use ($date, $isToday) {
            $q->whereDate('created_at', $date);
            if ($isToday) {
                $q->orWhereIn('status', $this->activeOrderStatuses());
            }
        });
    }

    /**
     * Tab counts matching the order-list scope. Computed with two grouped
     * queries (date-scoped tally + active-carryover tally) instead of one
     * COUNT per status, to keep the polled dashboard cheap.
     */
    protected function orderTabCounts(string $date): array
    {
        $isToday = $date === today()->toDateString();
        $active  = $this->activeOrderStatuses();

        // Counts of the selected day's orders, grouped by status.
        $byStatusOnDate = \App\Models\Order::whereDate('created_at', $date)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        // On the live board, active statuses carry over from every day.
        $activeAll = $isToday
            ? \App\Models\Order::whereIn('status', $active)
                ->selectRaw('status, COUNT(*) as c')
                ->groupBy('status')
                ->pluck('c', 'status')
            : collect();

        // Per-status: active statuses use the carryover tally on the live board,
        // terminal statuses always use the date-scoped tally.
        $count = fn (string $s) => (int) ($isToday && in_array($s, $active, true)
            ? ($activeAll[$s] ?? 0)
            : ($byStatusOnDate[$s] ?? 0));

        // 'all' = orders on the date, plus active orders from other days (live only),
        // avoiding double-counting the date's own active orders.
        $allOnDate    = (int) $byStatusOnDate->sum();
        $activeOnDate = (int) collect($active)->sum(fn ($s) => $byStatusOnDate[$s] ?? 0);
        $activeTotal  = (int) $activeAll->sum();
        $all = $isToday ? ($allOnDate + ($activeTotal - $activeOnDate)) : $allOnDate;

        return [
            'all'       => $all,
            'Pending'   => $count('Pending'),
            'Preparing' => $count('Preparing'),
            'Ready'     => $count('Ready'),
            'Completed' => $count('Completed'),
            'Cancelled' => $count('Cancelled'),
        ];
    }

    /**
     * Map an order's line items for the order-detail drawer (shared by the
     * cashier and owner dashboards).
     *
     * Non-custom products carry their product image + name. Custom corndogs
     * (identified by custom_notes) carry a layered preview image (varian base +
     * first sauce overlay, mirroring the customer order-history thumbnail) plus
     * a readable "isi · varian · sauces" breakdown. Item text is rendered into
     * innerHTML on the client, so the dashboards must escape it.
     */
    protected function mapOrderItems(\App\Models\Order $order): array
    {
        $customVarianMap = config('corndog.varian_images', []);
        $customSauceMap  = config('corndog.sauce_images', []);
        $fallbackImage   = config('corndog.fallback_image', 'assets/img/CA_ORIGINAL.png');

        return $order->items->map(function ($i) use ($customVarianMap, $customSauceMap, $fallbackImage) {
            $isCustom = !empty($i->custom_notes);
            $custom   = $isCustom ? (json_decode($i->custom_notes, true) ?: []) : [];

            $customInfo = '';
            $baseImg    = '';
            $sauceImg   = '';

            if ($isCustom) {
                $customInfo = implode(' · ', array_filter([
                    !empty($custom['isi'])    ? $custom['isi']    : null,
                    !empty($custom['varian']) ? $custom['varian'] : null,
                    !empty($custom['sauces']) ? $custom['sauces'] : null,
                ]));

                $varianKey = strtoupper(trim($custom['varian'] ?? ''));
                $baseImg   = asset($customVarianMap[$varianKey] ?? $fallbackImage);

                $firstSauce = strtoupper(trim(explode(',', $custom['sauces'] ?? '')[0] ?? ''));
                $sauceImg   = isset($customSauceMap[$firstSauce]) ? asset($customSauceMap[$firstSauce]) : '';
            }

            return [
                'name'        => $i->product_name ?? $i->product?->name ?? 'Item',
                'is_custom'   => $isCustom,
                'custom_info' => $customInfo,
                'variant'     => '',
                'price'       => $i->quantity > 0 ? (int) ($i->subtotal / $i->quantity) : 0,
                'qty'         => $i->quantity,
                'subtotal'    => $i->subtotal,
                'img'         => $i->product?->image ? asset($i->product->image) : '',
                'base_img'    => $baseImg,
                'sauce_img'   => $sauceImg,
            ];
        })->values()->toArray();
    }

    /**
     * Single source of truth for the store's opening hours before an owner
     * customizes them. Shared by the status calc, the chatbot, and the Owner UI
     * so every surface agrees on the same default state.
     */
    protected function defaultSchedule(): array
    {
        return [
            ['key' => 'senin',  'label' => 'Senin',  'open' => true,  'buka' => '10:00', 'tutup' => '21:00'],
            ['key' => 'selasa', 'label' => 'Selasa', 'open' => true,  'buka' => '10:00', 'tutup' => '21:00'],
            ['key' => 'rabu',   'label' => 'Rabu',   'open' => true,  'buka' => '10:00', 'tutup' => '21:00'],
            ['key' => 'kamis',  'label' => 'Kamis',  'open' => true,  'buka' => '10:00', 'tutup' => '21:00'],
            ['key' => 'jumat',  'label' => 'Jumat',  'open' => true,  'buka' => '10:00', 'tutup' => '22:00'],
            ['key' => 'sabtu',  'label' => 'Sabtu',  'open' => true,  'buka' => '10:00', 'tutup' => '22:00'],
            ['key' => 'minggu', 'label' => 'Minggu', 'open' => false, 'buka' => '',      'tutup' => ''],
        ];
    }

    /** The live operational schedule from cache, falling back to the shared default. */
    protected function operationalSchedule(): array
    {
        $schedule = Cache::get('jadwal_operasional');

        return empty($schedule) ? $this->defaultSchedule() : $schedule;
    }

    /**
     * Human-readable opening hours, derived LIVE from the operational schedule
     * (the same data that drives calcStoreStatus). Returns one entry per day,
     * e.g. "Senin 10:00–21:00, ..., Minggu tutup (WIB)". Pass a pre-fetched
     * schedule to avoid re-reading the cache.
     */
    protected function scheduleHours(?array $schedule = null): string
    {
        $schedule = $schedule ?: $this->operationalSchedule();

        $parts = [];
        foreach ($schedule as $day) {
            $label = $day['label'] ?? ($day['key'] ?? '');
            $parts[] = (($day['open'] ?? false) && ($day['buka'] ?? '') && ($day['tutup'] ?? ''))
                ? "{$label} {$day['buka']}–{$day['tutup']}"
                : "{$label} tutup";
        }

        return implode(', ', $parts) . ' (WIB)';
    }

    /** Maps a moment to its schedule key (e.g. Monday → 'senin'). */
    protected function scheduleKeyFor(Carbon $when): string
    {
        $dayMap = [
            'Sunday'    => 'minggu',
            'Monday'    => 'senin',
            'Tuesday'   => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday'  => 'kamis',
            'Friday'    => 'jumat',
            'Saturday'  => 'sabtu',
        ];

        return $dayMap[$when->format('l')];
    }

    /**
     * Real-time store status: checks manual override first, then today's
     * schedule. Pass a pre-fetched schedule to avoid re-reading the cache;
     * otherwise it falls back to the shared default schedule.
     */
    protected function calcStoreStatus(?array $schedule = null): array
    {
        $override = Cache::get('manual_override');

        if ($override && ($override['is_open'] ?? false)) {
            return ['is_open' => true, 'reason' => 'manual', 'reopen_day' => '', 'reopen_time' => ''];
        }

        if ($override && ($override['is_closed'] ?? false)) {
            return [
                'is_open'     => false,
                'reason'      => 'manual',
                'reopen_day'  => $override['reopen_day']  ?? '',
                'reopen_time' => $override['reopen_time'] ?? '',
            ];
        }

        $schedule = $schedule ?: $this->operationalSchedule();

        $now        = Carbon::now('Asia/Jakarta');
        $todayKey   = $this->scheduleKeyFor($now);
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

    // Find the next open day after today.
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
