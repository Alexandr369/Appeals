<?php

namespace Azuriom\Plugin\Appeals\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Server;
use Azuriom\Notifications\AlertNotification;
use Azuriom\Plugin\Appeals\Models\Appeal;
use Azuriom\Plugin\Appeals\Notifications\DiscordNotifier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppealController extends Controller
{
    /**
     * Display the list of appeals, with optional status and player filters.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Appeal::query()->with('user')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%");
            });
        }

        return view('appeals::admin.index', [
            'appeals' => $query->paginate(20)->withQueryString(),
            'statuses' => Appeal::STATUSES,
            'counts' => $this->statusCounts(),
        ]);
    }

    /**
     * The number of appeals per status, for the summary cards.
     *
     * @return array<string, int>
     */
    private function statusCounts(): array
    {
        $counts = Appeal::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(Appeal::STATUSES)
            ->mapWithKeys(fn ($status) => [$status => (int) ($counts[$status] ?? 0)])
            ->all();
    }

    /**
     * The staff's pre-written quick responses, for the dropdown when processing an appeal.
     *
     * @return string[]
     */
    private function quickResponses(): array
    {
        $value = setting('appeals.quick_responses');

        if ($value === null) {
            return trans('appeals::admin.settings.quick_responses_default');
        }

        return array_values(array_filter(array_map('trim', explode("\n", (string) $value))));
    }

    /**
     * Display a single appeal.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Appeal $appeal)
    {
        return view('appeals::admin.show', [
            'appeal' => $appeal->load('user', 'handledBy'),
            'statuses' => Appeal::STATUSES,
            'autoActionEnabled' => (bool) setting('appeals.auto_action_enabled', false),
            'quickResponses' => $this->quickResponses(),
        ]);
    }

    /**
     * Update the status and staff response of an appeal.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appeal $appeal)
    {
        $this->validate($request, [
            'status' => ['required', 'string', Rule::in(Appeal::STATUSES)],
            'staff_response' => ['nullable', 'string', 'max:5000'],
            'auto_action' => ['nullable', 'string', 'in:lift'],
        ]);

        $statusChanged = $appeal->status !== $request->input('status');

        $appeal->status = $request->input('status');
        $appeal->staff_response = $request->input('staff_response');
        $appeal->handled_by = $request->user()->id;
        $appeal->handled_at = now();
        $appeal->save();

        (new DiscordNotifier())->notifyStatusChanged($appeal);

        if ($statusChanged) {
            $this->notifyPlayer($appeal);
        }

        $redirect = redirect()->route('appeals.admin.show', $appeal)
            ->with('success', trans('appeals::admin.appeals.updated'));

        // Not tied to $statusChanged on purpose: staff must be able to retry the
        // automatic action (e.g. after fixing a setting) even if the appeal was
        // already marked as accepted.
        if ($appeal->status === Appeal::STATUS_ACCEPTED && setting('appeals.auto_action_enabled', false)) {
            $autoActionMessage = $this->dispatchAutoAction($appeal, $request->input('auto_action'));

            if ($autoActionMessage !== null) {
                $redirect->with('auto_action_info', $autoActionMessage);
            }
        }

        return $redirect;
    }

    /**
     * Send the lift command on the game server configured in the settings,
     * through Azuriom's own server bridge (AzLink). Never writes to the external
     * AdvancedBan/LibertyBans database directly.
     *
     * @return string|null a feedback message to show the staff, or null if no
     *                      action was requested in the form.
     */
    private function dispatchAutoAction(Appeal $appeal, ?string $action): ?string
    {
        if ($action === null || $action === '') {
            return null;
        }

        if ($appeal->sanction_type === null || $appeal->user === null) {
            return trans('appeals::admin.appeals.auto_action_error_no_sanction');
        }

        $server = Server::find(setting('appeals.auto_action_server_id'));

        if ($server === null) {
            return trans('appeals::admin.appeals.auto_action_error_no_server');
        }

        $liftCommand = setting("appeals.auto_action_lift_command_{$appeal->sanction_type}");

        if (! $liftCommand) {
            return trans('appeals::admin.appeals.auto_action_error_no_command');
        }

        $server->bridge()->sendCommands([$liftCommand], $appeal->user);

        return trans('appeals::admin.appeals.auto_action_sent', ['server' => $server->name]);
    }

    /**
     * Notify the player on the website that their appeal's status changed.
     */
    private function notifyPlayer(Appeal $appeal): void
    {
        if ($appeal->user === null) {
            return;
        }

        (new AlertNotification(trans('appeals::messages.notifications.'.$appeal->status)))
            ->level($this->notificationLevel($appeal->status))
            ->link(route('appeals.show', $appeal))
            ->send($appeal->user);
    }

    private function notificationLevel(string $status): string
    {
        return match ($status) {
            Appeal::STATUS_ACCEPTED => 'success',
            Appeal::STATUS_REJECTED => 'danger',
            default => 'info',
        };
    }

    /**
     * Delete an appeal.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appeal $appeal)
    {
        $appeal->delete();

        return redirect()->route('appeals.admin.index')
            ->with('success', trans('appeals::admin.appeals.deleted'));
    }
}
