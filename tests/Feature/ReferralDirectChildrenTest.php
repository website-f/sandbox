<?php

namespace Tests\Feature;

use App\Models\Referral;
use App\Models\User;
use App\Services\ReferralTreeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReferralDirectChildrenTest extends TestCase
{
    use RefreshDatabase;

    public function test_attach_resyncs_direct_children_to_actual_child_count(): void
    {
        $referrer = User::factory()->create();
        $existingChild = User::factory()->create();
        $newChild = User::factory()->create();

        Referral::create([
            'user_id' => $referrer->id,
            'parent_id' => null,
            'root_id' => $referrer->id,
            'level' => 1,
            'direct_children' => 255,
            'ref_code' => $this->refCode(),
        ]);

        Referral::create([
            'user_id' => $existingChild->id,
            'parent_id' => $referrer->id,
            'root_id' => $referrer->id,
            'level' => 2,
            'direct_children' => 0,
            'ref_code' => $this->refCode(),
        ]);

        app(ReferralTreeService::class)->attach($referrer, $newChild);

        $this->assertDatabaseHas('referrals', [
            'user_id' => $referrer->id,
            'direct_children' => 2,
        ]);
    }

    public function test_reparenting_a_referral_resyncs_both_parent_counts(): void
    {
        $oldParent = User::factory()->create();
        $newParent = User::factory()->create();
        $child = User::factory()->create();

        Referral::create([
            'user_id' => $oldParent->id,
            'parent_id' => null,
            'root_id' => $oldParent->id,
            'level' => 1,
            'direct_children' => 99,
            'ref_code' => $this->refCode(),
        ]);

        Referral::create([
            'user_id' => $newParent->id,
            'parent_id' => null,
            'root_id' => $newParent->id,
            'level' => 1,
            'direct_children' => 42,
            'ref_code' => $this->refCode(),
        ]);

        $childReferral = Referral::create([
            'user_id' => $child->id,
            'parent_id' => $oldParent->id,
            'root_id' => $oldParent->id,
            'level' => 2,
            'direct_children' => 0,
            'ref_code' => $this->refCode(),
        ]);

        $childReferral->update([
            'parent_id' => $newParent->id,
            'root_id' => $newParent->id,
            'level' => 2,
        ]);

        $this->assertDatabaseHas('referrals', [
            'user_id' => $oldParent->id,
            'direct_children' => 0,
        ]);

        $this->assertDatabaseHas('referrals', [
            'user_id' => $newParent->id,
            'direct_children' => 1,
        ]);
    }

    public function test_link_member_to_vendor_resyncs_vendor_child_count(): void
    {
        config(['services.rizqmall.api_key' => 'test-api-key']);

        $vendor = User::factory()->create();
        $member = User::factory()->create();

        Referral::create([
            'user_id' => $vendor->id,
            'parent_id' => null,
            'root_id' => $vendor->id,
            'level' => 1,
            'direct_children' => 255,
            'ref_code' => $this->refCode(),
        ]);

        Referral::create([
            'user_id' => $member->id,
            'parent_id' => null,
            'root_id' => null,
            'level' => 1,
            'direct_children' => 0,
            'ref_code' => $this->refCode(),
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => 'test-api-key',
        ])->postJson('/api/rizqmall/link-member', [
            'vendor_user_id' => $vendor->id,
            'member_user_id' => $member->id,
            'store_id' => 123,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('referrals', [
            'user_id' => $member->id,
            'parent_id' => $vendor->id,
            'level' => 2,
        ]);

        $this->assertDatabaseHas('referrals', [
            'user_id' => $vendor->id,
            'direct_children' => 1,
        ]);
    }

    private function refCode(): string
    {
        return Str::upper(Str::random(8));
    }
}
