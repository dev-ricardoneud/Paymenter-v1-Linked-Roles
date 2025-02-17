<?php

use App\Models\Setting;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Livewire\Callback;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Livewire\Custom;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\CustomPage;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\LinkedRoleSetting;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Http\Controllers\DiscordLinkedRolesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
    
    if (!$selectedBotId) {
        abort(503, 'Service Unavailable: Unable to retrieve bot settings.');
    }

    $bot = LinkedRoleSetting::find($selectedBotId);
    
    if (!$bot) {
        abort(503, 'Service Unavailable: Invalid bot.');
    }

    $discordLinkedRolesURL = $bot->linkedroles_url;
    $discordLinkedRolesCallbackURL = $bot->linkedroles_callback_url;
    $customPage = $bot->callback_redirect_page;
    $customPageURL = $bot->success_route;

    if (!in_array($customPage, ['home', 'callback'])) {
        Route::get($customPageURL, Custom::class)->name($customPage);
    }
    if ($customPage === 'callback') {
        Route::get('linkedroles/success', Callback::class)->name('callback');
    }
    Route::get($discordLinkedRolesURL, [DiscordLinkedRolesController::class, 'index'])->name('linkedroles.index');
    Route::get($discordLinkedRolesCallbackURL, [DiscordLinkedRolesController::class, 'connected'])->name('linkedroles.connected');
});
