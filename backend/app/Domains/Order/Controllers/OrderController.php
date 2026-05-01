<?php

namespace App\Domains\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Order\Services\OrderService;
use App\Domains\Order\Models\Order;

class OrderController extends Controller
{
    public function store($courseId)
    {
        $service = new OrderService();

        return $service->createOrder(auth()->user(), $courseId);
    }
    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'data' => $order
        ]);
    }
}