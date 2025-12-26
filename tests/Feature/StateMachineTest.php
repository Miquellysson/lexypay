<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Payments\Enums\PaymentStatus;
use App\Payments\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StateMachineTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_transition_is_ignored_and_logged(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'paid',
        ]);

        $service = app(PaymentService::class);

        $result = $service->transitionStatus($payment, PaymentStatus::Pending);

        $this->assertFalse($result);
        $payment->refresh();
        $this->assertSame('paid', $payment->status);
        $this->assertDatabaseHas('payment_events', [
            'payment_id' => $payment->id,
            'type' => 'error',
        ]);
    }
}
