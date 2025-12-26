<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Models\Payment;
use App\Payments\Enums\PaymentStatus;
use App\Payments\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2))
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        'canceled', 'expired' => 'gray',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('provider')
                    ->label('Provider')
                    ->sortable(),
                TextColumn::make('metadata.order_id')
                    ->label('Order ID'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'canceled' => 'Canceled',
                        'expired' => 'Expired',
                    ]),
                SelectFilter::make('provider')
                    ->options([
                        'stripe' => 'Stripe',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markPaid')
                    ->label('Mark paid')
                    ->requiresConfirmation()
                    ->visible(fn () => app()->environment('local', 'testing'))
                    ->action(function (Payment $record, PaymentService $paymentService): void {
                        DB::transaction(function () use ($record, $paymentService) {
                            $payment = Payment::where('id', $record->id)->lockForUpdate()->first();

                            if (! $payment) {
                                return;
                            }

                            $payment->paid_at = now();
                            $payment->save();

                            $paymentService->transitionStatus($payment, PaymentStatus::Paid);
                        });
                    }),
            ])
            ->toolbarActions([]);
    }
}
