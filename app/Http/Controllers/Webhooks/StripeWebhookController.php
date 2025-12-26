<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\WebhookEvent;
use App\Payments\Enums\PaymentStatus;
use App\Payments\Services\PaymentService;
use App\Payments\Stripe\WebhookVerifier;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class StripeWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        WebhookVerifier $verifier,
        PaymentService $paymentService,
    ): JsonResponse {
        try {
            $event = $verifier->verify($request);
        } catch (Throwable $exception) {
            Log::warning('Stripe webhook signature invalid.', [
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        $externalEventId = $event->id ?? null;

        if (! $externalEventId) {
            return response()->json(['message' => 'ok']);
        }

        try {
            WebhookEvent::create([
                'provider' => 'stripe',
                'external_event_id' => $externalEventId,
                'received_at' => now(),
            ]);
        } catch (QueryException) {
            return response()->json(['message' => 'ok']);
        }

        $type = $event->type ?? '';
        $object = $event->data->object ?? null;
        $metadata = $object->metadata ?? null;
        $paymentId = $metadata->payment_id ?? null;
        $payload = json_decode($request->getContent(), true) ?? [];

        if (! $paymentId) {
            Log::warning('Stripe webhook missing payment_id.', [
                'event_id' => $externalEventId,
                'type' => $type,
            ]);

            return response()->json(['message' => 'ok']);
        }

        DB::transaction(function () use ($paymentId, $type, $payload, $paymentService) {
            $payment = Payment::where('id', $paymentId)->lockForUpdate()->first();

            if (! $payment) {
                Log::warning('Stripe webhook payment not found.', [
                    'payment_id' => $paymentId,
                    'event_type' => $type,
                ]);

                return;
            }

            $paymentService->recordEvent($payment, 'webhook_received', [
                'type' => $type,
                'payload' => $payload,
            ]);

            if ($type === 'checkout.session.completed') {
                $payment->paid_at = now();
                $payment->save();
                $paymentService->transitionStatus($payment, PaymentStatus::Paid);
            } elseif ($type === 'checkout.session.expired') {
                $paymentService->transitionStatus($payment, PaymentStatus::Expired);
            } elseif ($type === 'payment_intent.payment_failed') {
                $paymentService->transitionStatus($payment, PaymentStatus::Failed);
            }
        });

        return response()->json(['message' => 'ok']);
    }
}
