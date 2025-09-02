<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['user_id','type','active','expires_at'];
  protected $casts = ['active'=>'boolean','expires_at'=>'date'];
  public function user(){ return $this->belongsTo(User::class); }
}
