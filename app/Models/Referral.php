<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['user_id','parent_id','root_id','level','direct_children','ref_code'];
  public function children() {
    return $this->hasMany(Referral::class, 'parent_id', 'user_id');
}

public function parent()
{
    return $this->belongsTo(User::class, 'parent_id', 'id');
}


public function root() {
    return $this->belongsTo(User::class, 'root_id', 'user_id');
}



public function user() {
    return $this->belongsTo(User::class);
}

// in App\Models\Referral.php
public function rootUser()
{
    $current = $this->user; // start from the current user
    while ($current->referral && $current->referral->parent) {
        $current = $current->referral->parent;
    }
    return $current;
}

}
