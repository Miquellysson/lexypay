<?php

namespace Database\Factories;

use App\Models\Merchant;
use App\Payments\Support\ApiKeyHasher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Merchant>
 */
class MerchantFactory extends Factory
{
    protected $model = Merchant::class;

    public function definition(): array
    {
        $apiKey = Str::random(40);

        return [
            'name' => $this->faker->company(),
            'api_key_hash' => ApiKeyHasher::hash($apiKey),
            'status' => 'active',
        ];
    }
}
