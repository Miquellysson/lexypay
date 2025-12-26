<?php

namespace App\Payments\Enums;

enum PaymentStatus: string
{
    case Created = 'created';
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Canceled = 'canceled';
    case Expired = 'expired';
}
