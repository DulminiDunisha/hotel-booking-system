<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import { Swiper, SwiperSlide } from 'swiper/vue';
    import { Navigation } from 'swiper/modules';
    import 'swiper/css';
    import 'swiper/css/navigation';

    import WelcomeLayout from '@/layouts/WelcomeLayout.vue';

    // Props from the server
    const props = defineProps<{
        rooms: Array<{
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
        }>;
        roomTypes: string[];
    }>();

    // Reactive data
    const rooms = ref(props.rooms || []);
    const roomTypes = ref(props.roomTypes || []);

    // Filters
    const selectedTypes = ref<string[]>(roomTypes.value);
    const priceRange = ref<[number, number]>([0, 1000]);
    const minCapacity = ref<number>(1);
    const sortBy = ref<string>('featured');

    // Computed filtered rooms
    const filteredRooms = computed(() => {
        let result = [...rooms.value];

        // Filter by room type
        if (selectedTypes.value.length > 0) {
            result = result.filter(room => selectedTypes.value.includes(room.type));
        }

        // Filter by price
        result = result.filter(room => room.base_price >= priceRange.value[0] && room.base_price <= priceRange.value[1]);

        // Filter by capacity
        result = result.filter(room => room.capacity >= minCapacity.value);

        // Sorting
        if (sortBy.value === 'price-low') {
            result.sort((a, b) => a.base_price - b.base_price);
        } else if (sortBy.value === 'price-high') {
            result.sort((a, b) => b.base_price - a.base_price);
        } else if (sortBy.value === 'capacity') {
            result.sort((a, b) => b.capacity - a.capacity);
        }

        return result;
    });

    // Capacity options
    const capacityOptions = [1, 2, 3, 4, 5, 6];

    // Toggle room type filter
    const toggleRoomType = (type: string) => {
        if (selectedTypes.value.includes(type)) {
            selectedTypes.value = selectedTypes.value.filter(t => t !== type);
        } else {
            selectedTypes.value.push(type);
        }
    };

    // Reset filters
    const resetFilters = () => {
        selectedTypes.value = [...roomTypes.value];
        priceRange.value = [0, 1000];
        minCapacity.value = 1;
        sortBy.value = 'featured';
    };

    // Book room
    const bookRoom = (roomId: number) => {
        router.visit(route('room.booking'), {
            data: { room_id: roomId }
        });
    };

    // View room details
    const viewDetails = (roomId: number) => {
        router.visit(route('room.details', roomId));
    };

    // Modules for Swiper
    const modules = [Navigation];

    // Get room features from amenities
    const getRoomFeatures = (room: any) => {
        if (room.amenities && Array.isArray(room.amenities) && room.amenities.length > 0) {
            return room.amenities.slice(0, 3);
        }
        return ['Free WiFi', 'Air Conditioning', 'Private Bathroom'];
    };

    // Get room size based on type
    const getRoomSize = (type: string) => {
        const sizes = {
            'Single': '25 m²',
            'Double': '35 m²',
            'Suite': '65 m²',
            'Deluxe': '45 m²',
            'Standard': '30 m²'
        };
        return sizes[type] || '30 m²';
    };

    // Get room rating (mock for now)
    const getRoomRating = (roomId: number) => {
        const ratings = [4.5, 4.7, 4.8, 4.6, 4.9];
        return ratings[roomId % ratings.length];
    };

    // Get room reviews count (mock for now)
    const getRoomReviews = (roomId: number) => {
        const reviews = [45, 67, 89, 123, 156];
        return reviews[roomId % reviews.length];
    };

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
                <div class="h-full bg-gradient-to-br from-blue-100 to-gold-100 flex items-center justify-center">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 mx-auto text-blue-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-blue-600 font-medium text-sm sm:text-base">Room Image</p>
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
</script>

<template>
    <WelcomeLayout>
        <div class="min-h-screen bg-blue-50">
            <!-- Hero Section -->
            <section class="bg-blue-900 py-16 sm:py-20 lg:py-24 relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-gold-600/20 z-0"></div>
                <div class="container mx-auto px-4 sm:px-6 max-w-6xl relative z-10">
                    <div class="max-w-3xl">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-3 sm:mb-4 leading-tight">Luxurious Accommodations</h1>
                        <p class="text-lg sm:text-xl text-blue-100 mb-6 sm:mb-8">Discover our exquisite collection of rooms and suites designed for ultimate comfort and relaxation.</p>
                        <div class="flex items-center">
                            <div class="h-1 w-12 sm:w-16 bg-gold-500 mr-3 sm:mr-4"></div>
                            <p class="text-gold-300 text-sm sm:text-base">Experience unparalleled luxury with breathtaking views</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main Content -->
            <div class="container mx-auto px-4 sm:px-6 max-w-6xl py-8 sm:py-12 -mt-8 sm:-mt-12 lg:-mt-16 relative z-20">
                <div class="bg-white rounded-xl shadow-xl p-4 sm:p-6 mb-6 sm:mb-8 border border-gold-200">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 sm:gap-6">
                        <div class="w-full lg:w-auto">
                            <h2 class="text-xl sm:text-2xl font-bold text-blue-900">Find Your Perfect Stay</h2>
                            <p class="text-blue-700 text-sm sm:text-base">{{ filteredRooms.length }} rooms match your criteria</p>
                        </div>

                        <div class="flex flex-wrap gap-3 sm:gap-4 w-full lg:w-auto">
                            <!-- Sort by dropdown -->
                            <div class="relative flex-1 sm:flex-none">
                                <select v-model="sortBy" class="appearance-none bg-white border border-blue-200 rounded-lg pl-3 sm:pl-4 pr-8 sm:pr-10 py-2 sm:py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 text-blue-900 text-sm w-full sm:w-auto">
                                    <option value="featured">Sort by: Featured</option>
                                    <option value="price-low">Price: Low to High</option>
                                    <option value="price-high">Price: High to Low</option>
                                    <option value="capacity">Capacity</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-blue-700">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Reset filters button -->
                            <button
                                @click="resetFilters"
                                class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-2 px-3 sm:px-4 rounded-lg transition flex items-center text-sm"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span class="hidden sm:inline">Reset</span>
                                <span class="sm:hidden">Reset</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-6 sm:gap-8">
                    <!-- Filters Sidebar -->
                    <div class="w-full lg:w-1/4">
                        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-6 border border-gold-100">
                            <h3 class="text-base sm:text-lg font-bold text-blue-900 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter Rooms
                            </h3>

                            <!-- Room Type Filter -->
                            <div class="mb-6">
                                <h4 class="font-medium text-blue-900 mb-3 text-sm sm:text-base">Room Type</h4>
                                <div class="space-y-2">
                                    <div v-for="type in roomTypes" :key="type" class="flex items-center">
                                        <input
                                            :id="type"
                                            type="checkbox"
                                            class="h-4 w-4 text-gold-600 border-blue-300 rounded focus:ring-gold-500"
                                            :checked="selectedTypes.includes(type)"
                                            @change="toggleRoomType(type)"
                                        >
                                        <label :for="type" class="ml-3 text-blue-700 text-sm sm:text-base">{{ type }}</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Filter -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-medium text-blue-900 text-sm sm:text-base">Price Range</h4>
                                    <span class="text-gold-600 font-medium text-sm">${{ priceRange[0] }} - ${{ priceRange[1] }}</span>
                                </div>
                                <div class="px-2">
                                    <input
                                        type="range"
                                        min="0"
                                        max="1000"
                                        step="10"
                                        v-model="priceRange[0]"
                                        class="w-full accent-gold-500"
                                    >
                                    <input
                                        type="range"
                                        min="0"
                                        max="1000"
                                        step="10"
                                        v-model="priceRange[1]"
                                        class="w-full accent-gold-500 mt-2"
                                    >
                                </div>
                                <div class="flex justify-between mt-1 text-xs sm:text-sm text-blue-600">
                                    <span>$0</span>
                                    <span>$1000</span>
                                </div>
                            </div>

                            <!-- Capacity Filter -->
                            <div>
                                <h4 class="font-medium text-blue-900 mb-3 text-sm sm:text-base">Guests</h4>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        v-for="cap in capacityOptions"
                                        :key="cap"
                                        @click="minCapacity = cap"
                                        class="py-2 rounded-lg border transition text-xs sm:text-sm"
                                        :class="{
                    'bg-gold-500 text-blue-900 border-gold-500 font-medium': minCapacity === cap,
                    'bg-white text-blue-700 border-blue-200 hover:bg-blue-50': minCapacity !== cap
                  }"
                                    >
                                        {{ cap }} {{ cap > 1 ? 'Guests' : 'Guest' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Promo Banner -->
                        <div class="bg-gradient-to-br from-blue-900 to-gold-600 rounded-xl shadow-md p-4 sm:p-6 text-white">
                            <h3 class="text-base sm:text-lg font-bold mb-2">Special Offer</h3>
                            <p class="mb-4 text-sm sm:text-base">Book 3 nights or more and get 15% discount on your stay!</p>
                            <button class="bg-gold-500 hover:bg-gold-400 text-blue-900 font-bold py-2 px-3 sm:px-4 rounded-full transition w-full text-sm">
                                View Details
                            </button>
                        </div>
                    </div>

                    <!-- Room Listings -->
                    <div class="w-full lg:w-3/4">
                        <div v-if="filteredRooms.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div v-for="room in filteredRooms" :key="room.id" class="bg-white rounded-xl shadow-md overflow-hidden border border-gold-100 transition duration-300 hover:shadow-xl">
                                <!-- Room Image -->
                                <div class="h-48 sm:h-56 lg:h-64 overflow-hidden relative">
                                    <div v-if="room.image" class="relative h-full">
                                        <!-- Loading skeleton -->
                                        <div class="absolute inset-0 bg-gray-200 animate-pulse rounded-lg"></div>
                                        <img 
                                            :src="room.image" 
                                            :alt="room.name"
                                            class="relative z-10 w-full h-full object-cover transition duration-300 hover:scale-105"
                                            @error="handleImageError"
                                            @load="handleImageLoad"
                                        >
                                    </div>
                                    <div v-else class="h-full bg-gradient-to-br from-blue-100 to-gold-100 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 mx-auto text-blue-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <p class="text-blue-600 font-medium text-sm sm:text-base">Room {{ room.number }}</p>
                                        </div>
                                    </div>
                                    <!-- Image overlay with room type badge -->
                                    <div class="absolute top-3 left-3 z-20">
                                        <span class="bg-blue-900/80 text-white px-2 py-1 rounded-full text-xs font-medium backdrop-blur-sm">
                                            {{ room.type }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Room Info -->
                                <div class="p-4 sm:p-6">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="min-w-0 flex-1">
                                            <h2 class="text-lg sm:text-xl font-bold text-blue-900 truncate">{{ room.name }}</h2>
                                            <div class="flex items-center mt-1">
                                                <span class="text-gold-500 font-bold text-base sm:text-lg">${{ room.base_price }}</span>
                                                <span class="text-gray-500 text-xs sm:text-sm ml-1">/ night</span>
                                            </div>
                                        </div>
                                        <span class="bg-blue-100 text-blue-900 text-xs font-semibold px-2 py-1 rounded ml-2 flex-shrink-0">
                    {{ room.type }}
                  </span>
                                    </div>

                                    <!-- Rating -->
                                    <div class="flex items-center mb-3 sm:mb-4">
                                        <div class="flex mr-2">
                                            <svg v-for="star in 5" :key="star" class="h-3 w-3 sm:h-4 sm:w-4" :class="star <= Math.floor(getRoomRating(room.id)) ? 'text-gold-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <span class="text-gray-600 text-xs sm:text-sm">{{ getRoomRating(room.id) }} ({{ getRoomReviews(room.id) }} reviews)</span>
                                    </div>

                                    <!-- Room Features -->
                                    <div class="mb-3 sm:mb-4">
                                        <ul class="flex flex-wrap gap-1 sm:gap-2">
                                            <li v-for="(feature, index) in getRoomFeatures(room)" :key="index" class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded">
                                                {{ feature }}
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Room Details -->
                                    <div class="flex items-center justify-between text-xs sm:text-sm text-gray-600 mb-4 sm:mb-6">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            {{ room.capacity }} Guests
                                        </div>
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                                            </svg>
                                            {{ getRoomSize(room.type) }}
                                        </div>
                                    </div>

                                    <!-- CTA Button -->
                                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                                        <button @click="bookRoom(room.id)" class="w-full sm:flex-1 bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-full transition duration-300 text-sm">
                                            Book Now
                                        </button>
                                        <button @click="viewDetails(room.id)" class="w-full sm:w-auto text-blue-700 hover:text-blue-900 flex items-center justify-center text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No results state -->
                        <div v-else class="bg-white rounded-xl shadow-md p-8 sm:p-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 mx-auto text-blue-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-xl sm:text-2xl font-bold text-blue-900 mb-2">No rooms match your criteria</h3>
                            <p class="text-blue-700 mb-6 text-sm sm:text-base">Try adjusting your filters to find available rooms</p>
                            <button
                                @click="resetFilters"
                                class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 sm:px-8 rounded-full transition duration-300 text-sm sm:text-base"
                            >
                                Reset Filters
                            </button>
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
    .border-gold-100 { border-color: #FAF3E0; }
    .border-gold-200 { border-color: #F5E7C1; }
    .bg-blue-900 { background-color: #0C2D6A; }
    .text-blue-900 { color: #0C2D6A; }
    .text-blue-700 { color: #1A4480; }
    .bg-blue-50 { background-color: #F0F5FF; }

    /* Range slider styling */
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #D4AF37;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    input[type=range]::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #D4AF37;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Image loading styles */
    img {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    img[src] {
        opacity: 1;
    }
</style>
