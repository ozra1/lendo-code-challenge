<?php

namespace Tests\Unit;

use App\Enums\InstallmentStatus;
use App\Enums\OrderStatus;
use App\Models\Installment;
use App\Models\InstallmentDetail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var OrderService $orderService */
    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = app(OrderService::class);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $this->actingAs($user);

        $orderItem = [
            'shop_id' => $shop->id,
            'quantity' => 2,
            'price' => 30000,
            'month_count' => 3,
        ];

        $this->orderService->store($user->id, [$orderItem]);

        $orders = Order::all();
        $this->assertCount(1, $orders);
        $this->assertEquals($user->id, $orders[0]->user_id);

        $orderItems = OrderItem::all();
        $this->assertCount(1, $orderItems);
        $this->assertEquals($orders[0]->id, $orderItems[0]->order_id);
        $this->assertEquals($shop->id, $orderItems[0]->shop_id);

        $installments = Installment::all();
        $this->assertCount($orderItem['month_count'], $installments);

        $installmentDetails = InstallmentDetail::all();
        $this->assertCount($orderItem['month_count'] + 2, $installmentDetails);
    }

    /**
     * @return void
     */
    public function testPayInstallment()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_quantity' => 2,
            'total_price' => 20000000,
        ]);
        $installment = Installment::factory()->create([
            'order_id' => $order->id,
        ]);
        $installment2 = Installment::factory()->create([
            'order_id' => $order->id,
        ]);

        $this->orderService->payInstallment($installment);

        // Status should be changed.
        $this->assertEquals(InstallmentStatus::Paid, $installment->refresh()->status);

        // The status of order shouldn not changed!
        $this->assertEquals(OrderStatus::Unpaid, $order->refresh()->status);

        // Pay "last" installment
        $this->orderService->payInstallment($installment2);

        // Status should be changed.
        $this->assertEquals(InstallmentStatus::Paid, $installment2->refresh()->status);

        // The status of order should be changed! (Order status can change in "InstallmentObserver" class).
        $this->assertEquals(OrderStatus::Paid, $order->refresh()->status);
    }
}
