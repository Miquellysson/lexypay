<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentsChart extends ChartWidget
{
    protected function getData(): array
    {
        $merchantId = auth()->user()?->merchant_id;

        $labels = [];
        $data = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $labels[] = $date->format('M d');

            $amount = Payment::query()
                ->where('merchant_id', $merchantId)
                ->where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('amount');

            $data[] = round($amount / 100, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'USD',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): ?string
    {
        return 'Paid volume (last 14 days)';
    }
}
