<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Installment;
use App\Services\InstallmentService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;
    private InstallmentService $installmentService;

    public function __construct(OrderService $orderService, InstallmentService $installmentService)
    {
        $this->orderService = $orderService;
        $this->installmentService = $installmentService;
    }

    public function store(StoreOrderRequest $request)
    {
        $this->orderService->create(auth()->id(), $request->items);

        return response()->json();
    }

    public function payInstallment(Request $request, Installment $installment)
    {
        $this->installmentService->pay($installment);

        return response()->json();
    }
}
