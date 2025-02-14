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
    const GITHUB_REPO = 'dev-ricardoneud/Paymenter-v1-Linked-Roles';
    const DISCORD_API_URL = 'https://discord.com/api/v10';

    public function getConfig($values = [])
    {
        try {
            $bots = LinkedRoleSetting::all(['id', 'discordlinkedroles_client_id']);
            $botOptions = $bots->filter(function ($bot) {
                return !empty($bot->discordlinkedroles_client_id);
            })->pluck('discordlinkedroles_client_id', 'id')->toArray();
            
            $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
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
                    'description' => new HtmlString($this->getDiscordBotName($selectedBotId)),
                    'value' => $selectedBotId,
                ],
                [
                    'name' => 'Discord Bot Connections',
                    'type' => 'placeholder',
                    'label' => new HtmlString($this->getDiscordBotConnections($selectedBotId)),
                ],
            ];
        } catch (\Exception $e) {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('Discord Linked Roles Extension, originally created by Corwin, the owner of Paymenter, and later adapted by Ricardo Neud.'),
                ],
            ];
        }
    }

    public function enabled()
    {
        Artisan::call('migrate', ['--path' => 'extensions/Others/DiscordLinkedRoles/database/migrations/2025_02_13_122225_create_linkedroles_table.php']);
    }

    public function boot()
    {
        require __DIR__ . '/routes/web.php';
    }

    public function getVersion()
    {
        try {
            $response = Http::get("https://api.github.com/repos/" . self::GITHUB_REPO . "/releases/latest");
            $latestRelease = $response->json();
            if (!is_array($latestRelease) || !isset($latestRelease['tag_name'])) {
                return 'Could not check for updates at this time.';
            }
            $latestVersion = $latestRelease['tag_name'];
            $currentVersion = 'v1.0.4';
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
            ])->get(self::DISCORD_API_URL . '/applications/@me');
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

    public function getDiscordBotName($botId = null)
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
            ])->get(self::DISCORD_API_URL . '/applications/@me');
            if ($response->failed()) {
                return 'Could not retrieve bot name at this time.';
            }
            $data = $response->json();
            if (!is_array($data) || !isset($data['name'])) {
                return 'Could not retrieve bot name at this time.';
            }
            return 'Linked Roles connected with ' . $data['name'] . '';
        } catch (\Exception $e) {
            return 'Could not retrieve bot connection data at this time.';
        }
    }
}
