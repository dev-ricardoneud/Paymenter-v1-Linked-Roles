<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedRoleSetting extends Model
{
    protected $table = 'linked_role_settings';
    protected $fillable = [
        'discordlinkedroles_client_id',
        'discordlinkedroles_client_secret',
        'discordlinkedroles_bot_token',
    ];
    public $timestamps = false;

    public static function getSettings()
    {
        return self::firstOrCreate([]);
    }
}
