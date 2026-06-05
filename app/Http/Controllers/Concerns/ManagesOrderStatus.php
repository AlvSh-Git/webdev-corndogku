<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Shared order-status mutation for the cashier and owner dashboards.
 *
 * Centralizing this is what keeps cancellations correct on BOTH boards: a paid
 * online order always triggers a Midtrans refund (previously the owner path
 * skipped it and silently dropped the payment record).
 */
trait ManagesOrderStatus
{
    use RestoresOrderStock;

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status'              => ['required', 'in:Pending,Preparing,Ready,Completed,Cancelled'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::with(['items.product', 'payment'])->findOrFail($id);

        $updateData = ['status' => $request->status];

        // Assign the processing staff member the first time anyone touches this order.
        if (!$order->cashier_id) {
            $updateData['cashier_id'] = auth()->id();
        }

        if ($request->status === 'Cancelled') {
            $updateData['cancellation_reason'] = $request->cancellation_reason;

            $isRefundedViaMidtrans = false;

            // Refund paid online (QRIS/e-wallet) orders through Midtrans.
            if ($order->payment
                && $order->payment->status === 'Paid'
                && in_array(strtolower($order->payment->payment_method), ['qris', 'gopay', 'shopeepay'])
            ) {
                $serverKey    = config('services.midtrans.server_key');
                $isProduction = config('services.midtrans.is_production', false);
                $baseUrl      = $isProduction ? 'https://api.midtrans.com/v2' : 'https://api.sandbox.midtrans.com/v2';

                $response = Http::withBasicAuth($serverKey, '')
                    ->withHeaders([
                        'Accept'       => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->post("{$baseUrl}/{$order->order_number}/refund", [
                        'refund_key' => 'REF-' . $order->order_number . '-' . time(),
                        'amount'     => (int) $order->payment->amount,
                        'reason'     => $request->cancellation_reason ?? 'Dibatalkan oleh Toko',
                    ]);

                // Abort the cancellation if Midtrans rejects the refund.
                if ($response->failed() && $response->status() !== 200) {
                    Log::error('Midtrans Refund Failed', [
                        'order_id' => $order->order_number,
                        'response' => $response->json(),
                    ]);

                    return response()->json([
                        'error'   => 'refund_failed',
                        'message' => 'Gagal Refund Midtrans: ' . ($response->json('status_message') ?? 'Terjadi kesalahan jaringan.'),
                    ], 422);
                }

                $order->payment->update(['status' => 'Refunded']);
                $isRefundedViaMidtrans = true;
            }

            $this->restoreStockForOrder($order);

            // Cash/unpaid orders have no money to return — drop the payment row
            // so revenue stays accurate. Refunded online orders keep their row.
            if (!$isRefundedViaMidtrans) {
                $order->payment()->delete();
            }
        }

        $order->update($updateData);

        // Ensure every completed order has a payment record so the Sales Report
        // can always join on payments without missing rows.
        if ($request->status === 'Completed' && !$order->payment()->exists()) {
            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => $order->order_type === 'online' ? 'QRIS' : 'Cash',
                'amount'         => $order->total_price,
                'status'         => 'Paid',
            ]);
        }

        return response()->json([
            'success'  => true,
            'status'   => $request->status,
            'order_id' => (int) $id,
        ]);
    }
}
