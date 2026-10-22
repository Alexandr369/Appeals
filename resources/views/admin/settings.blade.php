@extends('admin.layouts.admin')

@section('title', trans('appeals::admin.settings.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('appeals.admin.settings') }}" method="POST">
                @csrf

                <h5>{{ trans('appeals::admin.settings.section_source') }}</h5>

                <div class="mb-3">
                    <label class="form-label" for="source">{{ trans('appeals::admin.settings.source') }}</label>
                    <select class="form-select" id="source" name="source">
                        <option value="none" @selected($source === 'none')>{{ trans('appeals::admin.settings.source_none') }}</option>
                        @if ($advancedbanInstalled)
                            <option value="advancedban" @selected($source === 'advancedban')>AdvancedBan</option>
                        @endif
                        @if ($libertybansInstalled)
                            <option value="libertybans" @selected($source === 'libertybans')>LibertyBans</option>
                        @endif
                    </select>
                    <div class="form-text">{{ trans('appeals::admin.settings.source_help') }}</div>
                </div>

                @if (! $advancedbanInstalled && ! $libertybansInstalled)
                    <div class="alert alert-info">{{ trans('appeals::admin.settings.no_source_plugin') }}</div>
                @endif

                @if ($source !== 'none')
                    @if ($connectionStatus === true)
                        <div class="alert alert-success">{{ trans('appeals::admin.settings.connection_ok') }}</div>
                    @elseif ($connectionStatus === false)
                        <div class="alert alert-danger">{{ trans('appeals::admin.settings.connection_error') }}</div>
                    @else
                        <div class="alert alert-warning">{{ trans('appeals::admin.settings.connection_not_configured') }}</div>
                    @endif
                @endif

                <div class="mb-3">
                    <label class="form-label">{{ trans('appeals::admin.settings.appealable_types') }}</label>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="appealable_types_ban" name="appealable_types[]" value="ban" @checked(in_array('ban', $appealableTypes))>
                        <label class="form-check-label" for="appealable_types_ban">{{ trans('appeals::messages.sanctions.types.ban') }}</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="appealable_types_mute" name="appealable_types[]" value="mute" @checked(in_array('mute', $appealableTypes))>
                        <label class="form-check-label" for="appealable_types_mute">{{ trans('appeals::messages.sanctions.types.mute') }}</label>
                    </div>

                    <div class="form-text">{{ trans('appeals::admin.settings.appealable_types_help') }}</div>
                </div>

                <hr>

                <h5>{{ trans('appeals::admin.settings.section_general') }}</h5>

                <div class="mb-3">
                    <label class="form-label" for="reappeal_delay_hours">{{ trans('appeals::admin.settings.reappeal_delay_hours') }}</label>
                    <input type="number" min="0" max="8760" class="form-control" id="reappeal_delay_hours" name="reappeal_delay_hours" value="{{ $reappealDelayHours }}">
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" id="block_resolved_sanction_reappeal" name="block_resolved_sanction_reappeal" @checked($blockResolvedSanctionReappeal)>
                    <label class="form-check-label" for="block_resolved_sanction_reappeal">{{ trans('appeals::admin.settings.block_resolved_sanction_reappeal') }}</label>
                    <div class="form-text">{{ trans('appeals::admin.settings.block_resolved_sanction_reappeal_help') }}</div>
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" id="show_banner" name="show_banner" @checked($showBanner)>
                    <label class="form-check-label" for="show_banner">{{ trans('appeals::admin.settings.show_banner') }}</label>
                    <div class="form-text">{{ trans('appeals::admin.settings.show_banner_help') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="reasons">{{ trans('appeals::admin.settings.reasons') }}</label>
                    <textarea class="form-control" id="reasons" name="reasons" rows="5" placeholder="{{ trans('appeals::admin.settings.reasons_placeholder') }}">{{ $reasons }}</textarea>
                    <div class="form-text">{{ trans('appeals::admin.settings.reasons_help') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="quick_responses">{{ trans('appeals::admin.settings.quick_responses') }}</label>
                    <textarea class="form-control" id="quick_responses" name="quick_responses" rows="5" placeholder="{{ trans('appeals::admin.settings.quick_responses_placeholder') }}">{{ $quickResponses }}</textarea>
                    <div class="form-text">{{ trans('appeals::admin.settings.quick_responses_help') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="discord_webhook">{{ trans('appeals::admin.settings.discord_webhook') }}</label>
                    <input class="form-control" id="discord_webhook" name="discord_webhook" value="{{ $discordWebhook }}" placeholder="https://discord.com/api/webhooks/...">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="discord_message_created">{{ trans('appeals::admin.settings.discord_message_created') }}</label>
                    <input class="form-control" id="discord_message_created" name="discord_message_created" value="{{ $discordMessageCreated }}">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="discord_message_updated">{{ trans('appeals::admin.settings.discord_message_updated') }}</label>
                    <input class="form-control" id="discord_message_updated" name="discord_message_updated" value="{{ $discordMessageUpdated }}">
                    <div class="form-text">{{ trans('appeals::admin.settings.discord_message_help') }}</div>
                </div>

                <hr>

                <h5>{{ trans('appeals::admin.settings.section_automation') }}</h5>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" id="auto_action_enabled" name="auto_action_enabled" @checked($autoActionEnabled) onchange="appealsToggleAutomationFields()">
                    <label class="form-check-label" for="auto_action_enabled">{{ trans('appeals::admin.settings.auto_action_enabled') }}</label>
                    <div class="form-text">{{ trans('appeals::admin.settings.auto_action_enabled_help') }}</div>
                </div>

                <div id="appeals-automation-fields">
                    <div class="mb-3">
                        <label class="form-label" for="auto_action_server_id">{{ trans('appeals::admin.settings.auto_action_server') }}</label>
                        <select class="form-select" id="auto_action_server_id" name="auto_action_server_id">
                            <option value="" disabled @selected(! $autoActionServerId)>{{ trans('appeals::admin.settings.auto_action_server_placeholder') }}</option>
                            @foreach ($autoActionServers as $server)
                                <option value="{{ $server->id }}" @selected((string) $autoActionServerId === (string) $server->id)>{{ $server->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">{{ trans('appeals::admin.settings.auto_action_server_help') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="auto_action_lift_command_ban">{{ trans('appeals::admin.settings.auto_action_lift_command_ban') }}</label>
                        <input class="form-control" id="auto_action_lift_command_ban" name="auto_action_lift_command_ban" value="{{ $autoActionLiftCommandBan }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="auto_action_lift_command_mute">{{ trans('appeals::admin.settings.auto_action_lift_command_mute') }}</label>
                        <input class="form-control" id="auto_action_lift_command_mute" name="auto_action_lift_command_mute" value="{{ $autoActionLiftCommandMute }}">
                        <div class="form-text">{{ trans('appeals::admin.settings.auto_action_commands_help') }}</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function appealsToggleAutomationFields() {
            var enabled = document.getElementById('auto_action_enabled').checked;

            document.getElementById('appeals-automation-fields').style.display = enabled ? '' : 'none';
        }

        window.addEventListener('load', appealsToggleAutomationFields);
    </script>
@endpush
