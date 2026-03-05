<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SandboxReferral extends Model
{
    protected $fillable = [
        'user_id', 'parent_id', 'root_id', 'serial', 'position'
    ];

    /**
     * The parent user in the sandbox referral tree.
     * parent_id stores a User ID (set by ReferralRewardService).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Children sandbox referrals under this user.
     * Finds SandboxReferral records where parent_id matches this record's user_id.
     */
    public function children(): HasMany
    {
        return $this->hasMany(SandboxReferral::class, 'parent_id', 'user_id');
    }

    /**
     * The user this sandbox referral belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The root user of this sandbox referral tree.
     * root_id stores a User ID.
     */
    public function root(): BelongsTo
    {
        return $this->belongsTo(User::class, 'root_id');
    }

    /**
     * Scope: find all sandbox referrals under a given parent user.
     */
    public function scopeUnderParent($query, int $parentUserId)
    {
        return $query->where('parent_id', $parentUserId);
    }
}
