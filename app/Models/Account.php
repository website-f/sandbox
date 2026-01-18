<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['user_id', 'type', 'subtype', 'active', 'expires_at', 'serial_number', 'account_type_id'];
    protected $casts = ['active' => 'boolean', 'expires_at' => 'date'];

    /**
     * Account type constants
     */
    const TYPE_RIZQMALL = 'rizqmall';
    const TYPE_SANDBOX = 'sandbox';

    /**
     * Sandbox subtypes
     */
    const SUBTYPE_USAHAWAN = 'usahawan';
    const SUBTYPE_REMAJA = 'remaja';
    const SUBTYPE_AWAM = 'awam';

    /**
     * Serial number prefixes
     */
    const SERIAL_PREFIX_RIZQMALL = 'RM';
    const SERIAL_PREFIX_SANDBOX_USAHAWAN = 'SB';
    const SERIAL_PREFIX_SANDBOX_REMAJA = 'SR';
    const SERIAL_PREFIX_SANDBOX_AWAM = 'SA';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    /**
     * Get collections for this account (via user and matching type)
     */
    public function collections()
    {
        $accountType = $this->getSandboxAccountType();
        if (!$accountType) {
            return collect();
        }

        return Collection::where('user_id', $this->user_id)
            ->whereHas('collectionType', function ($query) use ($accountType) {
                $query->where('account_type', $accountType);
            })
            ->get();
    }

    /**
     * Get the sandbox account type string for collection types
     */
    public function getSandboxAccountType(): ?string
    {
        if ($this->type !== self::TYPE_SANDBOX) {
            return null;
        }

        return match ($this->subtype) {
            self::SUBTYPE_USAHAWAN, null => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
            self::SUBTYPE_REMAJA => CollectionType::ACCOUNT_SANDBOX_REMAJA,
            self::SUBTYPE_AWAM => CollectionType::ACCOUNT_SANDBOX_AWAM,
            default => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
        };
    }

    /**
     * Check if this is a Sandbox Usahawan account
     */
    public function isSandboxUsahawan(): bool
    {
        return $this->type === self::TYPE_SANDBOX &&
            (is_null($this->subtype) || $this->subtype === self::SUBTYPE_USAHAWAN);
    }

    /**
     * Check if this is a Sandbox Remaja account
     */
    public function isSandboxRemaja(): bool
    {
        return $this->type === self::TYPE_SANDBOX && $this->subtype === self::SUBTYPE_REMAJA;
    }

    /**
     * Check if this is a Sandbox Awam account
     */
    public function isSandboxAwam(): bool
    {
        return $this->type === self::TYPE_SANDBOX && $this->subtype === self::SUBTYPE_AWAM;
    }

    /**
     * Get display name for the account
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->type === self::TYPE_RIZQMALL) {
            return 'RizqMall';
        }

        return match ($this->subtype) {
            self::SUBTYPE_REMAJA => 'Sandbox Remaja',
            self::SUBTYPE_AWAM => 'Sandbox Awam',
            default => 'Sandbox Usahawan',
        };
    }

    /**
     * Get the serial prefix for a given type/subtype combination
     */
    public static function getSerialPrefix(string $type, ?string $subtype = null): string
    {
        if ($type === self::TYPE_RIZQMALL) {
            return self::SERIAL_PREFIX_RIZQMALL;
        }

        return match ($subtype) {
            self::SUBTYPE_REMAJA => self::SERIAL_PREFIX_SANDBOX_REMAJA,
            self::SUBTYPE_AWAM => self::SERIAL_PREFIX_SANDBOX_AWAM,
            default => self::SERIAL_PREFIX_SANDBOX_USAHAWAN,
        };
    }

    /**
     * Generate unique serial number for account
     * Format: PREFIX + YYMMDD + 4-digit running number
     * Examples: RM2601070001, SB2601070001, SR2601070001, SA2601070001
     */
    public static function generateSerial($type, $subtype = null)
    {
        $prefix = self::getSerialPrefix($type, $subtype);
        $today  = Carbon::now()->format('ymd'); // YYMMDD format

        // Find the highest serial number for this prefix and date
        $lastSerial = self::where('serial_number', 'like', $prefix . $today . '%')
            ->orderByRaw('CAST(SUBSTRING(serial_number, -4) AS UNSIGNED) DESC')
            ->value('serial_number');

        // Extract the running number (last 4 digits) and increment
        if ($lastSerial) {
            $number = intval(substr($lastSerial, -4)) + 1;
        } else {
            $number = 1;
        }

        // Format: PREFIX + YYMMDD + 4-digit padded number
        return $prefix . $today . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique serial number with database lock to prevent duplicates
     */
    public static function generateUniqueSerial($type, $subtype = null)
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $serial = self::generateSerial($type, $subtype);
            $exists = self::where('serial_number', $serial)->exists();
            $attempt++;
        } while ($exists && $attempt < $maxAttempts);

        if ($exists) {
            // Fallback: add timestamp suffix
            $serial = $serial . Carbon::now()->format('is'); // seconds + microseconds
        }

        return $serial;
    }

    /**
     * Check if the user is eligible for Sandbox Remaja based on age
     * Must be between 11 and 20 years old
     */
    public static function isEligibleForRemaja(?string $dob): bool
    {
        if (empty($dob)) {
            return false;
        }

        try {
            $birthDate = Carbon::parse($dob);
            $age = $birthDate->age;
            return $age >= 11 && $age <= 20;
        } catch (\Exception $e) {
            return false;
        }
    }
}
