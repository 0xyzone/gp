<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Order Summary')
                            ->description('Order identification and status')
                            ->icon('heroicon-o-receipt-percent')
                            ->columnSpan(2)
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('order_number')
                                            ->label('Order Number')
                                            ->disabled()
                                            ->dehydrated(),
                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'completed' => 'Completed',
                                                'pending' => 'Pending',
                                                'cancelled' => 'Cancelled',
                                            ])
                                            ->default('completed')
                                            ->required(),
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('subtotal')
                                            ->label('Subtotal (रू)')
                                            ->numeric()
                                            ->prefix('रू')
                                            ->required(),
                                        TextInput::make('discount')
                                            ->label('Discount (रू)')
                                            ->numeric()
                                            ->prefix('रू')
                                            ->default(0.0)
                                            ->required(),
                                        TextInput::make('grand_total')
                                            ->label('Grand Total (रू)')
                                            ->numeric()
                                            ->prefix('रू')
                                            ->required(),
                                    ]),
                                Textarea::make('notes')
                                    ->label('Customer / Order Notes')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ]),

                        Section::make('Payment Info')
                            ->description('Tender method and cash tracking')
                            ->icon('heroicon-o-currency-rupee')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('payment_method')
                                    ->label('Payment Method')
                                    ->options([
                                        'cash' => 'Cash',
                                        'qr' => 'QR Payment (Siddhartha Bank)',
                                        'card' => 'Card / POS Machine',
                                    ])
                                    ->default('cash')
                                    ->required(),
                                TextInput::make('cash_received')
                                    ->label('Cash Received (रू)')
                                    ->numeric()
                                    ->prefix('रू'),
                                TextInput::make('change_returned')
                                    ->label('Change Returned (रू)')
                                    ->numeric()
                                    ->prefix('रू'),
                            ]),
                    ]),
            ]);
    }
}
