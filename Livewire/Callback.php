<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Livewire;

use Livewire\Component;

class Callback extends Component
{
    public function render()
    {
        return <<<'blade'
<div class="container text-center p-5">
    <div class="alert alert-success" role="alert">
        <h1 class="display-4">You have successfully connected your discord! 🎉</h1>
        <p class="lead">🎉 Congratulations! Your Discord account is now successfully linked to your account. You are one step closer to enjoying exclusive roles and perks! 💎</p>
        <hr class="my-4">
        <p>Click the button below to go back to Discord and claim your role. ⬇️</p>
        <a href="discord://discord.com/channels/@me" class="btn btn-primary btn-lg">
            Go to Discord 🚀
        </a>
    </div>
</div>
blade;
    }
}
