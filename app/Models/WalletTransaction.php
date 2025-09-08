<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['wallet_id','type','amount','description','subscription_id'];

    public function wallet() {
        return $this->belongsTo(Wallet::class);
    }
}
