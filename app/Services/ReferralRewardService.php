<?php

namespace App\Services;

use App\Models\User;
use App\Models\Referral;
use App\Models\Collection;
use App\Models\SandboxReferral;

class ReferralRewardService
{
    /**
     * Process referral rewards for a new sandbox subscription.
     */
   public function processSandboxRewards(User $newUser)
{
    $referral = $newUser->referral;
    if (!$referral) return;

    // STEP 1: Find root for BFS
    $root = $referral->user;
    while ($root->referral && $root->referral->parent) {
        $root = $root->referral->parent;
    }

    // STEP 2: Find available parent under root
    $parent = $this->findAvailableSandboxParent($root);

    // STEP 3: Determine position (1–10)
    $position = SandboxReferral::where('parent_id', $parent->id)->count() + 1;

    // STEP 4: Insert or update sandbox_referrals table
    $sandboxReferral = SandboxReferral::updateOrCreate(
        ['user_id' => $newUser->id],
        [
            'parent_id' => $parent->id,
            'root_id'   => $root->id,
            'serial'    => 'SB' . now()->format('ymd') . $newUser->id,
            'position'  => $position,
        ]
    );

    // STEP 5: Continue reward calculation using $parent
    $collections = $this->ensureCollections($parent);

    // Geran Asas only if parent has <=10 direct sandbox referrals
    $directSubs = SandboxReferral::where('parent_id', $parent->id)->count();
    if ($directSubs <= 10) {
        $geran = $collections['geran_asas'];
        $geran->pending_balance += 6000;
        $geran->save();

        if ($directSubs == 10 && $geran->pending_balance >= 60000) {
            $geran->balance += $geran->pending_balance;
            $geran->pending_balance = 0;
            $geran->save();
            $geran->transactions()->create([
                'type' => 'credit',
                'amount' => 60000,
                'description' => 'Geran Asas completed (10 referrals)',
            ]);
        }
    }

    // Tabung Usahawan & Had Pembiayaan split
    $this->splitReward($collections, 2000, "Sandbox reward from {$newUser->name}");

    // Reward upline if any
    $upline = $parent->referrer ?? null;
    if ($upline) {
        $uCollections = $this->ensureCollections($upline);
        $this->splitReward($uCollections, 2000, "Upline reward from {$newUser->name}");
    }
}

    /**
     * Find a new parent under the current parent for sandbox overflow
     */
    private function findAvailableSandboxParent(User $root)
{
    $queue = [$root];

    while (!empty($queue)) {
        $current = array_shift($queue);

        // Count direct sandbox children
        $count = SandboxReferral::where('parent_id', $current->id)->count();

        if ($count < 10) {
            return $current;
        }

        // Add children to queue
        $children = SandboxReferral::where('parent_id', $current->id)->get();
        foreach ($children as $child) {
            $queue[] = User::find($child->user_id);
        }
    }

    return $root;
}



    /**
     * Ensure all 3 tabung collections exist for a user.
     */
    private function ensureCollections(User $u): array
    {
        return [
            'geran_asas' => Collection::firstOrCreate(
                ['user_id' => $u->id, 'type' => 'geran_asas'],
                ['balance' => 0, 'pending_balance' => 0, 'limit' => 60000]
            ),
            'tabung_usahawan' => Collection::firstOrCreate(
                ['user_id' => $u->id, 'type' => 'tabung_usahawan'],
                ['balance' => 0, 'limit' => 50000000]
            ),
            'had_pembiayaan' => Collection::firstOrCreate(
                ['user_id' => $u->id, 'type' => 'had_pembiayaan'],
                ['balance' => 0, 'limit' => 50000000]
            ),
        ];
    }

    /**
     * Split reward equally between Tabung Usahawan and Had Pembiayaan.
     */
    private function splitReward(array $collections, int $amount, string $desc): void
    {
        $half = (int) ($amount / 2);

        foreach (['tabung_usahawan', 'had_pembiayaan'] as $type) {
            $c = $collections[$type];
            if ($c->balance + $half <= $c->limit) {
                $c->balance += $half;
                $c->save();
                $c->transactions()->create([
                    'type' => 'credit',
                    'amount' => $half,
                    'description' => $desc,
                ]);
            }
        }
    }
}
