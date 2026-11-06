<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $currency = Setting::get('currency_symbol', 'रू');

        $today = Carbon::today();

        // Real sales data from database
        $todayRevenue = (float) Order::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('grand_total');

        $totalRevenue = (float) Order::where('status', 'completed')->sum('grand_total');

        $todayOrdersCount = Order::whereDate('created_at', $today)->count();
        $totalOrdersCount = Order::count();

        $completedOrdersCount = Order::where('status', 'completed')->count();
        $avgOrderValue = $completedOrdersCount > 0 ? ($totalRevenue / $completedOrdersCount) : 0;

        $cashCount = Order::where('payment_method', 'cash')->where('status', 'completed')->count();
        $qrCount = Order::where('payment_method', 'qr')->where('status', 'completed')->count();

        // 7-day sparkline trend data
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyTrend[] = (float) Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('grand_total');
        }

        return [
            Stat::make("Today's Revenue", $currency.' '.number_format($todayRevenue, 2))
                ->description('Total sales: '.$currency.' '.number_format($totalRevenue, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($weeklyTrend)
                ->color('primary'),

            Stat::make('Orders Placed', (string) $todayOrdersCount.' Today')
                ->description((string) $totalOrdersCount.' total expo orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Average Order Value', $currency.' '.number_format($avgOrderValue, 2))
                ->description('Per completed transaction')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),

            Stat::make('Payment Mix', $cashCount.' Cash / '.$qrCount.' QR')
                ->description('Settled digital & cash count')
                ->descriptionIcon('heroicon-m-qr-code')
                ->color('info'),
        ];
    }
}
