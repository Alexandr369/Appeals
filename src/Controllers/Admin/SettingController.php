<?php

namespace Azuriom\Plugin\Appeals\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Server;
use Azuriom\Models\Setting;
use Azuriom\Plugin\Appeals\Support\SanctionSourceManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Display the Appeals settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('appeals::admin.settings', [
            'source' => setting('appeals.source', 'none'),
            'connectionStatus' => (new SanctionSourceManager())->make()->testConnection(),
            'advancedbanInstalled' => plugins()->isEnabled('advancedban'),
            'libertybansInstalled' => plugins()->isEnabled('libertybans'),
            'appealableTypes' => explode(',', (string) setting('appeals.appealable_types', 'ban,mute')),
            'blockResolvedSanctionReappeal' => (bool) setting('appeals.block_resolved_sanction_reappeal', false),
            'showBanner' => (bool) setting('appeals.show_banner', true),
            'reappealDelayHours' => setting('appeals.reappeal_delay_hours', 72),
            'reasons' => setting('appeals.reasons', ''),
            'quickResponses' => setting('appeals.quick_responses', ''),
            'discordWebhook' => setting('appeals.discord_webhook'),
            'discordMessageCreated' => setting(
                'appeals.discord_message_created',
                trans('appeals::admin.settings.discord_message_created_default')
            ),
            'discordMessageUpdated' => setting(
                'appeals.discord_message_updated',
                trans('appeals::admin.settings.discord_message_updated_default')
            ),
            'autoActionEnabled' => (bool) setting('appeals.auto_action_enabled', false),
            'autoActionServerId' => setting('appeals.auto_action_server_id'),
            'autoActionServers' => Server::executable()->get(),
            'autoActionLiftCommandBan' => setting('appeals.auto_action_lift_command_ban', 'unban {player}'),
            'autoActionLiftCommandMute' => setting('appeals.auto_action_lift_command_mute', 'unmute {player}'),
        ]);
    }

    /**
     * Save the Appeals settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $this->validate($request, [
            'source' => ['required', 'string', Rule::in($this->availableSources())],
            'appealable_types' => ['required', 'array', 'min:1'],
            'appealable_types.*' => ['string', 'in:ban,mute'],
            'block_resolved_sanction_reappeal' => ['sometimes', 'string'],
            'show_banner' => ['sometimes', 'string'],
            'reappeal_delay_hours' => ['required', 'integer', 'min:0', 'max:8760'],
            'reasons' => ['required', 'string'],
            'quick_responses' => ['nullable', 'string'],
            'discord_webhook' => ['nullable', 'url', 'max:255'],
            'discord_message_created' => ['nullable', 'string', 'max:500'],
            'discord_message_updated' => ['nullable', 'string', 'max:500'],
            'auto_action_enabled' => ['sometimes', 'string'],
            'auto_action_server_id' => ['required_if:auto_action_enabled,on', 'nullable', 'integer', 'exists:servers,id'],
            'auto_action_lift_command_ban' => ['nullable', 'string', 'max:255'],
            'auto_action_lift_command_mute' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::updateSettings([
            'appeals.source' => $request->input('source'),
            'appeals.appealable_types' => implode(',', $request->input('appealable_types')),
            'appeals.block_resolved_sanction_reappeal' => $request->input('block_resolved_sanction_reappeal') === 'on',
            'appeals.show_banner' => $request->input('show_banner') === 'on',
            'appeals.reappeal_delay_hours' => $request->input('reappeal_delay_hours'),
            'appeals.reasons' => $request->input('reasons'),
            'appeals.quick_responses' => $request->input('quick_responses'),
            'appeals.discord_webhook' => $request->input('discord_webhook'),
            'appeals.discord_message_created' => $request->input('discord_message_created'),
            'appeals.discord_message_updated' => $request->input('discord_message_updated'),
            'appeals.auto_action_enabled' => $request->input('auto_action_enabled') === 'on',
            'appeals.auto_action_server_id' => $request->input('auto_action_server_id'),
            'appeals.auto_action_lift_command_ban' => $request->input('auto_action_lift_command_ban'),
            'appeals.auto_action_lift_command_mute' => $request->input('auto_action_lift_command_mute'),
        ]);

        return redirect()->route('appeals.admin.settings')->with('success', trans('admin.settings.updated'));
    }

    /**
     * The sanction sources that can be selected: "none" plus the integration plugins
     * that are actually installed on this site.
     *
     * @return string[]
     */
    private function availableSources(): array
    {
        return array_merge(['none'], array_keys(array_filter([
            'advancedban' => plugins()->isEnabled('advancedban'),
            'libertybans' => plugins()->isEnabled('libertybans'),
        ])));
    }
}
