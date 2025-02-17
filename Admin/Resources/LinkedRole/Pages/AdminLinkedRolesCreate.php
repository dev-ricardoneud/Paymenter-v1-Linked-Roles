<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRole\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRole;

class AdminLinkedRolesCreate extends CreateRecord
{
    protected static string $resource = LinkedRole::class;
}
