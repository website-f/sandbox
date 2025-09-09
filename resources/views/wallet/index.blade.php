<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-4xl text-gray-900 leading-tight">
            My Wallet 💸
        </h2>
        <p class="mt-2 text-base text-gray-500">
            View your balance and track all your transactions.
        </p>
    </x-slot>


    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            {{-- Success/Error Message --}}
            @if(session('success'))
                <div class="flex items-center p-5 rounded-2xl bg-green-100 text-green-800 font-semibold shadow-md">
                    <svg class="w-6 h-6 mr-3 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center p-5 rounded-2xl bg-red-100 text-red-800 font-semibold shadow-md">
                    <svg class="w-6 h-6 mr-3 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Balance Card --}}
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl shadow-2xl p-8 md:p-10 flex flex-col md:flex-row items-start md:items-center justify-between transition-transform duration-300 transform hover:scale-[1.01]">
                <div class="flex-grow">
                    <h3 class="text-xl font-medium opacity-80">Current Balance</h3>
                    <p class="text-5xl font-extrabold mt-2 tracking-tight">
                        RM {{ number_format(($user->wallet->balance ?? 0)/100, 2) }}
                    </p>
                </div>
                <div class="mt-8 md:mt-0 flex flex-row space-x-4 w-full md:w-auto justify-end">
                    <button class="flex-1 px-8 py-4 rounded-xl bg-white text-indigo-700 font-bold shadow-md hover:bg-gray-100 transition-colors duration-300 min-w-[120px]">
                        Top Up
                    </button>
                    <button class="flex-1 px-8 py-4 rounded-xl bg-white text-red-600 font-bold shadow-md hover:bg-gray-100 transition-colors duration-300 min-w-[120px]">
                        Withdraw
                    </button>
                </div>
            </div>

            {{-- Transaction History --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <h3 class="text-2xl font-bold text-gray-800">Transaction History</h3>
                    <span class="text-sm text-gray-500 mt-2 sm:mt-0">Last 20 transactions</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($user->wallet->transactions()->latest()->take(20)->get() as $tx)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-5 text-sm text-gray-600">
                                        {{ $tx->created_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td class="px-6 py-5 text-sm text-gray-800 font-medium">
                                        {{ $tx->description ?? '-' }}
                                    </td>
                                    <td class="px-6 py-5 text-sm text-right font-bold
                                        {{ $tx->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $tx->type === 'credit' ? '+' : '-' }}
                                        RM {{ number_format($tx->amount/100, 2) }}
                                    </td>
                                    <td class="px-6 py-5 text-sm text-center">
                                        <span class="px-4 py-1.5 rounded-full text-xs font-bold 
                                            {{ $tx->type === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ ucfirst($tx->type) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-lg italic">
                                        No transactions have been recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>