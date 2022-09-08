<?php

namespace Database\Factories\Read;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Read\Transactions>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'event_id' => Uuid::uuid4()->toString(),
            'wallet_id' => '00000000-0000-0000-0000-000000000000',
            'amount' => $this->faker->numberBetween(-100, 100),
            'transacted_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
