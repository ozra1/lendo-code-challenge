<?php

namespace App\Services;

use App\Enums\InstallmentType;
use App\Models\Installment;
use App\Repositories\InstallmentDetailRepository;

class InstallmentDetailService
{
    private InstallmentDetailRepository $installmentDetailRepository;

    public function __construct(InstallmentDetailRepository $installmentDetailRepository)
    {
        $this->installmentDetailRepository = $installmentDetailRepository;
    }

    /**
     * @param Installment $installment
     */
    public function create(Installment $installment): void
    {
        $orderItems = $installment->order()->items;

        foreach ($orderItems as $orderItem) {

            // Creates VAT and Delivery details just in first turn.
            if ($installment->turn == 1) {
                $this->installmentDetailRepository->create($installment->id, $orderItem->shop_id, InstallmentType::VAT, 10000);
                $this->installmentDetailRepository->create($installment->id, $orderItem->shop_id, InstallmentType::Delivery, 10000);
            }

            if ($orderItem->month_count >= $installment->turn) {
                $this->installmentDetailRepository->create($installment->id, $orderItem->shop_id, InstallmentType::Main, $orderItem->price_per_month);
            }
        }
    }
}
