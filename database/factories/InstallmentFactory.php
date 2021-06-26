<?php

namespace Database\Factories;

use App\Models\Installment;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstallmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Installment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_id' => $this->faker->randomNumber(4),
            'total_price' => $this->faker->randomNumber(4),
            'period_date' => $this->faker->date(),
            'turn' => $this->faker->randomNumber(1),
            'status' => 0,
            'paid_at' => $this->faker->date(),
        ];
    }
}
