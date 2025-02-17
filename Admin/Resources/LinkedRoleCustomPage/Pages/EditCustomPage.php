<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRoleCustomPage\Pages;

use Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRoleCustomPage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomPage extends EditRecord
{
    protected static string $resource = LinkedRoleCustomPage::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
