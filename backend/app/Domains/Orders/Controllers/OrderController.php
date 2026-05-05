<?php

namespace App\Domains\Orders\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Orders\Services\OrderService;
use App\Domains\Orders\Models\Order;
use App\Support\ApiResponse;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store($courseId)
    {
        try {
            $order = $this->orderService->createOrder(auth()->user(), $courseId);
            return ApiResponse::success($order, 'Order created successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return ApiResponse::error('Order not found', 404);
        }

        return ApiResponse::success($order, 'Order retrieved successfully');
    }
}