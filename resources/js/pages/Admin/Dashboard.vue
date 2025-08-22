<template>
    <AdminLayout>
        <!-- Dashboard Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-blue-900 mb-2">Dashboard Overview</h1>
            <div class="flex items-center text-blue-700 text-sm sm:text-base">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Today: {{ currentDate }}</span>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Occupancy Rate -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-t-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm sm:text-lg font-medium text-blue-900 mb-1">Occupancy Rate</h3>
                        <p class="text-2xl sm:text-3xl font-bold text-blue-900">{{ stats.occupancy_rate }}%</p>
                    </div>
                    <div class="bg-blue-100 p-2 sm:p-3 rounded-lg flex-shrink-0 ml-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4">
                    <div class="flex items-center">
                        <div class="h-2 bg-gray-200 rounded-full flex-1 mr-2">
                            <div class="h-2 bg-blue-500 rounded-full" :style="`width: ${stats.occupancy_rate}%`"></div>
                        </div>
                        <span class="text-xs sm:text-sm text-gray-600">{{ stats.checked_in_bookings }} occupied</span>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-t-4 border-gold-500">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm sm:text-lg font-medium text-blue-900 mb-1">Revenue</h3>
                        <p class="text-2xl sm:text-3xl font-bold text-blue-900">${{ formatNumber(stats.revenue) }}</p>
                    </div>
                    <div class="bg-gold-100 p-2 sm:p-3 rounded-lg flex-shrink-0 ml-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4">
                    <p class="text-xs sm:text-sm text-gray-600">This month</p>
                </div>
            </div>

            <!-- Bookings -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-t-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm sm:text-lg font-medium text-blue-900 mb-1">Bookings</h3>
                        <p class="text-2xl sm:text-3xl font-bold text-blue-900">{{ stats.total_bookings }}</p>
                    </div>
                    <div class="bg-green-100 p-2 sm:p-3 rounded-lg flex-shrink-0 ml-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4">
                    <div class="flex items-center">
                        <div class="h-2 bg-gray-200 rounded-full flex-1 mr-2">
                            <div class="h-2 bg-green-500 rounded-full" :style="`width: ${stats.total_bookings > 0 ? (stats.confirmed_bookings / stats.total_bookings * 100) : 0}%`"></div>
                        </div>
                        <span class="text-xs sm:text-sm text-gray-600">{{ stats.confirmed_bookings }} confirmed</span>
                    </div>
                </div>
            </div>

            <!-- Average Room Rate -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border-t-4 border-purple-500">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm sm:text-lg font-medium text-blue-900 mb-1">Avg. Room Rate</h3>
                        <p class="text-2xl sm:text-3xl font-bold text-blue-900">${{ stats.average_room_rate }}</p>
                    </div>
                    <div class="bg-purple-100 p-2 sm:p-3 rounded-lg flex-shrink-0 ml-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4">
                    <p class="text-xs sm:text-sm text-gray-600">Per night</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Revenue Trend Chart -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                <Chart
                    type="line"
                    :data="revenueChartData"
                    :options="lineChartOptions"
                    title="Revenue Trend (Last 6 Months)"
                    height="300"
                />
            </div>

            <!-- Occupancy Trend Chart -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                <Chart
                    type="bar"
                    :data="occupancyChartData"
                    :options="barChartOptions"
                    title="Occupancy Rate (Last 6 Months)"
                    height="300"
                />
            </div>
        </div>

        <!-- Room Type Distribution Chart -->
        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-6 sm:mb-8">
            <Chart
                type="doughnut"
                :data="roomTypeChartData"
                :options="doughnutChartOptions"
                title="Room Type Distribution"
                height="300"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Left Column - Upcoming Check-ins/Check-outs -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                    <!-- Upcoming Check-ins -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-blue-900 text-white px-4 sm:px-6 py-3 sm:py-4">
                            <h3 class="text-base sm:text-lg font-bold flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Upcoming Check-ins ({{ upcomingCheckins.length }})
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-64 sm:max-h-96 overflow-y-auto">
                            <div v-for="(checkin, index) in upcomingCheckins" :key="index" class="p-3 sm:p-4 hover:bg-blue-50 transition">
                                <div class="flex justify-between items-start">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-medium text-blue-900 text-sm sm:text-base truncate">{{ checkin.name }}</h4>
                                        <div class="text-xs sm:text-sm text-gray-600">Room {{ checkin.room }}</div>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-3">
                                        <div class="font-medium text-sm sm:text-base">{{ checkin.time }}</div>
                                        <div class="text-xs sm:text-sm text-gray-600">{{ checkin.date }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center mt-2 text-xs sm:text-sm">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2">#{{ checkin.bookingId }}</span>
                                    <span class="text-gray-600">{{ checkin.guests }} guests</span>
                                </div>
                            </div>
                            <div v-if="upcomingCheckins.length === 0" class="p-4 text-center text-gray-500 text-sm sm:text-base">
                                No upcoming check-ins
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Check-outs -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-gold-500 text-blue-900 px-4 sm:px-6 py-3 sm:py-4">
                            <h3 class="text-base sm:text-lg font-bold flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Upcoming Check-outs ({{ upcomingCheckouts.length }})
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-64 sm:max-h-96 overflow-y-auto">
                            <div v-for="(checkout, index) in upcomingCheckouts" :key="index" class="p-3 sm:p-4 hover:bg-gold-50 transition">
                                <div class="flex justify-between items-start">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-medium text-blue-900 text-sm sm:text-base truncate">{{ checkout.name }}</h4>
                                        <div class="text-xs sm:text-sm text-gray-600">Room {{ checkout.room }}</div>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-3">
                                        <div class="font-medium text-sm sm:text-base">{{ checkout.time }}</div>
                                        <div class="text-xs sm:text-sm text-gray-600">{{ checkout.date }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center mt-2 text-xs sm:text-sm">
                                    <span class="bg-gold-100 text-gold-800 px-2 py-1 rounded mr-2">#{{ checkout.bookingId }}</span>
                                    <span class="text-gray-600">Stayed {{ checkout.nights }} nights</span>
                                </div>
                            </div>
                            <div v-if="upcomingCheckouts.length === 0" class="p-4 text-center text-gray-500 text-sm sm:text-base">
                                No upcoming check-outs
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3">
                        <h3 class="text-base sm:text-lg font-bold text-blue-900">Revenue Overview</h3>
                        <div class="flex">
                            <button class="text-xs sm:text-sm font-medium px-2 sm:px-3 py-1 rounded-lg mr-2 bg-blue-100 text-blue-900">This Month</button>
                            <button class="text-xs sm:text-sm font-medium px-2 sm:px-3 py-1 rounded-lg text-gray-600 hover:bg-gray-100">Last Month</button>
                        </div>
                    </div>
                    <div class="h-48 sm:h-64">
                        <!-- Chart Placeholder -->
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl w-full h-full flex items-center justify-center text-gray-500 text-sm sm:text-base">
                            Revenue Chart Visualization
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Notifications -->
            <div>
                <!-- Notifications -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                    <div class="bg-blue-900 text-white px-4 sm:px-6 py-3 sm:py-4">
                        <h3 class="text-base sm:text-lg font-bold flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Notifications
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div v-for="(notification, index) in notifications" :key="index" class="p-3 sm:p-4 hover:bg-blue-50 transition">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div :class="['h-6 w-6 sm:h-8 sm:w-8 rounded-full flex items-center justify-center', notification.bgClass]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path v-if="notification.type === 'cancellation'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            <path v-if="notification.type === 'emergency'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            <path v-if="notification.type === 'new'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 min-w-0 flex-1">
                                    <h4 class="font-medium text-blue-900 text-sm sm:text-base truncate">{{ notification.title }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ notification.description }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ notification.time }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 sm:p-4 border-t border-gray-100">
                        <button class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium w-full text-center">
                            View All Notifications
                        </button>
                    </div>
                </div>

                <!-- Occupancy Chart -->
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                    <Chart
                        type="line"
                        :data="occupancyForecastData"
                        :options="forecastChartOptions"
                        title="Occupancy Forecast (Next 30 Days)"
                        height="250"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
    import { ref, computed } from 'vue';
    import AdminLayout from '@/layouts/AdminLayout.vue';
    import { Chart } from '@/components/ui';

    // Props from the server
    const props = defineProps<{
        stats: {
            occupancy_rate: number;
            revenue: number;
            total_bookings: number;
            confirmed_bookings: number;
            pending_bookings: number;
            checked_in_bookings: number;
            checked_out_bookings: number;
            average_room_rate: number;
        };
        upcomingCheckins: any[];
        upcomingCheckouts: any[];
        notifications: any[];
    }>();

    // Current date
    const currentDate = computed(() => {
        return new Date().toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    });

    // Format number with commas
    const formatNumber = (num: number) => {
        return num.toLocaleString();
    };

    // Chart data
    const revenueChartData = computed(() => ({
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Revenue',
                data: [45000, 52000, 48000, 61000, 55000, 68000],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
            },
        ],
    }));

    const occupancyChartData = computed(() => ({
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Occupancy Rate',
                data: [75, 82, 78, 88, 85, 92],
                backgroundColor: '#10B981',
                borderColor: '#10B981',
                borderWidth: 1,
            },
        ],
    }));

    const roomTypeChartData = computed(() => ({
        labels: ['Standard', 'Deluxe', 'Suite', 'Presidential'],
        datasets: [
            {
                data: [40, 30, 20, 10],
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
                borderWidth: 0,
            },
        ],
    }));

    const occupancyForecastData = computed(() => {
        const next30Days = Array.from({ length: 30 }, (_, i) => {
            const date = new Date();
            date.setDate(date.getDate() + i);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });

        return {
            labels: next30Days,
            datasets: [
                {
                    label: 'Forecasted Occupancy',
                    data: Array.from({ length: 30 }, () => Math.floor(Math.random() * 30) + 70),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                },
            ],
        };
    });

    // Chart options
    const lineChartOptions = {
        plugins: {
            legend: {
                display: false,
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: (value: any) => `$${value.toLocaleString()}`,
                },
            },
        },
        responsive: true,
        maintainAspectRatio: false,
    };

    const barChartOptions = {
        plugins: {
            legend: {
                display: false,
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: (value: any) => `${value}%`,
                },
            },
        },
        responsive: true,
        maintainAspectRatio: false,
    };

    const doughnutChartOptions = {
        plugins: {
            legend: {
                position: 'bottom' as const,
            },
        },
        responsive: true,
        maintainAspectRatio: false,
    };

    const forecastChartOptions = {
        plugins: {
            legend: {
                display: false,
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: (value: any) => `${value}%`,
                },
            },
        },
        responsive: true,
        maintainAspectRatio: false,
    };
</script>

<style scoped>
    /* Custom color palette */
    .bg-gold-500 { background-color: #D4AF37; }
    .text-gold-500 { color: #D4AF37; }
    .bg-blue-900 { background-color: #0C2D6A; }
    .text-blue-900 { color: #0C2D6A; }

    /* Scrollbar styling */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c5c5c5;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
