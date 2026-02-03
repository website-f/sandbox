<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    protected $table = 'hutang';

    protected $fillable = [
        'user_id',
        'hutang_date',
        'reference',
        'description',
        'amount',
        'is_settled',
        'settled_date',
        'notes',
    ];

    protected $casts = [
        'hutang_date' => 'date',
        'settled_date' => 'date',
        'is_settled' => 'boolean',
        'amount' => 'integer',
    ];

    /**
     * Maximum total hutang allowed (RM500,000 in cents)
     */
    const MAX_TOTAL_AMOUNT = 50000000; // 500,000 * 100 cents

    /**
     * Get the user that owns this hutang
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the documents for this hutang
     */
    public function documents()
    {
        return $this->hasMany(HutangDocument::class);
    }

    /**
     * Get formatted amount in RM
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'RM ' . number_format($this->amount / 100, 2);
    }

    /**
     * Get total hutang amount for a user
     */
    public static function getTotalForUser(int $userId): int
    {
        return self::where('user_id', $userId)->sum('amount');
    }

    /**
     * Get unsettled hutang total for a user
     */
    public static function getUnsettledTotalForUser(int $userId): int
    {
        return self::where('user_id', $userId)
            ->where('is_settled', false)
            ->sum('amount');
    }

    /**
     * Check if user can add more hutang
     */
    public static function canAddHutang(int $userId, int $newAmount): bool
    {
        $currentTotal = self::getTotalForUser($userId);
        return ($currentTotal + $newAmount) <= self::MAX_TOTAL_AMOUNT;
    }

    /**
     * Get remaining hutang limit for user
     */
    public static function getRemainingLimit(int $userId): int
    {
        $currentTotal = self::getTotalForUser($userId);
        return max(0, self::MAX_TOTAL_AMOUNT - $currentTotal);
    }

    /**
     * Check if hutang date is valid (must be before user's registration)
     */
    public static function isDateValid(int $userId, $date): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $hutangDate = Carbon::parse($date);
        $registrationDate = Carbon::parse($user->created_at)->startOfDay();

        return $hutangDate->lt($registrationDate);
    }

    /**
     * Scope for unsettled hutang
     */
    public function scopeUnsettled($query)
    {
        return $query->where('is_settled', false);
    }

    /**
     * Scope for settled hutang
     */
    public function scopeSettled($query)
    {
        return $query->where('is_settled', true);
    }
}
