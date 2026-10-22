<?php

namespace Azuriom\Plugin\Appeals\SanctionSources;

use Azuriom\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDOException;

/**
 * Reads the active sanction of a player from an external LibertyBans database.
 *
 * This source never stores its own database credentials: it reuses the connection
 * already configured in the "LibertyBans" Azuriom plugin, so the admin only has to
 * fill it in once.
 *
 * This source is strictly read-only: it never writes to the LibertyBans database.
 */
class LibertyBansSanctionSource implements SanctionSource
{
    private const CONNECTION = 'appeals_libertybans';

    // The LibertyBans Azuriom plugin itself hardcodes this prefix, it is not configurable there.
    private const TABLE_PREFIX = 'libertybans_';

    private const TYPE_BAN = 0;

    private const TYPE_MUTE = 1;

    public function isConfigured(): bool
    {
        return setting('libertybans.host') !== null && setting('libertybans.database') !== null;
    }

    public function findActiveSanctions(User $user): array
    {
        if (! $this->isConfigured() || $user->game_id === null) {
            return [];
        }

        $driver = $this->connect();

        $query = DB::connection(self::CONNECTION)
            ->table('simple_history')
            ->leftJoin('names as operator_name', 'simple_history.operator', '=', 'operator_name.uuid')
            ->whereIn('simple_history.type', [self::TYPE_BAN, self::TYPE_MUTE])
            ->orderByDesc('simple_history.start')
            ->select(
                'simple_history.id',
                'simple_history.type',
                'simple_history.reason',
                'simple_history.start',
                'simple_history.end',
                'operator_name.name as operator_name',
                DB::raw('CASE WHEN simple_history.type = 0 THEN EXISTS(SELECT 1 FROM '.self::TABLE_PREFIX.'bans WHERE id = '.self::TABLE_PREFIX.'simple_history.id)
                              WHEN simple_history.type = 1 THEN EXISTS(SELECT 1 FROM '.self::TABLE_PREFIX.'mutes WHERE id = '.self::TABLE_PREFIX.'simple_history.id)
                              ELSE false END AS active')
            )
            ->limit(20);

        if ($driver === 'pgsql') {
            $query->whereRaw('simple_history.victim_uuid = ?::uuid', [$this->toDashedUuid($user->game_id)]);
        } else {
            $query->whereRaw('simple_history.victim_uuid = UNHEX(?)', [$this->toRawUuid($user->game_id)]);
        }

        $sanctions = [];

        foreach ($query->get() as $row) {
            $end = (int) $row->end;
            $notExpired = $end === 0 || $end > now()->getTimestamp();

            if (! $row->active || ! $notExpired) {
                continue;
            }

            $type = (int) $row->type === self::TYPE_BAN ? 'ban' : 'mute';

            if (isset($sanctions[$type])) {
                continue; // Already have the most recent one of this type (ordered by start desc).
            }

            $sanctions[$type] = new SanctionData(
                type: $type,
                reason: $row->reason,
                staff: $row->operator_name,
                date: Carbon::createFromTimestamp((int) $row->start),
                permanent: $end === 0,
                expiresAt: $end === 0 ? null : Carbon::createFromTimestamp($end),
                source: 'libertybans',
                reference: (string) $row->id,
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
     * Reuses the database connection configured in the LibertyBans plugin's own settings,
     * and returns its driver name.
     */
    private function connect(): string
    {
        $driver = setting('libertybans.driver', 'mysql');

        if (config()->get('database.connections.'.self::CONNECTION) === null) {
            config()->set('database.connections.'.self::CONNECTION, [
                'driver' => $driver,
                'host' => setting('libertybans.host', '127.0.0.1'),
                'port' => setting('libertybans.port', $driver === 'pgsql' ? '5432' : '3306'),
                'database' => setting('libertybans.database'),
                'username' => setting('libertybans.username'),
                'password' => setting('libertybans.password'),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => self::TABLE_PREFIX,
                'strict' => true,
            ]);
        }

        return $driver;
    }

    private function toRawUuid(string $uuid): string
    {
        return strtolower(str_replace('-', '', $uuid));
    }

    private function toDashedUuid(string $uuid): string
    {
        $hex = $this->toRawUuid($uuid);

        if (strlen($hex) !== 32) {
            return $uuid;
        }

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }
}
