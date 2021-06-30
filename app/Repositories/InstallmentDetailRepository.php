<?php


namespace App\Repositories;


use App\Models\Installment;
use App\Models\InstallmentDetail;

class InstallmentDetailRepository
{
    /**
     * @param int $instalmentId
     * @param int $shopId
     * @param int $installmentType
     * @param int $price
     * @return Installment
     */
    public function create(int $instalmentId, int $shopId, int $installmentType, int $price): Installment
    {
        return InstallmentDetail::create([
            'installment_id' => $instalmentId,
            'shop_id' => $shopId,
            'installment_type' => $installmentType,
            'price' => $price,
        ]);
    }
}
