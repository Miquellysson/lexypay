<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreatePaymentRequest;
use App\Models\Payment;
use App\Payments\DTOs\CreatePaymentData;
use App\Payments\Providers\StripeProvider;
use App\Payments\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(
        CreatePaymentRequest $request,
        PaymentService $paymentService,
        StripeProvider $stripeProvider,
    ): JsonResponse {
        $merchant = $request->attributes->get('merchant');

        $provider = $request->input('provider', 'stripe');

        if ($provider !== 'stripe') {
            return response()->json(['message' => 'Provider not supported.'], 422);
        }

        $data = new CreatePaymentData(
            amount: (int) $request->input('amount'),
            currency: $request->input('currency', 'USD'),
            provider: $provider,
            idempotencyKey: $request->input('idempotency_key'),
            metadata: $request->input('metadata'),
        );

        try {
            $payment = $paymentService->createPayment($merchant, $data);
        } catch (\InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        $session = $stripeProvider->createCheckoutSession($payment, [
            'success' => config('services.stripe.checkout_success_url'),
            'cancel' => config('services.stripe.checkout_cancel_url'),
        ]);

        return response()->json([
            'id' => $payment->id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'provider' => $payment->provider,
            'checkout_url' => $session['checkout_url'],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $merchant = $request->attributes->get('merchant');

        $payment = Payment::where('merchant_id', $merchant->id)
            ->where('id', $id)
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json([
            'id' => $payment->id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'provider' => $payment->provider,
            'paid_at' => $payment->paid_at,
            'metadata' => $payment->metadata,
        ]);
    }
}
