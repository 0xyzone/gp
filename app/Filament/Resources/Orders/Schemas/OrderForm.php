<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('completed'),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('grand_total')
                    ->required()
                    ->numeric(),
                TextInput::make('payment_method')
                    ->required()
                    ->default('cash'),
                TextInput::make('cash_received')
                    ->numeric(),
                TextInput::make('change_returned')
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
