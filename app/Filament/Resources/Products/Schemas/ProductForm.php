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
            ->columns(12)
            ->components([
                Section::make('General Information')
                    ->description('Basic product name, category classification and pricing')
                    ->icon('heroicon-o-shopping-bag')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 7,
                        'xl' => 8,
                    ])
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
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
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('price')
                                    ->label('Actual / Regular Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('रू')
                                    ->minValue(0.01)
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->placeholder('0.00')
                                    ->helperText('Standard retail price'),
                                TextInput::make('offer_price')
                                    ->label('Offer / Stall Price')
                                    ->numeric()
                                    ->prefix('रू')
                                    ->minValue(0.01)
                                    ->step(0.01)
                                    ->lt('price')
                                    ->validationMessages([
                                        'lt' => 'The offer price must be strictly less than the actual price.',
                                    ])
                                    ->placeholder('e.g. Discounted stall price')
                                    ->helperText('Must be strictly less than the actual price'),
                            ]),
                        Section::make('Inventory & Stock Management')
                            ->description('Track physical stall inventory, available units, and low stock warnings')
                            ->icon('heroicon-o-cube')
                            ->compact()
                            ->schema([
                                Toggle::make('track_stock')
                                    ->label('Enable Stock Tracking')
                                    ->default(true)
                                    ->live()
                                    ->helperText('When enabled, stock will decrease as sales are made in POS'),
                                Grid::make(2)
                                    ->visible(fn ($get) => (bool) $get('track_stock'))
                                    ->schema([
                                        TextInput::make('stock_quantity')
                                            ->label('Current Stock Units')
                                            ->numeric()
                                            ->required()
                                            ->default(50)
                                            ->minValue(0)
                                            ->step(1)
                                            ->suffix('Units')
                                            ->helperText('Available inventory at stall'),
                                        TextInput::make('low_stock_threshold')
                                            ->label('Low Stock Alert Level')
                                            ->numeric()
                                            ->required()
                                            ->default(10)
                                            ->minValue(1)
                                            ->step(1)
                                            ->suffix('Units')
                                            ->helperText('Triggers warning badge when stock drops to or below this level'),
                                    ]),
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
                    ->columnSpan([
                        'default' => 12,
                        'md' => 5,
                        'xl' => 4,
                    ])
                    ->schema([
                        FileUpload::make('image')
                            ->label('Product Image')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
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
            ]);
    }
}
