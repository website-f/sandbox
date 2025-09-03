<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NextOfKin extends Model
{
    protected $table = 'next_of_kins';
    protected $fillable = ['user_id','name','relationship','phone','address'];
    public function user() { return $this->belongsTo(User::class); }
}
