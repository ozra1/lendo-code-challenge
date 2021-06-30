<?php

namespace App\Services;

use App\Enums\InstallmentStatus;
use App\Models\Installment;
use App\Models\Order;
use App\Repositories\InstallmentRepository;
use Carbon\Carbon;

class InstallmentService
{
    private InstallmentDetailService $installmentDetailService;
    private InstallmentRepository $installmentRepository;

    public function __construct(InstallmentDetailService $installmentDetailService, InstallmentRepository $installmentRepository)
    {
        $this->installmentDetailService = $installmentDetailService;
        $this->installmentRepository = $installmentRepository;
    }

    /**
     * Create all installments and details for the order.
     *
     * @param Order $order
     */
    public function createManyForOrder(Order $order): void
    {
        $orderItems = $order->items;

        $turnsCount = max(array_column($orderItems->toArray(), 'month_count'));

        for ($turn = 1; $turn <= $turnsCount; $turn++) {

            // Calculate total price of installment.
            $totalPrice = 0;
            foreach ($orderItems as $orderItem) {
                if ($orderItem->month_count >= $turn) {
                    $totalPrice += $orderItem->price_per_month;
                }
            }

            $periodDate = Carbon::parse($order->created_at)->addMonths($turn)->toDateTimeString();

            $installment = $this->installmentRepository->create($order->id, $totalPrice, $periodDate, $turn);

            $this->installmentDetailService->create($installment);
        }
    }

    public function pay(Installment $installment): void
    {
        $this->installmentRepository->update($installment, [
            'status' => InstallmentStatus::Paid,
            'paid_at' => Carbon::now(),
        ]);
    }
}
