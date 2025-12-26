<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Payment;
use App\Payments\Support\ApiKeyHasher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiKeyAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_authorization_returns_401(): void
    {
        $response = $this->getJson('/api/v1/payments/'.Str::uuid());

        $response->assertStatus(401);
    }

    public function test_invalid_token_returns_401(): void
    {
        $response = $this->getJson('/api/v1/payments/'.Str::uuid(), [
            'Authorization' => 'Bearer invalid',
        ]);

        $response->assertStatus(401);
    }

    public function test_inactive_merchant_returns_403(): void
    {
        $token = 'inactive_key_123456';

        $merchant = Merchant::create([
            'name' => 'Inactive',
            'api_key_hash' => ApiKeyHasher::hash($token),
            'status' => 'inactive',
        ]);

        $payment = Payment::factory()->create([
            'merchant_id' => $merchant->id,
        ]);

        $response = $this->getJson('/api/v1/payments/'.$payment->id, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(403);
    }

    public function test_valid_token_allows_access(): void
    {
        $token = 'active_key_123456';

        $merchant = Merchant::create([
            'name' => 'Active',
            'api_key_hash' => ApiKeyHasher::hash($token),
            'status' => 'active',
        ]);

        $payment = Payment::factory()->create([
            'merchant_id' => $merchant->id,
        ]);

        $response = $this->getJson('/api/v1/payments/'.$payment->id, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200);
    }
}
