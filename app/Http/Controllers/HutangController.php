<?php

namespace App\Http\Controllers;

use App\Models\Hutang;
use App\Models\HutangDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class HutangController extends Controller
{
    /**
     * Ensure user is Sandbox Awam type
     */
    private function ensureAwamUser()
    {
        $user = Auth::user();
        if (!$user || $user->getSandboxSubtype() !== 'awam') {
            abort(403, 'This feature is only available for Sandbox Awam accounts.');
        }
        return $user;
    }

    /**
     * Display hutang list page
     */
    public function index(Request $request)
    {
        $user = $this->ensureAwamUser();

        $query = Hutang::where('user_id', $user->id)
            ->with('documents')
            ->orderBy('hutang_date', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'settled') {
                $query->settled();
            } elseif ($request->status === 'unsettled') {
                $query->unsettled();
            }
        }

        $hutangList = $query->paginate(10);

        // Calculate totals
        $totalHutang = Hutang::getTotalForUser($user->id);
        $unsettledTotal = Hutang::getUnsettledTotalForUser($user->id);
        $settledTotal = $totalHutang - $unsettledTotal;
        $remainingLimit = Hutang::getRemainingLimit($user->id);

        return view('wallet.hutang.index', compact(
            'hutangList',
            'totalHutang',
            'unsettledTotal',
            'settledTotal',
            'remainingLimit'
        ));
    }

    /**
     * Store a new hutang record
     */
    public function store(Request $request)
    {
        $user = $this->ensureAwamUser();

        // Validate input
        $validated = $request->validate([
            'hutang_date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01|max:500000',
            'notes' => 'nullable|string|max:1000',
            'documents' => 'nullable|array|max:5',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,gif|max:5120', // 5MB max per file
        ], [
            'hutang_date.required' => 'Tarikh hutang diperlukan / Date is required',
            'description.required' => 'Penerangan diperlukan / Description is required',
            'amount.required' => 'Jumlah diperlukan / Amount is required',
            'amount.max' => 'Jumlah maksimum RM500,000 / Maximum amount is RM500,000',
            'documents.*.max' => 'Saiz fail maksimum 5MB / Maximum file size is 5MB',
        ]);

        // Convert amount to cents
        $amountInCents = (int) round($validated['amount'] * 100);

        // Validate date is before registration
        if (!Hutang::isDateValid($user->id, $validated['hutang_date'])) {
            return back()->withErrors([
                'hutang_date' => 'Tarikh hutang mesti sebelum tarikh pendaftaran anda (' . $user->created_at->format('d/m/Y') . ') / Hutang date must be before your registration date',
            ])->withInput();
        }

        // Check if adding this amount exceeds the limit
        if (!Hutang::canAddHutang($user->id, $amountInCents)) {
            $remainingLimit = Hutang::getRemainingLimit($user->id);
            return back()->withErrors([
                'amount' => 'Jumlah hutang melebihi had RM500,000. Baki yang boleh ditambah: RM' . number_format($remainingLimit / 100, 2) . ' / Total hutang exceeds RM500,000 limit. Remaining limit: RM' . number_format($remainingLimit / 100, 2),
            ])->withInput();
        }

        // Create hutang record
        $hutang = Hutang::create([
            'user_id' => $user->id,
            'hutang_date' => $validated['hutang_date'],
            'reference' => $validated['reference'],
            'description' => $validated['description'],
            'amount' => $amountInCents,
            'notes' => $validated['notes'],
            'is_settled' => false,
        ]);

        // Handle document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('hutang-documents/' . $user->id, 'public');

                HutangDocument::create([
                    'hutang_id' => $hutang->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('hutang.index')
            ->with('success', 'Hutang berjaya ditambah / Hutang added successfully');
    }

    /**
     * Toggle hutang settled status
     */
    public function toggleSettled(Hutang $hutang)
    {
        $user = $this->ensureAwamUser();

        // Verify ownership
        if ($hutang->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $hutang->update([
            'is_settled' => !$hutang->is_settled,
            'settled_date' => !$hutang->is_settled ? now() : null,
        ]);

        $status = $hutang->is_settled ? 'selesai / settled' : 'belum selesai / unsettled';

        return back()->with('success', "Status hutang dikemaskini kepada {$status} / Hutang status updated to {$status}");
    }

    /**
     * Delete hutang record
     */
    public function destroy(Hutang $hutang)
    {
        $user = $this->ensureAwamUser();

        // Verify ownership
        if ($hutang->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Delete associated documents from storage
        foreach ($hutang->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $hutang->delete();

        return back()->with('success', 'Hutang berjaya dipadam / Hutang deleted successfully');
    }

    /**
     * Delete a specific document
     */
    public function deleteDocument(HutangDocument $document)
    {
        $user = $this->ensureAwamUser();

        // Verify ownership through hutang
        if ($document->hutang->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Delete file from storage
        Storage::disk('public')->delete($document->file_path);

        $document->delete();

        return back()->with('success', 'Dokumen berjaya dipadam / Document deleted successfully');
    }

    /**
     * Add documents to existing hutang
     */
    public function addDocuments(Request $request, Hutang $hutang)
    {
        $user = $this->ensureAwamUser();

        // Verify ownership
        if ($hutang->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'documents' => 'required|array|min:1|max:5',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,gif|max:5120',
        ]);

        foreach ($request->file('documents') as $file) {
            $path = $file->store('hutang-documents/' . $user->id, 'public');

            HutangDocument::create([
                'hutang_id' => $hutang->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return back()->with('success', 'Dokumen berjaya ditambah / Documents added successfully');
    }
}
