<?php

namespace Paymenter\Extensions\Others\DiscordLinkedRoles\Livewire;

use App\Models\Setting;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\LinkedRoleSetting;
use Paymenter\Extensions\Others\DiscordLinkedRoles\Models\CustomPage;
use Livewire\Component;

class Custom extends Component
{
    public $successTitle;
    public $successMessage;
    public $successAboveButtonText;
    public $successButtonText;
    public $successButtonLink;

    public function mount()
    {
        $selectedBotId = Setting::where('key', 'discord_bot_id')->value('value');
        if (!$selectedBotId) {
            abort(503, 'Service Unavailable: Unable to retrieve bot settings.');
        }

        $bot = LinkedRoleSetting::where('id', $selectedBotId)->first();
        if (!$bot) {
            abort(503, 'Service Unavailable: Invalid bot.');
        }

        $selectedCustomPage = $bot->callback_redirect_page ?? null;
        $customPage = CustomPage::where('key', $selectedCustomPage)->first();

        if ($customPage) {
            $this->successTitle = $customPage->title;
            $this->successMessage = $customPage->text;
            $this->successAboveButtonText = $customPage->text_above_button;
            $this->successButtonText = $customPage->button_text;
            $this->successButtonLink = $customPage->button_link;
        } else {
            abort(404);
        }
    }

    public function render()
    {
        return <<<'blade'
<div class="container text-center p-5">
    <div class="alert alert-success" role="alert">
        <h1 class="display-4">{{ $successTitle }}</h1>
        <article class="prose dark:prose-invert mb-2 max-w-full">
            {!! $successMessage !!}
        </article>
        <hr class="my-4">
        <p>{{ $successAboveButtonText }}</p>
        <a href="{{ $successButtonLink }}" class="btn btn-primary btn-lg">
            {{ $successButtonText }}
        </a>
    </div>
</div>
blade;
    }
}
