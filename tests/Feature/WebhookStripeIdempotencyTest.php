<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\WebhookEvent;
use App\Payments\Stripe\WebhookVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookStripeIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_idempotency_prevents_double_processing(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'pending',
        ]);

        $event = (object) [
            'id' => 'evt_123',
            'type' => 'checkout.session.completed',
            'data' => (object) [
                'object' => (object) [
                    'metadata' => (object) [
                        'payment_id' => $payment->id,
                    ],
                ],
            ],
        ];

        app()->bind(WebhookVerifier::class, function () use ($event) {
            return new class($event) implements WebhookVerifier {
                public function __construct(private readonly object $event)
                {
                }

                public function verify($request): object
                {
                    return $this->event;
                }
            };
        });

        $response1 = $this->postJson('/api/v1/webhooks/stripe', []);
        $response2 = $this->postJson('/api/v1/webhooks/stripe', []);

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $payment->refresh();

        $this->assertSame('paid', $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame(1, WebhookEvent::count());
    }
}
