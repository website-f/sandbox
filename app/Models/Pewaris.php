<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pewaris extends Model
{
    use HasFactory;

    protected $table = 'pewaris';

    protected $fillable = [
        'user_id',          // Owner user
        'linked_user_id',   // Automatically created linked user
        'name',
        'relationship',
        'phone',
        'email',
        'address',
    ];

    // Owner (who added this pewaris)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Linked user (if account was created)
    public function linkedUser()
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }
}
