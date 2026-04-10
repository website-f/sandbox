<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['user_id','parent_id','root_id','level','direct_children','ref_code'];

    protected static function booted(): void
    {
        static::created(function (Referral $referral): void {
            static::syncDirectChildrenCountForUserId($referral->parent_id);
        });

        static::updated(function (Referral $referral): void {
            if ($referral->wasChanged('parent_id')) {
                static::syncDirectChildrenCountForUserIds([
                    $referral->getOriginal('parent_id'),
                    $referral->parent_id,
                ]);
            }
        });

        static::deleted(function (Referral $referral): void {
            static::syncDirectChildrenCountForUserId($referral->parent_id);
        });
    }

    public static function syncDirectChildrenCountForUserId(?int $userId): void
    {
        if (!$userId) {
            return;
        }

        $referral = static::where('user_id', $userId)->first();
        if (!$referral) {
            return;
        }

        $actualChildren = static::where('parent_id', $userId)->count();
        if ((int) $referral->direct_children === $actualChildren) {
            return;
        }

        $referral->direct_children = $actualChildren;
        $referral->save();
    }

    public static function syncDirectChildrenCountForUserIds(array $userIds): void
    {
        foreach (array_unique(array_filter($userIds)) as $userId) {
            static::syncDirectChildrenCountForUserId((int) $userId);
        }
    }

    public function children()
    {
        return $this->hasMany(Referral::class, 'parent_id', 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function root()
    {
        return $this->belongsTo(User::class, 'root_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rootUser()
    {
        $current = $this->user;
        while ($current->referral && $current->referral->parent) {
            $current = $current->referral->parent;
        }

        return $current;
    }
}
