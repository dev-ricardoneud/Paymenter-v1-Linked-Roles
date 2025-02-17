<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRoleCustomPage\Pages;

use Paymenter\Extensions\Others\DiscordLinkedRoles\Admin\Resources\LinkedRoleCustomPage;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomPage extends CreateRecord
{
    protected static string $resource = LinkedRoleCustomPage::class;
}
