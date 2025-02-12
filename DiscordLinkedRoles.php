<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles;

use App\Classes\Extension\Extension;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

class DiscordLinkedRoles extends Extension
{
    public function getConfig($values = [])
    {
        try {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('Discord Linked Roles Extension, originally created by Corwin, the owner of Paymenter, and later adapted by Ricardo Neud.'),
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
}