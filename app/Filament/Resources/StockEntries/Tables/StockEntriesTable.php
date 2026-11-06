<?php

namespace App\Filament\Resources\StockEntries\Tables;

use App\Models\StockEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entry_date')
                    ->label('Entry Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (StockEntry $record) => $record->product?->code ? "Code: {$record->product->code}" : null),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'add' => '+ Restock',
                        'subtract' => '- Outward',
                        'adjustment' => '~ Adjustment',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'add' => 'success',
                        'subtract' => 'danger',
                        'adjustment' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('quantity')
                    ->label('Units')
                    ->numeric()
                    ->sortable()
                    ->weight('black')
                    ->alignEnd()
                    ->formatStateUsing(fn ($state, StockEntry $record) => match ($record->type) {
                        'subtract' => "- {$state} units",
                        'add' => "+ {$state} units",
                        default => "{$state} units",
                    })
                    ->color(fn (StockEntry $record) => match ($record->type) {
                        'subtract' => 'danger',
                        'add' => 'success',
                        default => 'warning',
                    }),

                TextColumn::make('product.stock_quantity')
                    ->label('Live Balance')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => "{$state} in stall")
                    ->alignCenter(),

                TextColumn::make('reference_number')
                    ->label('Reference #')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime('M d, Y • h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Filter by Product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label('Filter by Type')
                    ->options([
                        'add' => 'Restock / Inward',
                        'subtract' => 'Outward / Damaged',
                        'adjustment' => 'Adjustment',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver()
                    ->modalWidth(Width::Large),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
