<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { Head, Link } from '@inertiajs/vue3';
    import AdminLayout from '@/layouts/AdminLayout.vue';
    import Modal from '@/components/Modal.vue'; // Added import for Modal component

    // Sample transaction data
    const transactions = ref([
        {
            id: 'TRX-78901',
            bookingId: 'BK-12345',
            guest: 'John Smith',
            amount: 25000,
            date: '2023-05-15',
            status: 'completed',
            method: 'Credit Card'
        },
        {
            id: 'TRX-78902',
            bookingId: 'BK-12346',
            guest: 'Sarah Johnson',
            amount: 18000,
            date: '2023-05-16',
            status: 'refunded',
            method: 'Online Banking'
        },
        {
            id: 'TRX-78903',
            bookingId: 'BK-12347',
            guest: 'Michael Brown',
            amount: 32000,
            date: '2023-05-17',
            status: 'failed',
            method: 'Credit Card'
        }
    ]);

    const selectedTransaction = ref(null);
    const showRefundModal = ref(false);
    const refundAmount = ref(0);
    const refundReason = ref('');
    const activeTab = ref('transactions');
    const searchQuery = ref('');

    // Filter transactions based on search
    const filteredTransactions = computed(() => {
        return transactions.value.filter(trx =>
            trx.id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            trx.bookingId.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            trx.guest.toLowerCase().includes(searchQuery.value.toLowerCase())
        )});

    // Process refund
    const processRefund = () => {
        if (selectedTransaction.value && refundAmount.value > 0) {
            // API call would go here
            const trx = transactions.value.find(t => t.id === selectedTransaction.value.id);
            if (trx) {
                trx.status = 'refunded';
            }
            showRefundModal.value = false;
            refundAmount.value = 0;
            refundReason.value = '';
        }
    };

    // Format currency
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'LKR',
            minimumFractionDigits: 2
        }).format(amount);
    };

    // Get status color
    const getStatusColor = (status) => {
        switch (status) {
            case 'completed': return 'bg-green-100 text-green-800';
            case 'refunded': return 'bg-blue-100 text-blue-800';
            case 'failed': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };
</script>

<template>
    <AdminLayout>
        <div class="min-h-screen bg-blue-50">
            <Head title="Payment Hub" />

            <!-- Header -->
            <div class="bg-blue-900 text-white shadow-md">
                <div class="container mx-auto px-4 py-6">
                    <h1 class="text-3xl font-bold">Payment Hub</h1>
                    <p class="text-blue-200 mt-2">Manage transactions, refunds, and payment alerts</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container mx-auto px-4 py-8">
                <!-- Tabs -->
                <div class="flex border-b border-blue-200 mb-6">
                    <button
                        @click="activeTab = 'transactions'"
                        :class="{'border-b-2 border-gold-600 text-blue-900 font-medium': activeTab === 'transactions'}"
                        class="px-4 py-2 text-blue-700 hover:text-blue-900 focus:outline-none"
                    >
                        Transaction History
                    </button>
                    <button
                        @click="activeTab = 'refunds'"
                        :class="{'border-b-2 border-gold-600 text-blue-900 font-medium': activeTab === 'refunds'}"
                        class="px-4 py-2 text-blue-700 hover:text-blue-900 focus:outline-none"
                    >
                        Refund Processing
                    </button>
                    <button
                        @click="activeTab = 'alerts'"
                        :class="{'border-b-2 border-gold-600 text-blue-900 font-medium': activeTab === 'alerts'}"
                        class="px-4 py-2 text-blue-700 hover:text-blue-900 focus:outline-none"
                    >
                        Payment Alerts
                    </button>
                </div>

                <!-- Transactions Tab -->
                <div v-if="activeTab === 'transactions'" class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-blue-900">Transaction History</h2>
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search transactions..."
                                class="pl-10 pr-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent"
                            />
                            <svg class="w-5 h-5 text-blue-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-blue-200">
                            <thead class="bg-blue-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Transaction ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Booking ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-blue-200">
                            <tr v-for="trx in filteredTransactions" :key="trx.id" class="hover:bg-blue-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-900">{{ trx.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">{{ trx.bookingId }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">{{ trx.guest }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">{{ formatCurrency(trx.amount) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">{{ trx.date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[getStatusColor(trx.status), 'px-2 py-1 rounded-full text-xs font-medium']">
                    {{ trx.status }}
                  </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">{{ trx.method }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button
                                        v-if="trx.status === 'completed'"
                                        @click="selectedTransaction = trx; showRefundModal = true"
                                        class="text-gold-600 hover:text-gold-800 mr-3"
                                    >
                                        Refund
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-800">
                                        Details
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination would go here -->
                    <div class="flex justify-between items-center mt-6">
                        <div class="text-sm text-blue-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of <span class="font-medium">3</span> results
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-blue-200 rounded-md text-blue-700 bg-white disabled:opacity-50" disabled>
                                Previous
                            </button>
                            <button class="px-3 py-1 border border-blue-200 rounded-md text-blue-700 bg-white">
                                Next
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Refunds Tab -->
                <div v-if="activeTab === 'refunds'" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-blue-900 mb-6">Refund Processing</h2>
                    <div class="bg-blue-50 border-l-4 border-gold-600 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gold-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Refunds may take 3-5 business days to process depending on the payment method.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white border border-blue-200 rounded-lg p-6 shadow-sm">
                            <h3 class="text-lg font-medium text-blue-900 mb-4">Recent Refunds</h3>
                            <div class="space-y-4">
                                <div v-for="trx in transactions.filter(t => t.status === 'refunded')" :key="trx.id" class="border-b border-blue-100 pb-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-blue-900">{{ trx.bookingId }}</span>
                                        <span class="text-sm text-blue-700">{{ formatCurrency(trx.amount) }}</span>
                                    </div>
                                    <div class="text-xs text-blue-500 mt-1">{{ trx.date }} • {{ trx.guest }}</div>
                                </div>
                                <div v-if="transactions.filter(t => t.status === 'refunded').length === 0" class="text-sm text-blue-500">
                                    No refunds processed recently.
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-blue-200 rounded-lg p-6 shadow-sm">
                            <h3 class="text-lg font-medium text-blue-900 mb-4">Refund Statistics</h3>
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium text-blue-700">Total Refunds This Month</h4>
                                    <p class="text-2xl font-bold text-gold-600 mt-1">{{ formatCurrency(18000) }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-700">Average Processing Time</h4>
                                    <p class="text-2xl font-bold text-blue-600 mt-1">3.2 days</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-700">Refund Success Rate</h4>
                                    <div class="w-full bg-blue-200 rounded-full h-2.5 mt-2">
                                        <div class="bg-gold-600 h-2.5 rounded-full" style="width: 95%"></div>
                                    </div>
                                    <p class="text-xs text-blue-500 mt-1">95% successful (19/20)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts Tab -->
                <div v-if="activeTab === 'alerts'" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-blue-900 mb-6">Payment Failure Alerts</h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-blue-200">
                            <thead class="bg-blue-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Booking ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Attempts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Last Attempt</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Error</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-blue-200">
                            <tr v-for="trx in transactions.filter(t => t.status === 'failed')" :key="trx.id" class="hover:bg-blue-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-900">{{ trx.bookingId }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">{{ trx.guest }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">{{ formatCurrency(trx.amount) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">2</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">2023-05-17 14:32</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">Insufficient funds</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-800 mr-3">
                                        Retry
                                    </button>
                                    <button class="text-gold-600 hover:text-gold-800">
                                        Notify Guest
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="transactions.filter(t => t.status === 'failed').length === 0" class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-blue-900">No payment failures</h3>
                        <p class="mt-1 text-sm text-blue-500">All recent payments have been processed successfully.</p>
                    </div>
                </div>
            </div>

            <!-- Refund Modal -->
            <Modal
                v-model:show="showRefundModal"
                title="Process Refund"
                size="md"
                @close="showRefundModal = false"
            >
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <div class="mt-2">
                            <div class="bg-blue-50 p-4 rounded-md mb-4">
                                <p class="text-sm text-blue-700">
                                    <span class="font-medium">Transaction:</span> {{ selectedTransaction?.id }}<br>
                                    <span class="font-medium">Amount:</span> {{ formatCurrency(selectedTransaction?.amount) }}<br>
                                    <span class="font-medium">Guest:</span> {{ selectedTransaction?.guest }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label for="refundAmount" class="block text-sm font-medium text-blue-700">Refund Amount</label>
                                <input
                                    v-model="refundAmount"
                                    type="number"
                                    id="refundAmount"
                                    :max="selectedTransaction?.amount"
                                    class="mt-1 block w-full border border-blue-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gold-500 focus:border-gold-500 sm:text-sm"
                                />
                            </div>
                            <div class="mb-4">
                                <label for="refundReason" class="block text-sm font-medium text-blue-700">Reason for Refund</label>
                                <select
                                    v-model="refundReason"
                                    id="refundReason"
                                    class="mt-1 block w-full border border-blue-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gold-500 focus:border-gold-500 sm:text-sm"
                                >
                                    <option value="">Select a reason</option>
                                    <option value="cancellation">Booking Cancellation</option>
                                    <option value="emergency">Medical Emergency</option>
                                    <option value="overcharge">Overcharged</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <template #footer>
                    <button
                        @click="showRefundModal = false"
                        class="bg-white border border-blue-300 text-blue-700 hover:bg-blue-50 font-bold py-3 px-6 rounded-lg transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="processRefund"
                        class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-lg transition"
                    >
                        Confirm Refund
                    </button>
                </template>
            </Modal>
        </div>
    </AdminLayout>
</template>

<style>
    /* Custom gold color for Tailwind */
    .bg-gold-100 { background-color: #fff9e6; }
    .bg-gold-200 { background-color: #ffedb3; }
    .bg-gold-500 { background-color: #ffd700; }
    .bg-gold-600 { background-color: #e6c200; }
    .bg-gold-700 { background-color: #ccad00; }
    .text-gold-600 { color: #e6c200; }
    .text-gold-800 { color: #997a00; }
    .border-gold-500 { border-color: #ffd700; }
    .border-gold-600 { border-color: #e6c200; }
    .hover\:bg-gold-700:hover { background-color: #ccad00; }
    .hover\:text-gold-800:hover { color: #997a00; }
    .focus\:ring-gold-500:focus { --tw-ring-color: #ffd700; }
</style>
