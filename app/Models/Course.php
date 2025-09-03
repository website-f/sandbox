<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $fillable = ['user_id','title','provider','year'];
    public function user() { return $this->belongsTo(User::class); }
}
