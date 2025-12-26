<?php

namespace App\Payments\Services;

use App\Models\Merchant;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Payments\DTOs\CreatePaymentData;
use App\Payments\Enums\PaymentStatus;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function createPayment(Merchant $merchant, CreatePaymentData $data): Payment
    {
        if ($data->amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }

        if (strtoupper($data->currency) !== 'USD') {
            throw new \InvalidArgumentException('Only USD is supported.');
        }

        if (strlen($data->idempotencyKey) < 6 || strlen($data->idempotencyKey) > 128) {
            throw new \InvalidArgumentException('Invalid idempotency key length.');
        }

        return DB::transaction(function () use ($merchant, $data) {
            $existing = Payment::where('merchant_id', $merchant->id)
                ->where('idempotency_key', $data->idempotencyKey)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            try {
                $payment = Payment::create([
                    'merchant_id' => $merchant->id,
                    'amount' => $data->amount,
                    'currency' => strtoupper($data->currency),
                    'status' => PaymentStatus::Pending->value,
                    'provider' => $data->provider,
                    'idempotency_key' => $data->idempotencyKey,
                    'metadata' => $data->metadata,
                ]);
            } catch (QueryException $exception) {
                $payment = Payment::where('merchant_id', $merchant->id)
                    ->where('idempotency_key', $data->idempotencyKey)
                    ->first();

                if ($payment) {
                    return $payment;
                }

                throw $exception;
            }

            $this->recordEvent($payment, 'created', [
                'amount' => $payment->amount,
                'currency' => $payment->currency,
            ]);

            return $payment;
        });
    }

    public function transitionStatus(Payment $payment, PaymentStatus $newStatus): bool
    {
        $allowed = [
            PaymentStatus::Pending->value => [
                PaymentStatus::Paid->value,
                PaymentStatus::Failed->value,
                PaymentStatus::Expired->value,
                PaymentStatus::Canceled->value,
            ],
            PaymentStatus::Paid->value => [
                PaymentStatus::Refunded->value,
            ],
        ];

        $current = $payment->status;
        $validTargets = $allowed[$current] ?? [];

        if (! in_array($newStatus->value, $validTargets, true)) {
            $this->recordEvent($payment, 'error', [
                'message' => 'Invalid status transition.',
                'from' => $current,
                'to' => $newStatus->value,
            ]);

            Log::warning('Invalid payment status transition.', [
                'payment_id' => $payment->id,
                'merchant_id' => $payment->merchant_id,
                'from' => $current,
                'to' => $newStatus->value,
            ]);

            return false;
        }

        $payment->status = $newStatus->value;
        $payment->save();

        $this->recordEvent($payment, 'status_changed', [
            'from' => $current,
            'to' => $newStatus->value,
        ]);

        return true;
    }

    public function recordEvent(Payment $payment, string $type, array $payload): void
    {
        PaymentEvent::create([
            'payment_id' => $payment->id,
            'type' => $type,
            'payload_json' => $payload,
            'created_at' => now(),
        ]);
    }
}
