<?php

namespace Modules\Farms\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Models\FarmShopSetting;
use Modules\Farms\Services\ShopSettingsService;

class FarmShopSettingsPage extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 99;

    protected static ?string $navigationLabel = 'Shop Settings';

    protected string $view = 'farms::filament.pages.farm-shop-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $companyId = Filament::getTenant()?->id ?? auth()->user()?->current_company_id;
        $settings  = $companyId
            ? FarmShopSetting::firstOrNew(['company_id' => $companyId])
            : new FarmShopSetting();

        $this->form->fill([
            'shop_name'                  => $settings->shop_name          ?? 'Alpha Farms',
            'tagline'                    => $settings->tagline             ?? '',
            'primary_color'              => $settings->primary_color       ?? '#15803d',
            'secondary_color'            => $settings->secondary_color     ?? '#166534',
            'phone'                      => $settings->phone               ?? '',
            'whatsapp_number'            => $settings->whatsapp_number     ?? '',
            'email'                      => $settings->email               ?? '',
            'address'                    => $settings->address             ?? '',
            'delivery_fee'               => $settings->delivery_fee        ?? 20.00,
            'free_delivery_above'        => $settings->free_delivery_above ?? null,
            'order_cutoff_time'          => $settings->order_cutoff_time
                ? substr($settings->order_cutoff_time, 0, 5) : '18:00',
            'hero_heading'               => $settings->hero_heading        ?? '',
            'hero_subheading'            => $settings->hero_subheading     ?? '',
            'announcement_bar_active'    => (bool) ($settings->announcement_bar_active    ?? false),
            'announcement_bar_text'      => $settings->announcement_bar_text              ?? '',
            'announcement_bar_starts_at' => $settings->announcement_bar_starts_at?->format('Y-m-d\TH:i'),
            'announcement_bar_ends_at'   => $settings->announcement_bar_ends_at?->format('Y-m-d\TH:i'),
            'popup_active'               => (bool) ($settings->popup_active ?? false),
            'popup_title'                => $settings->popup_title         ?? '',
            'popup_body'                 => $settings->popup_body          ?? '',
            'popup_cta_text'             => $settings->popup_cta_text      ?? '',
            'popup_cta_url'              => $settings->popup_cta_url       ?? '',
            'popup_starts_at'            => $settings->popup_starts_at?->format('Y-m-d\TH:i'),
            'popup_ends_at'              => $settings->popup_ends_at?->format('Y-m-d\TH:i'),
            'meta_title'                 => $settings->meta_title          ?? '',
            'meta_description'           => $settings->meta_description    ?? '',
            'facebook_url'               => $settings->facebook_url        ?? '',
            'instagram_url'              => $settings->instagram_url       ?? '',
            'twitter_url'                => $settings->twitter_url         ?? '',
            'footer_about_text'          => $settings->footer_about_text   ?? '',
            'loyalty_enabled'            => (bool) ($settings->loyalty_enabled         ?? false),
            'loyalty_points_per_ghs'     => $settings->loyalty_points_per_ghs         ?? 1.00,
            'loyalty_points_value_ghs'   => $settings->loyalty_points_value_ghs       ?? 0.0100,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Branding')
                    ->columns(2)
                    ->schema([
                        TextInput::make('shop_name')
                            ->label('Shop Name')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('tagline')
                            ->maxLength(200)
                            ->placeholder('Fresh from the farm to your table'),
                        ColorPicker::make('primary_color')
                            ->label('Primary Colour'),
                        ColorPicker::make('secondary_color')
                            ->label('Secondary Colour'),
                    ]),

                Section::make('Contact Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(30)
                            ->placeholder('+233 XX XXX XXXX'),
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp Number')
                            ->tel()
                            ->maxLength(30)
                            ->placeholder('+233 XX XXX XXXX'),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(100)
                            ->placeholder('orders@yourshop.com'),
                        TextInput::make('address')
                            ->label('Physical Address')
                            ->maxLength(300)
                            ->placeholder('P.O. Box 123, Accra'),
                    ]),

                Section::make('Delivery Settings')
                    ->columns(3)
                    ->schema([
                        TextInput::make('delivery_fee')
                            ->label('Delivery Fee')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('GHS'),
                        TextInput::make('free_delivery_above')
                            ->label('Free Delivery Above')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('GHS')
                            ->placeholder('Leave blank to disable'),
                        TimePicker::make('order_cutoff_time')
                            ->label('Order Cutoff Time')
                            ->seconds(false)
                            ->helperText('Orders after this time process the next business day'),
                    ]),

                Section::make('Homepage Content')
                    ->columns(1)
                    ->schema([
                        TextInput::make('hero_heading')
                            ->label('Hero Heading')
                            ->maxLength(200)
                            ->placeholder('Farm Fresh Produce, Delivered to You'),
                        TextInput::make('hero_subheading')
                            ->label('Hero Sub-heading')
                            ->maxLength(300)
                            ->placeholder('Order by Wednesday, receive by Friday'),
                    ]),

                Section::make('Announcement Bar')
                    ->columns(2)
                    ->schema([
                        Toggle::make('announcement_bar_active')
                            ->label('Active')
                            ->columnSpanFull(),
                        TextInput::make('announcement_bar_text')
                            ->label('Message')
                            ->maxLength(200)
                            ->placeholder('Free delivery this weekend on all orders!')
                            ->columnSpanFull(),
                        DateTimePicker::make('announcement_bar_starts_at')
                            ->label('Show From')
                            ->seconds(false),
                        DateTimePicker::make('announcement_bar_ends_at')
                            ->label('Hide After')
                            ->seconds(false),
                    ]),

                Section::make('Popup / Promotional Modal')
                    ->columns(2)
                    ->schema([
                        Toggle::make('popup_active')
                            ->label('Enable Popup')
                            ->helperText('Shows once per visitor after 1.5 s. Dismissed via localStorage.')
                            ->columnSpanFull(),
                        TextInput::make('popup_title')
                            ->label('Title')
                            ->maxLength(200)
                            ->placeholder('🎉 Weekend Sale — 20% off all orders!')
                            ->columnSpanFull(),
                        Textarea::make('popup_body')
                            ->label('Body Text')
                            ->rows(2)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        TextInput::make('popup_cta_text')
                            ->label('CTA Button Text')
                            ->maxLength(100)
                            ->placeholder('Shop Now'),
                        TextInput::make('popup_cta_url')
                            ->label('CTA Button URL')
                            ->maxLength(500)
                            ->placeholder('/farm-shop'),
                        DateTimePicker::make('popup_starts_at')
                            ->label('Show From')
                            ->seconds(false),
                        DateTimePicker::make('popup_ends_at')
                            ->label('Hide After')
                            ->seconds(false),
                    ]),

                Section::make('Loyalty Program')
                    ->columns(2)
                    ->schema([
                        Toggle::make('loyalty_enabled')
                            ->label('Enable Loyalty Program')
                            ->helperText('Customers earn points on every paid order and can redeem them for discounts')
                            ->columnSpanFull(),
                        TextInput::make('loyalty_points_per_ghs')
                            ->label('Points Earned per GHS Spent')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('e.g. 1.00 = 1 point per GHS 1 spent'),
                        TextInput::make('loyalty_points_value_ghs')
                            ->label('GHS Value per Loyalty Point')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.0001)
                            ->helperText('e.g. 0.0100 = 100 points = GHS 1.00'),
                    ]),

                Section::make('SEO')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(200)
                            ->placeholder('Alpha Farms — Fresh Produce Online'),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Buy fresh farm produce online. Direct from the farm, delivered to your door.'),
                    ]),

                Section::make('Social Media & Footer')
                    ->collapsible()
                    ->collapsed()
                    ->columns(3)
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://facebook.com/...'),
                        TextInput::make('instagram_url')
                            ->label('Instagram URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://instagram.com/...'),
                        TextInput::make('twitter_url')
                            ->label('Twitter/X URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://twitter.com/...'),
                        Textarea::make('footer_about_text')
                            ->label('Footer About Text')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Alpha Farms connects local farmers with customers across Ghana...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $companyId = Filament::getTenant()?->id ?? auth()->user()?->current_company_id;

        if (! $companyId) {
            Notification::make()->title('Could not determine company')->danger()->send();

            return;
        }

        FarmShopSetting::updateOrCreate(
            ['company_id' => $companyId],
            [
                'shop_name'                  => $data['shop_name'],
                'tagline'                    => $data['tagline']             ?: null,
                'primary_color'              => $data['primary_color']       ?: '#15803d',
                'secondary_color'            => $data['secondary_color']     ?: '#166534',
                'phone'                      => $data['phone']               ?: null,
                'whatsapp_number'            => $data['whatsapp_number']     ?: null,
                'email'                      => $data['email']               ?: null,
                'address'                    => $data['address']             ?: null,
                'delivery_fee'               => $data['delivery_fee']        ?: 20.00,
                'free_delivery_above'        => $data['free_delivery_above'] ?: null,
                'order_cutoff_time'          => $data['order_cutoff_time']   ?: null,
                'hero_heading'               => $data['hero_heading']        ?: null,
                'hero_subheading'            => $data['hero_subheading']     ?: null,
                'announcement_bar_active'    => $data['announcement_bar_active'],
                'announcement_bar_text'      => $data['announcement_bar_text']      ?: null,
                'announcement_bar_starts_at' => $data['announcement_bar_starts_at'] ?: null,
                'announcement_bar_ends_at'   => $data['announcement_bar_ends_at']   ?: null,
                'popup_active'               => $data['popup_active'],
                'popup_title'                => $data['popup_title']      ?: null,
                'popup_body'                 => $data['popup_body']       ?: null,
                'popup_cta_text'             => $data['popup_cta_text']   ?: null,
                'popup_cta_url'              => $data['popup_cta_url']    ?: null,
                'popup_starts_at'            => $data['popup_starts_at']  ?: null,
                'popup_ends_at'              => $data['popup_ends_at']    ?: null,
                'meta_title'                 => $data['meta_title']        ?: null,
                'meta_description'           => $data['meta_description']  ?: null,
                'facebook_url'               => $data['facebook_url']      ?: null,
                'instagram_url'              => $data['instagram_url']     ?: null,
                'twitter_url'                => $data['twitter_url']       ?: null,
                'footer_about_text'          => $data['footer_about_text'] ?: null,
                'loyalty_enabled'            => $data['loyalty_enabled'],
                'loyalty_points_per_ghs'     => $data['loyalty_points_per_ghs']   ?: 1.00,
                'loyalty_points_value_ghs'   => $data['loyalty_points_value_ghs'] ?: 0.0100,
            ]
        );

        app(ShopSettingsService::class)->forget($companyId);

        Notification::make()
            ->title('Shop settings saved successfully')
            ->success()
            ->send();
    }
}
