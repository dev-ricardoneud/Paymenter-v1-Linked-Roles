<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\LinkedRoleSetting;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\CustomPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class DiscordLinkedRolesController extends Controller
{
    public function index()
    {
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            abort(503, 'Service Unavailable: Unable to retrieve bot settings.');
        }

        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            abort(503, 'Service Unavailable: Invalid bot.');
        }

        $discordClientId = $bot->discordlinkedroles_client_id;
        $discordLinkedRolesCallbackURL = $bot->linkedroles_callback_url;
        config()->set('services.discord.redirect', config('settings.app_url') . '' . $discordLinkedRolesCallbackURL . '');
        $url = 'https://discord.com/api/oauth2/authorize?client_id=' . $discordClientId . '&redirect_uri=' . urlencode(config('services.discord.redirect')) . '&response_type=code&scope=role_connections.write%20identify';
        return redirect($url);
    }

    public function connected(Request $request)
    {
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            abort(503, 'Service Unavailable: Unable to retrieve bot settings.');
        }

        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            abort(503, 'Service Unavailable: Invalid bot.');
        }

        $page = CustomPage::get('key');
        if (!$page) {
            abort(503, 'Service Unavailable: Invalid page.');
        }

        $discordClientId = $bot->discordlinkedroles_client_id;
        $discordClientSecret = $bot->discordlinkedroles_client_secret;
        $discordLinkedRolesCallbackURL = $bot->linkedroles_callback_url;
        $discordLinkedRolesCallbackPage = $bot->callback_redirect_page;

        if (empty($discordLinkedRolesCallbackPage) || !Route::has($discordLinkedRolesCallbackPage)) {
            $redirectRoute = 'callback';
        } else {
            $redirectRoute = $discordLinkedRolesCallbackPage;
        }

        config()->set('services.discord.redirect', config('settings.app_url') . '' . $discordLinkedRolesCallbackURL . '');
        $code = $request->input('code');
        if (!$code) {
            return redirect()->route($redirectRoute)->with('error', 'Something went wrong while linking your discord account');
        }
        $token = $this->getAccessToken($code, $discordClientId, $discordClientSecret);
        if (!$token) {
            return redirect()->route($redirectRoute)->with('error', 'Something went wrong while linking your discord account');
        }
        $user = $this->getUser($token);
        if (!$user) {
            return redirect()->route($redirectRoute)->with('error', 'Something went wrong while linking your discord account');
        }
        $this->updateMetaData($user['id'], $token, $discordClientId);
        return redirect()->route($redirectRoute)->with('success', 'Your discord account has been linked!');
    }

    private function getAccessToken($code, $discordClientId, $discordClientSecret)
    {
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            abort(503, 'Service Unavailable: Unable to retrieve bot settings.');
        }

        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            abort(503, 'Service Unavailable: Invalid bot.');
        }

        $discordLinkedRolesCallbackURL = $bot->linkedroles_callback_url;
        $ApiURL = $bot->api_url;

        $url = 'https://' . $ApiURL . '/api/oauth2/token';
        $response = Http::asForm()->post($url, [
            'client_id' => $discordClientId,
            'client_secret' => $discordClientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('settings.app_url') . '' . $discordLinkedRolesCallbackURL . '',
            'scope' => 'role_connections.write'
        ]);
        if ($response->failed()) {
            return false;
        }
        return $response->json()['access_token'];
    }

    private function getUser($token)
    {
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            abort(503, 'Service Unavailable: Unable to retrieve bot settings.');
        }

        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            abort(503, 'Service Unavailable: Invalid bot.');
        }

        $ApiURL = $bot->api_url;
        $ApiURLVersion = $bot->api_url_version;

        $url = 'https://' . $ApiURL . '/api/' . $ApiURLVersion . '/users/@me';
        $response = Http::withToken($token)->get($url);
        if ($response->failed()) {
            return false;
        }
        return $response->json();
    }

    private function updateMetaData($id, $token, $discordClientId)
    {
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            abort(503, 'Service Unavailable: Unable to retrieve bot settings.');
        }

        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            abort(503, 'Service Unavailable: Invalid bot.');
        }

        $ApiURL = $bot->api_url;
        $ApiURLVersion = $bot->api_url_version;

        $url = 'https://' . $ApiURL . '/api/' . $ApiURLVersion . '/users/@me/applications/' . $discordClientId . '/role-connection';
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
