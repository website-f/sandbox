<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id','balance'];

    public function transactions() {
        return $this->hasMany(WalletTransaction::class);
    }

    public function credit($amount, $description, $subscriptionId = null) {
        $this->increment('balance', $amount);
        $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'description' => $description,
            'subscription_id' => $subscriptionId,
        ]);
    }

    public function debit($amount, $description, $subscriptionId = null) {
        if ($this->balance < $amount) throw new \Exception("Insufficient balance");
        $this->decrement('balance', $amount);
        $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'description' => $description,
            'subscription_id' => $subscriptionId,
        ]);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}


}
