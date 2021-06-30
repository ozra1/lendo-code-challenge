<?php

namespace Tests\Unit;

use App\Enums\InstallmentStatus;
use App\Models\Installment;
use App\Models\Order;
use App\Repositories\InstallmentRepository;
use App\Services\InstallmentDetailService;
use App\Services\InstallmentService;
use Carbon\Carbon;
use Tests\TestCase;

class InstallmentServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateManyForOrder()
    {
        $order = new Order();
        $order->id = 1;
        $order->created_at = Carbon::now()->toDateTimeString();
        $order->items = collect([
            (object)([
                'month_count' => 3,
                'price_per_month' => 10000,
            ]),
            (object)([
                'month_count' => 3,
                'price_per_month' => 10000,
            ]),
        ]);

        $installment1 = new Installment([
            'order_id' => $order->id,
            'total_price' => 20000,
            'period_date' => Carbon::parse($order->created_at)->addMonths(1)->toDateTimeString(),
            'turn' => 1,
            'status' => InstallmentStatus::Paid,
        ]);

        $installment2 = new Installment([
            'order_id' => $order->id,
            'total_price' => 20000,
            'period_date' => Carbon::parse($order->created_at)->addMonths(2)->toDateTimeString(),
            'turn' => 2,
            'status' => InstallmentStatus::Unpaid,
        ]);

        $installment3 = new Installment([
            'order_id' => $order->id,
            'total_price' => 20000,
            'period_date' => Carbon::parse($order->created_at)->addMonths(3)->toDateTimeString(),
            'turn' => 3,
            'status' => InstallmentStatus::Unpaid,
        ]);

        $installmentRepositoryMock = $this->mock(InstallmentRepository::class);

        $installmentRepositoryMock->shouldReceive('create')
            ->with(1, 20000, $installment1->period_date->toDateTimeString(), 1)
            ->andReturn($installment1)
            ->once();

        $installmentRepositoryMock->shouldReceive('create')
            ->with(1, 20000, $installment2->period_date->toDateTimeString(), 2)
            ->andReturn($installment2)
            ->once();

        $installmentRepositoryMock->shouldReceive('create')
            ->with(1, 20000, $installment3->period_date->toDateTimeString(), 3)
            ->andReturn($installment3)
            ->once();


        $installmentDetailServiceMock = $this->mock(InstallmentDetailService::class);
        $installmentDetailServiceMock->shouldReceive('create')->with($installment1)->once();
        $installmentDetailServiceMock->shouldReceive('create')->with($installment2)->once();
        $installmentDetailServiceMock->shouldReceive('create')->with($installment3)->once();


        $installmentService = app(InstallmentService::class);

        $installmentService->createManyForOrder($order);
    }
}
