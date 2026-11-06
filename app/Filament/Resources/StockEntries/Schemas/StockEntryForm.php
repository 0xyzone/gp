<?php

namespace App\Filament\Resources\StockEntries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Stock Entry Details')
                    ->description('Record new inventory batches or corrections linked to stall products')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Select::make('product_id')
                            ->label('Select Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Product whose stock will be adjusted by this entry'),

                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Entry Type')
                                    ->options([
                                        'add' => 'Add Inventory (Restock / Inward)',
                                        'subtract' => 'Subtract Inventory (Damaged / Return)',
                                        'adjustment' => 'Manual Adjustment',
                                    ])
                                    ->default('add')
                                    ->required()
                                    ->helperText('Whether this entry adds to or reduces available stall stock'),

                                TextInput::make('quantity')
                                    ->label('Quantity (Units)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->step(1)
                                    ->suffix('Units')
                                    ->placeholder('e.g. 50')
                                    ->helperText('Number of units to record'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('entry_date')
                                    ->label('Entry Date')
                                    ->default(now())
                                    ->required(),

                                TextInput::make('reference_number')
                                    ->label('Reference / Batch #')
                                    ->placeholder('e.g. BATCH-2026-A1, INV-882')
                                    ->helperText('Optional batch or invoice reference number'),
                            ]),

                        Textarea::make('notes')
                            ->label('Remarks / Notes')
                            ->placeholder('e.g. Morning expo opening stock, restocked from production kitchen...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
