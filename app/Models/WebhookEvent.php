<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'provider',
        'external_event_id',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];
}
