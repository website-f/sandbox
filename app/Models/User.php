<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'referral_code',
        'sandbox_type',
        'rizqmall_activated_at',
        'rizqmall_stores_quota',
        'last_rizqmall_sync',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'rizqmall_activated_at' => 'datetime',
        'last_rizqmall_sync' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    public function hasRole($name)
    {
        return $this->roles()->where('name', $name)->exists();
    }

    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$role->id]);
    }
    public function removeRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function referrer()
    {
        return $this->hasOneThrough(
            User::class,
            Referral::class,
            'user_id',
            'id',
            'id',
            'parent_id'
        );
    }



    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function bank()
    {
        return $this->hasOne(BankDetail::class);
    }
    public function business()
    {
        return $this->hasOne(Business::class);
    }
    public function education()
    {
        return $this->hasOne(Education::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function nextOfKin()
    {
        return $this->hasOne(NextOfKin::class);
    }
    public function affiliations()
    {
        return $this->hasMany(Affiliation::class);
    }

    public function referral()
    {
        return $this->hasOne(Referral::class);
    }
    public function referrals()
    {
        // all Referral records where this user is the parent/upline
        return $this->hasMany(Referral::class, 'parent_id', 'id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function checkBlacklist(): bool
    {
        return Blacklist::where('email', $this->email)->exists();
    }

    public function pewaris()
    {
        return $this->hasMany(Pewaris::class, 'user_id');
    }

    // If this user is a linked user of a pewaris
    public function linkedPewaris()
    {
        return $this->hasOne(Pewaris::class, 'linked_user_id');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * Get a specific collection by type
     */
    public function collectionByType(string $type)
    {
        return $this->collections()->where('type', $type)->first();
    }

    /**
     * Get the user's active sandbox account
     */
    public function sandboxAccount()
    {
        return $this->accounts()->where('type', Account::TYPE_SANDBOX)->first();
    }

    /**
     * Get the sandbox type (usahawan, remaja, awam)
     * First check the sandbox account subtype, then fallback to user's sandbox_type field
     */
    public function getSandboxSubtype(): string
    {
        $sandboxAccount = $this->sandboxAccount();
        if ($sandboxAccount && $sandboxAccount->subtype) {
            return $sandboxAccount->subtype;
        }
        return $this->sandbox_type ?? Account::SUBTYPE_USAHAWAN;
    }

    /**
     * Get display name for sandbox type
     */
    public function getSandboxDisplayName(): string
    {
        return match ($this->getSandboxSubtype()) {
            Account::SUBTYPE_REMAJA => 'Sandbox Remaja',
            Account::SUBTYPE_AWAM => 'Sandbox Awam',
            default => 'Sandbox Usahawan',
        };
    }

    /**
     * Get the collection account type for this user's sandbox
     */
    public function getCollectionAccountType(): string
    {
        return match ($this->getSandboxSubtype()) {
            Account::SUBTYPE_REMAJA => CollectionType::ACCOUNT_SANDBOX_REMAJA,
            Account::SUBTYPE_AWAM => CollectionType::ACCOUNT_SANDBOX_AWAM,
            default => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
        };
    }
}
