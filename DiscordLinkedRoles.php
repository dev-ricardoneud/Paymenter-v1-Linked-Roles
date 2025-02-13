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
                    'name' => 'Discord Bot Connections',
                    'type' => 'placeholder',
                    'label' => new HtmlString($this->getDiscordBotConnections()),
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
            $latestVersion = $latestRelease['tag_name'] ?? 'unknown';
            $currentVersion = 'v1.0.2';

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

    public function getDiscordBotConnections()
    {
        try {
            $discordBotToken = LinkedRoleSetting::value('discordlinkedroles_bot_token');
            $response = Http::withHeaders([
                'Authorization' => 'Bot ' . $discordBotToken,
            ])->get(self::DISCORD_API_URL . '/applications/@me');

            if ($response->failed()) {
                return 'Could not retrieve bot connection data at this time.';
            }

            $data = $response->json();
            $userCount = $data['approximate_user_install_count'] ?? 'unknown';

            return 'The bot is authorized by approximately ' . $userCount . ' users.';
        } catch (\Exception $e) {
            return 'Could not retrieve bot connection data at this time.';
        }
    }
}
