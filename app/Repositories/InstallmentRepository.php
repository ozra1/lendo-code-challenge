<?php


namespace App\Repositories;


use App\Enums\InstallmentStatus;
use App\Models\Installment;

class InstallmentRepository
{
    /**
     * @param int $orderId
     * @param int $totalPrice
     * @param string $periodDate
     * @param int $turn
     * @return Installment
     */
    public function create(int $orderId, int $totalPrice, string $periodDate, int $turn): Installment
    {
        return Installment::create([
            'order_id' => $orderId,
            'total_price' => $totalPrice,
            'period_date' => $periodDate,
            'turn' => $turn,
            'status' => ($turn == 1) ? InstallmentStatus::Paid : InstallmentStatus::Unpaid,
        ]);
    }

    /**
     * @param Installment $installment
     * @param array $attributes
     * @return bool
     */
    public function update(Installment $installment, array $attributes)
    {
        return $installment->update($attributes);
    }
}
