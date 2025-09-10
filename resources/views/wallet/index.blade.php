<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-bgray-900 lg:text-3xl lg:leading-[36.4px]">
            My Wallet 💸
        </h2>
        <p class="text-xs font-medium text-bgray-600 lg:text-sm lg:leading-[25.2px]">
            Let’s check your wallet today
        </p>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8 ">
        <div class="2xl">
            {{-- Success/Error Message --}}
            @if(session('success'))
                <div class="mb-6 flex items-center rounded-2xl bg-green-100 p-5 font-semibold text-green-800 shadow-md">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center rounded-2xl bg-red-100 p-5 font-semibold text-red-800 shadow-md">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Balance Card --}}
            <div class="mb-[48px] w-full rounded-xl bg-white px-7 py-11">
                <div class="rounded-lg border border-bgray-300 p-8 pb-12">
                    <h3 class="text-2xl font-semibold text-bgray-900">Total Balance</h3>
                    <h2 class="mb-2 font-poppins text-4xl font-bold text-bgray-900">
                        RM {{ number_format(($user->wallet->balance ?? 0) / 100, 2) }}
                        <span class="text-base font-medium uppercase text-bgray-500">MYR</span>
                    </h2>
                </div>
                <button class="mt-14 w-full rounded-lg bg-success-300 py-4 font-bold text-white transition-all hover:bg-success-400">
                    Topup
                </button>
            </div>
            
        </div>

        <div class="2xl:flex-1">
            <div class="w-full xl:flex xl:space-x-[24px]">
                {{-- Overall Balance Chart --}}
                <div class="mb-12 flex w-full flex-col justify-between rounded-lg bg-white px-8 py-7 xl:w-[613px]">
                    <div class="mb-2 flex items-center justify-between pb-2">
                        <div>
                            <span class="text-sm font-medium text-bgray-600">Overall Balance</span>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-2xl font-bold leading-[36px] text-bgray-900">
                                    RM {{ number_format(($user->wallet->balance ?? 0) / 100, 2) }}
                                </h3>
                                <span class="text-sm font-medium text-success-300">+20%</span>
                            </div>
                        </div>
                        <div class="date-filter relative">
                            <button onclick="dateFilterAction('#date-filter-body')" type="button" class="flex items-center space-x-1 overflow-hidden rounded-lg bg-bgray-100 px-3 py-2">
                                <span class="text-sm font-medium text-bgray-900">Jan 10 - Jan 16</span>
                                <span>
                                    <svg class="stroke-bgray-900" width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 6.5L8 10.5L12 6.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                            </button>
                            <div id="date-filter-body" class="absolute right-0 top-[44px] z-10 hidden overflow-hidden rounded-lg bg-white shadow-lg">
                                <ul>
                                    <li onclick="dateFilterAction('#date-filter-body')" class="text-bgray-90 cursor-pointer px-5 py-2 text-sm font-semibold hover:bg-bgray-100">January</li>
                                    <li onclick="dateFilterAction('#date-filter-body')" class="cursor-pointer px-5 py-2 text-sm font-semibold text-bgray-900 hover:bg-bgray-100">February</li>
                                    <li onclick="dateFilterAction('#date-filter-body')" class="cursor-pointer px-5 py-2 text-sm font-semibold text-bgray-900 hover:bg-bgray-100">March</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    
                    <div class="w-full">
                        <canvas id="overAllBalance" height="280"></canvas>
                    </div>
                </div>
            </div>

            {{-- Transaction History --}}
            <div class="w-full rounded-lg bg-white px-[24px] py-[20px] mb-12">
                <div class="flex flex-col space-y-5">
                    <div class="flex h-[56px] w-full items-center justify-between">
                        <h3 class="text-2xl font-bold text-bgray-900">Transaction History</h3>
                    </div>
                    <div class="table-content w-full overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr class="border-b border-bgray-300">
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-bgray-600">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-bgray-600">Description</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-bgray-600">Amount</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-bgray-600">Type</th>
                                    <th class="w-[165px] px-6 py-5 xl:px-0"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($user->wallet->transactions()->latest()->take(20)->get() as $tx)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-5 text-sm text-bgray-600">
                                            {{ $tx->created_at->format('d M Y, h:i A') }}
                                        </td>
                                        <td class="px-6 py-5 text-sm font-medium text-bgray-800">
                                            {{ $tx->description ?? '-' }}
                                        </td>
                                        <td class="px-6 py-5 text-right text-sm font-bold {{ $tx->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $tx->type === 'credit' ? '+' : '-' }}
                                            RM {{ number_format($tx->amount / 100, 2) }}
                                        </td>
                                        <td class="px-6 py-5 text-center text-sm">
                                            <span class="rounded-full px-4 py-1.5 text-xs font-bold {{ $tx->type === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ ucfirst($tx->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 xl:px-0">
                                            <div class="flex justify-center">
                                                <button type="button">
                                                    <svg width="18" height="4" viewBox="0 0 18 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8 2.00024C8 2.55253 8.44772 3.00024 9 3.00024C9.55228 3.00024 10 2.55253 10 2.00024C10 1.44796 9.55228 1.00024 9 1.00024C8.44772 1.00024 8 1.44796 8 2.00024Z" stroke="#A0AEC0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M1 2.00024C1 2.55253 1.44772 3.00024 2 3.00024C2.55228 3.00024 3 2.55253 3 2.00024C3 1.44796 2.55228 1.00024 2 1.00024C1.44772 1.00024 1 1.44796 1 2.00024Z" stroke="#A0AEC0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M15 2.00024C15 2.55253 15.4477 3.00024 16 3.00024C16.5523 3.00024 17 2.55253 17 2.00024C17 1.44796 16.5523 1.00024 16 1.00024C15.4477 1.00024 15 1.44796 15 2.00024Z" stroke="#A0AEC0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-lg italic text-gray-500">
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
    </div>
    
    @push('scripts')
    <script>
  let months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

  // Data from controller
  let transactionData = @json($chartData);

  let ctx = document.getElementById("overAllBalance").getContext("2d");
  let chart = new Chart(ctx, {
    type: "line",
    data: {
      labels: months,
      datasets: [{
        label: "Net Monthly Change",
        data: transactionData.map(v => v/100), // if stored in cents
        borderColor: "#22C55E",
        backgroundColor: (ctx) => {
          const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 450);
          gradient.addColorStop(0, "rgba(34, 197, 94,0.41)");
          gradient.addColorStop(0.6, "rgba(255, 255, 255, 0)");
          return gradient;
        },
        fill: true,
        tension: 0.4,
        pointBackgroundColor: "#22C55E",
        pointBorderColor: "#fff",
        pointBorderWidth: 3,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => `RM ${value.toFixed(2)}`,
          }
        }
      }
    }
  });
</script>

    @endpush
</x-app-layout>