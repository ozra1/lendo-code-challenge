<?php


namespace App\Repositories;


use App\Models\Order;

class OrderRepository
{
    /**
     * @param int $userId
     * @param int $status
     * @param int $totalQuantity
     * @param int $totalPrice
     * @return Order
     */
    public function create(int $userId, int $status, int $totalQuantity, int $totalPrice): Order
    {
        return Order::create([
            'user_id' => $userId,
            'status' => $status,
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
        ]);
    }

    /**
     * @param Order $order
     * @param array $items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createItems(Order $order, array $items)
    {
        return $order->items()->createMany($items);
    }
}
