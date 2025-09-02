<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['user_id','plan','amount','status','starts_at','ends_at','provider','provider_ref','meta'];
  protected $casts = ['meta'=>'array','starts_at'=>'datetime','ends_at'=>'datetime'];
  public function user(){ return $this->belongsTo(User::class); }
  public function payment(){ return $this->hasOne(Payment::class); }
}
