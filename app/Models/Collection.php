<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = ['user_id', 'type', 'balance', 'pending_balance', 'limit', 'is_redeemed'];

    public function transactions()
    {
        return $this->hasMany(CollectionTransaction::class);
    }

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

    public function canWithdraw(): bool
    {
        // Fetch Geran Asas of same user
        $geran = self::where('user_id',$this->user_id)
                     ->where('type','geran_asas')
                     ->first();

        // If not sandbox user or no Geran record → block
        if (!$geran) return false;

        // Only allow withdrawal if Geran Asas completed (60000 cents)
        return $geran->balance >= 60000;
    }
}

