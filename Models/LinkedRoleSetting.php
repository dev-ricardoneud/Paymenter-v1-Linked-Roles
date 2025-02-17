<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Models;

use Illuminate\Database\Eloquent\Model;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\LinkedRole;

class LinkedRoleSetting extends Model
{
    protected $table = 'linked_role_settings';
    protected $fillable = [
        'discordlinkedroles_bot_name',
        'discordlinkedroles_client_id',
        'discordlinkedroles_client_secret',
        'discordlinkedroles_bot_token',
        'syncedwithpaymenter',
        'syncedwithpaymenter_description',
        'activeproducts',
        'activeproducts_description',
        'api_url',
        'api_url_version',
        'linkedroles_url',
        'linkedroles_callback_url',
        'callback_redirect_page',
        'success_route',
    ];
    public $timestamps = false;

    public static function getSettings()
    {
        return self::firstOrCreate([]);
    }
}
