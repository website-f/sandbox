<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = 'businesses';
    protected $fillable = [
        'user_id','company_name','ssm_no','business_address','industry','main_products_services','business_model','achievements'
    ];
    public function user() { return $this->belongsTo(User::class); }
}
