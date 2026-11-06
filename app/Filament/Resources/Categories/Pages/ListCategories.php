<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Categories\Widgets\CategoryOverviewWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->modalWidth(Width::FiveExtraLarge)
                ->extraModalWindowAttributes(['class' => 'fi-fixed-positioning-context']),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CategoryOverviewWidget::class,
        ];
    }
}
