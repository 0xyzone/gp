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
            ->columns(12)
            ->components([
                // Primary Column: Core Stock Transaction Form (8 cols on desktop)
                Section::make('Stock Transaction Details')
                    ->description('Record inward stock arrival, outward returns/damage, or corrections')
                    ->icon('heroicon-o-cube')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 7,
                        'xl' => 8,
                    ])
                    ->schema([
                        Select::make('product_id')
                            ->label('Select Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Product whose stock will be adjusted by this entry'),

                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
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
                                    ->helperText('Number of physical units to adjust'),
                            ]),

                        Textarea::make('notes')
                            ->label('Remarks / Notes')
                            ->placeholder('e.g. Morning expo opening stock, restocked from production kitchen, breakage correction...')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Optional explanation for stall cashiers and inventory audit'),
                    ]),

                // Secondary Column: Metadata, Dates & Batch Tracking (4 cols on desktop)
                Section::make('Reference & Audit')
                    ->description('Tracking dates and invoice identifiers')
                    ->icon('heroicon-o-tag')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 5,
                        'xl' => 4,
                    ])
                    ->schema([
                        DatePicker::make('entry_date')
                            ->label('Entry Date')
                            ->default(now())
                            ->required()
                            ->helperText('Date of physical receipt or adjustment'),

                        TextInput::make('reference_number')
                            ->label('Batch / Invoice Ref #')
                            ->placeholder('e.g. BATCH-2026-A1')
                            ->helperText('Supplier batch or transfer note number'),
                    ]),
            ]);
    }
}
