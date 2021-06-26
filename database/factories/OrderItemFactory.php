<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_id' => $this->faker->randomNumber(4),
            'shop_id' => $this->faker->randomNumber(4),
            'quantity' => 1,
            'price' => $this->faker->randomNumber(4),
            'month_count' => $this->faker->randomNumber(4),
        ];
    }
}
