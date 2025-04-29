<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRole\Pages;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\LinkedRoleSetting;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\CustomPage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Http;


class LinkedRole extends Resource
{
    protected static ?string $model = LinkedRoleSetting::class;
    protected static ?string $navigationGroup = 'Discord Linked Roles';
    protected static ?string $navigationLabel = 'Linked Roles';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        $pages = CustomPage::all(['key', 'name']);
        $pageOptions = $pages->filter(function ($page) {
            return !empty($page->name);
        })->pluck('name', 'key')->toArray();

        return $form->schema([
            Tabs::make('Linked Roles')
                ->id('linked-roles')
                ->persistTab()
                ->tabs([
                    Tabs\Tab::make('General')
                        ->schema([
                            TextInput::make('discordlinkedroles_bot_name')
                                ->label('Bot Name')
                                ->placeholder('Give the bot a recognizable name for all settings.')
                                ->required(),
                        ]),
                        Tabs\Tab::make('Bot Settings')
                        ->schema([
                            TextInput::make('discordlinkedroles_client_id')
                                ->label('Discord Client ID')
                                ->placeholder('1296581432969265306')
                                ->required(),
                            TextInput::make('discordlinkedroles_client_secret')
                                ->label('Discord Client Secret')
                                ->placeholder('your-client-secret-here')
                                ->required()
                                ->password()
                                ->revealable(),
                            TextInput::make('discordlinkedroles_bot_token')
                                ->label('Discord Bot Token')
                                ->placeholder('your-bot-token-here')
                                ->required()
                                ->password()
                                ->revealable(),
                        ]),
                        Tabs\Tab::make('Sync Settings')
                        ->schema([
                            TextInput::make('syncedwithpaymenter')
                                ->label('Is registered on the website')
                                ->placeholder('Enter here the name that appears in Discord as a requirement for the role')
                                ->default('Is registered on the website')
                                ->required(),
                            Textarea::make('syncedwithpaymenter_description')
                                ->label('Description')
                                ->placeholder('Enter here the description that appears in Discord as a requirement for the role')
                                ->default('Is logged in on the website')
                                ->required(),
                            TextInput::make('activeproducts')
                                ->label('Active Products')
                                ->placeholder('Enter here the name that appears in Discord as a requirement for the role')
                                ->default('Active Products')
                                ->required(),
                            Textarea::make('activeproducts_description')
                                ->label('Description')
                                ->placeholder('Enter here the description that appears in Discord as a requirement for the role')
                                ->default('How many active products the user has')
                                ->required()
                        ]),
                        Tabs\Tab::make('Api Settings')
                        ->schema([
                            Select::make('api_url')
                                ->label('Api URL')
                                ->options([
                                    'discord.com' => 'Discord.com',
                                ])
                                ->default('discord.com')
                                ->searchable()                            
                                ->required(),
                            Select::make('api_url_version')
                                ->label('Api version')
                                ->options([
                                    'v10' => 'v10 (Available)',
                                    'v9' => 'v9 (Available)',
                                    'v8' => 'v8 (Deprecated)',
                                    'v7' => 'v7 (Deprecated)',
                                    'v6' => 'v6 (Deprecated - Default)',
                                ])
                                ->default('v6')
                                ->searchable()
                                ->required()
                        ]),
                        Tabs\Tab::make('Route Settings')
                        ->schema([
                            TextInput::make('linkedroles_url')
                                ->label('Linked Roles Verification URL')
                                ->placeholder('Enter the route for the linked roles (e.g., /linkedroles)')
                                ->default('/linkedroles')
                                ->required()
                                ->rules('regex:/^\/[a-zA-Z\/]*$/'),
                            TextInput::make('linkedroles_callback_url')
                                ->label('Linked Roles Callback URL')
                                ->placeholder('Enter the route for the callback (e.g., /linkedroles/callback)')
                                ->default('/linkedroles/callback')
                                ->required()
                                ->rules('regex:/^\/[a-zA-Z\/]*$/'),
                            Select::make('callback_redirect_page')
                                ->label('Landing Page')
                                ->options([
                                    'Basic Pages' => [
                                        'home' => 'Home Page',
                                        'callback' => 'Callback Page',
                                    ],
                                    'Custom Pages' => array_merge($pageOptions, [
                                    ]),
                                ])
                                ->default('callback')
                                ->searchable()
                                ->required(),
                            TextInput::make('success_route')
                                ->label('Success Route (Custom Page Only)')
                                ->placeholder('Enter the route for the success (e.g., /linkedroles/success)')
                                ->nullable()
                                ->rules('regex:/^\/[a-zA-Z\/]*$/')
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('discordlinkedroles_bot_name')
                    ->label('Bot Name')
                    ->getStateUsing(fn($record) => $record->discordlinkedroles_bot_name)
                    ->searchable(),
                Tables\Columns\TextColumn::make('discordlinkedroles_client_id')
                    ->label('Discord Client ID')
                    ->getStateUsing(fn($record) => $record->discordlinkedroles_client_id)
                    ->searchable(),
                Tables\Columns\TextColumn::make('discordlinkedroles_client_secret')
                    ->label('Discord Client Secret')
                    ->getStateUsing(fn($record) => 'Hidden for security reasons')
                    ->tooltip('You can only see it in the edit tab, as it is hidden for security reasons.')
                    ->searchable()
            ])
            ->filters([])
            ->actions([
                Action::make('sync_now')
                    ->label('Sync Now')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function ($record) {
                        $discordBotName = $record->discordlinkedroles_bot_name;
                        $discordClientId = $record->discordlinkedroles_client_id;
                        $discordBotToken = $record->discordlinkedroles_bot_token;
                        $syncedwithpaymenter = $record->syncedwithpaymenter;
                        $syncedwithpaymenter_description = $record->syncedwithpaymenter_description;
                        $activeproducts = $record->activeproducts;
                        $activeproducts_description = $record->activeproducts_description;
                        $ApiURL = $record->api_url;
                        $ApiURLVersion = $record->api_url_version;
                        
                        if (!$discordClientId || !$discordBotToken || !$ApiURL || !$ApiURLVersion) {
                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body('You need to provide the Discord Client ID, bot token, API URL, and API version in the Discord Linked Roles Extension.')
                                ->send();
                            return;
                        }

                        $url = 'https://' . $ApiURL . '/api/' . $ApiURLVersion . '/applications/' . $discordClientId . '/role-connections/metadata';

                        $response = Http::withHeaders([
                            'Authorization' => 'Bot ' . $discordBotToken,
                        ])->put($url, [
                            [
                                'key' => 'syncedwithpaymenter',
                                'name' => $syncedwithpaymenter,
                                'description' => $syncedwithpaymenter_description,
                                'type' => 7,
                            ],
                            [
                                'key' => 'activeproducts',
                                'name' => $activeproducts,
                                'description' => $activeproducts_description,
                                'type' => 2,
                            ]
                        ]);

                        if ($response->failed()) {
                            Notification::make()
                                ->title('Sync Failed')
                                ->danger()
                                ->body('Something went wrong while pushing the linked roles to Discord for: ' . $discordBotName)
                                ->send();
                            return;
                        }

                        Notification::make()
                            ->title('Sync Successful')
                            ->success()
                            ->body('Linked roles have been pushed to Discord for: ' . $discordBotName)
                            ->send();
                    }),
                    Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])
            ]);
    }

    public static function canAccess(): bool
    {
       $user = auth()->user();
       return $user && $user->hasPermission('*');
    }

    public static function getPages(): array
    {
      try {
        $bots = LinkedRoleSetting::all();
    } catch (\Illuminate\Database\QueryException $e) {
        $bots = collect();
    }

    if ($bots->isEmpty()) {
        return [
            'index' => Pages\AdminLinkedRoles::route('/'),
            'create' => Pages\AdminLinkedRolesCreate::route('create/settings'),
            'edit' => Pages\AdminLinkedRolesEdit::route('{record}/settings'),
        ];
    }

    $pages = [
        'index' => Pages\AdminLinkedRoles::route('/'),
        'create' => Pages\AdminLinkedRolesCreate::route('create/settings'),
        'edit' => Pages\AdminLinkedRolesEdit::route('{record}/settings'),
    ];

    return $pages;
  }
}
