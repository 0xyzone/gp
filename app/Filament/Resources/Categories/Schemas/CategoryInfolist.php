<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Overview')
                    ->icon('heroicon-o-tag')
                    ->description('Classification details and stall visibility')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('CATEGORY NAME')
                                    ->weight(FontWeight::ExtraBold)
                                    ->size('lg'),

                                TextEntry::make('slug')
                                    ->label('URL SLUG')
                                    ->badge()
                                    ->color('gray'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('sort_order')
                                    ->label('DISPLAY ORDER')
                                    ->weight(FontWeight::Bold),

                                IconEntry::make('is_active')
                                    ->label('POS VISIBILITY')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
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
            ]);
    }
}
