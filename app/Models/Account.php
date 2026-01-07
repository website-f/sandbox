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

    /**
     * Generate unique serial number for account
     * Format: PREFIX + YYMMDD + 4-digit running number
     * Example: RM2601070001, SB2601070001
     */
    public static function generateSerial($type)
    {
        $prefix = $type === 'rizqmall' ? 'RM' : 'SB';
        $today  = Carbon::now()->format('ymd'); // YYMMDD format

        // Find the highest serial number for this type and date
        $lastSerial = self::where('type', $type)
            ->where('serial_number', 'like', $prefix . $today . '%')
            ->orderByRaw('CAST(SUBSTRING(serial_number, -4) AS UNSIGNED) DESC')
            ->value('serial_number');

        // Extract the running number (last 4 digits) and increment
        if ($lastSerial) {
            $number = intval(substr($lastSerial, -4)) + 1;
        } else {
            $number = 1;
        }

        // Format: PREFIX + YYMMDD + 4-digit padded number
        return $prefix . $today . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique serial number with database lock to prevent duplicates
     */
    public static function generateUniqueSerial($type)
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $serial = self::generateSerial($type);
            $exists = self::where('serial_number', $serial)->exists();
            $attempt++;
        } while ($exists && $attempt < $maxAttempts);

        if ($exists) {
            // Fallback: add timestamp suffix
            $serial = $serial . Carbon::now()->format('is'); // seconds + microseconds
        }

        return $serial;
    }
}
