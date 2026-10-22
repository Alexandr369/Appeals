<?php

namespace Azuriom\Plugin\Appeals\SanctionSources;

use Azuriom\Models\User;

/**
 * Used when no sanction source is configured: the player fills the appeal form manually.
 */
class NullSanctionSource implements SanctionSource
{
    public function isConfigured(): bool
    {
        // Never "configured" to look up real sanction data: this is what tells the
        // caller whether a missing sanction should block the appeal or not.
        return false;
    }

    public function findActiveSanctions(User $user): array
    {
        return [];
    }

    public function testConnection(): ?bool
    {
        return null;
    }
}
