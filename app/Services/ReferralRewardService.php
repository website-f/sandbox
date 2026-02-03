<?php

namespace App\Services;

use App\Models\User;
use App\Models\Referral;
use App\Models\Collection;
use App\Models\SandboxReferral;
use App\Models\Account;

class ReferralRewardService
{
    /**
     * Collection type mappings based on sandbox type
     */
    private const COLLECTION_TYPES = [
        'usahawan' => [
            'starter' => 'geran_asas',
            'secondary' => ['tabung_usahawan', 'had_pembiayaan'],
        ],
        'remaja' => [
            'starter' => 'biasiswa_pemula',
            'secondary' => ['had_biasiswa', 'dana_usahawan_muda'],
        ],
        'awam' => [
            'starter' => 'modal_pemula',
            'secondary' => ['had_pembiayaan_hutang', 'khairat_kematian'],
        ],
    ];

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
                'serial'    => $this->generateSandboxSerial($newUser),
                'position'  => $position,
            ]
        );

        // STEP 5: Process rewards based on parent's sandbox type
        $parentSandboxType = $parent->getSandboxSubtype();
        $collections = $this->ensureCollections($parent, $parentSandboxType);
        $starterType = self::COLLECTION_TYPES[$parentSandboxType]['starter'];

        // Starter Collection - add to pending if still collecting
        $directSubs = SandboxReferral::where('parent_id', $parent->id)->count();

        if ($directSubs <= 10) {
            $starter = $collections[$starterType];
            $starter->pending_balance += 6000;
            $starter->save();

            // When completing the 10th referral, move to balance
            if ($directSubs == 10) {
                $starter->balance += $starter->pending_balance; // Should be 60000
                $starter->pending_balance = 0;
                $starter->save();

                $starterName = $this->getCollectionName($starterType);
                $starter->transactions()->create([
                    'type' => 'credit',
                    'amount' => 60000,
                    'description' => "{$starterName} completed (10 referrals)",
                ]);
            }
        }

        // Secondary collections split
        $secondaryTypes = self::COLLECTION_TYPES[$parentSandboxType]['secondary'];
        $this->splitReward($collections, $secondaryTypes, 2000, "Sandbox reward from {$newUser->name}");

        // Reward upline if any
        $upline = $parent->referrer ?? null;
        if ($upline) {
            $uplineSandboxType = $upline->getSandboxSubtype();
            $uCollections = $this->ensureCollections($upline, $uplineSandboxType);
            $uplineSecondaryTypes = self::COLLECTION_TYPES[$uplineSandboxType]['secondary'];
            $this->splitReward($uCollections, $uplineSecondaryTypes, 2000, "Upline reward from {$newUser->name}");
        }
    }

    /**
     * Generate sandbox serial based on user's sandbox type
     */
    private function generateSandboxSerial(User $user): string
    {
        $sandboxType = $user->sandbox_type ?? 'usahawan';
        $prefix = match($sandboxType) {
            'remaja' => 'SR',
            'awam' => 'SA',
            default => 'SB',
        };
        return $prefix . now()->format('ymd') . $user->id;
    }

    /**
     * Get human readable collection name
     */
    private function getCollectionName(string $type): string
    {
        return match($type) {
            'geran_asas' => 'Geran Asas',
            'biasiswa_pemula' => 'Biasiswa Pemula',
            'modal_pemula' => 'Modal Pemula',
            default => ucfirst(str_replace('_', ' ', $type)),
        };
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
     * Ensure collections exist for a user based on their sandbox type.
     */
    private function ensureCollections(User $u, string $sandboxType): array
    {
        $config = self::COLLECTION_TYPES[$sandboxType] ?? self::COLLECTION_TYPES['usahawan'];

        $collections = [];

        // Starter collection
        $starterType = $config['starter'];
        $collections[$starterType] = Collection::firstOrCreate(
            ['user_id' => $u->id, 'type' => $starterType],
            ['balance' => 0, 'pending_balance' => 0, 'limit' => 60000]
        );

        // Secondary collections
        foreach ($config['secondary'] as $type) {
            $collections[$type] = Collection::firstOrCreate(
                ['user_id' => $u->id, 'type' => $type],
                ['balance' => 0, 'limit' => 50000000]
            );
        }

        return $collections;
    }

    /**
     * Split reward equally between two secondary collections.
     */
    private function splitReward(array $collections, array $secondaryTypes, int $amount, string $desc): void
    {
        $half = (int) ($amount / 2);

        foreach ($secondaryTypes as $type) {
            if (isset($collections[$type])) {
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
}
