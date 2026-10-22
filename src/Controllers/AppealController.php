<?php

namespace Azuriom\Plugin\Appeals\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\Appeals\Models\Appeal;
use Azuriom\Plugin\Appeals\Notifications\DiscordNotifier;
use Azuriom\Plugin\Appeals\SanctionSources\SanctionData;
use Azuriom\Plugin\Appeals\Support\SanctionSourceManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppealController extends Controller
{
    /**
     * Display the appeals submitted by the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return view('appeals::index', [
            'appeals' => Appeal::where('user_id', $user->id)->latest()->paginate(10),
            'activeAppeal' => $this->findActiveAppeal($user),
            'nextAllowedAt' => $this->nextAllowedAppealAt($user),
        ]);
    }

    /**
     * Display the appeal creation form.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = $request->user();

        if (($activeAppeal = $this->findActiveAppeal($user)) !== null) {
            return redirect()->route('appeals.show', $activeAppeal);
        }

        if (($nextAllowedAt = $this->nextAllowedAppealAt($user)) !== null) {
            return redirect()->route('appeals.index')
                ->with('error', trans('appeals::messages.errors.too_soon', ['date' => format_date($nextAllowedAt)]));
        }

        $sanctionSource = (new SanctionSourceManager())->make();
        $matching = $this->typeFilteredSanctions($sanctionSource, $user);
        $sanctions = $this->removeAlreadyDecided($user, $matching);

        if ($sanctionSource->isConfigured() && $matching !== [] && $sanctions === []) {
            return redirect()->route('appeals.index')
                ->with('error', trans('appeals::messages.errors.sanction_already_decided'));
        }

        if ($sanctionSource->isConfigured() && $sanctions === []) {
            return redirect()->route('appeals.index')
                ->with('error', trans('appeals::messages.errors.no_active_sanction'));
        }

        return view('appeals::create', [
            'reasons' => $this->reasons(),
            'sanctions' => $sanctions,
            'sanction' => count($sanctions) === 1 ? reset($sanctions) : null,
        ]);
    }

    /**
     * Store a newly submitted appeal.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($this->findActiveAppeal($user) !== null) {
            return redirect()->route('appeals.index')
                ->with('error', trans('appeals::messages.errors.already_active'));
        }

        if (($nextAllowedAt = $this->nextAllowedAppealAt($user)) !== null) {
            return redirect()->route('appeals.index')
                ->with('error', trans('appeals::messages.errors.too_soon', ['date' => format_date($nextAllowedAt)]));
        }

        $sanctionSource = (new SanctionSourceManager())->make();
        $matching = $this->typeFilteredSanctions($sanctionSource, $user);
        $sanctions = $this->removeAlreadyDecided($user, $matching);

        if ($sanctionSource->isConfigured() && $matching !== [] && $sanctions === []) {
            return redirect()->route('appeals.index')
                ->with('error', trans('appeals::messages.errors.sanction_already_decided'));
        }

        if ($sanctionSource->isConfigured() && $sanctions === []) {
            return redirect()->route('appeals.index')
                ->with('error', trans('appeals::messages.errors.no_active_sanction'));
        }

        $rules = [
            'reason_key' => ['required', 'string', Rule::in($this->reasons())],
            'explanation' => ['required', 'string', 'max:5000'],
        ];

        if (count($sanctions) > 1) {
            $rules['sanction_type'] = ['required', 'string', Rule::in(array_keys($sanctions))];
        }

        $this->validate($request, $rules);

        $sanction = count($sanctions) === 1
            ? reset($sanctions)
            : ($sanctions[$request->input('sanction_type')] ?? null);

        $appeal = new Appeal($request->only('reason_key', 'explanation'));

        if ($sanction !== null) {
            $appeal->sanction_type = $sanction->type;
            $appeal->sanction_reason = $sanction->reason;
            $appeal->sanction_date = $sanction->date;
            $appeal->sanction_duration = $sanction->durationLabel();
            $appeal->sanction_staff = $sanction->staff;
            $appeal->sanction_source = $sanction->source;
            $appeal->sanction_reference = $sanction->reference;
        }

        $appeal->save();

        (new DiscordNotifier())->notifyCreated($appeal);

        return redirect()->route('appeals.show', $appeal)->with('success', trans('appeals::messages.created'));
    }

    /**
     * Display a single appeal belonging to the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Appeal $appeal)
    {
        $this->authorizeOwner($request, $appeal);

        return view('appeals::show', ['appeal' => $appeal]);
    }

    /**
     * Display the form to edit a pending appeal.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Appeal $appeal)
    {
        $this->authorizeOwner($request, $appeal);

        abort_unless($appeal->isActive(), 403);

        return view('appeals::edit', [
            'appeal' => $appeal,
            'reasons' => $this->reasons(),
        ]);
    }

    /**
     * Update a pending appeal.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appeal $appeal)
    {
        $this->authorizeOwner($request, $appeal);

        abort_unless($appeal->isActive(), 403);

        $this->validate($request, [
            'reason_key' => ['required', 'string', Rule::in($this->reasons())],
            'explanation' => ['required', 'string', 'max:5000'],
        ]);

        $appeal->update($request->only('reason_key', 'explanation'));

        return redirect()->route('appeals.show', $appeal)->with('success', trans('appeals::messages.updated'));
    }

    /**
     * Abort with a 403 unless the given appeal belongs to the current user.
     */
    protected function authorizeOwner(Request $request, Appeal $appeal): void
    {
        abort_if($appeal->user_id !== $request->user()->id, 403);
    }

    /**
     * The user's currently active sanctions restricted to the types configured
     * as appealable, keyed by type ('ban'/'mute').
     *
     * @return array<string, SanctionData>
     */
    protected function typeFilteredSanctions($sanctionSource, User $user): array
    {
        $allowedTypes = explode(',', (string) setting('appeals.appealable_types', 'ban,mute'));

        return array_intersect_key(
            $sanctionSource->findActiveSanctions($user),
            array_flip($allowedTypes)
        );
    }

    /**
     * Removes sanctions that already have a decided (accepted/rejected) appeal,
     * when the admin has enabled this restriction.
     *
     * @param  array<string, SanctionData>  $sanctions
     * @return array<string, SanctionData>
     */
    protected function removeAlreadyDecided(User $user, array $sanctions): array
    {
        if (! setting('appeals.block_resolved_sanction_reappeal', false)) {
            return $sanctions;
        }

        return array_filter($sanctions, fn ($sanction) => ! $this->sanctionAlreadyDecided($user, $sanction));
    }

    /**
     * Whether the user already had an appeal accepted or rejected for this exact sanction.
     */
    protected function sanctionAlreadyDecided(User $user, SanctionData $sanction): bool
    {
        if ($sanction->reference === null) {
            return false;
        }

        return Appeal::where('user_id', $user->id)
            ->whereIn('status', [Appeal::STATUS_ACCEPTED, Appeal::STATUS_REJECTED])
            ->where('sanction_source', $sanction->source)
            ->where('sanction_reference', $sanction->reference)
            ->exists();
    }

    /**
     * The reasons configured by the admin, one per line.
     */
    protected function reasons(): array
    {
        return array_values(array_filter(array_map(
            'trim',
            explode("\n", (string) setting('appeals.reasons', ''))
        )));
    }

    /**
     * The user's appeal that is still pending or under review, if any.
     */
    protected function findActiveAppeal(User $user): ?Appeal
    {
        return Appeal::where('user_id', $user->id)->active()->first();
    }

    /**
     * When the user will be allowed to submit a new appeal after a rejection, if still blocked.
     */
    protected function nextAllowedAppealAt(User $user): ?Carbon
    {
        $lastRejected = Appeal::where('user_id', $user->id)
            ->where('status', Appeal::STATUS_REJECTED)
            ->latest('handled_at')
            ->first();

        if ($lastRejected === null || $lastRejected->handled_at === null) {
            return null;
        }

        $nextAllowedAt = $lastRejected->handled_at->copy()
            ->addHours((int) setting('appeals.reappeal_delay_hours', 72));

        return $nextAllowedAt->isFuture() ? $nextAllowedAt : null;
    }
}
