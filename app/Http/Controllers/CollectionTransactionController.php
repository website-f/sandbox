<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Collection;
use App\Models\CollectionTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CollectionTransactionController extends Controller
{
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'collection_type' => 'required|in:geran_asas,tabung_usahawan,had_pembiayaan',
            'transaction_type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'admin_notes' => 'nullable|string|max:1000',
            'transaction_date' => 'required|date',
            'slip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        // Convert amount to cents
        $amountInCents = (int)($validated['amount'] * 100);

        // Find the collection
        $collection = Collection::where('user_id', $user->id)
            ->where('type', $validated['collection_type'])
            ->firstOrFail();

        DB::beginTransaction();
        
        try {
            // Handle file upload
            $slipPath = null;
            if ($request->hasFile('slip')) {
                $slipPath = $request->file('slip')->store('collection-slips', 'public');
            }

            // Check if debit and has sufficient balance
            if ($validated['transaction_type'] === 'debit') {
                if ($collection->balance < $amountInCents) {
                    return back()->with('error', 'Insufficient balance in collection');
                }
                $collection->balance -= $amountInCents;
            } else {
                // Credit transaction
                $collection->balance += $amountInCents;
            }

            $collection->save();

            // Create transaction record
            CollectionTransaction::create([
                'collection_id' => $collection->id,
                'type' => $validated['transaction_type'],
                'amount' => $amountInCents,
                'description' => $validated['description'],
                'slip_path' => $slipPath,
                'transaction_date' => $validated['transaction_date'],
                'admin_notes' => $validated['admin_notes'],
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Transaction added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if transaction failed
            if ($slipPath) {
                Storage::disk('public')->delete($slipPath);
            }
            
            return back()->with('error', 'Failed to add transaction: ' . $e->getMessage());
        }
    }

public function destroy(CollectionTransaction $transaction)
{
    DB::beginTransaction();
    
    try {
        // Load the collection relationship
        $collection = $transaction->collection;
        
        // Check if collection exists
        if (!$collection) {
            throw new \Exception('Collection not found for this transaction');
        }
        
        // Reverse the transaction
        if ($transaction->type === 'credit') {
            $collection->balance -= $transaction->amount;
        } else {
            $collection->balance += $transaction->amount;
        }
        
        // Prevent negative balance
        if ($collection->balance < 0) {
            throw new \Exception('Cannot delete transaction: would result in negative balance');
        }
        
        $collection->save();
        
        // Delete slip file if exists
        if ($transaction->slip_path) {
            Storage::disk('public')->delete($transaction->slip_path);
        }
        
        $transaction->delete();
        
        DB::commit();
        
        return back()->with('success', 'Transaction deleted successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
    }
}
}