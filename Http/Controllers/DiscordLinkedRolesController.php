<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class DiscordLinkedRolesController extends Controller
{
    public function index()
    {
        config()->set('services.discord.redirect', config('settings.app_url') . '/linkedroles/callback');
        $url = 'https://discord.com/api/oauth2/authorize?client_id=' . config('settings.oauth_discord_client_id') . '&redirect_uri=' . urlencode(config('services.discord.redirect')) . '&response_type=code&scope=role_connections.write%20identify';
        return redirect($url);
    }

    public function callback(Request $request)
    {
        config()->set('services.discord.redirect', config('settings.app_url') . '/linkedroles/callback');
        $code = $request->input('code');
        if (!$code) return redirect()->route('home')->with('error', 'Something went wrong while linking your discord account');
        $token = $this->getAccessToken($code);
        if (!$token) return redirect()->route('home')->with('error', 'Something went wrong while linking your discord account');
        $user = $this->getUser($token);
        if (!$user) return redirect()->route('home')->with('error', 'Something went wrong while linking your discord account');
        $this->updateMetaData($user['id'], $token);
        return redirect()->route('home')->with('success', 'Your discord account has been linked!');
    }

    private function getAccessToken($code)
    {
        $url = 'https://discord.com/api/oauth2/token';
        $response = Http::asForm()->post($url, [
            'client_id' => config('settings.oauth_discord_client_id'),
            'client_secret' => config('settings.oauth_discord_client_secret'),
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
        $url = 'https://discord.com/api/v10/users/@me';
        $response = Http::withToken($token)->get($url);
        if ($response->failed()) return false;
        return $response->json();
    }

    private function updateMetaData($id, $token)
    {
        $url = 'https://discord.com/api/v10/users/@me/applications/' . config('settings.oauth_discord_client_id') . '/role-connection';
        $user = User::find(auth()->user()->id);
        $products = $user->services()->where('status', 'paid')->get();
        $activeProducts = count($products);
        Http::withToken($token)->put($url, [
            'platform_name' => 'DiscordLinkedRoles',
            'metadata' => [
                'syncedwithpaymenter' => true,
                'activeproducts' => $activeProducts
            ]
        ]);
    }
}
