<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionTransaction extends Model
{
    protected $fillable = ['collection_id','type','amount','description','subscription_id'];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}

