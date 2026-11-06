<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                // HERO / MEDIA CARD (Left Column)
                Section::make()
                    ->columnSpan([
                        'default' => 12,
                        'md' => 5,
                    ])
                    ->schema([
                        ImageEntry::make('image')
                            ->hiddenLabel()
                            ->disk('public')
                            ->defaultImageUrl(url('/images/placeholder-product.svg'))
                            ->extraImgAttributes([
                                'class' => 'w-full aspect-square object-contain rounded-2xl bg-white/40 dark:bg-black/40 p-4 border border-rose-200/40 dark:border-rose-900/40 backdrop-blur-md shadow-[inset_0_2px_6px_rgba(0,0,0,0.15)]',
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('price')
                                    ->label('REGULAR PRICE')
                                    ->formatStateUsing(fn ($state) => 'रू '.number_format((float) $state, 2))
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('offer_price')
                                    ->label('STALL OFFER PRICE')
                                    ->formatStateUsing(fn ($state) => $state ? 'रू '.number_format((float) $state, 2) : 'No Offer')
                                    ->size('lg')
                                    ->weight(FontWeight::Black)
                                    ->color('primary'),
                            ]),

                        IconEntry::make('is_active')
                            ->label('POS AVAILABILITY STATUS')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ]),

                // PRODUCT DETAILS & METADATA (Right Column)
                Group::make()
                    ->columnSpan([
                        'default' => 12,
                        'md' => 7,
                    ])
                    ->schema([
                        Section::make('Product Specifications')
                            ->icon('heroicon-o-shopping-bag')
                            ->description('Catalog identity and categorisation')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('PRODUCT NAME')
                                            ->weight(FontWeight::ExtraBold)
                                            ->size('md'),

                                        TextEntry::make('code')
                                            ->label('ITEM CODE / SKU')
                                            ->badge()
                                            ->color('gray')
                                            ->placeholder('None Assigned'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('category.name')
                                            ->label('CATEGORY')
                                            ->badge()
                                            ->color('primary')
                                            ->placeholder('Uncategorized'),

                                        TextEntry::make('sort_order')
                                            ->label('CATALOG DISPLAY ORDER')
                                            ->weight(FontWeight::Bold)
                                            ->placeholder('0'),
                                    ]),

                                TextEntry::make('description')
                                    ->label('DESCRIPTION & NOTES')
                                    ->placeholder('No custom notes or description provided for this product.')
                                    ->columnSpanFull()
                                    ->prose(),
                            ]),

                        Section::make('Audit Information')
                            ->icon('heroicon-o-clock')
                            ->collapsed()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('CREATED ON')
                                            ->dateTime('M d, Y • h:i A')
                                            ->placeholder('-'),

                                        TextEntry::make('updated_at')
                                            ->label('LAST UPDATED')
                                            ->dateTime('M d, Y • h:i A')
                                            ->placeholder('-'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
