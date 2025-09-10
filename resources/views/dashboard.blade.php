<x-app-layout>
<x-slot name="header">
    <h2 class="font-extrabold text-3xl text-gray-900 tracking-tight">
        Welcome, {{ auth()->user()->name }}
    </h2>
    <p class="mt-1 text-gray-500">Manage your referral network and accounts here</p>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

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

        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Account Status</h3>
            @if(session('error'))
                <div class="bg-red-100 text-red-600 px-4 py-2 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
                        <ul class="space-y-4">
                            @php
                                $today = now();
                            @endphp
                    
            @foreach (['rizqmall'=>'RizqMall','sandbox'=>'Sandbox'] as $k => $label)
                @php
                    $account = $accounts[$k] ?? null;
                    $indicatorColor = 'bg-red-500';
                    $indicatorText = 'inactive';
                    $expiryText = '';
                    $serialText = '';
                    $showButton = true;
            
                    if($account) {
                        $expires = $account->expires_at ? \Carbon\Carbon::parse($account->expires_at) : null;
            
                        if($account->active) {
                            $indicatorColor = 'bg-green-500';
                            $indicatorText = 'active';
                            $showButton = false;
            
                            if($expires) {
                                $expiryText = 'Valid until ' . $expires->toFormattedDateString();
                            }

                             if($account->serial_number) {
                                $serialText = "Serial: {$account->serial_number}";
                            }
                        }
                    }
            
                    // Pricing logic
                    $basePrice = $k === 'sandbox' ? 300 : 20; // RM
                    $tax = round($basePrice * 0.08, 2);
                    $fpx = 1.00;
                    $final = $basePrice + $tax + $fpx;
                @endphp
            
                <li class="flex justify-between items-center p-4 rounded-xl bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-700">{{ $label }}</span>
                        <span class="flex items-center gap-2 text-sm text-gray-500">
                            <span class="inline-block w-3 h-3 rounded-full {{ $indicatorColor }}"></span>
                            {{ ucfirst($indicatorText) }}
                        </span>
                        @if($expiryText)
                            <span class="text-xs text-gray-500 mt-1">{{ $expiryText }}</span>
                        @endif
                        @if($serialText)
                            <span class="text-xs font-semibold text-indigo-600 mt-1">{{ $serialText }}</span>
                        @endif
                    </div>
            
                    <div>
                        @if($showButton)
                            <button 
                                x-data 
                                @click="$dispatch('open-modal', {plan: '{{ $k }}', label: '{{ $label }}', base: '{{ $basePrice }}', tax: '{{ $tax }}', fpx: '{{ $fpx }}', final: '{{ $final }}'})"
                                class="px-4 py-2 text-sm font-semibold text-white rounded-full shadow bg-indigo-600 hover:bg-indigo-700">
                                Subscribe
                            </button>
                        @endif
                    </div>
                </li>
            @endforeach
            
            {{-- Modal --}}
            <div 
                x-data="{ open: false, plan: '', label: '', base: 0, tax: 0, fpx: 0, final: 0 }"
                x-on:open-modal.window="open = true; plan = $event.detail.plan; label = $event.detail.label; base = $event.detail.base; tax = $event.detail.tax; fpx = $event.detail.fpx; final = $event.detail.final"
                x-show="open"
                style="display: none"
                class="fixed inset-0 z-50 flex items-center justify-center 
           bg-white backdrop-blur-sm transition-opacity">
                
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-6">
                    <h2 class="text-lg font-bold text-gray-800" x-text="label + ' Subscription'"></h2>
            
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between"><span>Base Price</span> <span>RM <span x-text="base"></span></span></div>
                        <div class="flex justify-between"><span>Tax (8%)</span> <span>RM <span x-text="tax"></span></span></div>
                        <div class="flex justify-between"><span>FPX Charge</span> <span>RM <span x-text="fpx"></span></span></div>
                        <div class="border-t pt-2 flex justify-between font-semibold text-gray-800">
                            <span>Total</span> <span>RM <span x-text="final"></span></span>
                        </div>
                    </div>
            
                    <div class="flex justify-end gap-3">
                        <button @click="open = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Cancel</button>
                        
                        <form method="POST" :action="'/subscribe/' + plan">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                                Confirm & Pay
                            </button>
                        </form>
                    </div>
                </div>
            </div>



            </ul>
        </div>



        @if(auth()->user()->hasRole('Admin'))
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Network Tree</h3>
            <div id="tree" class="bg-gray-50 border border-gray-200 rounded-2xl p-6 min-h-[500px] shadow-inner overflow-auto relative">
                <p class="text-center text-gray-400">Loading network tree...</p>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .node circle {
        fill: #999;
        stroke: #555;
        stroke-width: 1.5px;
    }
    .node text {
        font: 12px sans-serif;
        fill: #333;
    }
    .link {
        fill: none;
        stroke: #555;
        stroke-opacity: 0.4;
        stroke-width: 1.5px;
    }
    .current-user-node {
        fill: #4F46E5 !important;
        stroke: #312E81 !important;
    }
    .current-user-text {
        fill: #4F46E5 !important;
        font-weight: bold;
    }
    .current-user-marker {
        fill: gold;
        stroke: #9C891C;
    }
</style>

<script src="https://d3js.org/d3.v7.min.js"></script>
<script>
function copyToClipboard(button) {
    const input = button.parentNode.querySelector('input');
    input.select();
    document.execCommand('copy');
    const original = button.innerHTML;
    button.innerHTML = '<span class="text-green-500 font-semibold">Copied!</span>';
    setTimeout(() => button.innerHTML = original, 2000);
}

let svg, g, treeLayout, rootData;

async function fetchAndDrawTree() {
    const res = await fetch('{{ route('referrals.tree') }}');
    const data = await res.json();
    const container = document.getElementById('tree');
    container.innerHTML = '';

    if (!data.nodes || data.nodes.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-400">No referrals yet. Share your link to get started! 🚀</p>';
        return;
    }

    // Identify the current user's node
    const currentUserId = '{{ auth()->user()->id }}'; 
    data.nodes.forEach(d => {
        d.is_current_user = (d.id == currentUserId);
    });

    rootData = d3.stratify()
        .id(d => d.id)
        .parentId(d => d.parent_id)
        (data.nodes);

    svg = d3.select(container).append("svg")
        .style("width", "100%")
        .style("height", "100%")
        .attr("viewBox", `0 0 ${container.clientWidth} ${container.clientHeight}`)
        .attr("preserveAspectRatio", "xMidYMid meet");

    g = svg.append("g");

    const zoom = d3.zoom()
        .scaleExtent([0.1, 4])
        .on("zoom", (event) => {
            g.attr("transform", event.transform);
        });
    svg.call(zoom);

    function redraw() {
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;
        const margin = { top: 60, right: 20, bottom: 20, left: 20 };
        const innerWidth = containerWidth - margin.left - margin.right;
        const innerHeight = containerHeight - margin.top - margin.bottom;

        const treeHeight = Math.max(innerHeight, rootData.height * 100);
        const treeWidth = Math.max(innerWidth, (rootData.leaves().length * 60));

        treeLayout = d3.tree().size([treeWidth, treeHeight]);
        treeLayout(rootData);

        let minX = Infinity, maxX = -Infinity;
        rootData.descendants().forEach(d => {
            minX = Math.min(minX, d.x);
            maxX = Math.max(maxX, d.x);
        });
        const treeOccupiedWidth = maxX - minX;
        const offsetX = (innerWidth - treeOccupiedWidth) / 2 - minX;
        const offsetY = 0;

        g.attr("transform", `translate(${margin.left + offsetX},${margin.top + offsetY})`);

        // Update links
        const link = g.selectAll('.link')
            .data(rootData.links(), d => d.target.id);

        link.enter().insert('path', 'g')
            .attr('class', 'link')
            .attr('d', d3.linkVertical()
                .x(d => d.x)
                .y(d => d.y));

        link.transition().duration(500)
            .attr('d', d3.linkVertical()
                .x(d => d.x)
                .y(d => d.y));

        link.exit().remove();

        // Update nodes
        const node = g.selectAll('.node')
            .data(rootData.descendants(), d => d.id);

        const nodeEnter = node.enter().append('g')
            .attr('class', 'node')
            .attr('transform', d => `translate(${d.x},${d.y})`);

        nodeEnter.append('circle')
            .attr('r', 8)
            .attr('class', d => d.data.is_current_user ? 'current-user-node' : 'default-node');

        nodeEnter.append('text')
            .attr('dy', '0.31em')
            .attr('y', d => d.children ? -15 : 15)
            .attr('text-anchor', 'middle')
            .text(d => d.data.name)
            .attr('class', d => d.data.is_current_user ? 'current-user-text' : 'default-text');

        // Add a marker for the current user
        nodeEnter.filter(d => d.data.is_current_user)
            .append('path')
            .attr('d', d3.symbol(d3.symbolStar, 80)) // A star marker
            .attr('class', 'current-user-marker')
            .attr('transform', `translate(0, -20)`); // Position above the node

        node.transition().duration(500)
            .attr('transform', d => `translate(${d.x},${d.y})`);

        node.exit().remove();

        svg.attr("viewBox", `0 0 ${containerWidth} ${containerHeight}`);
    }

    redraw();
    window.addEventListener('resize', redraw);
}

document.addEventListener('DOMContentLoaded', fetchAndDrawTree);
</script>
</x-app-layout>