<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'merchant_id',
        'amount',
        'currency',
        'status',
        'provider',
        'provider_payment_id',
        'provider_checkout_url',
        'idempotency_key',
        'fee_amount',
        'net_amount',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(PaymentEvent::class)->orderBy('created_at');
    }
}
