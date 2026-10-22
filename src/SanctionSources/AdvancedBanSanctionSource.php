<?php

namespace Azuriom\Plugin\Appeals\SanctionSources;

use Azuriom\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDOException;

/**
 * Reads the active sanction of a player from an external AdvancedBan database.
 *
 * This source never stores its own database credentials: it reuses the connection
 * already configured in the "AdvancedBan" Azuriom plugin, so the admin only has to
 * fill it in once.
 *
 * This source is strictly read-only: it never writes to the AdvancedBan database.
 */
class AdvancedBanSanctionSource implements SanctionSource
{
    private const CONNECTION = 'appeals_advancedban';

    // Exact values stored in `punishmentType`: the Java enum constant names
    // (PunishmentType.name()) from AdvancedBan's own source, not the human-readable labels.
    private const BAN_TYPES = ['BAN', 'TEMP_BAN', 'IP_BAN', 'TEMP_IP_BAN'];

    private const MUTE_TYPES = ['MUTE', 'TEMP_MUTE'];

    public function isConfigured(): bool
    {
        return setting('advancedban.host') !== null && setting('advancedban.database') !== null;
    }

    public function findActiveSanctions(User $user): array
    {
        if (! $this->isConfigured() || $user->game_id === null) {
            return [];
        }

        $this->connect();

        $uuid = strtolower(str_replace('-', '', $user->game_id));

        $punishments = DB::connection(self::CONNECTION)
            ->table(setting('advancedban.punishmentTable', 'Punishments'))
            ->where(function ($query) use ($uuid, $user) {
                // AdvancedBan stores the lowercase player name instead of a real uuid
                // when its UUID fetcher is disabled (common on offline-mode servers),
                // so a name match is needed alongside the normal uuid match.
                $query->whereRaw('LOWER(REPLACE(uuid, \'-\', \'\')) = ?', [$uuid])
                    ->orWhereRaw('LOWER(uuid) = ?', [strtolower($user->name)])
                    ->orWhereRaw('LOWER(name) = ?', [strtolower($user->name)]);
            })
            ->whereIn('punishmentType', array_merge(self::BAN_TYPES, self::MUTE_TYPES))
            ->where(function ($query) {
                // AdvancedBan uses -1 for a punishment with no expiration (permanent).
                $query->where('end', -1)->orWhere('end', '>', now()->getTimestamp() * 1000);
            })
            ->orderByDesc('start')
            ->get();

        $sanctions = [];

        foreach ($punishments as $punishment) {
            $row = (array) $punishment;
            $type = in_array($row['punishmentType'], self::BAN_TYPES, true) ? 'ban' : 'mute';

            if (isset($sanctions[$type])) {
                continue; // Already have the most recent one of this type (ordered by start desc).
            }

            $end = (int) $row['end'];

            $sanctions[$type] = new SanctionData(
                type: $type,
                reason: $row['reason'] ?? null,
                staff: $row['operator'] ?? null,
                date: Carbon::createFromTimestampMs((int) $row['start']),
                permanent: $end === -1,
                expiresAt: $end === -1 ? null : Carbon::createFromTimestampMs($end),
                source: 'advancedban',
                reference: (string) ($row['id'] ?? $row['uuid'].':'.$row['start']),
            );
        }

        return $sanctions;
    }

    public function testConnection(): ?bool
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $this->connect();

        try {
            DB::connection(self::CONNECTION)->getPdo();

            return true;
        } catch (PDOException) {
            return false;
        }
    }

    /**
     * Reuses the database connection configured in the AdvancedBan plugin's own settings.
     */
    private function connect(): void
    {
        if (config()->get('database.connections.'.self::CONNECTION) !== null) {
            return;
        }

        config()->set('database.connections.'.self::CONNECTION, [
            'driver' => 'mysql',
            'host' => setting('advancedban.host', '127.0.0.1'),
            'port' => setting('advancedban.port', '3306'),
            'database' => setting('advancedban.database'),
            'username' => setting('advancedban.username'),
            'password' => setting('advancedban.password'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
        ]);
    }
}
