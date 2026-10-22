<?php

namespace Azuriom\Plugin\Appeals\Models;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\Traits\HasUser;
use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;

class Appeal extends Model
{
    use HasTablePrefix;
    use HasUser;

    public const STATUS_PENDING = 'pending';

    public const STATUS_REVIEWING = 'reviewing';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    /**
     * All the statuses an appeal can have, in their display order.
     */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_REVIEWING,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
    ];

    /**
     * Statuses for which the player is not allowed to submit a new appeal.
     */
    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_REVIEWING,
    ];

    protected $prefix = 'appeals_';

    protected $fillable = [
        'reason_key', 'explanation',
        'sanction_type', 'sanction_reason', 'sanction_date', 'sanction_duration',
        'sanction_staff', 'sanction_source', 'sanction_reference',
    ];

    protected $casts = [
        'sanction_date' => 'datetime',
        'handled_at' => 'datetime',
    ];

    /**
     * Get the user who submitted this appeal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the staff member who handled this appeal, if any.
     */
    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Get the history of status changes for this appeal, in chronological order.
     */
    public function statusHistory()
    {
        return $this->hasMany(AppealStatusHistory::class)->orderBy('created_at');
    }

    /**
     * Scope a query to only include appeals that are still pending or under review.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true);
    }

    public function isHandled(): bool
    {
        return $this->handled_at !== null;
    }

    /**
     * Bootstrap badge color matching a given status, used in the player and admin views.
     */
    public static function statusColor(string $status): string
    {
        return match ($status) {
            self::STATUS_ACCEPTED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_REVIEWING => 'info',
            default => 'secondary',
        };
    }

    /**
     * Display label for a sanction type. The 'ban'/'mute' types coming from an
     * automatic source are translated, while a manually entered type (free text,
     * so it fits any game or sanction system) is displayed as-is.
     */
    public static function sanctionTypeLabel(?string $type): ?string
    {
        if ($type === null) {
            return null;
        }

        return in_array($type, ['ban', 'mute'], true)
            ? trans('appeals::messages.sanctions.types.'.$type)
            : $type;
    }
}
