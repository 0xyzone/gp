<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentOrdersTable extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Expo Orders (Live Feed)';

    protected ?string $pollingInterval = '10s';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(10))
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->weight('bold')
                    ->icon('heroicon-o-receipt-percent')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('created_at')
                    ->label('Time')
                    ->since()
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'qr' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('grand_total')
                    ->label('Total (रू)')
                    ->weight('bold')
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                    ->alignEnd(),
            ])
            ->recordActions([
                Action::make('printReceipt')
                    ->label('Print 80mm')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn (Order $record): string => route('orders.receipt', $record))
                    ->openUrlInNewTab(),

                ViewAction::make()
                    ->slideOver()
                    ->modalWidth(Width::SevenExtraLarge),
            ])
            ->paginated(false);
    }
}
