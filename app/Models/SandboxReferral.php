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

    // Immediate parent referral (self-relation)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SandboxReferral::class, 'parent_id');
    }

    // Children referrals
    public function children(): HasMany
    {
        return $this->hasMany(SandboxReferral::class, 'parent_id');
    }

    // Associated user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Root referral
    public function root(): BelongsTo
    {
        return $this->belongsTo(SandboxReferral::class, 'root_id');
    }
}
