<?php

namespace App\Observers;

use App\Enums\InstallmentStatus;
use App\Enums\OrderStatus;
use App\Models\Installment;

class InstallmentObserver
{
    /**
     * Handle the Installment "updated" event.
     *
     * @param  \App\Models\Installment  $installment
     * @return void
     */
    public function updated(Installment $installment)
    {
        $unpaidInstallments = Installment::query()
            ->where('order_id', $installment->order_id)
            ->where('status', InstallmentStatus::Unpaid)
            ->get();

        if (!count($unpaidInstallments)) {
            $installment->order()->update([
                'status' => OrderStatus::Paid,
            ]);
        }
    }
}
