<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources;

use Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRoleCustomPage\Pages;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\CustomPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LinkedRoleCustomPage extends Resource
{
    protected static ?string $model = CustomPage::class;

    protected static ?string $navigationGroup = 'Discord Linked Roles';
    protected static ?string $navigationLabel = 'Custom Pages';
    protected static ?string $navigationIcon = 'heroicon-m-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')->required()->rule('regex:/^[a-z0-9_-]+$/')->rule('not_in:home,callback')->default('demo'),
                Forms\Components\TextInput::make('name')->required()->default('Demo'),
                Forms\Components\TextInput::make('title')->columnSpanFull()->required()->default('You have successfully connected your discord! 🎉'),
                Forms\Components\RichEditor::make('text')->columnSpanFull()->label('Content')->required()->default('🎉 Congratulations! Your Discord account is now successfully linked to your account. You are one step closer to enjoying exclusive roles and perks! 💎'),
                Forms\Components\TextInput::make('text_above_button')->label('Text above the button')->required()->default('Click the button below to go back to Discord and claim your role. ⬇️'),
                Forms\Components\TextInput::make('button_text')->label('Button Text')->required()->default('Go to Discord 🚀'),
                Forms\Components\TextInput::make('button_link')->columnSpanFull()->label('Button URL')->required()->default('discord://discord.com/channels/@me'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key'),
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomPage::route('/'),
            'create' => Pages\CreateCustomPage::route('/create'),
            'edit' => Pages\EditCustomPage::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermission('*');
    }
}
