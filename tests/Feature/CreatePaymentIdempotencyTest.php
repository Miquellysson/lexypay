<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Payment;
use App\Payments\Providers\StripeProvider;
use App\Payments\Services\PaymentService;
use App\Payments\Support\ApiKeyHasher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreatePaymentIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_idempotency_key_returns_same_payment(): void
    {
        config([
            'services.stripe.checkout_success_url' => 'https://example.com/success/{PAYMENT_ID}',
            'services.stripe.checkout_cancel_url' => 'https://example.com/cancel/{PAYMENT_ID}',
        ]);

        $token = 'merchant_key_123456';
        $merchant = Merchant::create([
            'name' => 'Demo',
            'api_key_hash' => ApiKeyHasher::hash($token),
            'status' => 'active',
        ]);

        app()->bind(StripeProvider::class, function ($app) {
            return new class($app->make(PaymentService::class)) extends StripeProvider {
                public function __construct(PaymentService $paymentService)
                {
                    parent::__construct($paymentService);
                }

                public function createCheckoutSession(Payment $payment, array $urls): array
                {
                    $payment->update([
                        'provider_payment_id' => 'cs_test_123',
                        'provider_checkout_url' => 'https://checkout.test/session',
                    ]);

                    return [
                        'session_id' => 'cs_test_123',
                        'checkout_url' => 'https://checkout.test/session',
                    ];
                }
            };
        });

        $payload = [
            'amount' => 1500,
            'currency' => 'USD',
            'provider' => 'stripe',
            'idempotency_key' => 'order_1',
            'metadata' => ['order_id' => 'order_1'],
        ];

        $response1 = $this->postJson('/api/v1/payments', $payload, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response2 = $this->postJson('/api/v1/payments', $payload, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $this->assertSame($response1->json('id'), $response2->json('id'));
        $this->assertSame($response1->json('checkout_url'), $response2->json('checkout_url'));
        $this->assertDatabaseCount('payments', 1);
    }
}
