<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $fillable = [
        'user_id','bank_name','account_number','account_holder'
    ];
    public function user() { return $this->belongsTo(User::class); }
}
