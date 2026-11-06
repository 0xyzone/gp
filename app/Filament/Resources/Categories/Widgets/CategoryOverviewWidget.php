<?php

namespace App\Filament\Resources\Categories\Widgets;

use App\Models\Category;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoryOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total = Category::count();
        $active = Category::where('is_active', true)->count();
        $inactive = $total - $active;

        return [
            Stat::make('Total Categories', (string) $total)
                ->description('All catalog groups')
                ->descriptionIcon(Heroicon::OutlinedTag)
                ->color('primary'),
            Stat::make('Active Categories', (string) $active)
                ->description('Live on POS and catalog')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Inactive Categories', (string) $inactive)
                ->description('Hidden from catalog')
                ->descriptionIcon(Heroicon::OutlinedEyeSlash)
                ->color('gray'),
        ];
    }
}
