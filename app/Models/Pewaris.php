<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pewaris extends Model
{
    use HasFactory;

    protected $table = 'pewaris';

    protected $fillable = [
        'user_id',          // Owner user
        'linked_user_id',   // Automatically created linked user
        'name',
        'relationship',
        'phone',
        'email',
        'dob',              // Date of birth for age validation
        'ic_number',        // IC number
        'address',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Age limits for Sandbox Remaja eligibility
     */
    const REMAJA_MIN_AGE = 11;
    const REMAJA_MAX_AGE = 20;

    /**
     * Get the age of the pewaris
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->dob) {
            return null;
        }
        return $this->dob->age;
    }

    /**
     * Check if pewaris is eligible for Sandbox Remaja (11-20 years old)
     */
    public function isEligibleForRemaja(): bool
    {
        $age = $this->age;
        if ($age === null) {
            return false;
        }
        return $age >= self::REMAJA_MIN_AGE && $age <= self::REMAJA_MAX_AGE;
    }

    /**
     * Get age eligibility status message
     */
    public function getAgeEligibilityMessage(): string
    {
        $age = $this->age;

        if ($age === null) {
            return 'Date of birth not provided';
        }

        if ($age < self::REMAJA_MIN_AGE) {
            return "Too young for Sandbox Remaja (minimum " . self::REMAJA_MIN_AGE . " years old)";
        }

        if ($age > self::REMAJA_MAX_AGE) {
            return "Too old for Sandbox Remaja (maximum " . self::REMAJA_MAX_AGE . " years old)";
        }

        return "Eligible for Sandbox Remaja";
    }

    // Owner (who added this pewaris)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Linked user (if account was created)
    public function linkedUser()
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }
}
