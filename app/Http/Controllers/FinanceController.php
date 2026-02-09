<?php

namespace App\Http\Controllers;

use App\Models\CollectionTransaction;
use App\Models\Account;
use App\Models\Payment;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('Admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Admin access only.');
        }

        $data = $this->buildFinanceData($request);

        return view('admin.finance', $data);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('Admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Admin access only.');
        }

        $data = $this->buildFinanceData($request);
        $transactions = $data['transactions'] ?? collect();

        $filename = 'finance-report-' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Date',
                'Source',
                'Direction',
                'Status',
                'Amount (RM)',
                'User',
                'Email',
                'Account Type',
                'Reference',
                'Description',
            ]);

            foreach ($transactions as $tx) {
                fputcsv($handle, [
                    optional($tx['date'])->format('Y-m-d H:i:s'),
                    $tx['source'] ?? '',
                    $tx['direction'] ?? '',
                    $tx['status'] ?? '',
                    number_format(($tx['amount'] ?? 0) / 100, 2),
                    $tx['user'] ?? '',
                    $tx['email'] ?? '',
                    $tx['account_type'] ?? '',
                    $tx['reference'] ?? '',
                    $tx['description'] ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildFinanceData(Request $request): array
    {
        $type = $request->get('type', 'all'); // all, subscription, wallet, collection
        $direction = $request->get('direction', 'all'); // all, credit, debit
        $status = $request->get('status', 'all'); // all, pending, success, failed (subscriptions)
        $from = $request->get('from');
        $to = $request->get('to');
        $query = $request->get('q');
        $accountType = $request->get('account_type', 'all');
        $minAmount = $request->get('min_amount');
        $maxAmount = $request->get('max_amount');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
        $toDate = $to ? Carbon::parse($to)->endOfDay() : null;

        $transactions = collect();

        // Subscription payments
        if ($type === 'all' || $type === 'subscription') {
            $paymentsQuery = Payment::with(['subscription.user']);

            if ($status !== 'all') {
                $paymentsQuery->where('status', $status);
            }

            if ($fromDate || $toDate) {
                $paymentsQuery->whereBetween(
                    DB::raw('COALESCE(paid_at, created_at)'),
                    [$fromDate ?? Carbon::create(1970, 1, 1), $toDate ?? now()]
                );
            }

            $payments = $paymentsQuery->get();

            foreach ($payments as $payment) {
                $subscription = $payment->subscription;
                $payer = $subscription?->user;
                $transactions->push([
                    'source' => 'Subscription',
                    'direction' => 'credit',
                    'status' => $payment->status ?? 'pending',
                    'amount' => (int) $payment->amount,
                    'date' => $payment->paid_at ?? $payment->created_at,
                    'user' => $payer?->name,
                    'email' => $payer?->email,
                    'account_type' => $payer ? $payer->getSandboxDisplayName() : '—',
                    'account_key' => $payer?->getSandboxSubtype() ?? Account::SUBTYPE_USAHAWAN,
                    'reference' => $payment->bill_code ?? ('SUB-' . $payment->subscription_id),
                    'description' => $subscription?->plan
                        ? 'Plan: ' . strtoupper($subscription->plan)
                        : 'Subscription payment',
                ]);
            }
        }

        // Wallet transactions
        if ($type === 'all' || $type === 'wallet') {
            $walletQuery = WalletTransaction::with(['wallet.user']);

            if ($direction !== 'all') {
                $walletQuery->where('type', $direction);
            }

            if ($fromDate || $toDate) {
                $walletQuery->whereBetween(
                    'created_at',
                    [$fromDate ?? Carbon::create(1970, 1, 1), $toDate ?? now()]
                );
            }

            $walletTxs = $walletQuery->get();

            foreach ($walletTxs as $tx) {
                $walletUser = $tx->wallet?->user;
                $transactions->push([
                    'source' => 'Wallet',
                    'direction' => $tx->type,
                    'status' => 'completed',
                    'amount' => (int) $tx->amount,
                    'date' => $tx->created_at,
                    'user' => $walletUser?->name,
                    'email' => $walletUser?->email,
                    'account_type' => $walletUser ? $walletUser->getSandboxDisplayName() : '—',
                    'account_key' => $walletUser?->getSandboxSubtype() ?? Account::SUBTYPE_USAHAWAN,
                    'reference' => 'WALLET-' . $tx->id,
                    'description' => $tx->description ?? 'Wallet transaction',
                ]);
            }
        }

        // Collection transactions
        if ($type === 'all' || $type === 'collection') {
            $collectionQuery = CollectionTransaction::with(['collection.user', 'creator']);

            if ($direction !== 'all') {
                $collectionQuery->where('type', $direction);
            }

            if ($fromDate || $toDate) {
                $collectionQuery->whereBetween(
                    DB::raw('COALESCE(transaction_date, created_at)'),
                    [$fromDate ?? Carbon::create(1970, 1, 1), $toDate ?? now()]
                );
            }

            $collectionTxs = $collectionQuery->get();

            foreach ($collectionTxs as $tx) {
                $collectionUser = $tx->collection?->user;
                $transactions->push([
                    'source' => 'Collection',
                    'direction' => $tx->type,
                    'status' => 'completed',
                    'amount' => (int) $tx->amount,
                    'date' => $tx->transaction_date ?? $tx->created_at,
                    'user' => $collectionUser?->name,
                    'email' => $collectionUser?->email,
                    'account_type' => $collectionUser ? $collectionUser->getSandboxDisplayName() : '—',
                    'account_key' => $collectionUser?->getSandboxSubtype() ?? Account::SUBTYPE_USAHAWAN,
                    'reference' => 'COLLECT-' . $tx->id,
                    'description' => $tx->description ?? 'Collection transaction',
                ]);
            }
        }

        // Direction filter should apply to all sources
        if ($direction !== 'all') {
            $transactions = $transactions->where('direction', $direction)->values();
        }

        if ($status !== 'all') {
            $transactions = $transactions->filter(function ($tx) use ($status) {
                return ($tx['status'] ?? '') === $status;
            })->values();
        }

        if (!empty($query)) {
            $needle = mb_strtolower($query);
            $transactions = $transactions->filter(function ($tx) use ($needle) {
                return str_contains(mb_strtolower((string) ($tx['user'] ?? '')), $needle)
                    || str_contains(mb_strtolower((string) ($tx['email'] ?? '')), $needle)
                    || str_contains(mb_strtolower((string) ($tx['reference'] ?? '')), $needle)
                    || str_contains(mb_strtolower((string) ($tx['description'] ?? '')), $needle);
            })->values();
        }

        if ($accountType !== 'all') {
            $transactions = $transactions->filter(function ($tx) use ($accountType) {
                return ($tx['account_key'] ?? '') === $accountType;
            })->values();
        }

        $minAmountCents = is_numeric($minAmount) ? (int) round(((float) $minAmount) * 100) : null;
        $maxAmountCents = is_numeric($maxAmount) ? (int) round(((float) $maxAmount) * 100) : null;
        if ($minAmountCents !== null) {
            $transactions = $transactions->where('amount', '>=', $minAmountCents)->values();
        }
        if ($maxAmountCents !== null) {
            $transactions = $transactions->where('amount', '<=', $maxAmountCents)->values();
        }

        $transactions = $transactions->sortByDesc('date')->values();

        $summary = [
            'credits' => (int) $transactions->where('direction', 'credit')->sum('amount'),
            'debits' => (int) $transactions->where('direction', 'debit')->sum('amount'),
            'count' => $transactions->count(),
        ];
        $summary['net'] = $summary['credits'] - $summary['debits'];

        $bySource = [
            'subscription' => [
                'count' => $transactions->where('source', 'Subscription')->count(),
                'credit' => (int) $transactions->where('source', 'Subscription')->sum('amount'),
                'debit' => 0,
            ],
            'wallet' => [
                'count' => $transactions->where('source', 'Wallet')->count(),
                'credit' => (int) $transactions->where('source', 'Wallet')->where('direction', 'credit')->sum('amount'),
                'debit' => (int) $transactions->where('source', 'Wallet')->where('direction', 'debit')->sum('amount'),
            ],
            'collection' => [
                'count' => $transactions->where('source', 'Collection')->count(),
                'credit' => (int) $transactions->where('source', 'Collection')->where('direction', 'credit')->sum('amount'),
                'debit' => (int) $transactions->where('source', 'Collection')->where('direction', 'debit')->sum('amount'),
            ],
        ];

        $bySource['subscription']['net'] = $bySource['subscription']['credit'] - $bySource['subscription']['debit'];
        $bySource['wallet']['net'] = $bySource['wallet']['credit'] - $bySource['wallet']['debit'];
        $bySource['collection']['net'] = $bySource['collection']['credit'] - $bySource['collection']['debit'];

        // Charts (last 12 months or selected range)
        $chartStart = $fromDate ? $fromDate->copy()->startOfMonth() : now()->copy()->subMonths(11)->startOfMonth();
        $chartEnd = $toDate ? $toDate->copy()->endOfMonth() : now()->copy()->endOfMonth();
        if ($chartStart->gt($chartEnd)) {
            $chartStart = $chartEnd->copy()->subMonths(11)->startOfMonth();
        }

        $labels = [];
        $credits = [];
        $debits = [];
        $net = [];
        $bucket = [];

        $cursor = $chartStart->copy();
        while ($cursor->lte($chartEnd)) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->format('M Y');
            $bucket[$key] = ['credit' => 0, 'debit' => 0];
            $cursor->addMonth();
        }

        foreach ($transactions as $tx) {
            $date = $tx['date'] ?? null;
            if (!$date) {
                continue;
            }
            $key = Carbon::parse($date)->format('Y-m');
            if (!isset($bucket[$key])) {
                continue;
            }
            if ($tx['direction'] === 'debit') {
                $bucket[$key]['debit'] += (int) $tx['amount'];
            } else {
                $bucket[$key]['credit'] += (int) $tx['amount'];
            }
        }

        foreach ($bucket as $values) {
            $credits[] = round($values['credit'] / 100, 2);
            $debits[] = round($values['debit'] / 100, 2);
            $net[] = round(($values['credit'] - $values['debit']) / 100, 2);
        }

        $sourceLabels = ['Subscription', 'Wallet', 'Collection'];
        $sourceTotals = [
            round(($bySource['subscription']['credit'] ?? 0) / 100, 2),
            round((($bySource['wallet']['credit'] ?? 0) + ($bySource['wallet']['debit'] ?? 0)) / 100, 2),
            round((($bySource['collection']['credit'] ?? 0) + ($bySource['collection']['debit'] ?? 0)) / 100, 2),
        ];

        return [
            'transactions' => $transactions,
            'summary' => $summary,
            'bySource' => $bySource,
            'type' => $type,
            'direction' => $direction,
            'status' => $status,
            'from' => $from,
            'to' => $to,
            'q' => $query,
            'accountType' => $accountType,
            'minAmount' => $minAmount,
            'maxAmount' => $maxAmount,
            'chartLabels' => $labels,
            'chartCredits' => $credits,
            'chartDebits' => $debits,
            'chartNet' => $net,
            'sourceLabels' => $sourceLabels,
            'sourceTotals' => $sourceTotals,
        ];
    }
}
