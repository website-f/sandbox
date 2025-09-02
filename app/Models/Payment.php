<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['subscription_id','provider','bill_code','status','amount','paid_at','payload'];
  protected $casts = ['payload'=>'array','paid_at'=>'datetime'];
  public function subscription(){ return $this->belongsTo(Subscription::class); }
}
