<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $fillable = ['name', 'base_price'];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'account_type_id');
    }
}
