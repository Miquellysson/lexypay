<?php

namespace App\Payments\Providers;

use App\Models\Payment;
use App\Payments\Services\PaymentService;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeProvider
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {
    }

    public function createCheckoutSession(Payment $payment, array $urls): array
    {
        if ($payment->provider_payment_id && $payment->provider_checkout_url) {
            return [
                'session_id' => $payment->provider_payment_id,
                'checkout_url' => $payment->provider_checkout_url,
            ];
        }

        $client = new StripeClient(config('services.stripe.secret'));

        $orderName = $payment->metadata['order_id'] ?? $payment->id;

        $session = $client->checkout->sessions->create(
            [
                'mode' => 'payment',
                'line_items' => [
                    [
                        'quantity' => 1,
                        'price_data' => [
                            'currency' => 'usd',
                            'unit_amount' => $payment->amount,
                            'product_data' => [
                                'name' => 'Order '.$orderName,
                            ],
                        ],
                    ],
                ],
                'metadata' => [
                    'payment_id' => $payment->id,
                    'merchant_id' => (string) $payment->merchant_id,
                    'idempotency_key' => $payment->idempotency_key,
                ],
                'success_url' => str_replace('{PAYMENT_ID}', $payment->id, $urls['success']),
                'cancel_url' => str_replace('{PAYMENT_ID}', $payment->id, $urls['cancel']),
            ],
            [
                'idempotency_key' => $payment->idempotency_key,
            ],
        );

        $payment->update([
            'provider_payment_id' => $session->id,
            'provider_checkout_url' => $session->url,
        ]);

        $this->paymentService->recordEvent($payment, 'provider_session_created', [
            'session_id' => $session->id,
            'checkout_url' => $session->url,
        ]);

        return [
            'session_id' => $session->id,
            'checkout_url' => $session->url,
        ];
    }
}
