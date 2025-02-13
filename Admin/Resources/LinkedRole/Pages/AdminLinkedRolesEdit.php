<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRole\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRole;

class AdminLinkedRolesEdit extends EditRecord
{
    protected static string $resource = LinkedRole::class;
}
