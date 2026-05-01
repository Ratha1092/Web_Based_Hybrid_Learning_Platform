<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\User\Models\User;
use App\Domains\Course\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Review;
use App\Domains\Order\Models\Order;
use App\Domains\Payment\Models\Payment;
use App\Domains\Finance\Models\PayoutRequest;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::orderByDesc('created_at')->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function courses()
    {
        $courses = Course::with(['instructor', 'category'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.courses', compact('courses'));
    }

    public function enrollments()
    {
        $enrollments = Enrollment::with(['user', 'course'])
            ->latest('enrolled_at')
            ->paginate(20);

        return view('admin.enrollments', compact('enrollments'));
    }

    public function reviews()
    {
        $reviews = Review::with(['user', 'course'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.reviews', compact('reviews'));
    }

    public function orders()
    {
        $orders = Order::with('user')
            ->orderByDesc('paid_at')
            ->paginate(20);

        return view('admin.orders', compact('orders'));
    }

    public function payments()
    {
        $payments = Payment::with('order')
            ->orderByDesc('paid_at')
            ->paginate(20);

        return view('admin.payments', compact('payments'));
    }

    public function payouts()
    {
        $payouts = PayoutRequest::with('instructor')
            ->orderByDesc('requested_at')
            ->paginate(20);

        return view('admin.payouts', compact('payouts'));
    }

    public function settings()
    {
        $stats = [
            'users' => User::count(),
            'courses' => Course::count(),
            'enrollments' => Enrollment::count(),
            'orders' => Order::count(),
            'payments' => Payment::count(),
            'payouts' => PayoutRequest::count(),
        ];

        return view('admin.settings', compact('stats'));
    }
}
