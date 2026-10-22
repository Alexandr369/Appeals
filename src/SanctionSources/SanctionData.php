<?php

namespace Azuriom\Plugin\Appeals\SanctionSources;

use Carbon\Carbon;

/**
 * Immutable snapshot of a sanction read from an external source (AdvancedBan, LibertyBans...).
 */
class SanctionData
{
    public function __construct(
        public readonly string $type, // 'ban' or 'mute'
        public readonly ?string $reason,
        public readonly ?string $staff,
        public readonly ?Carbon $date,
        public readonly bool $permanent,
        public readonly ?Carbon $expiresAt,
        public readonly string $source, // 'advancedban', 'libertybans'...
        public readonly ?string $reference = null,
    ) {
    }

    /**
     * Human readable duration, used for display and for the snapshot stored on the appeal.
     */
    public function durationLabel(): string
    {
        if ($this->permanent) {
            return trans('appeals::messages.sanctions.permanent');
        }

        return $this->expiresAt?->toDateTimeString() ?? trans('appeals::messages.sanctions.unknown_duration');
    }
}
