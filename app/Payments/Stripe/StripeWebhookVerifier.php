<?php

namespace App\Payments\Stripe;

use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookVerifier implements WebhookVerifier
{
    public function verify(Request $request): object
    {
        $signature = $request->header('Stripe-Signature');
        $payload = $request->getContent();

        return Webhook::constructEvent(
            $payload,
            $signature ?? '',
            config('services.stripe.webhook_secret')
        );
    }
}
