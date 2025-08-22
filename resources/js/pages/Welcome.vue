<script setup lang="ts">
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';
    import { Swiper, SwiperSlide } from 'swiper/vue';
    import { Autoplay, Pagination, Navigation } from 'swiper/modules';
    import 'swiper/css';
    import 'swiper/css/pagination';
    import 'swiper/css/navigation';
    import { Dumbbell, Utensils, WavesLadder, Spade } from 'lucide-vue-next'

    import WelcomeLayout from '@/layouts/WelcomeLayout.vue';

    // Hero images
    const heroSlides = ref([
        { id: 1, title: 'Luxury Beachfront Experience', subtitle: 'Where Paradise Meets Comfort', image: '/images/home-page/main-slider/1.jpg' },
        { id: 2, title: 'Premium Suite Collection', subtitle: 'Elegance Redefined', image: '/images/home-page/main-slider/2.jpg' },
        { id: 3, title: 'Award-Winning Hospitality', subtitle: 'Your Perfect Getaway', image: '/images/home-page/main-slider/3.jpeg' },
    ]);

    // Amenities data
    const amenities = ref([
        { icon: WavesLadder, title: 'Infinity Pool', description: 'Stunning ocean-view infinity pool' },
        { icon: Utensils, title: 'Fine Dining', description: 'Gourmet restaurants with sea view' },
        { icon: Spade, title: 'Spa Retreat', description: 'Luxury treatments & therapies' },
        { icon: Dumbbell, title: 'Fitness Center', description: '24/7 premium gym facilities' },
    ])

    // Testimonials
    const testimonials = ref([
        { id: 1, text: 'Absolutely breathtaking views and impeccable service. Will definitely return!', author: 'Sarah M.', rating: 5 },
        { id: 2, text: 'The suite was beyond luxurious. Attention to detail was exceptional.', author: 'James T.', rating: 5 },
        { id: 3, text: 'Best hospitality experience we\'ve ever had. Staff went above and beyond.', author: 'Priya K.', rating: 4.5 },
    ]);

    // Promotional offers
    const promotions = ref([
        { id: 1, title: 'Early Bird Special', description: 'Book 30 days in advance & save 20%', code: 'EARLY20' },
        { id: 2, title: 'Honeymoon Package', description: 'Romantic getaway with spa credits', code: 'LOVE24' },
        { id: 3, title: 'Long Stay Discount', description: 'Stay 7+ nights get 2 nights free', code: 'EXTEND7' },
    ]);

    // Booking form
    const checkIn = ref('');
    const checkOut = ref('');
    const roomType = ref('Deluxe');
    const guestCount = ref(2);

    // Check availability
    const checkAvailability = () => {
        if (checkIn.value && checkOut.value) {
            router.visit('/hotel-rooms', {
                data: {
                    check_in: checkIn.value,
                    check_out: checkOut.value,
                    room_type: roomType.value,
                    guests: guestCount.value
                }
            });
        }
    };

    // Explore rooms
    const exploreRooms = () => {
        router.visit('/hotel-rooms');
    };

    const modules = [Autoplay, Pagination, Navigation];
</script>

<template>
    <WelcomeLayout>
        <div class="min-h-screen bg-white">
            <!-- Hero Section -->
            <section class="relative h-[70vh] sm:h-[80vh] lg:h-[90vh]">
                <Swiper
                    :modules="modules"
                    :autoplay="{ delay: 5000 }"
                    :pagination="{ clickable: true }"
                    :navigation="true"
                    class="h-full"
                >
                    <SwiperSlide v-for="slide in heroSlides" :key="slide.id">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/70 to-gold-500/30 z-10"></div>
                        <img
                            :src="slide.image"
                            alt="Slide Image"
                            class="h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 z-20 flex items-center">
                            <div class="container mx-auto px-4 sm:px-6 max-w-6xl">
                                <div class="max-w-2xl text-white">
                                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4 leading-tight">{{ slide.title }}</h1>
                                    <p class="text-base sm:text-lg md:text-xl mb-6 sm:mb-8 opacity-90">{{ slide.subtitle }}</p>
                                    <button @click="exploreRooms" class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-2 sm:py-3 px-4 sm:px-6 md:px-8 rounded-full transition duration-300 transform hover:scale-105 text-sm sm:text-base">
                                        Explore Luxury
                                    </button>
                                </div>
                            </div>
                        </div>
                    </SwiperSlide>
                </Swiper>

                <!-- Quick Booking Widget -->
                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 z-30 w-full max-w-5xl px-4">
                    <div class="bg-white rounded-xl shadow-xl p-4 sm:p-6 border border-gold-300">
                        <h2 class="text-base sm:text-lg md:text-xl font-bold text-blue-900 mb-4">Book Your Stay</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs sm:text-sm font-medium text-blue-700 mb-1">Check In - Check Out</label>
                                <div class="flex border border-blue-200 rounded-lg p-2">
                                    <input type="date" v-model="checkIn" class="flex-1 focus:outline-none text-blue-900 text-xs sm:text-sm min-w-0">
                                    <span class="mx-1 sm:mx-2 text-blue-300 text-xs sm:text-sm">→</span>
                                    <input type="date" v-model="checkOut" class="flex-1 focus:outline-none text-blue-900 text-xs sm:text-sm min-w-0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-blue-700 mb-1">Room Type</label>
                                <select v-model="roomType" class="w-full border border-blue-200 rounded-lg p-2 focus:outline-none text-blue-900 text-xs sm:text-sm">
                                    <option>Standard</option>
                                    <option>Deluxe</option>
                                    <option>Suite</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-blue-700 mb-1">Guests</label>
                                <select v-model="guestCount" class="w-full border border-blue-200 rounded-lg p-2 focus:outline-none text-blue-900 text-xs sm:text-sm">
                                    <option v-for="n in 6" :value="n">{{ n }} {{ n > 1 ? 'Guests' : 'Guest' }}</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button @click="checkAvailability" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-3 sm:px-4 rounded-lg transition duration-300 transform hover:scale-105 text-xs sm:text-sm">
                                    Check Availability
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Amenities -->
            <section class="py-12 sm:py-16 md:py-24 bg-gradient-to-b from-white to-blue-50 pt-20 sm:pt-24 md:pt-32">
                <div class="container mx-auto px-4 sm:px-6 max-w-6xl">
                    <div class="text-center mb-8 sm:mb-12 md:mb-16">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-blue-900 mb-2">Luxury Amenities</h2>
                        <div class="w-16 sm:w-24 h-1 bg-gold-500 mx-auto"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 md:gap-8">
                        <div
                            v-for="amenity in amenities"
                            :key="amenity.title"
                            class="text-center p-4 sm:p-6 bg-white rounded-xl shadow-lg border border-gold-100 transition duration-300 hover:shadow-xl hover:border-gold-300"
                        >
                            <div class="flex justify-center mb-3 sm:mb-4 text-blue-900">
                                <component :is="amenity.icon" class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10" />
                            </div>
                            <h3 class="text-base sm:text-lg md:text-xl font-semibold text-blue-900 mb-2">{{ amenity.title }}</h3>
                            <p class="text-blue-700 text-xs sm:text-sm md:text-base">{{ amenity.description }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Testimonials -->
            <section class="py-12 sm:py-16 md:py-20 bg-blue-900 text-white">
                <div class="container mx-auto px-4 sm:px-6 max-w-6xl">
                    <div class="text-center mb-8 sm:mb-12 md:mb-16">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-2">Guest Experiences</h2>
                        <div class="w-16 sm:w-24 h-1 bg-gold-500 mx-auto"></div>
                    </div>

                    <Swiper
                        :modules="[Autoplay, Pagination]"
                        :autoplay="{ delay: 7000 }"
                        :pagination="{ clickable: true }"
                        class="pb-8 sm:pb-12"
                    >
                        <SwiperSlide v-for="testimonial in testimonials" :key="testimonial.id">
                            <div class="max-w-3xl mx-auto text-center px-4">
                                <div class="text-gold-400 text-3xl sm:text-4xl md:text-5xl mb-4 sm:mb-6 md:mb-8">"</div>
                                <p class="text-base sm:text-lg md:text-xl italic mb-4 sm:mb-6 md:mb-8 leading-relaxed">{{ testimonial.text }}</p>
                                <div class="flex justify-center mb-3 sm:mb-4">
                                    <span v-for="star in 5" :key="star" class="text-base sm:text-lg md:text-xl">
                                        {{ star <= Math.floor(testimonial.rating) ? '★' : '☆' }}
                                    </span>
                                    <span class="ml-2 text-gold-300 text-sm sm:text-base">{{ testimonial.rating }}</span>
                                </div>
                                <p class="font-semibold text-gold-300 text-sm sm:text-base">{{ testimonial.author }}</p>
                            </div>
                        </SwiperSlide>
                    </Swiper>
                </div>
            </section>

            <!-- Promotional Offers -->
            <section class="py-12 sm:py-16 md:py-24 bg-gradient-to-b from-blue-50 to-white">
                <div class="container mx-auto px-4 sm:px-6 max-w-6xl">
                    <div class="text-center mb-8 sm:mb-12 md:mb-16">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-blue-900 mb-2">Special Offers</h2>
                        <p class="text-blue-700 max-w-2xl mx-auto text-sm sm:text-base">Exclusive deals for your luxury getaway</p>
                        <div class="w-16 sm:w-24 h-1 bg-gold-500 mx-auto mt-3 sm:mt-4"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                        <div v-for="promo in promotions" :key="promo.id" class="relative rounded-xl overflow-hidden shadow-xl border border-gold-200 transition duration-300 hover:shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 to-gold-600/80 z-0"></div>
                            <div class="relative z-10 p-4 sm:p-6 md:p-8 text-white h-full flex flex-col">
                                <div class="absolute top-3 right-3 sm:top-4 sm:right-4 bg-gold-500 text-blue-900 text-xs sm:text-sm font-bold px-2 sm:px-3 py-1 rounded-full">
                                    {{ promo.code }}
                                </div>
                                <h3 class="text-lg sm:text-xl md:text-2xl font-bold mb-3 sm:mb-4">{{ promo.title }}</h3>
                                <p class="mb-4 sm:mb-6 md:mb-8 flex-grow text-sm sm:text-base">{{ promo.description }}</p>
                                <button @click="exploreRooms" class="self-start bg-gold-500 hover:bg-gold-400 text-blue-900 font-bold py-2 px-3 sm:px-4 md:px-6 rounded-full transition text-xs sm:text-sm">
                                    View Offer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </WelcomeLayout>
</template>

<style lang="postcss">
    /* Custom gold color */
    .bg-gold-100 { background-color: #faf3e0; }
    .bg-gold-200 { background-color: #f5e7c1; }
    .bg-gold-300 { background-color: #f0d9a3; }
    .bg-gold-400 { background-color: #ebcc85; }
    .bg-gold-500 { background-color: #e6bf67; }
    .bg-gold-600 { background-color: #e1b249; }
    .border-gold-100 { border-color: #faf3e0; }
    .border-gold-200 { border-color: #f5e7c1; }
    .border-gold-300 { border-color: #f0d9a3; }

    /* Swiper custom styles */
    .swiper-pagination-bullet {
        @apply bg-white opacity-70;
    }
    .swiper-pagination-bullet-active {
        @apply bg-gold-500 opacity-100;
    }
    .swiper-button-next, .swiper-button-prev {
        @apply text-white hover:text-gold-500 transition;
    }

    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .swiper-button-next, .swiper-button-prev {
            @apply hidden;
        }
    }
</style>
