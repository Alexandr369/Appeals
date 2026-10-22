<?php

namespace Azuriom\Plugin\Appeals\Notifications;

use Azuriom\Plugin\Appeals\Models\Appeal;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Sends Discord notifications about appeals through the webhook configured in the settings.
 *
 * The message text itself is admin-configurable (settings), using {player}/{reason}/{status}
 * placeholders. Failures are always swallowed: a Discord outage must never block a player or
 * staff action.
 */
class DiscordNotifier
{
    public function notifyCreated(Appeal $appeal): void
    {
        $template = setting('appeals.discord_message_created', trans('appeals::admin.settings.discord_message_created_default'));

        $this->send($template, 3447003, $appeal);
    }

    public function notifyStatusChanged(Appeal $appeal): void
    {
        $template = setting('appeals.discord_message_updated', trans('appeals::admin.settings.discord_message_updated_default'));

        $this->send($template, $this->colorForStatus($appeal->status), $appeal);
    }

    private function send(string $template, int $color, Appeal $appeal): void
    {
        $webhook = setting('appeals.discord_webhook');

        if (! $webhook) {
            return;
        }

        try {
            Http::timeout(5)->post($webhook, [
                'embeds' => [[
                    'description' => $this->applyPlaceholders($template, $appeal),
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

    private function applyPlaceholders(string $template, Appeal $appeal): string
    {
        return str_replace(
            ['{player}', '{reason}', '{status}'],
            [
                $appeal->user?->name ?? '?',
                $appeal->reason_key,
                trans('appeals::messages.statuses.'.$appeal->status),
            ],
            $template
        );
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
