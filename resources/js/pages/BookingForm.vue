<script setup lang="ts">
    import { ref, computed, onMounted, watch } from 'vue';
    import { Link, router, useForm } from '@inertiajs/vue3';
    import WelcomeLayout from '@/layouts/WelcomeLayout.vue';

    // Props from the server
    const props = defineProps<{
        room?: {
            id: number;
            number: string;
            name: string;
            type: string;
            base_price: number;
            capacity: number;
            status: string;
            description: string;
        };
        room_id?: number;
        check_in?: string;
        check_out?: string;
        adults?: number;
        children?: number;
    }>();

    // Form data
    const form = useForm({
        room_id: props.room_id || props.room?.id || '',
        check_in_date: props.check_in || '',
        check_out_date: props.check_out || '',
        adults: props.adults || 1,
        children: props.children || 0,
        guest_name: '',
        guest_email: '',
        guest_phone: '',
        special_requests: '',
        emergency_contact: '',
        emergency_phone: '',
        payment_method: 'credit_card'
    });

    // Reactive data
    const selectedRoom = ref(props.room);
    const availableRooms = ref([]);
    const loading = ref(false);

    // Computed values
    const nights = computed(() => {
        if (form.check_in_date && form.check_out_date) {
            const start = new Date(form.check_in_date);
            const end = new Date(form.check_out_date);
            const diffTime = Math.abs(end.getTime() - start.getTime());
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        }
        return 0;
    });

    const totalPrice = computed(() => {
        if (selectedRoom.value) {
            return selectedRoom.value.base_price * nights.value;
        }
        return 0;
    });

    const totalGuests = computed(() => {
        return form.adults + form.children;
    });

    // Load available rooms
    const loadAvailableRooms = async () => {
        if (form.check_in_date && form.check_out_date) {
            loading.value = true;
            try {
                const response = await fetch(`/api/bookings/available-rooms?check_in_date=${form.check_in_date}&check_out_date=${form.check_out_date}`);
                const data = await response.json();
                availableRooms.value = data;
            } catch (error) {
                console.error('Error loading available rooms:', error);
            } finally {
                loading.value = false;
            }
        }
    };

    // Select room
    const selectRoom = (room: any) => {
        selectedRoom.value = room;
        form.room_id = room.id;
    };

    // Update checkout date when checkin changes
    const updateCheckout = () => {
        if (form.check_in_date) {
            const checkInDate = new Date(form.check_in_date);
            const checkOutDate = new Date(checkInDate);
            checkOutDate.setDate(checkOutDate.getDate() + 1);
            form.check_out_date = checkOutDate.toISOString().split('T')[0];
        }
    };

    // Submit booking
    const submitBooking = () => {
        form.post(route('room.booking.store'), {
            onSuccess: () => {
                // Redirect will be handled by the controller
            },
            onError: (errors) => {
                console.error('Booking errors:', errors);
                // You can add toast notifications here if needed
            }
        });
    };

    // Watch for date changes
    watch(() => form.check_in_date, () => {
        updateCheckout();
        loadAvailableRooms();
    });

    watch(() => form.check_out_date, () => {
        loadAvailableRooms();
    });

    // Initialize
    onMounted(() => {
        if (form.check_in_date) {
            updateCheckout();
            loadAvailableRooms();
        }
    });
</script>

<template>
    <WelcomeLayout>
        <div class="min-h-screen bg-blue-50">
            <!-- Breadcrumb -->
            <div class="bg-white border-b border-gray-200">
                <div class="container mx-auto px-6 py-4">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <Link :href="route('home')" class="text-blue-600 hover:text-blue-800">
                                    Home
                                </Link>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <Link :href="route('hotel-rooms')" class="ml-1 text-blue-600 hover:text-blue-800 md:ml-2">
                                        Rooms
                                    </Link>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-1 text-gray-500 md:ml-2">Book Room</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="container mx-auto px-6 py-8">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-3xl font-bold text-blue-900 mb-8">Book Your Stay</h1>

                    <form @submit.prevent="submitBooking" class="space-y-8">
                        <!-- Date Selection -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4">Select Dates</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Check-in Date</label>
                                    <input 
                                        type="date" 
                                        v-model="form.check_in_date"
                                        :min="new Date().toISOString().split('T')[0]"
                                        :class="[
                                            'w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500',
                                            form.errors.check_in_date ? 'border-red-500' : 'border-blue-200'
                                        ]"
                                        required
                                    >
                                    <p v-if="form.errors.check_in_date" class="text-red-500 text-sm mt-1">{{ form.errors.check_in_date }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Check-out Date</label>
                                    <input 
                                        type="date" 
                                        v-model="form.check_out_date"
                                        :min="form.check_in_date"
                                        :class="[
                                            'w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500',
                                            form.errors.check_out_date ? 'border-red-500' : 'border-blue-200'
                                        ]"
                                        required
                                    >
                                    <p v-if="form.errors.check_out_date" class="text-red-500 text-sm mt-1">{{ form.errors.check_out_date }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Guest Information -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4">Guest Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Adults</label>
                                    <select 
                                        v-model="form.adults"
                                        class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                    >
                                        <option v-for="n in 4" :key="n" :value="n">{{ n }} {{ n > 1 ? 'Adults' : 'Adult' }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Children</label>
                                    <select 
                                        v-model="form.children"
                                        class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                    >
                                        <option v-for="n in 4" :key="n" :value="n-1">{{ n-1 }} {{ n-1 > 1 ? 'Children' : 'Child' }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Full Name</label>
                                    <input 
                                        type="text" 
                                        v-model="form.guest_name"
                                        :class="[
                                            'w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500',
                                            form.errors.guest_name ? 'border-red-500' : 'border-blue-200'
                                        ]"
                                        required
                                    >
                                    <p v-if="form.errors.guest_name" class="text-red-500 text-sm mt-1">{{ form.errors.guest_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Email</label>
                                    <input 
                                        type="email" 
                                        v-model="form.guest_email"
                                        :class="[
                                            'w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500',
                                            form.errors.guest_email ? 'border-red-500' : 'border-blue-200'
                                        ]"
                                        required
                                    >
                                    <p v-if="form.errors.guest_email" class="text-red-500 text-sm mt-1">{{ form.errors.guest_email }}</p>
                                </div>
                            </div>
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-blue-700 mb-2">Phone Number</label>
                                <input 
                                    type="tel" 
                                    v-model="form.guest_phone"
                                    :class="[
                                        'w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500',
                                        form.errors.guest_phone ? 'border-red-500' : 'border-blue-200'
                                    ]"
                                    required
                                >
                                <p v-if="form.errors.guest_phone" class="text-red-500 text-sm mt-1">{{ form.errors.guest_phone }}</p>
                            </div>
                        </div>

                        <!-- Room Selection -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4">Select Room</h2>
                            <div v-if="loading" class="text-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gold-500 mx-auto"></div>
                                <p class="text-gray-600 mt-2">Loading available rooms...</p>
                            </div>
                            <div v-if="form.errors.room_id" class="text-red-500 text-sm mb-2">{{ form.errors.room_id }}</div>
                            <div v-else-if="availableRooms.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div 
                                    v-for="room in availableRooms" 
                                    :key="room.id"
                                    @click="selectRoom(room)"
                                    class="border-2 rounded-lg p-4 cursor-pointer transition"
                                    :class="{
                                        'border-gold-500 bg-gold-50': selectedRoom?.id === room.id,
                                        'border-gray-200 hover:border-blue-300': selectedRoom?.id !== room.id
                                    }"
                                >
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-blue-900">{{ room.name }}</h3>
                                        <span class="text-gold-500 font-bold">${{ room.base_price }}/night</span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">{{ room.description }}</p>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ room.capacity }} Guests
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <p class="text-gray-600">No rooms available for the selected dates</p>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4">Emergency Contact</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Emergency Contact Name</label>
                                    <input 
                                        type="text" 
                                        v-model="form.emergency_contact"
                                        class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Emergency Contact Phone</label>
                                    <input 
                                        type="tel" 
                                        v-model="form.emergency_phone"
                                        class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4">Special Requests</h2>
                            <textarea 
                                v-model="form.special_requests"
                                rows="4"
                                placeholder="Any special requests or preferences..."
                                class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                            ></textarea>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4">Payment Method</h2>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input 
                                        type="radio" 
                                        v-model="form.payment_method"
                                        value="credit_card"
                                        class="h-4 w-4 text-gold-600 border-blue-300 focus:ring-gold-500"
                                    >
                                    <span class="ml-3 text-blue-700">Credit Card</span>
                                </label>
                                <label class="flex items-center">
                                    <input 
                                        type="radio" 
                                        v-model="form.payment_method"
                                        value="paypal"
                                        class="h-4 w-4 text-gold-600 border-blue-300 focus:ring-gold-500"
                                    >
                                    <span class="ml-3 text-blue-700">PayPal</span>
                                </label>
                                <label class="flex items-center">
                                    <input 
                                        type="radio" 
                                        v-model="form.payment_method"
                                        value="bank_transfer"
                                        class="h-4 w-4 text-gold-600 border-blue-300 focus:ring-gold-500"
                                    >
                                    <span class="ml-3 text-blue-700">Bank Transfer</span>
                                </label>
                            </div>
                        </div>

                        <!-- Booking Summary -->
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4">Booking Summary</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Room:</span>
                                    <span class="font-medium">{{ selectedRoom?.name || 'Not selected' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Check-in:</span>
                                    <span class="font-medium">{{ form.check_in_date || 'Not selected' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Check-out:</span>
                                    <span class="font-medium">{{ form.check_out_date || 'Not selected' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nights:</span>
                                    <span class="font-medium">{{ nights }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Guests:</span>
                                    <span class="font-medium">{{ totalGuests }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between text-lg font-bold text-blue-900">
                                        <span>Total:</span>
                                        <span>${{ totalPrice }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button 
                                type="submit"
                                :disabled="form.processing || !selectedRoom || !form.check_in_date || !form.check_out_date || !form.guest_name || !form.guest_email || !form.guest_phone"
                                class="bg-gold-500 hover:bg-gold-600 disabled:bg-gray-400 text-blue-900 font-bold py-3 px-8 rounded-full transition duration-300"
                            >
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>Confirm Booking</span>
                            </button>
                        </div>
                    </form>
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
    .text-gold-500 { color: #D4AF37; }
    .bg-blue-900 { background-color: #0C2D6A; }
    .text-blue-900 { color: #0C2D6A; }
    .text-blue-700 { color: #1A4480; }
    .bg-blue-50 { background-color: #F0F5FF; }
</style>
