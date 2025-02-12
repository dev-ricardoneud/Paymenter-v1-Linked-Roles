<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles;

use App\Classes\Extension\Extension;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class DiscordLinkedRoles extends Extension
{
    const GITHUB_REPO = 'dev-ricardoneud/Paymenter-v1-Linked-Roles';

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
            $currentVersion = 'v1.0.1';

            if (version_compare($currentVersion, $latestVersion, '>')) {
                return 'The version ' . $currentVersion . ' does not exist. Please check the version number.';
            } elseif ($currentVersion === $latestVersion) {
                return 'You are using the latest version (' . $latestVersion . ').';
            } else {
                return 'You are using version ' . $currentVersion . ', but version ' . $latestVersion . ' is available. Please update!';
            }
        } catch (\Exception $e) {
            return 'Could not check for updates at this time.';
        }
    }
}
