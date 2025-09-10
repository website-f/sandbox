<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = ['user_id', 'balance'];

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
}

