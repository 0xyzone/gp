<?php

namespace App\Filament\Resources\StockEntries\Widgets;

use App\Models\Product;
use App\Models\StockEntry;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalInStall = Product::where('track_stock', true)->sum('stock_quantity');
        $lowStockCount = Product::where('track_stock', true)
            ->where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
        $outOfStockCount = Product::where('track_stock', true)
            ->where('stock_quantity', '<=', 0)
            ->count();
        $totalEntries = StockEntry::count();

        return [
            Stat::make('Total Units in Stall', number_format($totalInStall).' Units')
                ->description('Combined tracked physical inventory')
                ->descriptionIcon(Heroicon::OutlinedCube)
                ->color('primary'),

            Stat::make('Low Stock Items', (string) $lowStockCount)
                ->description($lowStockCount > 0 ? 'Requires attention / restock' : 'All stock levels healthy')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color($lowStockCount > 0 ? 'warning' : 'success'),

            Stat::make('Out of Stock', (string) $outOfStockCount)
                ->description($outOfStockCount > 0 ? 'Products unavailable in POS' : 'Zero depleted items')
                ->descriptionIcon(Heroicon::OutlinedXCircle)
                ->color($outOfStockCount > 0 ? 'danger' : 'success'),

            Stat::make('Total Logged Entries', (string) $totalEntries)
                ->description('Inward, outward & adjustments')
                ->descriptionIcon(Heroicon::OutlinedClipboardDocumentList)
                ->color('gray'),
        ];
    }
}
