<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\LinkedRoleSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class DiscordLinkedRolesController extends Controller
{
    public function index()
    {
        $bots = LinkedRoleSetting::all(['id', 'discordlinkedroles_client_id']);
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            return 'No bot selected.';
        }
        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            return 'Invalid bot.';
        }

        $discordClientId = $bot->discordlinkedroles_client_id;
        config()->set('services.discord.redirect', config('settings.app_url') . '/linkedroles/callback');
        $url = 'https://discord.com/api/oauth2/authorize?client_id=' . $discordClientId . '&redirect_uri=' . urlencode(config('services.discord.redirect')) . '&response_type=code&scope=role_connections.write%20identify';
        return redirect($url);
    }

    public function callback(Request $request)
    {
        $bots = LinkedRoleSetting::all(['id', 'discordlinkedroles_client_id']);
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            return 'No bot selected.';
        }
        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            return 'Invalid bot.';
        }

        $discordClientId = $bot->discordlinkedroles_client_id;
        $discordClientSecret = $bot->discordlinkedroles_client_secret;
        config()->set('services.discord.redirect', config('settings.app_url') . '/linkedroles/callback');
        $code = $request->input('code');
        if (!$code) return redirect()->route('home')->with('error', 'Something went wrong while linking your discord account');
        $token = $this->getAccessToken($code, $discordClientId, $discordClientSecret);
        if (!$token) return redirect()->route('home')->with('error', 'Something went wrong while linking your discord account');
        $user = $this->getUser($token);
        if (!$user) return redirect()->route('home')->with('error', 'Something went wrong while linking your discord account');
        $this->updateMetaData($user['id'], $token, $discordClientId);
        return redirect()->route('home')->with('success', 'Your discord account has been linked!');
    }

    private function getAccessToken($code, $discordClientId, $discordClientSecret)
    {
        $bots = LinkedRoleSetting::all(['id', 'discordlinkedroles_client_id']);
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            return 'No bot selected.';
        }
        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            return 'Invalid bot.';
        }

        $url = 'https://discord.com/api/oauth2/token';
        $response = Http::asForm()->post($url, [
            'client_id' => $discordClientId,
            'client_secret' => $discordClientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('settings.app_url') . '/linkedroles/callback',
            'scope' => 'role_connections.write'
        ]);
        if ($response->failed()) return false;
        return $response->json()['access_token'];
    }

    private function getUser($token)
    {
        $bots = LinkedRoleSetting::all(['id', 'discordlinkedroles_client_id']);
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            return 'No bot selected.';
        }
        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            return 'Invalid bot.';
        }

        $url = 'https://discord.com/api/v10/users/@me';
        $response = Http::withToken($token)->get($url);
        if ($response->failed()) return false;
        return $response->json();
    }

    private function updateMetaData($id, $token, $discordClientId)
    {
        $bots = LinkedRoleSetting::all(['id', 'discordlinkedroles_client_id']);
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            return 'No bot selected.';
        }
        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            return 'Invalid bot.';
        }
        
        $url = 'https://discord.com/api/v10/users/@me/applications/' . $discordClientId . '/role-connection';
        $user = User::find(auth()->user()->id);
        $products = $user->services()->where('status', 'paid')->get();
        $activeProducts = count($products);
        Http::withToken($token)->put($url, [
            'platform_name' => config('settings.company_name', 'Paymenter'),
            'metadata' => [
                'syncedwithpaymenter' => true,
                'activeproducts' => $activeProducts
            ]
        ]);
    }
}
