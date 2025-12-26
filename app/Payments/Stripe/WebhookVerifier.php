<?php

namespace App\Payments\Stripe;

use Illuminate\Http\Request;

interface WebhookVerifier
{
    public function verify(Request $request): object;
}
