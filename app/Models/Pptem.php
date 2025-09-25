<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pptem extends Model
{
    protected $fillable = [
        'user_id','pptem_number','pptem_ref','expire_date'
    ];
    public function user() { return $this->belongsTo(User::class); }
}
