<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Show the user's sandbox collection (tabung).
     */
    public function index()
    {
        $user = Auth::user();

        // Get or create the collection for this user
        $collection = Collection::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        // Load transactions (latest first)
        $collection->load(['transactions' => function ($q) {
            $q->latest();
        }]);

        return view('wallet.collection', compact('collection'));
    }
}
