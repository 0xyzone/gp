<?php

namespace App\Filament\Pages;

use App\Events\SettingsUpdated;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

    protected static ?string $title = 'Stall & Display Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Expo POS';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // General Stall Details
            'stall_name' => Setting::get('stall_name', 'घासा • Ghasa Products'),
            'currency_symbol' => Setting::get('currency_symbol', 'रू'),
            'contact_phone' => Setting::get('contact_phone', '+977-9800000000'),
            'account_holder' => Setting::get('account_holder', 'Ghasa Products Nepal'),
            'upi_bank_name' => Setting::get('upi_bank_name', 'Siddhartha Bank'),
            'payment_qr_image' => Setting::get('payment_qr_image', 'images/siddhartha-bank-qr.png'),

            // Public Screen Content Customization
            'display_hero_title' => Setting::get('display_hero_title', 'स्वादिलो घरको अचार र क्रिस्पी पानी पुरी!'),
            'display_hero_desc' => Setting::get('display_hero_desc', 'Taste our signature Akabare, Gundruk, Buff & Chicken Pickles, or enjoy freshly served hygienic Pani Puri with tingling jaljeera and sweet lapsi chutney.'),
            'display_badge_text' => Setting::get('display_badge_text', 'Authentic Nepali Achar & Pani Puri Stall'),

            // Modular Public Screen Visibility Toggles
            'show_display_header' => (bool) Setting::get('show_display_header', '1'),
            'show_hero_banner' => (bool) Setting::get('show_hero_banner', '1'),
            'show_qr_pill' => (bool) Setting::get('show_qr_pill', '1'),
            'show_carousel_dots' => (bool) Setting::get('show_carousel_dots', '1'),
            'show_display_footer' => (bool) Setting::get('show_display_footer', '1'),
            'show_product_description' => (bool) Setting::get('show_product_description', '0'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Grid::make(3)
                    ->schema([
                        // Column 1 & 2: Public Screen Content & Modular System
                        Section::make('Public Display Content & Modular Visibility')
                            ->description('Control texts and toggle individual elements on the 1920x1080 public customer screen.')
                            ->icon('heroicon-o-tv')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('display_hero_title')
                                    ->label('Hero Headline Title')
                                    ->required()
                                    ->maxLength(120),

                                Textarea::make('display_hero_desc')
                                    ->label('Hero Description')
                                    ->rows(3)
                                    ->required(),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('display_badge_text')
                                            ->label('Header Subtitle / Tagline')
                                            ->placeholder('e.g. Authentic Nepali Achar & Pani Puri Stall'),
                                        TextInput::make('contact_phone')
                                            ->label('Help / Contact Phone')
                                            ->placeholder('+977-98...'),
                                    ]),

                                Section::make('Modular Display Toggles')
                                    ->description('Turn individual modules on or off on the public screen')
                                    ->compact()
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('show_display_header')
                                                    ->label('Header Bar')
                                                    ->default(true),
                                                Toggle::make('show_hero_banner')
                                                    ->label('Hero Promo Banner')
                                                    ->default(true),
                                                Toggle::make('show_qr_pill')
                                                    ->label('Quick QR Pill')
                                                    ->default(true),
                                                Toggle::make('show_carousel_dots')
                                                    ->label('Progress Dots')
                                                    ->default(true),
                                                Toggle::make('show_display_footer')
                                                    ->label('Footer Bar')
                                                    ->default(true),
                                                Toggle::make('show_product_description')
                                                    ->label('Product Description')
                                                    ->helperText('Off for ultra-clean cards')
                                                    ->default(false),
                                            ]),
                                    ]),
                            ]),

                        // Column 3: Payment & QR Settings
                        Section::make('Payment & QR Artwork')
                            ->description('Brand identity, payment receiver & QR artwork.')
                            ->icon('heroicon-o-qr-code')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('stall_name')
                                    ->label('Stall Name')
                                    ->required(),
                                TextInput::make('currency_symbol')
                                    ->label('Currency')
                                    ->default('रू')
                                    ->required(),
                                TextInput::make('upi_bank_name')
                                    ->label('Bank Name')
                                    ->default('Siddhartha Bank'),
                                TextInput::make('account_holder')
                                    ->label('Account Payee')
                                    ->default('Ghasa Products Nepal'),
                                FileUpload::make('payment_qr_image')
                                    ->label('Custom QR Artwork')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings')
                                    ->visibility('public')
                                    ->helperText('Optional: Leaves current Siddhartha Bank QR if empty.'),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save All Settings')
                ->icon('heroicon-o-check')
                ->action('submit'),
        ];
    }

    public function submit(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            if ($key === 'payment_qr_image' && empty($value)) {
                continue;
            }

            if (is_bool($value)) {
                Setting::set($key, $value ? '1' : '0');
            } else {
                Setting::set($key, (string) $value);
            }
        }

        Notification::make()
            ->title('Settings Updated Successfully')
            ->body('Public display modular elements and texts have been updated.')
            ->success()
            ->send();

        // Broadcast settings update instantly over websockets to all connected screens
        $allSettings = Setting::allKeyed();
        if (isset($allSettings['payment_qr_image']) && $allSettings['payment_qr_image']) {
            $allSettings['payment_qr_image_url'] = str_starts_with($allSettings['payment_qr_image'], 'http')
                ? $allSettings['payment_qr_image']
                : (str_starts_with($allSettings['payment_qr_image'], 'images/')
                    ? asset($allSettings['payment_qr_image'])
                    : asset('storage/'.$allSettings['payment_qr_image']));
        }
        broadcast(new SettingsUpdated($allSettings));
    }
}
