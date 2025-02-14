<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRole\Pages;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\LinkedRoleSetting;
use Illuminate\Support\Facades\Http;

class LinkedRole extends Resource
{
    protected static ?string $model = LinkedRoleSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Linked Roles')
                ->tabs([
                    Tabs\Tab::make('General')
                        ->schema([
                            TextInput::make('discordlinkedroles_client_id')
                                ->label('Discord Client ID')
                                ->default('1296581432969265306')
                                ->required(),
                            TextInput::make('discordlinkedroles_client_secret')
                                ->label('Discord Client Secret')
                                ->default('your-client-secret-here')
                                ->required()
                                ->password()
                                ->revealable(),
                            TextInput::make('discordlinkedroles_bot_token')
                                ->label('Discord Bot Token')
                                ->default('your-bot-token-here')
                                ->required()
                                ->password()
                                ->revealable(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('discordlinkedroles_client_id')
                    ->label('Discord Client ID')
                    ->getStateUsing(fn($record) => $record->discordlinkedroles_client_id),
                Tables\Columns\TextColumn::make('discordlinkedroles_client_secret')
                    ->label('Discord Client Secret')
                    ->getStateUsing(fn($record) => 'Hidden for security reasons')
                    ->tooltip('You can only see it in the edit tab, as it is hidden for security reasons.')
            ])
            ->filters([])
            ->actions([
                Action::make('sync_now')
                    ->label('Sync Now With Discord')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function ($record) {
                        $discordClientId = $record->discordlinkedroles_client_id;
                        $discordBotToken = $record->discordlinkedroles_bot_token;
                        
                        if (!$discordClientId || !$discordBotToken) {
                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body('You need to provide a Discord client ID and bot token in the Discord Linked Roles Extension.')
                                ->send();
                            return;
                        }

                        $url = 'https://discord.com/api/v10/applications/' . $discordClientId . '/role-connections/metadata';

                        $response = Http::withHeaders([
                            'Authorization' => 'Bot ' . $discordBotToken,
                        ])->put($url, [
                            [
                                'key' => 'syncedwithpaymenter',
                                'name' => 'Is registered on the website',
                                'description' => 'Is logged in on the website',
                                'type' => 7,
                            ],
                            [
                                'key' => 'activeproducts',
                                'name' => 'Active Products',
                                'description' => 'How many active products the user has',
                                'type' => 2,
                            ]
                        ]);

                        if ($response->failed()) {
                            Notification::make()
                                ->title('Sync Failed')
                                ->danger()
                                ->body('Something went wrong while pushing the linked roles to Discord for Client ID: ' . $discordClientId)
                                ->send();
                            return;
                        }

                        Notification::make()
                            ->title('Sync Successful')
                            ->success()
                            ->body('Linked roles have been pushed to Discord for Client ID: ' . $discordClientId)
                            ->send();
                    }),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\AdminLinkedRoles::route('/'),
            'edit' => Pages\AdminLinkedRolesEdit::route('{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermission('*');
    }
}
