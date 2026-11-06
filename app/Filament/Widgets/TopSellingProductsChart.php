<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopSellingProductsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Top Selling Stall Products (Units Sold)';

    protected ?string $pollingInterval = '15s';

    protected function getData(): array
    {
        // Query genuine sales by product from order_items table
        $sales = OrderItem::query()
            ->select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_sales'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(8)
            ->get();

        $labels = [];
        $data = [];

        foreach ($sales as $item) {
            $labels[] = $item->product_name;
            $data[] = (int) $item->total_qty;
        }

        // Graceful fallback if no orders exist yet
        if (empty($labels)) {
            $labels = ['No Sales Recorded Yet'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => $data,
                    'backgroundColor' => [
                        '#881337', // Deep Burgundy
                        '#9f1239', // Rose Burgundy
                        '#be123c', // Bright Burgundy
                        '#e11d48', // Vibrant Rose
                        '#fb7185', // Rose Accent
                        '#fda4af', // Light Rose
                        '#d97706', // Amber Accent
                        '#b45309', // Dark Amber
                    ],
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
