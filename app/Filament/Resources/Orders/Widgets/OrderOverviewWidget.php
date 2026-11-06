<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Models\Order;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', ['completed', 'served'])->sum('grand_total');
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->whereIn('status', ['completed', 'served'])->sum('grand_total');

        return [
            Stat::make('Total Revenue', 'रू '.number_format((float) $totalRevenue, 2))
                ->description($totalOrders.' lifetime completed orders')
                ->descriptionIcon(Heroicon::OutlinedCurrencyRupee)
                ->color('success'),
            Stat::make("Today's Revenue", 'रू '.number_format((float) $todayRevenue, 2))
                ->description($todayOrders.' orders placed today')
                ->descriptionIcon(Heroicon::OutlinedSparkles)
                ->color('primary'),
            Stat::make('Total Orders', (string) $totalOrders)
                ->description('All recorded transactions')
                ->descriptionIcon(Heroicon::OutlinedReceiptPercent)
                ->color('info'),
        ];
    }
}
