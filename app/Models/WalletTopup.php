<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTopup extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'provider',
        'bill_code',
        'status',
        'paid_at',
        'payload',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
