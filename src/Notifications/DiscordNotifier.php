<?php

namespace Azuriom\Plugin\Appeals\Notifications;

use Azuriom\Plugin\Appeals\Models\Appeal;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Sends Discord notifications about appeals through the webhook configured in the settings.
 *
 * Failures are always swallowed: a Discord outage must never block a player or staff action.
 */
class DiscordNotifier
{
    public function notifyCreated(Appeal $appeal): void
    {
        $this->send(trans('appeals::admin.discord.created_title'), 3447003, $appeal);
    }

    public function notifyStatusChanged(Appeal $appeal): void
    {
        $this->send(trans('appeals::admin.discord.updated_title'), $this->colorForStatus($appeal->status), $appeal);
    }

    private function send(string $title, int $color, Appeal $appeal): void
    {
        $webhook = setting('appeals.discord_webhook');

        if (! $webhook) {
            return;
        }

        try {
            Http::timeout(5)->post($webhook, [
                'embeds' => [[
                    'title' => $title,
                    'color' => $color,
                    'fields' => [
                        ['name' => trans('appeals::admin.appeals.user'), 'value' => $appeal->user?->name ?? '?', 'inline' => true],
                        ['name' => trans('messages.fields.status'), 'value' => trans('appeals::messages.statuses.'.$appeal->status), 'inline' => true],
                        ['name' => trans('appeals::messages.fields.reason'), 'value' => $appeal->reason_key, 'inline' => false],
                    ],
                ]],
            ]);
        } catch (Throwable) {
            // Ignored on purpose: a notification failure must not break the appeal workflow.
        }
    }

    private function colorForStatus(string $status): int
    {
        return match ($status) {
            Appeal::STATUS_ACCEPTED => 3066993,
            Appeal::STATUS_REJECTED => 15158332,
            Appeal::STATUS_REVIEWING => 3426654,
            default => 9807270,
        };
    }
}
