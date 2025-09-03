<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['user_id','parent_id','root_id','level','direct_children','ref_code'];
  public function children() {
    return $this->hasMany(Referral::class, 'parent_id', 'user_id');
}

public function parent() {
    return $this->belongsTo(Referral::class, 'parent_id', 'user_id');
}

public function root() {
    return $this->belongsTo(Referral::class, 'root_id', 'user_id');
}



public function user() {
    return $this->belongsTo(User::class);
}
}
