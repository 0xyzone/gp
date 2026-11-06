<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Summary')
                    ->icon('heroicon-o-receipt-percent')
                    ->description('Order identity, status, and payment breakdown')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('order_number')
                                    ->label('ORDER NUMBER')
                                    ->weight(FontWeight::ExtraBold)
                                    ->size('lg'),

                                TextEntry::make('status')
                                    ->label('STATUS')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'completed' => 'primary',
                                        'served' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'warning',
                                    }),

                                TextEntry::make('payment_status')
                                    ->label('PAYMENT STATUS')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'paid' => 'success',
                                        'unpaid' => 'danger',
                                        default => 'warning',
                                    })
                                    ->formatStateUsing(fn (?string $state) => strtoupper($state ?? 'unpaid')),

                                TextEntry::make('payment_method')
                                    ->label('PAYMENT METHOD')
                                    ->badge()
                                    ->color('primary')
                                    ->formatStateUsing(fn (string $state) => strtoupper($state)),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('subtotal')
                                    ->label('SUBTOTAL')
                                    ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('discount')
                                    ->label('DISCOUNT')
                                    ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                                    ->weight(FontWeight::SemiBold)
                                    ->color('danger'),

                                TextEntry::make('grand_total')
                                    ->label('GRAND TOTAL')
                                    ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                                    ->size('lg')
                                    ->weight(FontWeight::Black)
                                    ->color('primary'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('cash_received')
                                    ->label('CASH RECEIVED')
                                    ->formatStateUsing(fn ($state) => $state ? 'रू '.number_format((float) $state, 2) : '-')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('change_returned')
                                    ->label('CHANGE RETURNED')
                                    ->formatStateUsing(fn ($state) => $state ? 'रू '.number_format((float) $state, 2) : '-')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),
                            ]),

                        TextEntry::make('notes')
                            ->label('ORDER NOTES')
                            ->placeholder('No customer notes attached.')
                            ->columnSpanFull()
                            ->prose(),
                    ]),

                Section::make('Purchased Items')
                    ->icon('heroicon-o-shopping-bag')
                    ->description('Line items registered with this order')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->hiddenLabel()
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('product_name')
                                            ->label('ITEM')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('quantity')
                                            ->label('QTY')
                                            ->badge()
                                            ->color('gray'),
                                        TextEntry::make('unit_price')
                                            ->label('UNIT PRICE')
                                            ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2)),
                                        TextEntry::make('total_price')
                                            ->label('TOTAL')
                                            ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                                            ->weight(FontWeight::ExtraBold)
                                            ->color('primary'),
                                    ]),
                            ]),
                    ]),

                Section::make('Audit Information')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('TIMESTAMP')
                                    ->dateTime('M d, Y • h:i A')
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label('UPDATED')
                                    ->dateTime('M d, Y • h:i A')
                                    ->placeholder('-'),
                            ]),
                    ]),
            ]);
    }
}
