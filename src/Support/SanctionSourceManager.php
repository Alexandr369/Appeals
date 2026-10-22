<?php

namespace Azuriom\Plugin\Appeals\Support;

use Azuriom\Plugin\Appeals\SanctionSources\AdvancedBanSanctionSource;
use Azuriom\Plugin\Appeals\SanctionSources\LibertyBansSanctionSource;
use Azuriom\Plugin\Appeals\SanctionSources\NullSanctionSource;
use Azuriom\Plugin\Appeals\SanctionSources\SanctionSource;

/**
 * Resolves the sanction source currently configured in the plugin settings.
 *
 * Adding a new source later (e.g. LiteBans in the Pro version) only requires
 * a new class and a new branch here, nothing else in the plugin changes.
 */
class SanctionSourceManager
{
    public function make(): SanctionSource
    {
        return match (setting('appeals.source', 'none')) {
            'advancedban' => new AdvancedBanSanctionSource(),
            'libertybans' => new LibertyBansSanctionSource(),
            default => new NullSanctionSource(),
        };
    }
}
