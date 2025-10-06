<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionTransaction extends Model
{
    protected $fillable = [
        'collection_id',
        'type',
        'amount',
        'description',
        'subscription_id',
        'slip_path',
        'transaction_date',
        'admin_notes',
        'created_by'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount / 100, 2);
    }
}