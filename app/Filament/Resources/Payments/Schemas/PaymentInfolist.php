<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment')
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('amount')
                            ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2)),
                        TextEntry::make('currency'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('provider'),
                        TextEntry::make('idempotency_key'),
                        TextEntry::make('paid_at')->dateTime(),
                        TextEntry::make('metadata')
                            ->formatStateUsing(fn (?array $state): string => $state ? json_encode($state) : '-'),
                    ])
                    ->columns(2),
                Section::make('Events')
                    ->schema([
                        RepeatableEntry::make('events')
                            ->schema([
                                TextEntry::make('type'),
                                TextEntry::make('created_at')->dateTime(),
                                TextEntry::make('payload_json')
                                    ->label('Payload')
                                    ->formatStateUsing(fn (?array $state): string => $state ? json_encode($state) : '-'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
