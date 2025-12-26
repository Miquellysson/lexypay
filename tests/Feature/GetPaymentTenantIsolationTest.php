<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Payment;
use App\Payments\Support\ApiKeyHasher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPaymentTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_is_isolated_by_merchant(): void
    {
        $tokenA = 'merchant_a_key';
        $merchantA = Merchant::create([
            'name' => 'Merchant A',
            'api_key_hash' => ApiKeyHasher::hash($tokenA),
            'status' => 'active',
        ]);

        $tokenB = 'merchant_b_key';
        $merchantB = Merchant::create([
            'name' => 'Merchant B',
            'api_key_hash' => ApiKeyHasher::hash($tokenB),
            'status' => 'active',
        ]);

        $payment = Payment::factory()->create([
            'merchant_id' => $merchantA->id,
        ]);

        $response = $this->getJson('/api/v1/payments/'.$payment->id, [
            'Authorization' => 'Bearer '.$tokenB,
        ]);

        $response->assertStatus(404);
    }
}
