<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'account_type',
        'limit',
        'target',
        'is_starter',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'limit' => 'integer',
        'target' => 'integer',
        'is_starter' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Collection types for Sandbox Usahawan
     */
    const SANDBOX_USAHAWAN_TYPES = [
        'geran_asas',
        'tabung_usahawan',
        'had_pembiayaan',
    ];

    /**
     * Collection types for Sandbox Remaja
     */
    const SANDBOX_REMAJA_TYPES = [
        'biasiswa_pemula',
        'had_biasiswa',
        'dana_usahawan_muda',
    ];

    /**
     * Collection types for Sandbox Awam
     */
    const SANDBOX_AWAM_TYPES = [
        'modal_pemula',
        'had_pembiayaan_hutang',
        'khairat_kematian',
    ];

    /**
     * Account type constants
     */
    const ACCOUNT_SANDBOX_USAHAWAN = 'sandbox_usahawan';
    const ACCOUNT_SANDBOX_REMAJA = 'sandbox_remaja';
    const ACCOUNT_SANDBOX_AWAM = 'sandbox_awam';

    /**
     * Get collections using this type
     */
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * Get collection types for a specific account type
     */
    public static function forAccountType(string $accountType)
    {
        return self::where('account_type', $accountType)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get the starter collection type for an account type
     */
    public static function starterForAccountType(string $accountType)
    {
        return self::where('account_type', $accountType)
            ->where('is_starter', true)
            ->first();
    }

    /**
     * Get formatted limit in RM
     */
    public function getFormattedLimitAttribute()
    {
        if ($this->limit === null) {
            return 'Unlimited';
        }
        return 'RM ' . number_format($this->limit / 100, 2);
    }

    /**
     * Get formatted target in RM
     */
    public function getFormattedTargetAttribute()
    {
        if ($this->target === null) {
            return null;
        }
        return 'RM ' . number_format($this->target / 100, 2);
    }

    /**
     * Map old collection type codes to account types
     */
    public static function getAccountTypeForCollectionCode(string $code): ?string
    {
        if (in_array($code, self::SANDBOX_USAHAWAN_TYPES)) {
            return self::ACCOUNT_SANDBOX_USAHAWAN;
        }
        if (in_array($code, self::SANDBOX_REMAJA_TYPES)) {
            return self::ACCOUNT_SANDBOX_REMAJA;
        }
        if (in_array($code, self::SANDBOX_AWAM_TYPES)) {
            return self::ACCOUNT_SANDBOX_AWAM;
        }
        return null;
    }
}
