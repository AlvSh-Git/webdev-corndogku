<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $statusFilter = $request->query('status');

        $statusMap = [
            'Menunggu'   => 'Pending',
            'Diproses'   => ['Preparing', 'Ready'],
            'Selesai'    => 'Completed',
            'Dibatalkan' => 'Cancelled',
        ];

        $query = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest();

        if ($statusFilter && isset($statusMap[$statusFilter])) {
            $mapped = $statusMap[$statusFilter];
            is_array($mapped)
                ? $query->whereIn('status', $mapped)
                : $query->where('status', $mapped);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('customer.history', compact('orders', 'statusFilter'));
    }

    public function show($id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $order = Order::with('items')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $subtotal = (int) round($order->total_price / 1.11);
        $tax      = $order->total_price - $subtotal;

        $orderTypeLabel = match ($order->order_type) {
            'dine-in'  => 'Dine-in',
            'takeaway' => 'Takeaway',
            'online'   => 'Online',
            default    => ucfirst($order->order_type ?? 'Online'),
        };

        return response()->json([
            'id'           => $order->id,
            'order_number' => $order->order_number,
            'date'         => $order->created_at->translatedFormat('d M Y, H:i'),
            'order_type'   => $orderTypeLabel,
            'status'       => $order->status,
            'items'        => $order->items->map(fn($i) => [
                'name'     => $i->product_name,
                'qty'      => $i->quantity,
                'subtotal' => $i->subtotal,
            ]),
            'subtotal'     => $subtotal,
            'tax'          => $tax,
            'total'        => $order->total_price,
        ]);
    }

    public function sendReceipt($id)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $order = Order::with(['user', 'items'])
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $rawPhone = $order->customer_phone ?? $order->user?->phone ?? null;
            if (!$rawPhone) {
                return response()->json(['success' => false, 'message' => 'Nomor WhatsApp tidak ditemukan.'], 400);
            }
            $targetPhone = $this->normalizePhone($rawPhone);

            $subtotal = (int) round($order->total_price / 1.11);
            $tax      = $order->total_price - $subtotal;

            $msg  = "🌽 *CORNDOG-KU - STRUK PEMBELIAN* 🌽\n";
            $msg .= "--------------------------------------\n";
            $msg .= "No. Pesanan : " . $order->order_number . "\n";
            $msg .= "Tanggal     : " . $order->created_at->format('d M Y, H:i') . "\n";
            $msg .= "Nama        : " . ($order->user?->name ?? 'Walk-in') . "\n";
            $msg .= "--------------------------------------\n";

            foreach ($order->items as $item) {
                $name = $item->product_name ?? $item->product?->name ?? 'Item';
                $msg .= "- " . $item->quantity . "x " . $name . "\n";
                $msg .= "  Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
            }

            $msg .= "--------------------------------------\n";
            $msg .= "Subtotal    : Rp " . number_format($subtotal, 0, ',', '.') . "\n";
            $msg .= "Pajak (11%) : Rp " . number_format($tax, 0, ',', '.') . "\n";
            $msg .= "*Total      : Rp " . number_format($order->total_price, 0, ',', '.') . "*\n";
            $msg .= "--------------------------------------\n";
            $msg .= "Terima kasih sudah mampir! 😊";

            $response = Http::withHeaders(['Authorization' => config('services.fonnte.token')])
                ->timeout(30)
                ->post('https://api.fonnte.com/send', [
                    'target'  => $targetPhone,
                    'message' => $msg,
                ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false) === true) {
                return response()->json(['success' => true, 'message' => 'Struk teks berhasil dikirim.']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Fonnte Error: ' . ($body['reason'] ?? 'Unknown error'),
            ], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($digits, '62')) return $digits;
        if (str_starts_with($digits, '0'))  return '62' . substr($digits, 1);
        return '62' . $digits;
    }
}
