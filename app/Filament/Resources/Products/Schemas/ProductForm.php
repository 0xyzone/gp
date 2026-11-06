<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Resources\Categories\Schemas\CategoryForm;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('General Information')
                            ->description('Basic product name, category classification and pricing')
                            ->icon('heroicon-o-shopping-bag')
                            ->columnSpan(2)
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Product Name')
                                            ->required()
                                            ->maxLength(150),
                                        TextInput::make('code')
                                            ->label('Item Code / SKU')
                                            ->placeholder('e.g. GP-TEA-01')
                                            ->maxLength(50),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('category_id')
                                            ->label('Category')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionModalHeading('Add New Category')
                                            ->createOptionForm(function (Schema $categorySchema): array {
                                                return CategoryForm::configure($categorySchema)->getComponents();
                                            })
                                            ->helperText('Select an existing category or create a new one instantly'),
                                        TextInput::make('price')
                                            ->label('Price (NPR)')
                                            ->required()
                                            ->numeric()
                                            ->prefix('रू')
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->placeholder('0.00'),
                                    ]),
                                Textarea::make('description')
                                    ->label('Description / Notes')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('Short product description for stall catalog and customer details...'),
                            ]),

                        Section::make('Media & Visibility')
                            ->description('Product visual representation & order')
                            ->icon('heroicon-o-photo')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Product Image')
                                    ->image()
                                    ->directory('products')
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('600')
                                    ->imageResizeTargetHeight('600'),
                                Toggle::make('is_active')
                                    ->label('Active on POS')
                                    ->default(true)
                                    ->helperText('When active, cashiers can tap to add to cart')
                                    ->inline(false),
                                TextInput::make('sort_order')
                                    ->label('Display Order')
                                    ->numeric()
                                    ->helperText('Leave empty or 0 to auto-assign')
                                    ->placeholder('Auto-assigned'),
                            ]),
                    ]),
            ]);
    }
}
