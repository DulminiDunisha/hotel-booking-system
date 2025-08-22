<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import { Head } from '@inertiajs/vue3';
    import WelcomeLayout from '@/layouts/WelcomeLayout.vue';
    import { Calendar, MapPin, Users, CreditCard, Clock, CheckCircle, XCircle } from 'lucide-vue-next';

    // Props from the server
    const props = defineProps<{
        user: {
            id: number;
            name: string;
            email: string;
            phone: string;
            address: string;
        };
        bookings: Array<{
            id: number;
            booking_code: string;
            check_in_date: string;
            check_out_date: string;
            total_amount: number;
            status: string;
            payment_status: string;
            room: {
                name: string;
                number: string;
            };
        }>;
    }>();

    // Reactive data
    const user = ref(props.user);
    const bookings = ref(props.bookings);
    const activeTab = ref('overview');

    // Computed values
    const upcomingBookings = computed(() => {
        return bookings.value.filter(booking => 
            new Date(booking.check_in_date) > new Date() && 
            booking.status === 'confirmed'
        );
    });

    const pastBookings = computed(() => {
        return bookings.value.filter(booking => 
            new Date(booking.check_out_date) < new Date()
        );
    });

    const currentBookings = computed(() => {
        const today = new Date();
        return bookings.value.filter(booking => {
            const checkIn = new Date(booking.check_in_date);
            const checkOut = new Date(booking.check_out_date);
            return checkIn <= today && checkOut >= today && booking.status === 'checked_in';
        });
    });

    // Format date
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    };

    // Calculate nights
    const calculateNights = (checkIn: string, checkOut: string) => {
        const start = new Date(checkIn);
        const end = new Date(checkOut);
        const diffTime = Math.abs(end.getTime() - start.getTime());
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    };

    // Get status badge class
    const getStatusClass = (status: string) => {
        const classes = {
            'confirmed': 'bg-green-100 text-green-800',
            'checked_in': 'bg-blue-100 text-blue-800',
            'checked_out': 'bg-gray-100 text-gray-800',
            'cancelled': 'bg-red-100 text-red-800',
            'pending': 'bg-yellow-100 text-yellow-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    };

    // Get payment status class
    const getPaymentStatusClass = (status: string) => {
        const classes = {
            'paid': 'bg-green-100 text-green-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'failed': 'bg-red-100 text-red-800',
            'refunded': 'bg-gray-100 text-gray-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    };

    // Navigate to booking details
    const viewBooking = (bookingId: number) => {
        router.visit(`/bookings/${bookingId}`);
    };

    // Book new room
    const bookNewRoom = () => {
        router.visit('/hotel-rooms');
    };
</script>

<template>
    <WelcomeLayout>
        <Head>
            <title>My Dashboard - Dumindu Hotel</title>
        </Head>

        <div class="min-h-screen bg-blue-50">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200">
                <div class="container mx-auto px-6 py-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-blue-900">Welcome back, {{ user.name }}!</h1>
                            <p class="text-blue-700 mt-2">Manage your bookings and profile</p>
                        </div>
                        <button 
                            @click="bookNewRoom"
                            class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-full transition duration-300"
                        >
                            Book New Room
                        </button>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-6 py-8">
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-md p-6 border border-gold-100">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <Calendar class="h-6 w-6 text-blue-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-900">{{ upcomingBookings.length }}</div>
                                <div class="text-blue-700">Upcoming Stays</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6 border border-gold-100">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-lg mr-4">
                                <CheckCircle class="h-6 w-6 text-green-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-900">{{ currentBookings.length }}</div>
                                <div class="text-blue-700">Current Stays</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6 border border-gold-100">
                        <div class="flex items-center">
                            <div class="bg-gold-100 p-3 rounded-lg mr-4">
                                <Clock class="h-6 w-6 text-gold-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-900">{{ pastBookings.length }}</div>
                                <div class="text-blue-700">Past Stays</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6 border border-gold-100">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                <CreditCard class="h-6 w-6 text-purple-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-900">{{ bookings.length }}</div>
                                <div class="text-blue-700">Total Bookings</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white rounded-xl shadow-md mb-8">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8 px-6">
                            <button
                                @click="activeTab = 'overview'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                                :class="activeTab === 'overview' ? 'border-gold-500 text-gold-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            >
                                Overview
                            </button>
                            <button
                                @click="activeTab = 'bookings'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                                :class="activeTab === 'bookings' ? 'border-gold-500 text-gold-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            >
                                My Bookings
                            </button>
                            <button
                                @click="activeTab = 'profile'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                                :class="activeTab === 'profile' ? 'border-gold-500 text-gold-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            >
                                Profile
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Overview Tab -->
                        <div v-if="activeTab === 'overview'" class="space-y-8">
                            <!-- Current Stay -->
                            <div v-if="currentBookings.length > 0">
                                <h3 class="text-lg font-bold text-blue-900 mb-4">Current Stay</h3>
                                <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                                    <div v-for="booking in currentBookings" :key="booking.id" class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-blue-900">{{ booking.room.name }} ({{ booking.room.number }})</h4>
                                            <p class="text-blue-700 text-sm">Check-out: {{ formatDate(booking.check_out_date) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Currently Staying
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Upcoming Stays -->
                            <div v-if="upcomingBookings.length > 0">
                                <h3 class="text-lg font-bold text-blue-900 mb-4">Upcoming Stays</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-for="booking in upcomingBookings.slice(0, 2)" :key="booking.id" class="bg-white rounded-xl p-6 border border-gold-200 hover:shadow-md transition-shadow cursor-pointer" @click="viewBooking(booking.id)">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="font-semibold text-blue-900">{{ booking.room.name }}</h4>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Confirmed
                                            </span>
                                        </div>
                                        <div class="space-y-2 text-sm text-blue-700">
                                            <div class="flex items-center">
                                                <Calendar class="h-4 w-4 mr-2" />
                                                {{ formatDate(booking.check_in_date) }} - {{ formatDate(booking.check_out_date) }}
                                            </div>
                                            <div class="flex items-center">
                                                <MapPin class="h-4 w-4 mr-2" />
                                                Room {{ booking.room.number }}
                                            </div>
                                            <div class="flex items-center">
                                                <CreditCard class="h-4 w-4 mr-2" />
                                                ${{ booking.total_amount }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div>
                                <h3 class="text-lg font-bold text-blue-900 mb-4">Quick Actions</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <button @click="bookNewRoom" class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-lg transition duration-300">
                                        Book New Room
                                    </button>
                                    <Link href="/contact" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 text-center">
                                        Contact Support
                                    </Link>
                                    <Link href="/gallery" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 text-center">
                                        View Gallery
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Bookings Tab -->
                        <div v-if="activeTab === 'bookings'" class="space-y-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-blue-900">All Bookings</h3>
                                <button @click="bookNewRoom" class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-2 px-4 rounded-lg transition duration-300">
                                    New Booking
                                </button>
                            </div>

                            <div v-if="bookings.length === 0" class="text-center py-12">
                                <div class="bg-blue-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <Calendar class="h-8 w-8 text-blue-600" />
                                </div>
                                <h3 class="text-lg font-bold text-blue-900 mb-2">No bookings yet</h3>
                                <p class="text-blue-700 mb-6">Start your journey with us by booking your first stay.</p>
                                <button @click="bookNewRoom" class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-full transition duration-300">
                                    Book Your First Stay
                                </button>
                            </div>

                            <div v-else class="space-y-4">
                                <div v-for="booking in bookings" :key="booking.id" class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-md transition-shadow cursor-pointer" @click="viewBooking(booking.id)">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="font-semibold text-blue-900">{{ booking.room.name }} ({{ booking.room.number }})</h4>
                                            <p class="text-blue-700 text-sm">Booking #{{ booking.booking_code }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mb-2" :class="getStatusClass(booking.status)">
                                                {{ booking.status.replace('_', ' ').toUpperCase() }}
                                            </span>
                                            <div class="text-sm text-gray-600">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getPaymentStatusClass(booking.payment_status)">
                                                    {{ booking.payment_status.toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-700">
                                        <div class="flex items-center">
                                            <Calendar class="h-4 w-4 mr-2" />
                                            {{ formatDate(booking.check_in_date) }} - {{ formatDate(booking.check_out_date) }}
                                        </div>
                                        <div class="flex items-center">
                                            <Clock class="h-4 w-4 mr-2" />
                                            {{ calculateNights(booking.check_in_date, booking.check_out_date) }} nights
                                        </div>
                                        <div class="flex items-center">
                                            <CreditCard class="h-4 w-4 mr-2" />
                                            ${{ booking.total_amount }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Tab -->
                        <div v-if="activeTab === 'profile'" class="space-y-6">
                            <h3 class="text-lg font-bold text-blue-900">Profile Information</h3>
                            <div class="bg-white rounded-xl p-6 border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-2">Full Name</label>
                                        <input type="text" :value="user.name" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-2">Email Address</label>
                                        <input type="email" :value="user.email" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-2">Phone Number</label>
                                        <input type="tel" :value="user.phone || 'Not provided'" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-2">Address</label>
                                        <input type="text" :value="user.address || 'Not provided'" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50" readonly>
                                    </div>
                                </div>
                                <div class="mt-6 flex justify-end">
                                    <Link href="/settings/profile" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                                        Edit Profile
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WelcomeLayout>
</template>

<style scoped>
    /* Custom color palette */
    .bg-gold-500 { background-color: #D4AF37; }
    .bg-gold-600 { background-color: #B8860B; }
    .bg-gold-100 { background-color: #FAF3E0; }
    .text-gold-500 { color: #D4AF37; }
    .text-gold-600 { color: #B8860B; }
    .bg-blue-900 { background-color: #0C2D6A; }
    .text-blue-900 { color: #0C2D6A; }
    .text-blue-700 { color: #1A4480; }
    .bg-blue-50 { background-color: #F0F5FF; }
    .border-gold-100 { border-color: #FAF3E0; }
    .border-gold-200 { border-color: #F5E7C1; }
</style>
