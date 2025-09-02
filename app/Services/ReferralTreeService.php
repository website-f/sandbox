<?php

// app/Services/ReferralTreeService.php
namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Str;

class ReferralTreeService
{
    const MAX_LEVEL = 7;
    const MAX_CHILDREN = 10;

    public function generateRefCode(User $user): string {
        $code = Str::upper(Str::random(8));
        while (Referral::where('ref_code',$code)->exists()){
            $code = Str::upper(Str::random(8));
        }
        return $code;
    }

    /**
     * Place $newUser under the network of $referrer.
     * Breadth-first search for first node with <10 children, up to level 7.
     * If no slot, returns null (user not attached).
     */
    public function attach(User $referrer, User $newUser): ?Referral
    {
        $rootReferral = $referrer->referral;
        if (!$rootReferral) {
            // If referrer has no referral record yet, make them a root at level 1.
            $rootReferral = Referral::create([
                'user_id' => $referrer->id,
                'parent_id' => null,
                'root_id' => $referrer->id,
                'level' => 1,
                'direct_children' => 0,
                'ref_code' => $this->generateRefCode($referrer),
            ]);
        }

        // BFS queue of [user_id, level]
        $queue = [[$rootReferral->user_id, 1]];

        while (!empty($queue)) {
            [$currentUserId, $level] = array_shift($queue);
            if ($level >= self::MAX_LEVEL) continue;

            $currentRef = Referral::where('user_id',$currentUserId)->first();
            if ($currentRef && $currentRef->direct_children < self::MAX_CHILDREN) {
                // attach here
                $placementLevel = $level + 1;
                $rec = Referral::create([
                    'user_id' => $newUser->id,
                    'parent_id' => $currentUserId,
                    'root_id'   => $rootReferral->root_id ?? $referrer->id,
                    'level'     => $placementLevel,
                    'direct_children' => 0,
                    'ref_code'  => $this->generateRefCode($newUser),
                ]);
                // increment parent child count
                $currentRef->increment('direct_children');
                return $rec;
            }

            // enqueue children of current node
            $children = Referral::where('parent_id', $currentUserId)->pluck('user_id')->all();
            foreach ($children as $childUserId) {
                $queue[] = [$childUserId, $level + 1];
            }
        }
        return null; // no available slot within 7 levels
    }
}
