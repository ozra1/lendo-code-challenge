<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\InstallmentService;
use App\Services\OrderService;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreate()
    {
        $order = new Order([
            'user_id' => 1,
            'status' => OrderStatus::Unpaid,
            'total_quantity' => 4,
            'total_price' => 60000,
        ]);
        $orderItems = [
            [
                'shop_id' => 2,
                'quantity' => 2,
                'price' => 30000,
                'month_count' => 3,
            ],
            [
                'shop_id' => 3,
                'quantity' => 2,
                'price' => 30000,
                'month_count' => 3,
            ],
        ];

        $orderRepositoryMock = \Mockery::mock(OrderRepository::class);
        $orderRepositoryMock->shouldReceive('create')
            ->with($order->user_id, $order->status, $order->total_quantity, $order->total_price)
            ->andReturn($order)
            ->once();
        $orderRepositoryMock->shouldReceive('createItems')
            ->with($order, $orderItems)
            ->once();
        $this->app->instance(OrderRepository::class, $orderRepositoryMock);

        $installmentServiceMock = \Mockery::mock(InstallmentService::class);
        $installmentServiceMock->shouldReceive('createManyForOrder')
            ->with($order)
            ->once();
        $this->app->instance(InstallmentService::class, $installmentServiceMock);

        $orderService = app(OrderService::class);

        $orderService->create($order->user_id, $orderItems);
    }
}
