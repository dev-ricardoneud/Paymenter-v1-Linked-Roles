<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRole\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRole;

class AdminLinkedRoles extends ListRecords
{
    protected static string $resource = LinkedRole::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
