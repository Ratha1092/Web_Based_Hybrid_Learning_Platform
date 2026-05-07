<?php

namespace App\Domains\Orders\Services;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Payments\Models\Payment;
use App\Domains\Courses\Models\Course;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(User $user, int $courseId): Order
    {
        return DB::transaction(function () use ($user, $courseId) {

            $course = Course::where('id', $courseId)
                ->where('is_published', true)
                ->firstOrFail();

            if ((int) $course->instructor_id === (int) $user->id) {
                throw new \RuntimeException('You cannot purchase your own course');
            }

            if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
                throw new \RuntimeException('You are already enrolled in this course');
            }

            // 🔥 Generate order number
            $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . uniqid();

            // Create Order
            $order = Order::create([
                'order_number' => $orderNumber, // ✅ FIX
                'user_id' => $user->id,
                'total_amount' => $course->price,
                'discount_amount' => 0,
                'final_amount' => $course->price,
                'currency' => 'USD',
                'status' => 'pending',
                'payment_status' => 'pending',
                'customer_name' => $user->name ?? 'Guest',
                'customer_email' => $user->email ?? null,
            ]);
            $commissionPercentage = 20;
            // Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'price' => $course->price,
                'instructor_id' => $course->instructor_id,
                'course_title' => $course->title,
                'commission_percentage' => $commissionPercentage,
            ]);

            // Payment record
            Payment::create([
                'order_id' => $order->id,
                'amount' => $course->price,
                'currency' => 'USD',
                'status' => 'pending',
                'payment_gateway' => 'khqr'
            ]);

            return $order;
        });
    }
}
