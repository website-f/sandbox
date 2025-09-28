<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['user_id', 'type', 'active', 'expires_at', 'serial_number', 'account_type_id'];
    protected $casts = ['active' => 'boolean', 'expires_at' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public static function generateSerial($type)
    {
        $prefix = $type === 'rizqmall' ? 'RM' : 'SB';
        $today  = Carbon::now()->format('ymd');

        $lastSerial = self::where('type', $type)
            ->where('serial_number', 'like', $prefix . $today . '%')
            ->orderBy('serial_number', 'desc')
            ->value('serial_number');

        $number = $lastSerial ? intval(substr($lastSerial, -2)) + 1 : 1;

        return $prefix . $today . str_pad($number, 2, '0', STR_PAD_LEFT);
    }
}
