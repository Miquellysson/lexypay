<?php

namespace Database\Factories;

use App\Models\Merchant;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'merchant_id' => Merchant::factory(),
            'amount' => $this->faker->numberBetween(100, 20000),
            'currency' => 'USD',
            'status' => 'pending',
            'provider' => 'stripe',
            'idempotency_key' => Str::random(12),
            'metadata' => null,
        ];
    }
}
