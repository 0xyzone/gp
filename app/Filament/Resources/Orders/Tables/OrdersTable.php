<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->icon('heroicon-o-receipt-percent'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'primary',
                        'served' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'served' => 'Served to Customer',
                        'completed' => 'Preparing / Placed',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'qr' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('discount')
                    ->label('Discount')
                    ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                    ->color('danger')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                    ->weight('bold')
                    ->color('primary')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'qr' => 'QR Payment',
                    ]),
            ])
            ->recordActions([
                Action::make('printReceipt')
                    ->label('Print Receipt')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn (Order $record): string => route('orders.receipt', $record))
                    ->openUrlInNewTab(),
                ViewAction::make()
                    ->slideOver()
                    ->modalWidth(Width::SevenExtraLarge),
                EditAction::make()
                    ->slideOver()
                    ->modalWidth(Width::SevenExtraLarge),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
