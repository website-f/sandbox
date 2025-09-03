<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'educations';
     protected $fillable = ['user_id','primary','secondary','higher','skills_training'];
    public function user() { return $this->belongsTo(User::class); }
}
