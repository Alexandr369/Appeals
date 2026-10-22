<?php

namespace Azuriom\Plugin\Appeals\SanctionSources;

use Azuriom\Models\User;

/**
 * Contract for reading a player's active sanction(s) from an external source.
 *
 * Implementations must only ever READ from the external database, never write to it.
 */
interface SanctionSource
{
    /**
     * Whether this source has enough settings configured to be used.
     */
    public function isConfigured(): bool;

    /**
     * Find the player's currently active sanctions, keyed by type ('ban'/'mute').
     * At most one entry per type, since only one sanction of a given type can be
     * active for a player at once.
     *
     * @return array<string, SanctionData>
     */
    public function findActiveSanctions(User $user): array;

    /**
     * Test the connection to the external source.
     *
     * @return bool|null null if not configured, true if reachable, false if configured but unreachable.
     */
    public function testConnection(): ?bool;
}
