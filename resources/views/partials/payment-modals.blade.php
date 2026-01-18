{{-- Installment Modal (for new Sandbox subscriptions) --}}
<div x-data="{
    open: false,
    plan: '',
    label: '',
    fullBase: 0,
    fullTax: 0,
    fullFpx: 0,
    fullFinal: 0,
    installments: 1,

    get installmentBase() {
        return (this.fullBase / this.installments).toFixed(2);
    },
    get installmentTax() {
        return (this.fullTax / this.installments).toFixed(2);
    },
    get installmentFpx() {
        return this.fullFpx.toFixed(2);
    },
    get installmentAmount() {
        return (parseFloat(this.installmentBase) + parseFloat(this.installmentTax) + parseFloat(this.installmentFpx)).toFixed(2);
    }
}"
    x-on:open-installment-modal.window="
        open = true;
        plan = $event.detail.plan;
        label = $event.detail.label;
        fullBase = parseFloat($event.detail.base);
        fullTax = parseFloat($event.detail.tax);
        fullFpx = parseFloat($event.detail.fpx);
        fullFinal = parseFloat($event.detail.final);
        installments = 1;
    "
    x-show="open" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">

    <div @click.away="open = false"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg p-8 space-y-6">

        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="label + ' Subscription'"></h2>
            <button @click="open = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-times text-gray-500 dark:text-gray-400"></i>
            </button>
        </div>

        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Choose your payment plan:</p>

        <div class="grid grid-cols-2 gap-4">
            {{-- Full Payment --}}
            <button @click="installments = 1"
                :class="{
                    'border-indigo-500 ring-2 ring-indigo-200 dark:ring-indigo-800 bg-indigo-50 dark:bg-indigo-900/20': installments == 1,
                    'border-gray-200 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500': installments != 1
                }"
                class="p-4 border-2 rounded-xl text-left transition-all duration-200">
                <p class="font-bold text-lg" :class="{ 'text-indigo-600 dark:text-indigo-400': installments == 1, 'text-gray-700 dark:text-gray-300': installments != 1 }">
                    Full Payment
                </p>
                <p class="text-2xl font-extrabold mt-1 text-gray-900 dark:text-white">
                    RM <span x-text="fullFinal.toFixed(2)"></span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">One-time charge</p>
            </button>

            {{-- 3 Installments --}}
            <button @click="installments = 3"
                :class="{
                    'border-indigo-500 ring-2 ring-indigo-200 dark:ring-indigo-800 bg-indigo-50 dark:bg-indigo-900/20': installments == 3,
                    'border-gray-200 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500': installments != 3
                }"
                class="p-4 border-2 rounded-xl text-left transition-all duration-200">
                <p class="font-bold text-lg" :class="{ 'text-indigo-600 dark:text-indigo-400': installments == 3, 'text-gray-700 dark:text-gray-300': installments != 3 }">
                    3 Installments
                </p>
                <p class="text-2xl font-extrabold mt-1 text-gray-900 dark:text-white">
                    RM <span x-text="((fullBase + fullTax) / 3 + fullFpx).toFixed(2)"></span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">per payment (3x)</p>
            </button>
        </div>

        {{-- Breakdown --}}
        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl space-y-2 text-sm">
            <h3 class="font-bold text-base text-gray-800 dark:text-white mb-3"
                x-text="'Payment Breakdown: ' + (installments == 1 ? 'Full Payment' : 'First Installment')">
            </h3>

            <div x-show="installments == 1">
                <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Base Price</span> <span>RM <span x-text="fullBase.toFixed(2)"></span></span></div>
                <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Tax (8%)</span> <span>RM <span x-text="fullTax.toFixed(2)"></span></span></div>
                <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>FPX Charge</span> <span>RM <span x-text="fullFpx.toFixed(2)"></span></span></div>
            </div>

            <div x-show="installments > 1">
                <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Base Price (per installment)</span> <span>RM <span x-text="installmentBase"></span></span></div>
                <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Tax (per installment)</span> <span>RM <span x-text="installmentTax"></span></span></div>
                <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>FPX Charge (per installment)</span> <span>RM <span x-text="installmentFpx"></span></span></div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-600 pt-3 mt-3 flex justify-between font-semibold text-gray-800 dark:text-white">
                <span>Total Subscription Cost</span> <span>RM <span x-text="fullFinal.toFixed(2)"></span></span>
            </div>

            <div class="pt-2 flex justify-between font-extrabold text-indigo-600 dark:text-indigo-400 text-lg">
                <span x-text="installments == 1 ? 'Total to Pay Now' : 'Amount for First Installment'"></span>
                <span>RM <span x-text="installmentAmount"></span></span>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <button @click="open = false"
                class="px-5 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 font-semibold transition-colors">
                Cancel
            </button>
            <form method="POST" :action="'/subscribe/' + plan">
                @csrf
                <input type="hidden" name="installments" x-model="installments">
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg transition-colors">
                    <i class="fas fa-credit-card mr-2"></i> Pay RM <span x-text="installmentAmount"></span>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Pay Next Installment Modal --}}
<div x-data="{
    open: false,
    subscriptionId: null,
    selectedOption: null,
    fullFinal: 0,
    paidCount: 0,
    totalInstallments: 0,
    base: 100,

    get tax() { return this.base * 0.08; },
    get fpx() { return 1.00; },
    get installmentAmount() {
        if (this.totalInstallments === 0) return 0;
        return this.base + this.tax + this.fpx;
    },
    get remainingAmount() {
        return this.installmentAmount * (this.totalInstallments - this.paidCount);
    },
    get currentAmount() {
        return this.selectedOption === 'full' ? this.remainingAmount : this.installmentAmount;
    },
    get currentBase() {
        return this.base;
    }
}"
    x-on:open-pay-next-modal.window="
        open = true;
        subscriptionId = $event.detail.subscriptionId ?? null;
        base = parseFloat($event.detail.base) || 0;
        fullFinal = parseFloat($event.detail.fullFinal) || 0;
        paidCount = parseInt($event.detail.paidCount) || 0;
        totalInstallments = parseInt($event.detail.totalInstallments) || 0;
        selectedOption = 'next';
    "
    x-show="open" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">

    <div @click.away="open = false"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg p-8 space-y-6">

        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Pay Subscription</h2>
            <button @click="open = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-times text-gray-500 dark:text-gray-400"></i>
            </button>
        </div>

        <div class="flex items-center justify-between p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
            <span class="text-gray-700 dark:text-gray-300 font-medium">Payment Progress</span>
            <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400" x-text="paidCount + ' / ' + totalInstallments"></span>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <button @click="selectedOption = 'next'"
                :class="{
                    'border-indigo-500 ring-2 ring-indigo-200 dark:ring-indigo-800 bg-indigo-50 dark:bg-indigo-900/20': selectedOption === 'next',
                    'border-gray-200 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500': selectedOption !== 'next'
                }"
                class="p-4 border-2 rounded-xl text-left transition-all duration-200">
                <p class="font-bold text-lg" :class="{ 'text-indigo-600 dark:text-indigo-400': selectedOption === 'next' }">
                    <i class="fas fa-arrow-right mr-2"></i> Next Installment
                </p>
                <p class="text-2xl font-extrabold mt-1 text-gray-900 dark:text-white">
                    RM <span x-text="Number(installmentAmount).toFixed(2)"></span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pay one installment</p>
            </button>
        </div>

        {{-- Breakdown --}}
        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl space-y-2 text-sm" x-show="selectedOption">
            <h3 class="font-bold text-base text-gray-800 dark:text-white mb-3"
                x-text="'Breakdown for: ' + (selectedOption === 'full' ? 'Full Settlement' : 'Next Installment')">
            </h3>

            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Base Price</span> <span>RM <span x-text="currentBase.toFixed(2)"></span></span></div>
            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Tax (8%)</span> <span>RM <span x-text="tax.toFixed(2)"></span></span></div>
            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>FPX Charge</span> <span>RM <span x-text="fpx.toFixed(2)"></span></span></div>

            <div class="border-t border-gray-200 dark:border-gray-600 pt-3 mt-3 flex justify-between font-extrabold text-indigo-600 dark:text-indigo-400 text-lg">
                <span x-text="selectedOption === 'full' ? 'Total Settlement Amount' : 'Total Installment Amount'"></span>
                <span>RM <span x-text="currentAmount.toFixed(2)"></span></span>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <button @click="open = false"
                class="px-5 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 font-semibold transition-colors">
                Cancel
            </button>

            <form method="POST" :action="'/subscribe/pay-next/' + subscriptionId">
                @csrf
                <input type="hidden" name="full_settlement" :value="selectedOption === 'full' ? 1 : 0">
                <button type="submit" :disabled="!selectedOption"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg transition-colors disabled:opacity-50">
                    <i class="fas fa-credit-card mr-2"></i> Pay RM <span x-text="currentAmount.toFixed(2)"></span>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Simple Subscribe Modal (for RizqMall) --}}
<div x-data="{ open: false, plan: '', label: '', base: 0, tax: 0, fpx: 0, final: 0 }"
    x-on:open-modal.window="open = true; plan = $event.detail.plan; label = $event.detail.label; base = $event.detail.base; tax = $event.detail.tax; fpx = $event.detail.fpx; final = $event.detail.final"
    x-show="open" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">

    <div @click.away="open = false"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-6">

        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="label + ' Subscription'"></h2>
            <button @click="open = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-times text-gray-500 dark:text-gray-400"></i>
            </button>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Base Price</span> <span>RM <span x-text="base"></span></span></div>
            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Tax (8%)</span> <span>RM <span x-text="tax"></span></span></div>
            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>FPX Charge</span> <span>RM <span x-text="fpx"></span></span></div>
            <div class="border-t border-gray-200 dark:border-gray-600 pt-3 flex justify-between font-bold text-lg text-gray-900 dark:text-white">
                <span>Total</span> <span>RM <span x-text="final"></span></span>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button @click="open = false"
                class="px-5 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 font-semibold transition-colors">
                Cancel
            </button>

            <form method="POST" :action="'/subscribe/' + plan">
                @csrf
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg transition-colors">
                    <i class="fas fa-credit-card mr-2"></i> Confirm & Pay
                </button>
            </form>
        </div>
    </div>
</div>
