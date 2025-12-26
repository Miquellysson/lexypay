<?php

namespace App\Payments\Support;

class ApiKeyHasher
{
    public static function hash(string $plainTextKey): string
    {
        // We use HMAC so we can look up merchants by a deterministic hash.
        return hash_hmac('sha256', $plainTextKey, config('app.key'));
    }

    public static function check(string $plainTextKey, string $hashedValue): bool
    {
        return hash_equals($hashedValue, self::hash($plainTextKey));
    }
}
