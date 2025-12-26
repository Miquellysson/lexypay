<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentsStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $merchantId = auth()->user()?->merchant_id;

        $baseQuery = Payment::query()->where('merchant_id', $merchantId);

        $paidToday = (clone $baseQuery)
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->startOfDay())
            ->sum('amount');

        $paid7d = (clone $baseQuery)
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subDays(6)->startOfDay())
            ->sum('amount');

        $paid30d = (clone $baseQuery)
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subDays(29)->startOfDay())
            ->sum('amount');

        $paidCount = (clone $baseQuery)->where('status', 'paid')->count();
        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $failedCount = (clone $baseQuery)->where('status', 'failed')->count();

        return [
            Stat::make('Paid today', '$'.number_format($paidToday / 100, 2)),
            Stat::make('Paid 7d', '$'.number_format($paid7d / 100, 2)),
            Stat::make('Paid 30d', '$'.number_format($paid30d / 100, 2)),
            Stat::make('Paid count', (string) $paidCount),
            Stat::make('Pending count', (string) $pendingCount),
            Stat::make('Failed count', (string) $failedCount),
        ];
    }
}
