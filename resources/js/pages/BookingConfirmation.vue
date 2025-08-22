<script setup lang="ts">
    import { Link, computed } from '@inertiajs/vue3';
    import WelcomeLayout from '@/layouts/WelcomeLayout.vue';

    // Props from the server
    const props = defineProps<{
        booking?: {
            id: number;
            booking_code: string;
            check_in_date: string;
            check_out_date: string;
            total_amount: number;
            room: {
                name: string;
                number: string;
            };
            guest: {
                name: string;
                email: string;
            };
        };
    }>();

    // Mock booking data if not provided
    const booking = props.booking || {
        id: 1,
        booking_code: 'BK' + Math.random().toString(36).substr(2, 9).toUpperCase(),
        check_in_date: '2025-01-15',
        check_out_date: '2025-01-17',
        total_amount: 240.00,
        room: {
            name: 'Deluxe Single Room',
            number: '101'
        },
        guest: {
            name: 'John Doe',
            email: 'john.doe@example.com'
        }
    };

    // Format date
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    // Calculate nights
    const nights = computed(() => {
        const start = new Date(booking.check_in_date);
        const end = new Date(booking.check_out_date);
        const diffTime = Math.abs(end.getTime() - start.getTime());
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    });
</script>

<template>
    <WelcomeLayout>
        <div class="min-h-screen bg-blue-50">
            <!-- Success Header -->
            <div class="bg-white border-b border-gray-200">
                <div class="container mx-auto px-6 py-8">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-blue-900 mb-2">Booking Confirmed!</h1>
                        <p class="text-blue-700 text-lg">Thank you for choosing our hotel. Your booking has been successfully confirmed.</p>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-6 py-8">
                <div class="max-w-2xl mx-auto">
                    <!-- Booking Details Card -->
                    <div class="bg-white rounded-xl shadow-md p-8 mb-8">
                        <h2 class="text-2xl font-bold text-blue-900 mb-6">Booking Details</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="font-semibold text-blue-900 mb-2">Booking Information</h3>
                                <div class="space-y-2 text-gray-700">
                                    <div class="flex justify-between">
                                        <span>Booking Code:</span>
                                        <span class="font-mono font-semibold text-blue-600">{{ booking.booking_code }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Room:</span>
                                        <span>{{ booking.room.name }} ({{ booking.room.number }})</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Nights:</span>
                                        <span>{{ nights }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Total Amount:</span>
                                        <span class="font-semibold text-gold-600">${{ booking.total_amount }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-semibold text-blue-900 mb-2">Guest Information</h3>
                                <div class="space-y-2 text-gray-700">
                                    <div class="flex justify-between">
                                        <span>Name:</span>
                                        <span>{{ booking.guest.name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Email:</span>
                                        <span>{{ booking.guest.email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="font-semibold text-blue-900 mb-4">Stay Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="font-semibold text-blue-900">Check-in</span>
                                    </div>
                                    <p class="text-blue-700">{{ formatDate(booking.check_in_date) }}</p>
                                    <p class="text-sm text-blue-600">From 2:00 PM</p>
                                </div>
                                
                                <div class="bg-gold-50 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="font-semibold text-blue-900">Check-out</span>
                                    </div>
                                    <p class="text-blue-700">{{ formatDate(booking.check_out_date) }}</p>
                                    <p class="text-sm text-blue-600">Until 11:00 AM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Information -->
                    <div class="bg-white rounded-xl shadow-md p-8 mb-8">
                        <h2 class="text-xl font-bold text-blue-900 mb-4">Important Information</h2>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-blue-900">Check-in Process</h3>
                                    <p class="text-gray-700">Please arrive at the hotel with a valid ID and the credit card used for booking. Check-in time is from 2:00 PM onwards.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-gold-100 p-2 rounded-lg mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-blue-900">Cancellation Policy</h3>
                                    <p class="text-gray-700">Free cancellation up to 24 hours before check-in. Late cancellations may incur charges.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-blue-900">Confirmation Email</h3>
                                    <p class="text-gray-700">A confirmation email has been sent to {{ booking.guest.email }} with all the details.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <Link 
                            :href="route('home')"
                            class="flex-1 bg-blue-900 hover:bg-blue-800 text-white font-bold py-3 px-6 rounded-full transition duration-300 text-center"
                        >
                            Return to Home
                        </Link>
                        <Link 
                            :href="route('hotel-rooms')"
                            class="flex-1 bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-full transition duration-300 text-center"
                        >
                            Browse More Rooms
                        </Link>
                    </div>

                    <!-- Contact Information -->
                    <div class="mt-8 text-center">
                        <p class="text-gray-600 mb-2">Need help? Contact us:</p>
                        <div class="flex justify-center space-x-6 text-sm">
                            <span class="text-blue-600">📞 +1 (555) 123-4567</span>
                            <span class="text-blue-600">✉️ info@luxuryhotel.com</span>
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
    .bg-gold-50 { background-color: #FAF3E0; }
    .text-gold-600 { color: #B8860B; }
    .bg-blue-900 { background-color: #0C2D6A; }
    .text-blue-900 { color: #0C2D6A; }
    .text-blue-700 { color: #1A4480; }
    .bg-blue-50 { background-color: #F0F5FF; }
</style>
