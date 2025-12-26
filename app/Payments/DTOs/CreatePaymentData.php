<?php

namespace App\Payments\DTOs;

class CreatePaymentData
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
        public readonly string $provider,
        public readonly string $idempotencyKey,
        public readonly ?array $metadata = null,
    ) {
    }
}
