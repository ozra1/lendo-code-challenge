<?php

namespace App\Services;

use App\Enums\InstallmentStatus;
use App\Enums\InstallmentType;
use App\Enums\OrderStatus;
use App\Models\Installment;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    public function store(int $userId, array $items): void
    {
        /** @var Order $order */
        $order = Order::query()->create([
            'user_id' => $userId,
            'status' => OrderStatus::Unpaid,
            'total_quantity' => array_sum(array_column($items, 'quantity')),
            'total_price' => array_sum(array_column($items, 'price')),
        ]);

        // Create all items for this order.
        $orderItems = $order->items()->createMany($items);

        $this->createInstallments($order, $orderItems);
    }

    public function payInstallment(Installment $installment): void
    {
         $installment->update([
             'status' => InstallmentStatus::Paid,
             'paid_at' => Carbon::now(),
         ]);
    }

    /**
     * Create all installments and details for the order.
     *
     * @param Order $order
     * @param Collection $orderItems
     */
    private function createInstallments(Order $order, Collection $orderItems): void
    {
        $turnsCount = max(array_column($orderItems->toArray(), 'month_count'));

        for ($turn = 1; $turn <= $turnsCount; $turn++) {

            // Calculate total price of installment.
            $total_price = 0;
            foreach ($orderItems as $orderItem) {
                if ($orderItem->month_count >= $turn) {
                    $total_price += $orderItem->price_per_month;
                }
            }

            /** @var Installment $installment */
            $installment = $order->installments()->create([
                'total_price' => $total_price,
                'period_date' => Carbon::parse($order->created_at)->addMonths($turn)->toDateTimeString(),
                'turn' => $turn,
                'status' => ($turn == 1) ? InstallmentStatus::Paid : InstallmentStatus::Unpaid,
            ]);

            $this->createInstallmentDetails($turn, $orderItems, $installment);
        }
    }

    /**
     * Create installment details for each shop.
     *
     * @param int $turn
     * @param Collection $orderItems
     * @param Installment $installment
     */
    protected function createInstallmentDetails(int $turn, Collection $orderItems, Installment $installment): void
    {
        foreach ($orderItems as $orderItem) {

            // Creates VAT and Delivery details just in first turn.
            if ($turn == 1) {
                $installment->details()->create([
                    'shop_id' => $orderItem->shop_id,
                    'installment_type' => InstallmentType::VAT,
                    'price' => 10000,
                ]);

                $installment->details()->create([
                    'shop_id' => $orderItem->shop_id,
                    'installment_type' => InstallmentType::Delivery,
                    'price' => 10000,
                ]);
            }

            if ($orderItem->month_count >= $turn) {
                $installment->details()->create([
                    'shop_id' => $orderItem->shop_id,
                    'installment_type' => InstallmentType::Main,
                    'price' => $orderItem->price_per_month,
                ]);
            }
        }
    }
}