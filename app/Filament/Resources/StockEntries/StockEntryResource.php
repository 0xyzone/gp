<?php

namespace App\Filament\Resources\StockEntries;

use App\Filament\Resources\StockEntries\Pages\ListStockEntries;
use App\Filament\Resources\StockEntries\Schemas\StockEntryForm;
use App\Filament\Resources\StockEntries\Tables\StockEntriesTable;
use App\Filament\Resources\StockEntries\Widgets\StockOverviewWidget;
use App\Models\StockEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockEntryResource extends Resource
{
    protected static ?string $model = StockEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?string $navigationLabel = 'Stock Management';

    protected static ?string $modelLabel = 'Stock Entry';

    protected static ?string $pluralModelLabel = 'Stock Entries';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return StockEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockEntriesTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            StockOverviewWidget::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockEntries::route('/'),
        ];
    }
}
