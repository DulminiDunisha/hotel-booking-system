<script setup lang="ts">
    import { ref, computed, watch } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import { Swiper, SwiperSlide } from 'swiper/vue';
    import { Navigation, Pagination } from 'swiper/modules';
    import 'swiper/css';
    import 'swiper/css/navigation';
    import 'swiper/css/pagination';

    import WelcomeLayout from '@/layouts/WelcomeLayout.vue';

    // Props from the server
    const props = defineProps<{
        room: {
            id: number;
            number: string;
            name: string;
            type: string;
            base_price: number;
            capacity: number;
            status: string;
            description: string;
            image?: string;
            amenities: string[] | null;
            seasonalRates: any[];
            availabilities: any[];
        };
        relatedRooms: any[];
    }>();

    // Reactive data
    const room = ref(props.room);
    const relatedRooms = ref(props.relatedRooms);
    const selectedDate = ref('');
    const nights = ref(1);
    const adults = ref(1);
    const children = ref(0);

    // Booking form
    const checkIn = ref('');
    const checkOut = ref('');

    // Computed values
    const totalPrice = computed(() => {
        return room.value.base_price * nights.value;
    });

    const totalGuests = computed(() => {
        return adults.value + children.value;
    });

    // Get room features
    const getRoomFeatures = () => {
        if (room.value.amenities && Array.isArray(room.value.amenities)) {
            return room.value.amenities;
        }
        return ['Free WiFi', 'Air Conditioning', 'Private Bathroom', 'TV', 'Mini Fridge'];
    };

    // Get room size based on type
    const getRoomSize = () => {
        const sizes = {
            'Single': '25 m²',
            'Double': '35 m²',
            'Suite': '65 m²',
            'Deluxe': '45 m²',
            'Standard': '30 m²'
        };
        return sizes[room.value.type] || '30 m²';
    };

    // Book this room
    const bookRoom = () => {
        router.visit(route('room.booking'), {
            data: {
                room_id: room.value.id,
                check_in: checkIn.value,
                check_out: checkOut.value,
                adults: adults.value,
                children: children.value
            }
        });
    };

    // Modules for Swiper
    const modules = [Navigation, Pagination];

    // Update checkout date when checkin changes
    const updateCheckout = () => {
        if (checkIn.value) {
            const checkInDate = new Date(checkIn.value);
            const checkOutDate = new Date(checkInDate);
            checkOutDate.setDate(checkOutDate.getDate() + nights.value);
            checkOut.value = checkOutDate.toISOString().split('T')[0];
        }
    };

    // Update nights when dates change
    const updateNights = () => {
        if (checkIn.value && checkOut.value) {
            const start = new Date(checkIn.value);
            const end = new Date(checkOut.value);
            const diffTime = Math.abs(end.getTime() - start.getTime());
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            nights.value = diffDays;
        }
    };

    // Watch for changes
    watch(checkIn, updateCheckout);
    watch(checkOut, updateNights);
    watch(nights, updateCheckout);

    // Handle image loading errors
    const handleImageError = (event: Event) => {
        const target = event.target as HTMLImageElement
        const parent = target.parentElement
        if (parent) {
            // Hide loading skeleton
            const skeleton = parent.querySelector('.animate-pulse')
            if (skeleton) {
                skeleton.classList.add('hidden')
            }
            
            parent.innerHTML = `
                <div class="h-96 bg-gradient-to-br from-blue-100 to-gold-100 flex items-center justify-center">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-blue-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-blue-600 font-medium text-lg">Room ${room.value.number}</p>
                        <p class="text-blue-500">${room.value.type} Room</p>
                    </div>
                </div>
            `
        }
    };

    // Handle image load success
    const handleImageLoad = (event: Event) => {
        const target = event.target as HTMLImageElement
        target.style.opacity = '1'
        // Hide loading skeleton
        const skeleton = target.parentElement?.querySelector('.animate-pulse')
        if (skeleton) {
            skeleton.classList.add('hidden')
        }
    };

    // Handle related room image loading errors
    const handleRelatedImageError = (event: Event) => {
        const target = event.target as HTMLImageElement
        const parent = target.parentElement
        if (parent) {
            // Hide loading skeleton
            const skeleton = parent.querySelector('.animate-pulse')
            if (skeleton) {
                skeleton.classList.add('hidden')
            }
            
            parent.innerHTML = `
                <div class="h-full bg-gradient-to-br from-blue-100 to-gold-100 flex items-center justify-center">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-blue-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-blue-600 font-medium text-sm">Room Image</p>
                    </div>
                </div>
            `
        }
    };

    // Handle related room image load success
    const handleRelatedImageLoad = (event: Event) => {
        const target = event.target as HTMLImageElement
        target.style.opacity = '1'
        // Hide loading skeleton
        const skeleton = target.parentElement?.querySelector('.animate-pulse')
        if (skeleton) {
            skeleton.classList.add('hidden')
        }
    };
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
                                    <span class="ml-1 text-gray-500 md:ml-2">{{ room.name }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="container mx-auto px-6 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <!-- Room Images -->
                        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                            <div v-if="room.image" class="h-96 overflow-hidden relative">
                                <div class="relative h-full">
                                    <!-- Loading skeleton -->
                                    <div class="absolute inset-0 bg-gray-200 animate-pulse rounded-lg"></div>
                                    <img 
                                        :src="room.image" 
                                        :alt="room.name"
                                        class="relative z-10 w-full h-full object-cover transition duration-300"
                                        @error="handleImageError"
                                        @load="handleImageLoad"
                                    >
                                </div>
                                <!-- Image overlay with room info -->
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 via-transparent to-transparent p-6 z-20">
                                    <div class="text-white">
                                        <h2 class="text-2xl font-bold mb-2">{{ room.name }}</h2>
                                        <p class="text-lg text-gray-200">{{ room.type }} Room</p>
                                    </div>
                                </div>
                                <!-- Room type badge -->
                                <div class="absolute top-4 left-4 z-20">
                                    <span class="bg-blue-900/80 text-white px-3 py-1 rounded-full text-sm font-medium backdrop-blur-sm">
                                        {{ room.type }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="h-96 bg-gradient-to-br from-blue-100 to-gold-100 flex items-center justify-center">
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-blue-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-blue-600 font-medium text-lg">Room {{ room.number }}</p>
                                    <p class="text-blue-500">{{ room.type }} Room</p>
                                </div>
                            </div>
                        </div>

                        <!-- Room Information -->
                        <div class="bg-white rounded-xl shadow-md p-8 mb-8">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold text-blue-900 mb-2">{{ room.name }}</h1>
                                    <p class="text-blue-700 text-lg">{{ room.description }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-gold-500">${{ room.base_price }}</div>
                                    <div class="text-gray-600">per night</div>
                                </div>
                            </div>

                            <!-- Room Details -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-blue-900">Capacity</div>
                                        <div class="text-gray-600">{{ room.capacity }} Guests</div>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <div class="bg-gold-100 p-3 rounded-lg mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-blue-900">Size</div>
                                        <div class="text-gray-600">{{ getRoomSize() }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <div class="bg-green-100 p-3 rounded-lg mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-blue-900">Status</div>
                                        <div class="text-gray-600 capitalize">{{ room.status || 'Available' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amenities -->
                            <div class="mb-8">
                                <h3 class="text-xl font-bold text-blue-900 mb-4">Room Amenities</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div v-for="amenity in getRoomFeatures()" :key="amenity" class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-gray-700">{{ amenity }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Rooms -->
                        <div v-if="relatedRooms.length > 0" class="bg-white rounded-xl shadow-md p-8">
                            <h3 class="text-xl font-bold text-blue-900 mb-6">Similar Rooms</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div v-for="relatedRoom in relatedRooms" :key="relatedRoom.id" class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                                    <!-- Related room image -->
                                    <div class="h-48 overflow-hidden">
                                        <div v-if="relatedRoom.image" class="relative h-full">
                                            <!-- Loading skeleton -->
                                            <div class="absolute inset-0 bg-gray-200 animate-pulse rounded-lg"></div>
                                            <img 
                                                :src="relatedRoom.image" 
                                                :alt="relatedRoom.name"
                                                class="relative z-10 w-full h-full object-cover transition duration-300 hover:scale-105"
                                                @error="handleRelatedImageError"
                                                @load="handleRelatedImageLoad"
                                            >
                                        </div>
                                        <div v-else class="h-full bg-gradient-to-br from-blue-100 to-gold-100 flex items-center justify-center">
                                            <div class="text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-blue-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <p class="text-blue-600 font-medium text-sm">Room {{ relatedRoom.number }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <h4 class="font-semibold text-blue-900 mb-2">{{ relatedRoom.name }}</h4>
                                        <p class="text-gold-500 font-bold mb-2">${{ relatedRoom.base_price }}/night</p>
                                        <p class="text-gray-600 text-sm mb-3">{{ relatedRoom.description }}</p>
                                        <Link :href="route('room.details', relatedRoom.id)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Details →
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-md p-6 sticky top-8">
                            <h3 class="text-xl font-bold text-blue-900 mb-6">Book This Room</h3>
                            
                            <!-- Date Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-blue-700 mb-2">Check-in Date</label>
                                <input 
                                    type="date" 
                                    v-model="checkIn"
                                    class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                >
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-blue-700 mb-2">Check-out Date</label>
                                <input 
                                    type="date" 
                                    v-model="checkOut"
                                    class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                >
                            </div>

                            <!-- Guest Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-blue-700 mb-2">Adults</label>
                                <select 
                                    v-model="adults"
                                    class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                >
                                    <option v-for="n in 4" :key="n" :value="n">{{ n }} {{ n > 1 ? 'Adults' : 'Adult' }}</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-blue-700 mb-2">Children</label>
                                <select 
                                    v-model="children"
                                    class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                >
                                    <option v-for="n in 4" :key="n" :value="n-1">{{ n-1 }} {{ n-1 > 1 ? 'Children' : 'Child' }}</option>
                                </select>
                            </div>

                            <!-- Price Summary -->
                            <div class="border-t border-gray-200 pt-4 mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Price per night:</span>
                                    <span class="font-medium">${{ room.base_price }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Nights:</span>
                                    <span class="font-medium">{{ nights }}</span>
                                </div>
                                <div class="flex justify-between items-center text-lg font-bold text-blue-900">
                                    <span>Total:</span>
                                    <span>${{ totalPrice }}</span>
                                </div>
                            </div>

                            <!-- Book Button -->
                            <button 
                                @click="bookRoom"
                                class="w-full bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-full transition duration-300"
                            >
                                Book Now
                            </button>

                            <!-- Availability Note -->
                            <p class="text-sm text-gray-600 mt-4 text-center">
                                Free cancellation up to 24 hours before check-in
                            </p>
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
    .text-gold-500 { color: #D4AF37; }
    .bg-blue-900 { background-color: #0C2D6A; }
    .text-blue-900 { color: #0C2D6A; }
    .text-blue-700 { color: #1A4480; }
    .bg-blue-50 { background-color: #F0F5FF; }

    /* Image loading styles */
    img {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    img[src] {
        opacity: 1;
    }
</style>
