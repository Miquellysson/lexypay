<?php

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Internal\InternalPaymentController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::middleware('api_key')->group(function () {
            Route::post('payments', [PaymentController::class, 'store']);
            Route::get('payments/{id}', [PaymentController::class, 'show']);
        });

        Route::post('webhooks/stripe', StripeWebhookController::class);
    });

    Route::post('internal/payments/{id}/mark-paid', [InternalPaymentController::class, 'markPaid'])
        ->middleware('internal_secret');
});
