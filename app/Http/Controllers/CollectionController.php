<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Show the user's sandbox collections (3 tabungs).
     */
    public function index()
{
    $user = Auth::user();

    // Ensure all 3 collections exist
    $types = ['geran_asas', 'tabung_usahawan', 'had_pembiayaan'];
    $collections = [];

    foreach ($types as $type) {
        $collections[$type] = Collection::firstOrCreate(
            [
                'user_id' => $user->id,
                'type'    => $type,   // ✅ include type in search
            ],
            [
                'balance'          => 0,
                'limit'            => null,
                'pending_balance'  => 0,
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
    ]);
}

}
