<?php

namespace App\Console\Commands;

use App\Http\Controllers\Concerns\RestoresOrderStock;
use App\Models\Order;
use Illuminate\Console\Command;

// Safety net for "expired → Cancelled". The Midtrans webhook cancels a charged
// order the moment its QR expires (transaction_status=expire); this sweep covers
// the cases the webhook can't: a checkout abandoned before any QR charge was
// created (Midtrans never sends a notification), or a notification that was lost.
class CancelExpiredOrders extends Command
{
    use RestoresOrderStock;

    protected $signature = 'orders:cancel-expired
        {--minutes=30 : Age in minutes after which an unpaid online order is cancelled}';

    protected $description = 'Cancel online orders left unpaid past the payment window and restore their reserved stock.';

    public function handle(): int
    {
        $minutes = max(1, (int) $this->option('minutes'));
        $cutoff  = now()->subMinutes($minutes);

        // Still-pending online orders older than the cutoff whose payment never
        // succeeded. The generous cutoff (well past Midtrans' 15-min QRIS expiry)
        // keeps this from racing the real-time webhook on a freshly-paid order.
        $orders = Order::with(['items', 'payment'])
            ->where('order_type', 'online')
            ->where('status', 'Pending')
            ->where('created_at', '<', $cutoff)
            ->where(function ($q) {
                $q->whereDoesntHave('payment')
                  ->orWhereHas('payment', fn ($p) => $p->where('status', 'Unpaid'));
            })
            ->get();

        foreach ($orders as $order) {
            $this->restoreStockForOrder($order);
            $order->payment()->update(['status' => 'Failed']);
            $order->update([
                'status'              => 'Cancelled',
                'cancellation_reason' => 'Pembayaran kedaluwarsa',
            ]);
        }

        $this->info("Cancelled {$orders->count()} expired unpaid order(s).");

        return self::SUCCESS;
    }
}
