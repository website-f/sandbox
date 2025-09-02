<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-gray-900 tracking-tight">
            Welcome, {{ auth()->user()->name }}
        </h2>
        <p class="mt-1 text-gray-500">Manage your referral network and accounts here</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Referral Link & QR -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Referral Link & QR</h3>

                <div class="mb-4 relative">
                    <input class="w-full p-3 pr-12 rounded-xl border border-gray-200 bg-gray-50 font-mono text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        readonly value="{{ route('register',['ref'=>auth()->user()->referral?->ref_code]) }}">
                    <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600" onclick="copyToClipboard(this)">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 2a2 2 0 00-2 2v2H4a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-2h2a2 2 0 002-2V4a2 2 0 00-2-2h-8zM6 4a1 1 0 011-1h6a1 1 0 011 1v2H7a1 1 0 00-1 1v6H4a1 1 0 01-1-1V8a1 1 0 011-1h2V4zm6 6a1 1 0 011-1h4a1 1 0 011 1v8a1 1 0 01-1 1h-4a1 1 0 01-1-1v-8z"/>
                        </svg>
                    </button>
                </div>

                <div class="flex justify-center">
                    <img src="{{ route('referrals.qr') }}" alt="QR Code" class="w-36 h-36 rounded-xl border border-gray-200 shadow-md">
                </div>
            </div>

            <!-- Account Status -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Account Status</h3>
                <ul class="space-y-4">
                    @php $acc = $accounts; @endphp
                    @foreach (['rizqmall'=>'RizqMall','sandbox'=>'Sandbox'] as $k=>$label)
                        <li class="flex justify-between items-center p-4 rounded-xl bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <span class="font-medium text-gray-700">{{ $label }}</span>
                            <div>
                                @if(($acc[$k]->active ?? false))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                        Active until {{ optional($acc[$k]->expires_at)->toFormattedDateString() }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('subscribe.plan',$k) }}">
                                        @csrf
                                        <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-full shadow transition-colors duration-200">
                                            Subscribe / Renew
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Network Tree -->
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Network</h3>
                <div id="tree" class="bg-gray-50 border border-gray-200 rounded-2xl p-6 min-h-[320px] shadow-inner">
                    <p class="text-center text-gray-400">Loading network tree...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(button) {
            const input = button.parentNode.querySelector('input');
            input.select();
            document.execCommand('copy');
            const original = button.innerHTML;
            button.innerHTML = '<span class="text-green-500 font-semibold">Copied!</span>';
            setTimeout(() => button.innerHTML = original, 2000);
        }

        async function drawTree(){
            const res = await fetch('{{ route('referrals.tree') }}');
            const data = await res.json();
            const container = document.getElementById('tree');
            container.innerHTML = '';

            const grouped = {};
            (data.nodes || []).forEach(n => {
                grouped[n.level] = grouped[n.level] || [];
                grouped[n.level].push(n);
            });

            Object.keys(grouped).sort((a,b)=>a-b).forEach(level => {
                const row = document.createElement('div');
                row.className = 'flex flex-wrap items-center gap-2 mb-2';
                row.innerHTML = `<span class="text-sm font-semibold text-indigo-600">Level ${level}:</span>
                                 <span class="text-gray-700">${grouped[level].map(n=>n.name).join(', ')}</span>`;
                container.appendChild(row);
            });
        }

        document.addEventListener('DOMContentLoaded', drawTree);
    </script>
</x-app-layout>
