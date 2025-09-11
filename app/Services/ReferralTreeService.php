<?php

// app/Services/ReferralTreeService.php
namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Str;

class ReferralTreeService
{
    public function generateRefCode(User $user): string {
        $code = Str::upper(Str::random(8));
        while (Referral::where('ref_code',$code)->exists()){
            $code = Str::upper(Str::random(8));
        }
        return $code;
    }

    /**
     * Attach $newUser directly under $referrer.
     * Unlimited children, unlimited levels.
     */
    public function attach(User $referrer, User $newUser): ?Referral
    {
        $referrerReferral = $referrer->referral;

        if (!$referrerReferral) {
            // if referrer has no referral record, make them root
            $referrerReferral = Referral::create([
                'user_id' => $referrer->id,
                'parent_id' => null,
                'root_id'   => $referrer->id,
                'level'     => 1,
                'direct_children' => 0, // optional now
                'ref_code'  => $this->generateRefCode($referrer),
            ]);
        }

        $placementLevel = $referrerReferral->level + 1;

        $newReferral = Referral::create([
            'user_id' => $newUser->id,
            'parent_id' => $referrer->id,
            'root_id'   => $referrerReferral->root_id ?? $referrer->id,
            'level'     => $placementLevel,
            'direct_children' => 0, // optional
            'ref_code'  => $this->generateRefCode($newUser),
        ]);

        // if you still want to track child count, increment
        $referrerReferral->increment('direct_children');

        return $newReferral;
    }
}
