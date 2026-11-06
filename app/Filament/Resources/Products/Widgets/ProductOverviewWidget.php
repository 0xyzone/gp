<?php

namespace App\Filament\Resources\Products\Widgets;

use App\Models\Product;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total = Product::count();
        $active = Product::where('is_active', true)->count();
        $avgPrice = Product::where('is_active', true)->avg('price') ?? 0;

        return [
            Stat::make('Total Products', (string) $total)
                ->description('All catalog items')
                ->descriptionIcon(Heroicon::OutlinedShoppingBag)
                ->color('primary'),
            Stat::make('Active Products', (string) $active)
                ->description('Available on POS terminal')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Average Price', 'रू '.number_format((float) $avgPrice, 2))
                ->description('Active catalog avg price')
                ->descriptionIcon(Heroicon::OutlinedCurrencyRupee)
                ->color('info'),
        ];
    }
}
