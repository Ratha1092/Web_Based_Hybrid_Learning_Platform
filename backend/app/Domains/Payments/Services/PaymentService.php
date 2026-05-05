<?php

namespace App\Domains\Payments\Services;

use App\Domains\Payments\Models\Payment;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Finance\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function handleSuccessfulPayment($paymentId)
    {
        return DB::transaction(function () use ($paymentId) {

            $payment = Payment::findOrFail($paymentId);

            if ($payment->status === 'completed') {
                return $payment;
            }

            $payment->update([
                'status' => 'completed',
                'paid_at' => now()
            ]);

            $order = $payment->order;

            foreach ($order->items as $item) {

                Enrollment::firstOrCreate([
                    'user_id' => $order->user_id,
                    'course_id' => $item->course_id,
                ]);

                $course = $item->course;

                $commission = $course->commission_percentage ?? 20;

                $platformAmount = ($item->price * $commission) / 100;
                $instructorAmount = $item->price - $platformAmount;

                RevenueShare::create([
                    'order_item_id' => $item->id,
                    'instructor_id' => $course->instructor_id,
                    'platform_amount' => $platformAmount,
                    'instructor_amount' => $instructorAmount,
                    'commission_percentage' => $commission
                ]);

                $wallet = InstructorWallet::firstOrCreate([
                    'instructor_id' => $course->instructor_id
                ]);

                $wallet->increment('balance', $instructorAmount);

                WalletTransaction::create([
                    'instructor_id' => $course->instructor_id,
                    'amount' => $instructorAmount,
                    'type' => 'credit',
                    'description' => 'Course sale',
                    'reference_id' => $item->id
                ]);
            }

            return $payment;
        });
    }
}