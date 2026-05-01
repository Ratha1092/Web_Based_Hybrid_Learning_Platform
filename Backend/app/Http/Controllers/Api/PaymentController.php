<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Payment\Models\Payment;
use App\Domains\Order\Models\Order;
use App\Domains\Payment\Events\PaymentSuccessEvent;
use App\Domains\Finance\Models\RevenueShare;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function success($id)
    {
        return DB::transaction(function () use ($id) {

            // 🔒 Lock row safely
            $payment = Payment::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $order = $payment->order;
            $orderItemIds = $order?->items->pluck('id')->filter()->values() ?? collect();

            if (!$order || $orderItemIds->isEmpty()) {
                return response()->json([
                    'message' => 'Cannot process payment for an order without items.'
                ], 422);
            }

            if (in_array($payment->status, ['paid', 'completed'], true)) {
                $existingShares = RevenueShare::whereIn('order_item_id', $orderItemIds)->count();

                if ($existingShares === $orderItemIds->count()) {
                    return response()->json([
                        'message' => 'Already processed'
                    ]);
                }

                // Re-dispatch the event if payment status already exists but revenue processing was incomplete.
                event(new PaymentSuccessEvent($order));

                return response()->json([
                    'message' => 'Payment recovered and revenue processing retried.'
                ]);
            }

            // ✅ Atomic update
            $updated = Payment::where('id', $id)
                ->whereNotIn('status', ['paid', 'completed'])
                ->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

            if (!$updated) {
                return response()->json([
                    'message' => 'Already processed'
                ]);
            }

            // 🔄 Refresh model
            $payment->refresh();

            // ✅ Update order
            $order->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // 🚀 Fire event AFTER success
            event(new PaymentSuccessEvent($order));

            return response()->json([
                'message' => 'Payment processed successfully'
            ]);
        });
    }
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($request->order_id);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_gateway' => 'manual', // for now
            'transaction_id' => null,
            'external_reference' => null,
            'idempotency_key' => uniqid('pay_'),
            'amount' => $request->amount,
            'currency' => 'USD',
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Payment created',
            'data' => $payment
        ]);
    }
}