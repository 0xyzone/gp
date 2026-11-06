<?php

namespace App\Filament\Resources\StockEntries\Pages;

use App\Filament\Resources\StockEntries\StockEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListStockEntries extends ListRecords
{
    protected static string $resource = StockEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Stock Entry')
                ->icon('heroicon-o-plus')
                ->slideOver()
                ->modalWidth(Width::Large),
        ];
    }
}
