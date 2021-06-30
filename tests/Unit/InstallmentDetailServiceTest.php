<?php

namespace Tests\Unit;

use App\Enums\InstallmentStatus;
use App\Models\Installment;
use App\Models\Order;
use App\Repositories\InstallmentDetailRepository;
use App\Services\InstallmentDetailService;
use Carbon\Carbon;
use Tests\TestCase;

class InstallmentDetailServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreate()
    {
        $order = new Order();
        $order->id = 1;
        $order->created_at = Carbon::now()->toDateTimeString();
        $order->items = collect([
            (object)([
                'shop_id' => 1,
                'month_count' => 3,
                'price_per_month' => 10000,
            ]),
            (object)([
                'shop_id' => 2,
                'month_count' => 3,
                'price_per_month' => 10000,
            ]),
        ]);

        $installmentMock = $this->partialMock(Installment::class);
        $installmentMock->shouldReceive('order')->once()->andReturn($order);


        $installment = app(Installment::class);
        $installment->id = 1;
        $installment->order_id = $order->id;
        $installment->total_price = 20000;
        $installment->period_date = Carbon::parse($order->created_at)->addMonths(1)->toDateTimeString();
        $installment->turn = 1;
        $installment->status = InstallmentStatus::Paid;


        $installmentDetailRepositoryMock = $this->mock(InstallmentDetailRepository::class);
        $installmentDetailRepositoryMock->shouldReceive('create')
            ->times(6);


        $installmentDetailService = app(InstallmentDetailService::class);

        $installmentDetailService->create($installment);
    }
}
