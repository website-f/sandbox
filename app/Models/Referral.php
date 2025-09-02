<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['user_id','parent_id','root_id','level','direct_children','ref_code'];
  public function user(){ return $this->belongsTo(User::class); }
  public function parent(){ return $this->belongsTo(User::class,'parent_id'); }
  public function root(){ return $this->belongsTo(User::class,'root_id'); }
}
