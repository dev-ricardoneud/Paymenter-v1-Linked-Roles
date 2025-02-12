<?php

use Paymenter\Extensions\Others\DiscordLinkedRoles\Http\Controllers\DiscordLinkedRolesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/linkedroles', [DiscordLinkedRolesController::class, 'index'])->name('linkedroles.index');
    Route::get('/linkedroles/callback', [DiscordLinkedRolesController::class, 'callback'])->name('linkedroles.callback');
});
