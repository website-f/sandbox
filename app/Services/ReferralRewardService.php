<?php

namespace App\Services;

use App\Models\User;
use App\Models\Referral;
use App\Models\Collection;
use App\Models\SandboxReferral;
use App\Models\Account;
use Illuminate\Support\Facades\Log;

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

    private const STARTER_REWARD_PER_REFERRAL = 6000; // RM60 in cents
    private const STARTER_TARGET = 60000; // RM600 in cents (10 referrals)
    private const SECONDARY_REWARD_TOTAL = 2000; // RM20 in cents, split between 2 collections
    private const MAX_CHILDREN_PER_PARENT = 10;

    /**
     * Process referral rewards for a new sandbox subscription.
     */
    public function processSandboxRewards(User $newUser)
    {
        $referral = $newUser->referral;
        if (!$referral) return;

        // STEP 1: Find root by walking up the Referral tree
        $root = $this->findTreeRoot($referral);
        if (!$root) return;

        // STEP 2: Find available parent under root (BFS, max 10 children)
        $parent = $this->findAvailableSandboxParent($root);

        // STEP 3: Determine position (1-10)
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

        if ($directSubs <= self::MAX_CHILDREN_PER_PARENT) {
            $starter = $collections[$starterType];
            $starter->pending_balance += self::STARTER_REWARD_PER_REFERRAL;
            $starter->save();

            // When completing the 10th referral, move to balance
            if ($directSubs == self::MAX_CHILDREN_PER_PARENT) {
                $expectedPending = self::STARTER_TARGET;

                // Defensive check: log warning if pending doesn't match expected
                if ($starter->pending_balance != $expectedPending) {
                    Log::warning('Starter pending_balance mismatch', [
                        'user_id' => $parent->id,
                        'expected' => $expectedPending,
                        'actual' => $starter->pending_balance,
                        'collection_type' => $starterType,
                    ]);
                }

                // Use the actual pending_balance (it should be correct)
                $transferAmount = $starter->pending_balance;
                $starter->balance += $transferAmount;
                $starter->pending_balance = 0;
                $starter->save();

                $starterName = $this->getCollectionName($starterType);
                $starter->transactions()->create([
                    'type' => 'credit',
                    'amount' => $transferAmount,
                    'description' => "{$starterName} completed ({$directSubs} referrals)",
                ]);
            }
        }

        // Secondary collections split
        $secondaryTypes = self::COLLECTION_TYPES[$parentSandboxType]['secondary'];
        $this->splitReward($collections, $secondaryTypes, self::SECONDARY_REWARD_TOTAL, "Sandbox reward from {$newUser->name}");

        // Reward upline (parent's referrer from the Referral tree)
        $upline = $this->findUpline($parent);
        if ($upline) {
            $uplineSandboxType = $upline->getSandboxSubtype();
            $uCollections = $this->ensureCollections($upline, $uplineSandboxType);
            $uplineSecondaryTypes = self::COLLECTION_TYPES[$uplineSandboxType]['secondary'];
            $this->splitReward($uCollections, $uplineSecondaryTypes, self::SECONDARY_REWARD_TOTAL, "Upline reward from {$newUser->name}");
        }
    }

    /**
     * Find the root user by walking up the Referral tree.
     */
    private function findTreeRoot(Referral $referral): ?User
    {
        // If root_id is set, use it directly
        if ($referral->root_id) {
            return User::find($referral->root_id);
        }

        // Walk up the tree
        $current = $referral;
        while ($current->parent_id) {
            $parentReferral = Referral::where('user_id', $current->parent_id)->first();
            if (!$parentReferral) break;
            $current = $parentReferral;
        }

        return User::find($current->user_id);
    }

    /**
     * Find the upline (direct referrer) for a user from the Referral tree.
     */
    private function findUpline(User $user): ?User
    {
        $referral = Referral::where('user_id', $user->id)->first();
        if (!$referral || !$referral->parent_id) {
            return null;
        }
        return User::find($referral->parent_id);
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
     * Find a new parent under the current parent for sandbox overflow.
     * Uses BFS to find the first user with fewer than 10 children.
     */
    private function findAvailableSandboxParent(User $root)
    {
        $queue = [$root];

        while (!empty($queue)) {
            $current = array_shift($queue);

            // Count direct sandbox children
            $count = SandboxReferral::where('parent_id', $current->id)->count();

            if ($count < self::MAX_CHILDREN_PER_PARENT) {
                return $current;
            }

            // Add children to queue
            $children = SandboxReferral::where('parent_id', $current->id)->get();
            foreach ($children as $child) {
                $childUser = User::find($child->user_id);
                if ($childUser) {
                    $queue[] = $childUser;
                }
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
            ['balance' => 0, 'pending_balance' => 0, 'limit' => self::STARTER_TARGET]
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
