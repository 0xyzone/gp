<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AppSettings extends Page
{
    protected string $view = 'filament.pages.app-settings';

    protected static ?string $title = 'Stall & Expo Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Expo POS';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'payment_qr_image' => Setting::get('payment_qr_image', 'images/siddhartha-bank-qr.png'),
            'stall_name' => Setting::get('stall_name', 'Ghasa Products - Expo Stall'),
            'currency_symbol' => Setting::get('currency_symbol', 'रू'),
            'upi_bank_name' => Setting::get('upi_bank_name', 'Siddhartha Bank'),
            'account_holder' => Setting::get('account_holder', 'Ghasa Products Nepal'),
            'contact_phone' => Setting::get('contact_phone', '+977-9800000000'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Customer Screen Payment QR')
                            ->description('The QR payment design displayed on the second monitor when taking orders or selecting QR pay.')
                            ->icon('heroicon-o-qr-code')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('payment_qr_image')
                                    ->label('QR Payment Artwork')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings')
                                    ->visibility('public')
                                    ->helperText('Upload a new QR design banner anytime (e.g. Siddhartha Bank, Fonepay, eSewa).')
                                    ->required(),
                            ]),

                        Section::make('Stall & Currency Details')
                            ->description('Brand identity and currency formatting across POS and customer display.')
                            ->icon('heroicon-o-building-storefront')
                            ->columnSpan(2)
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('stall_name')
                                            ->label('Stall / Exhibition Title')
                                            ->required(),
                                        TextInput::make('currency_symbol')
                                            ->label('Currency Symbol')
                                            ->default('रू')
                                            ->required()
                                            ->helperText('e.g. रू or NPR'),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('upi_bank_name')
                                            ->label('Bank / Payment Provider Name')
                                            ->placeholder('Siddhartha Bank'),
                                        TextInput::make('account_holder')
                                            ->label('Account Holder / Payee Name')
                                            ->placeholder('Ghasa Products'),
                                    ]),
                                TextInput::make('contact_phone')
                                    ->label('Support / Stall Helpline')
                                    ->placeholder('+977-98...'),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save App Settings')
                ->icon('heroicon-o-check')
                ->action('submit'),
        ];
    }

    public function submit(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            Setting::set($key, (string) $value);
        }

        Notification::make()
            ->title('Settings Saved Successfully')
            ->body('App settings and customer display QR have been updated.')
            ->success()
            ->send();
    }
}
