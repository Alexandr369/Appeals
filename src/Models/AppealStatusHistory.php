<?php

namespace Azuriom\Plugin\Appeals\Models;

use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * One row per status change of an appeal, so the full timeline can be displayed
 * (handled_at/handled_by on Appeal itself only ever keeps the latest change).
 */
class AppealStatusHistory extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'appeals_status_history';

    protected $fillable = [
        'status', 'changed_by',
    ];

    public function appeal()
    {
        return $this->belongsTo(Appeal::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
