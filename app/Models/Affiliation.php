<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affiliation extends Model
{
    protected $table = 'affiliations';
    protected $fillable = ['user_id','organization','position'];
    public function user() { return $this->belongsTo(User::class); }
}
