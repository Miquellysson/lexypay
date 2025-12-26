<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1'],
            'currency' => ['nullable', 'string', 'in:USD'],
            'provider' => ['nullable', 'string', 'in:stripe'],
            'idempotency_key' => ['required', 'string', 'min:6', 'max:128'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
