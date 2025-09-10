<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['user_id','type','active','expires_at','serial_number'];
    protected $casts = ['active'=>'boolean','expires_at'=>'date'];
    public function user(){ return $this->belongsTo(User::class); }
    public static function generateSerial($type)
    {
        $prefix = $type === 'rizqmall' ? 'RM' : 'SB';
        $today  = Carbon::now()->format('ymd'); // e.g. 250910

        // Find last serial for this plan today
        $lastSerial = self::where('type', $type)
            ->where('serial_number', 'like', $prefix . $today . '%')
            ->orderBy('serial_number', 'desc')
            ->value('serial_number');

        if ($lastSerial) {
            // Extract last 2 digits (running number)
            $lastNumber = intval(substr($lastSerial, -2));
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }

        return $prefix . $today . str_pad($number, 2, '0', STR_PAD_LEFT);
    }
}
