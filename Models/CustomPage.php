<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Models;

use Illuminate\Database\Eloquent\Model;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\CustomPage;

class CustomPage extends Model
{
    protected $table = 'linked_role_settings_custom_pages';
    protected $fillable = [
        'key',
        'name',
        'title',
        'text',
        'text_above_button',
        'button_text',
        'button_link',
    ];
    public $timestamps = false;

    public static function getSettings()
    {
        return self::firstOrCreate([]);
    }
}
