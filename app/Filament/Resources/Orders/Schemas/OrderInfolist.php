<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('order_number'),
                TextEntry::make('status'),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('discount')
                    ->numeric(),
                TextEntry::make('grand_total')
                    ->numeric(),
                TextEntry::make('payment_method'),
                TextEntry::make('cash_received')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('change_returned')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
