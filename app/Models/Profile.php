<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id','full_name','nric','dob','home_address','phone', 'country', 'state', 'city', 'email_alt','photo_path'
    ];
    public function user() { return $this->belongsTo(User::class); }
}
