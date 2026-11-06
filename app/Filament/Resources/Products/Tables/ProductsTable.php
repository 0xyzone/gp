<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Thumbnail')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-product.svg'))
                    ->disk('public'),
                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->code ? "Code: {$record->code}" : null),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Actual Price')
                    ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                    ->weight('medium')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('offer_price')
                    ->label('Offer Price')
                    ->formatStateUsing(fn ($state) => $state ? 'रू '.number_format((float) $state, 2) : '-')
                    ->badge(fn ($state) => filled($state))
                    ->color('primary')
                    ->weight('bold')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->formatStateUsing(function ($state, Product $record) {
                        if (! $record->track_stock) {
                            return '∞ Untracked';
                        }
                        if ($state <= 0) {
                            return 'Out of Stock';
                        }

                        return "{$state} left";
                    })
                    ->badge()
                    ->color(function ($state, Product $record) {
                        if (! $record->track_stock) {
                            return 'gray';
                        }
                        if ($state <= 0) {
                            return 'danger';
                        }
                        if ($state <= $record->low_stock_threshold) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->icon(function ($state, Product $record) {
                        if (! $record->track_stock) {
                            return null;
                        }
                        if ($state <= 0) {
                            return 'heroicon-m-x-circle';
                        }
                        if ($state <= $record->low_stock_threshold) {
                            return 'heroicon-m-exclamation-triangle';
                        }

                        return 'heroicon-m-check-circle';
                    })
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->label('POS Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Filter by Category'),
                TernaryFilter::make('is_active')
                    ->label('Availability')
                    ->boolean()
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->slideOver()
                    ->modalWidth(Width::FiveExtraLarge),
                EditAction::make()
                    ->slideOver()
                    ->modalWidth(Width::FiveExtraLarge),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
