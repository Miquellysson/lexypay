<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookStripeSignatureInvalidTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_signature_returns_400(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);

        $response = $this->postJson('/api/v1/webhooks/stripe', [
            'id' => 'evt_test',
            'type' => 'checkout.session.completed',
        ]);

        $response->assertStatus(400);
    }
}
