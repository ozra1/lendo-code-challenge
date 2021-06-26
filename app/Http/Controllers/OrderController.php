<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Installment;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(StoreOrderRequest $request)
    {
        $this->orderService->store(auth()->id(), $request->items);

        return response()->json();
    }

    public function payInstallment(Request $request, Installment $installment)
    {
        $this->orderService->payInstallment($installment);

        return response()->json();
    }
}
