<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\Profile;
use App\Models\Referral;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\ReferralTreeService;

class UserApiController extends Controller
{
    /**
     * Create user in Sandbox when they register in RizqMall
     * This ensures unified account management across both platforms
     */
    public function createFromRizqmall(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'rizqmall_user_id' => 'required|integer', // ID from RizqMall database
            ]);

            Log::info('Creating Sandbox user from RizqMall registration', [
                'email' => $request->email,
                'rizqmall_user_id' => $request->rizqmall_user_id,
            ]);

            // Check if user already exists
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                Log::warning('User already exists in Sandbox', [
                    'email' => $request->email,
                    'user_id' => $existingUser->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'User already exists',
                    'user' => [
                        'id' => $existingUser->id,
                        'email' => $existingUser->email,
                        'name' => $existingUser->name,
                    ],
                ], 200);
            }

            // Create user in Sandbox
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => null, // No password for RizqMall-originated users initially
            ]);

            // Create profile
            Profile::create([
                'user_id' => $user->id,
                'full_name' => $request->name,
                'phone' => $request->phone,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
            ]);

            // Assign default role: Entrepreneur
            $role = Role::where('name', 'Entrepreneur')->first();
            if ($role) {
                $user->roles()->attach($role);
            }

            // Create inactive accounts for both platforms
            $accountTypes = AccountType::whereIn('name', ['rizqmall', 'sandbox'])->get()->keyBy('name');

            foreach (['rizqmall', 'sandbox'] as $type) {
                Account::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'account_type_id' => $accountTypes[$type]->id ?? null,
                    'active' => false,
                ]);
            }

            // Create referral record (no parent for RizqMall-originated users)
            $tree = app(ReferralTreeService::class);
            $code = $tree->generateRefCode($user);
            Referral::create([
                'user_id' => $user->id,
                'parent_id' => null,
                'root_id' => null,
                'level' => 1,
                'direct_children' => 0,
                'ref_code' => $code,
            ]);

            Log::info('Sandbox user created successfully from RizqMall', [
                'sandbox_user_id' => $user->id,
                'rizqmall_user_id' => $request->rizqmall_user_id,
                'email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully in Sandbox',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'referral_code' => $code,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for RizqMall user creation', [
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create Sandbox user from RizqMall', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user in Sandbox',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Find user by email (for linking existing accounts)
     */
    public function findByEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            Log::info('User found by email', [
                'email' => $request->email,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to find user by email', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error finding user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Link a RizqMall customer as a member under a vendor in the referral system
     * This creates a parent-child relationship in the unlimited referral tree
     */
    public function linkMemberToVendor(Request $request)
    {
        try {
            $request->validate([
                'vendor_user_id' => 'required|integer|exists:users,id',
                'member_user_id' => 'required|integer|exists:users,id',
                'store_id' => 'required|integer',
            ]);

            Log::info('Linking member to vendor in referral system', [
                'vendor_user_id' => $request->vendor_user_id,
                'member_user_id' => $request->member_user_id,
                'store_id' => $request->store_id,
            ]);

            $vendor = User::find($request->vendor_user_id);
            $member = User::find($request->member_user_id);

            if (!$vendor || !$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor or member not found',
                ], 404);
            }

            // Check if member already has referral record with a parent
            $existingReferral = Referral::where('user_id', $member->id)->first();

            if ($existingReferral && $existingReferral->parent_id) {
                // Already linked to a vendor/parent
                Log::info('Member already has a parent in referral system', [
                    'member_id' => $member->id,
                    'existing_parent_id' => $existingReferral->parent_id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Member already has a referral parent',
                    'referral_id' => $existingReferral->id,
                    'already_linked' => true,
                ]);
            }

            // Get or create vendor's referral record
            $tree = app(ReferralTreeService::class);
            $vendorReferral = Referral::where('user_id', $vendor->id)->first();

            if (!$vendorReferral) {
                // Create referral record for vendor (they become a root)
                $vendorReferral = Referral::create([
                    'user_id' => $vendor->id,
                    'parent_id' => null,
                    'root_id' => $vendor->id,
                    'level' => 1,
                    'direct_children' => 0,
                    'ref_code' => $tree->generateRefCode($vendor),
                ]);
            }

            // Link member under vendor
            if ($existingReferral) {
                // Update existing referral to link to vendor
                $existingReferral->parent_id = $vendor->id;
                $existingReferral->root_id = $vendorReferral->root_id ?? $vendor->id;
                $existingReferral->level = $vendorReferral->level + 1;
                $existingReferral->save();

                $vendorReferral->increment('direct_children');

                Log::info('Updated existing referral to link to vendor', [
                    'referral_id' => $existingReferral->id,
                    'member_id' => $member->id,
                    'vendor_id' => $vendor->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Member linked to vendor successfully',
                    'referral_id' => $existingReferral->id,
                ]);
            }

            // Create new referral record for member under vendor
            $memberReferral = Referral::create([
                'user_id' => $member->id,
                'parent_id' => $vendor->id,
                'root_id' => $vendorReferral->root_id ?? $vendor->id,
                'level' => $vendorReferral->level + 1,
                'direct_children' => 0,
                'ref_code' => $tree->generateRefCode($member),
            ]);

            $vendorReferral->increment('direct_children');

            Log::info('Member linked to vendor in referral system', [
                'referral_id' => $memberReferral->id,
                'member_id' => $member->id,
                'vendor_id' => $vendor->id,
                'level' => $memberReferral->level,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Member linked to vendor successfully',
                'referral_id' => $memberReferral->id,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for link member request', [
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to link member to vendor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to link member to vendor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
