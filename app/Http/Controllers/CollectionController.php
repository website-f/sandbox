<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Show the user's sandbox collections based on their sandbox type.
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's sandbox type (usahawan, remaja, awam)
        $sandboxType = $user->getSandboxSubtype();

        // Map sandbox type to collection account type
        $collectionAccountType = match($sandboxType) {
            'remaja' => CollectionType::ACCOUNT_SANDBOX_REMAJA,
            'awam' => CollectionType::ACCOUNT_SANDBOX_AWAM,
            default => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
        };

        // Get collection types from database for this account type
        $collectionTypes = CollectionType::forAccountType($collectionAccountType);

        $collections = [];

        foreach ($collectionTypes as $collectionType) {
            $collections[$collectionType->code] = Collection::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'type'    => $collectionType->code,
                ],
                [
                    'collection_type_id' => $collectionType->id,
                    'balance'            => 0,
                    'pending_balance'    => 0,
                    'limit'              => $collectionType->limit,
                    'is_redeemed'        => false,
                ]
            );
        }

        // Load transactions for each collection (latest first)
        foreach ($collections as $col) {
            $col->load(['transactions' => function ($q) {
                $q->latest();
            }]);
        }

        return view('wallet.collection', [
            'collections' => $collections,
            'sandboxType' => $sandboxType,
        ]);
    }
}
