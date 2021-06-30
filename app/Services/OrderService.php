<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Repositories\OrderRepository;

class OrderService
{
    private InstallmentService $installmentService;
    private OrderRepository $orderRepository;

    public function __construct(InstallmentService $installmentService, OrderRepository $orderRepository)
    {
        $this->installmentService = $installmentService;
        $this->orderRepository = $orderRepository;
    }

    public function create(int $userId, array $items): void
    {
        $totalQuantity = array_sum(array_column($items, 'quantity'));
        $totalPrice = array_sum(array_column($items, 'price'));

        $order = $this->orderRepository->create($userId, OrderStatus::Unpaid, $totalQuantity, $totalPrice);

        $this->orderRepository->createItems($order, $items);

        $this->installmentService->createManyForOrder($order);
    }
}
