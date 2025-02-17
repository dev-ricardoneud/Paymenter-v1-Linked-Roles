<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles;

use App\Classes\Extension\Extension;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\LinkedRoleSetting;

class DiscordLinkedRoles extends Extension
{
    public function getConfig($values = [])
    {
        try {
            $bots = LinkedRoleSetting::all(['id', 'discordlinkedroles_bot_name']);
            $botOptions = $bots->filter(function ($bot) {
                return !empty($bot->discordlinkedroles_bot_name);
            })->pluck('discordlinkedroles_bot_name', 'id')->toArray();
            
            $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
            $discordBot = LinkedRoleSetting::where('id', $selectedBotId)->first();
            $discordBotName = $discordBot ? $discordBot->discordlinkedroles_bot_name : 'No bot';
            
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('Discord Linked Roles Extension, originally created by Corwin, the owner of Paymenter, and later adapted by Ricardo Neud.'),
                ],
                [
                    'name' => 'Version Check',
                    'type' => 'placeholder',
                    'label' => new HtmlString($this->getVersion()),
                ],
                [
                    'name' => 'discord_bot_id',
                    'type' => 'select',
                    'label' => 'Select a Discord Bot',
                    'options' => $botOptions,
                    'description' => 'Linked Roles connected with ' . $discordBotName,
                    'value' => $selectedBotId,
                    'disabled' => false,
                    'live' => true,
                ],
                [
                    'name' => 'Discord Bot Connections',
                    'type' => 'placeholder',
                    'label' => new HtmlString($this->getDiscordBotConnections($selectedBotId)),
                ],
            ];
        } catch (\Exception $e) {
            $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
            $discordBot = LinkedRoleSetting::where('id', $selectedBotId)->first();
            $discordBotName = $discordBot ? $discordBot->discordlinkedroles_bot_name : 'No bot selected';
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('Discord Linked Roles Extension, originally created by Corwin, the owner of Paymenter, and later adapted by Ricardo Neud.'),
                ],
                [
                    'name' => 'Version Check',
                    'type' => 'placeholder',
                    'label' => new HtmlString($this->getVersion()),
                ],
                [
                    'name' => 'discord_bot_id',
                    'type' => 'select',
                    'label' => 'Select a Discord Bot',
                    'options' => [
                        '' . $selectedBotId . '' => 'No Bots Found',
                    ],
                    'description' => 'Linked Roles connected with ' . $discordBotName,
                    'value' => $selectedBotId,
                    'disabled' => true,
                    'live' => true,
                ],
            ];
        }
    }

    public function enabled()
    {
        Artisan::call('migrate', ['--path' => [
            'extensions/Others/DiscordLinkedRoles/database/migrations/2025_02_13_122225_create_linkedroles_table.php',
            'extensions/Others/DiscordLinkedRoles/database/migrations/2025_02_13_122225_create_linkedroles_custom_pages_table.php'
        ]]);
    }

    public function boot()
    {
        require __DIR__ . '/routes/web.php';
    }

    public function getVersion()
    {
        try {
            $response = Http::get("https://api.github.com/repos/dev-ricardoneud/Paymenter-v1-Linked-Roles/releases/latest");
            $latestRelease = $response->json();
            if (!is_array($latestRelease) || !isset($latestRelease['tag_name'])) {
                return 'Could not check for updates at this time.';
            }
            $latestVersion = $latestRelease['tag_name'];
            $currentVersion = 'v1.0.5';
            if (version_compare($currentVersion, $latestVersion, '>')) {
                return 'The version ' . $currentVersion . ' does not exist. If this is the main branch, it may contain errors. Please downgrade to the latest stable version (' . $latestVersion . ') to avoid potential issues.';
            } elseif ($currentVersion === $latestVersion) {
                return 'You are using the latest version (' . $latestVersion . ').';
            } else {
                return 'You are using version ' . $currentVersion . ', but version ' . $latestVersion . ' is available. Please update!';
            }
        } catch (\Exception $e) {
            return 'Could not check for updates at this time.';
        }
    }

    public function getDiscordBotConnections($botId = null)
    {
        try {
            if (!$botId) {
                return 'No bot selected.';
            }
            $bot = LinkedRoleSetting::where('id', $botId)->first();
            if (!$bot) {
                return 'Invalid bot selection.';
            }
            $response = Http::withHeaders([
                'Authorization' => 'Bot ' . $bot->discordlinkedroles_bot_token,
            ])->get('https://' . $bot->api_url . '/api/' . $bot->api_url_version . '/applications/@me');
            if ($response->failed()) {
                return 'Could not retrieve bot connection data at this time.';
            }
            $data = $response->json();
            if (!is_array($data) || !isset($data['approximate_user_install_count']) || !isset($data['name'])) {
                return 'Could not retrieve bot connection data at this time.';
            }
            return 'The bot ' . $data['name'] . ' is authorized by approximately ' . $data['approximate_user_install_count'] . ' users.';
        } catch (\Exception $e) {
            return 'Could not retrieve bot connection data at this time.';
        }
    }
}
