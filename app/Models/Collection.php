<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = ['user_id', 'type', 'collection_type_id', 'serial_number', 'balance', 'pending_balance', 'limit', 'is_redeemed'];

    protected $casts = [
        'balance' => 'integer',
        'pending_balance' => 'integer',
        'limit' => 'integer',
        'is_redeemed' => 'boolean',
    ];

    /**
     * Get the user that owns this collection
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the collection type
     */
    public function collectionType()
    {
        return $this->belongsTo(CollectionType::class);
    }

    /**
     * Get transactions for this collection
     */
    public function transactions()
    {
        return $this->hasMany(CollectionTransaction::class);
    }

    /**
     * Credit amount to this collection
     */
    public function credit($amount, $description = null, $subscriptionId = null)
    {
        $this->balance += $amount;
        $this->save();

        $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'description' => $description,
            'subscription_id' => $subscriptionId,
        ]);
    }

    /**
     * Debit amount from this collection
     */
    public function debit($amount, $description = null, $subscriptionId = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception("Insufficient balance in Tabung");
        }

        $this->balance -= $amount;
        $this->save();

        $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'description' => $description,
            'subscription_id' => $subscriptionId,
        ]);
    }

    /**
     * Check if this collection can withdraw
     * Requires the starter collection to be redeemed first
     */
    public function canWithdraw(): bool
    {
        // Get the starter collection type for this user's sandbox type
        $starterType = $this->getStarterType();
        if (!$starterType) {
            return false;
        }

        // If this IS the starter collection, it cannot withdraw directly
        if ($this->type === $starterType) {
            return false;
        }

        // Fetch the starter collection of the same user
        $starterCollection = self::where('user_id', $this->user_id)
            ->where('type', $starterType)
            ->first();

        // If no starter collection exists, block withdrawal
        if (!$starterCollection) {
            return false;
        }

        // Check if starter collection is redeemed
        if ($starterCollection->is_redeemed) {
            return $this->balance > 0;
        }

        // Legacy check: if starter has reached target (60000 cents = RM600)
        $target = $starterCollection->collectionType->target ?? 60000;
        return $starterCollection->pending_balance >= $target && $this->balance > 0;
    }

    /**
     * Get the starter collection type code for this collection's account type
     */
    public function getStarterType(): ?string
    {
        // If we have a collection type, get the starter from there
        if ($this->collectionType) {
            $accountType = $this->collectionType->account_type;
            $starter = CollectionType::starterForAccountType($accountType);
            return $starter?->code;
        }

        // Legacy fallback based on type
        if (in_array($this->type, CollectionType::SANDBOX_USAHAWAN_TYPES)) {
            return 'geran_asas';
        }
        if (in_array($this->type, CollectionType::SANDBOX_REMAJA_TYPES)) {
            return 'biasiswa_pemula';
        }
        if (in_array($this->type, CollectionType::SANDBOX_AWAM_TYPES)) {
            return 'modal_pemula';
        }

        // Default to geran_asas for old records
        return 'geran_asas';
    }

    /**
     * Check if this is a starter collection
     */
    public function isStarter(): bool
    {
        if ($this->collectionType) {
            return $this->collectionType->is_starter;
        }

        // Legacy check
        return in_array($this->type, ['geran_asas', 'biasiswa_pemula', 'modal_pemula']);
    }

    /**
     * Get the target amount for this collection (for progress tracking)
     */
    public function getTarget(): ?int
    {
        if ($this->collectionType) {
            return $this->collectionType->target;
        }

        // Legacy: starter collections have RM600 target
        if ($this->isStarter()) {
            return 60000; // RM600 in cents
        }

        return null;
    }

    /**
     * Get progress percentage for starter collections
     */
    public function getProgressPercent(): float
    {
        $target = $this->getTarget();
        if (!$target || $target <= 0) {
            return 0;
        }

        return min(100, ($this->pending_balance / $target) * 100);
    }

    /**
     * Check if the target has been reached (for starter collections)
     */
    public function hasReachedTarget(): bool
    {
        $target = $this->getTarget();
        if (!$target) {
            return false;
        }
        return $this->pending_balance >= $target;
    }

    /**
     * Get formatted balance in RM
     */
    public function getFormattedBalanceAttribute(): string
    {
        return 'RM ' . number_format($this->balance / 100, 2);
    }

    /**
     * Get the display name for this collection
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->collectionType) {
            return $this->collectionType->name;
        }

        // Legacy names
        $names = [
            'geran_asas' => 'Geran Asas',
            'tabung_usahawan' => 'Tabung Usahawan',
            'had_pembiayaan' => 'Had Pembiayaan',
            'biasiswa_pemula' => 'Biasiswa Pemula',
            'had_biasiswa' => 'Had Biasiswa',
            'dana_usahawan_muda' => 'Dana Usahawan Muda',
            'modal_pemula' => 'Modal Pemula',
            'had_pembiayaan_hutang' => 'Had Pembiayaan Hutang',
            'khairat_kematian' => 'Khairat Kematian',
        ];

        return $names[$this->type] ?? ucwords(str_replace('_', ' ', $this->type));
    }

    /**
     * Fallback collection type codes for each sandbox account type
     */
    private const FALLBACK_TYPES = [
        CollectionType::ACCOUNT_SANDBOX_USAHAWAN => ['geran_asas', 'tabung_usahawan', 'had_pembiayaan'],
        CollectionType::ACCOUNT_SANDBOX_REMAJA => ['biasiswa_pemula', 'had_biasiswa', 'dana_usahawan_muda'],
        CollectionType::ACCOUNT_SANDBOX_AWAM => ['modal_pemula', 'had_pembiayaan_hutang', 'khairat_kematian'],
    ];

    /**
     * Create collections for a user based on their sandbox account type
     */
    public static function createForUser(int $userId, string $sandboxAccountType): array
    {
        $collectionTypes = CollectionType::forAccountType($sandboxAccountType);
        $created = [];

        // If collection types exist in database, use them
        if ($collectionTypes->isNotEmpty()) {
            foreach ($collectionTypes as $collectionType) {
                $collection = self::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'type' => $collectionType->code,
                    ],
                    [
                        'collection_type_id' => $collectionType->id,
                        'balance' => 0,
                        'pending_balance' => 0,
                        'limit' => $collectionType->limit,
                        'is_redeemed' => false,
                    ]
                );
                $created[] = $collection;
            }
        } else {
            // Fallback: use hardcoded collection types if database is empty
            $fallbackTypes = self::FALLBACK_TYPES[$sandboxAccountType] ?? self::FALLBACK_TYPES[CollectionType::ACCOUNT_SANDBOX_USAHAWAN];

            foreach ($fallbackTypes as $typeCode) {
                $collection = self::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'type' => $typeCode,
                    ],
                    [
                        'balance' => 0,
                        'pending_balance' => 0,
                        'limit' => null,
                        'is_redeemed' => false,
                    ]
                );
                $created[] = $collection;
            }
        }

        return $created;
    }
}

