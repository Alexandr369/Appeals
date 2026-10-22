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
            'reasons' => $this->listSetting('appeals.reasons', 'appeals::admin.settings.reasons_default'),
            'quickResponses' => $this->listSetting('appeals.quick_responses', 'appeals::admin.settings.quick_responses_default'),
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
            'autoActionCommands' => $this->autoActionCommands(),
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
            'reasons' => ['required', 'array', 'min:1'],
            'reasons.*' => ['nullable', 'string', 'max:255'],
            'quick_responses' => ['nullable', 'array'],
            'quick_responses.*' => ['nullable', 'string', 'max:1000'],
            'discord_webhook' => ['nullable', 'url', 'max:255'],
            'discord_message_created' => ['nullable', 'string', 'max:500'],
            'discord_message_updated' => ['nullable', 'string', 'max:500'],
            'auto_action_enabled' => ['sometimes', 'string'],
            'auto_action_server_id' => ['required_if:auto_action_enabled,on', 'nullable', 'integer', 'exists:servers,id'],
            'auto_action_names' => ['nullable', 'array'],
            'auto_action_names.*' => ['nullable', 'string', 'max:100'],
            'auto_action_command_values' => ['nullable', 'array'],
            'auto_action_command_values.*' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::updateSettings([
            'appeals.source' => $request->input('source'),
            'appeals.appealable_types' => implode(',', $request->input('appealable_types')),
            'appeals.block_resolved_sanction_reappeal' => $request->input('block_resolved_sanction_reappeal') === 'on',
            'appeals.show_banner' => $request->input('show_banner') === 'on',
            'appeals.reappeal_delay_hours' => $request->input('reappeal_delay_hours'),
            'appeals.reasons' => $this->joinList($request->input('reasons', [])),
            'appeals.quick_responses' => $this->joinList($request->input('quick_responses', [])),
            'appeals.discord_webhook' => $request->input('discord_webhook'),
            'appeals.discord_message_created' => $request->input('discord_message_created'),
            'appeals.discord_message_updated' => $request->input('discord_message_updated'),
            'appeals.auto_action_enabled' => $request->input('auto_action_enabled') === 'on',
            'appeals.auto_action_server_id' => $request->input('auto_action_server_id'),
            'appeals.auto_action_commands' => json_encode($this->zipAutoActionCommands(
                $request->input('auto_action_names', []),
                $request->input('auto_action_command_values', [])
            )),
        ]);

        return redirect()->route('appeals.admin.settings')->with('success', trans('admin.settings.updated'));
    }

    /**
     * A newline-separated setting (reasons, quick responses...), as a list for the
     * repeatable "add" form fields. Falls back to a translated default list only
     * when the setting has never been saved at all.
     *
     * @return string[]
     */
    private function listSetting(string $key, string $defaultTransKey): array
    {
        $value = setting($key);

        if ($value === null) {
            return trans($defaultTransKey);
        }

        return array_values(array_filter(array_map('trim', explode("\n", (string) $value))));
    }

    /**
     * Turns the submitted array of repeatable field values back into the
     * newline-separated string the setting is stored as.
     */
    private function joinList(array $values): string
    {
        return implode("\n", array_values(array_filter(array_map('trim', $values), fn ($value) => $value !== '')));
    }

    /**
     * The admin-configured automation commands, as a list of ['name' => ..., 'command' => ...].
     * Stored as JSON (not newline-separated) since each entry is a name/command pair.
     *
     * @return array<int, array{name: string, command: string}>
     */
    private function autoActionCommands(): array
    {
        $value = setting('appeals.auto_action_commands');

        if ($value === null) {
            return trans('appeals::admin.settings.auto_action_commands_default');
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Pairs up the parallel "name" and "command" arrays submitted by the repeatable
     * fields into a clean list, dropping any row missing either value.
     *
     * @return array<int, array{name: string, command: string}>
     */
    private function zipAutoActionCommands(array $names, array $commands): array
    {
        $result = [];

        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            $command = trim((string) ($commands[$index] ?? ''));

            if ($name === '' || $command === '') {
                continue;
            }

            $result[] = ['name' => $name, 'command' => $command];
        }

        return $result;
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
