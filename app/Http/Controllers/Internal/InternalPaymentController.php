<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Payments\Enums\PaymentStatus;
use App\Payments\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InternalPaymentController extends Controller
{
    public function markPaid(string $id, PaymentService $paymentService): JsonResponse
    {
        $payment = DB::transaction(function () use ($id, $paymentService) {
            $payment = Payment::where('id', $id)->lockForUpdate()->first();

            if (! $payment) {
                return null;
            }

            $payment->paid_at = now();
            $payment->save();

            $paymentService->transitionStatus($payment, PaymentStatus::Paid);

            return $payment;
        });

        if (! $payment) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json(['message' => 'ok']);
    }
}
