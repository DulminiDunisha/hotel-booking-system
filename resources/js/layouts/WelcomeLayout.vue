<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { Head, Link } from '@inertiajs/vue3';

    const mobileMenuOpen = ref(false);
    const userDropdownOpen = ref(false);

    // Handle scrolling to change header style
    const isScrolled = ref(false);
    onMounted(() => {
        window.addEventListener('scroll', () => {
            isScrolled.value = window.scrollY > 50;
        });
    });

    const navigation = [
        { name: 'Home', href: '/' },
        { name: 'Rooms', href: '/hotel-rooms' },
        { name: 'Gallery', href: '/gallery' },
        { name: 'About', href: '/about' },
        { name: 'Contact', href: '/contact' },
    ];
</script>

<template>
    <div class="min-h-screen flex flex-col bg-white">
        <Head>
            <title>Dumindu Hotel - Luxury Beachfront Experience</title>
            <meta name="description" content="Experience unparalleled luxury at Dumindu Hotel with our premium accommodations and world-class amenities.">
        </Head>

        <!-- Header -->
        <header
            class="fixed w-full z-50 transition-all duration-300"
            :class="{
        'bg-white/95 backdrop-blur-sm shadow-md py-2': isScrolled,
        'bg-transparent py-3 sm:py-4': !isScrolled
      }"
        >
            <div class="container mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center">
                        <div class="flex items-center">
                            <div class="bg-blue-900 rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center mr-2">
                                <span class="text-lg sm:text-xl font-bold text-gold-500">DH</span>
                            </div>
                            <span class="text-lg sm:text-xl font-bold text-blue-900">Dumindu</span>
                            <span class="text-lg sm:text-xl font-bold text-gold-500 ml-1">Hotel</span>
                        </div>
                    </Link>

                    <!-- Desktop Navigation -->
                    <nav class="hidden lg:flex items-center space-x-6 xl:space-x-8">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="font-medium text-blue-900 hover:text-gold-500 transition-colors text-sm xl:text-base relative group"
                        >
                            {{ item.name }}
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gold-500 transition-all duration-300 group-hover:w-full"></span>
                        </Link>
                    </nav>

                    <!-- User Actions -->
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="hidden md:flex items-center space-x-4">
                            <Link href="/login" class="text-blue-900 hover:text-gold-500 transition-colors p-2 rounded-lg hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </Link>
                            <Link href="/hotel-rooms" class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-2 px-4 sm:px-6 rounded-full transition duration-300 text-sm shadow-lg hover:shadow-xl transform hover:scale-105">
                                Book Now
                            </Link>
                        </div>

                        <!-- Mobile menu button -->
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="lg:hidden text-blue-900 focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition-colors"
                            :class="{ 'bg-gray-100': mobileMenuOpen }"
                        >
                            <svg v-if="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div
                class="lg:hidden transition-all duration-300 overflow-hidden bg-white/95 backdrop-blur-sm shadow-lg"
                :class="{
          'max-h-screen': mobileMenuOpen,
          'max-h-0': !mobileMenuOpen
        }"
            >
                <div class="container mx-auto px-4 sm:px-6 py-4">
                    <div class="flex flex-col space-y-4">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="font-medium text-blue-900 hover:text-gold-500 transition-colors py-3 px-4 rounded-lg hover:bg-gray-50 text-base"
                            @click="mobileMenuOpen = false"
                        >
                            {{ item.name }}
                        </Link>
                        <div class="flex items-center space-x-4 pt-4 border-t border-blue-100">
                            <Link href="/login" class="text-blue-900 hover:text-gold-500 transition-colors p-2 rounded-lg hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </Link>
                            <Link href="/hotel-rooms" class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-full transition duration-300 flex-1 text-center text-sm shadow-lg">
                                Book Now
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow pt-16 sm:pt-20 lg:pt-24">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-blue-900 text-white pt-12 sm:pt-16 pb-6 sm:pb-8">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                    <!-- Hotel Info -->
                    <div class="lg:col-span-1">
                        <h3 class="text-lg sm:text-xl font-bold mb-4 flex items-center">
                            <div class="bg-gold-500 rounded-full w-6 h-6 sm:w-8 sm:h-8 flex items-center justify-center mr-2">
                                <span class="text-xs sm:text-sm font-bold text-blue-900">DH</span>
                            </div>
                            Dumindu Hotel
                        </h3>
                        <p class="text-blue-200 mb-4 text-sm sm:text-base leading-relaxed">Experience luxury hospitality at its finest with breathtaking ocean views and world-class amenities.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gold-300 hover:text-white transition-colors p-2 rounded-lg hover:bg-blue-800">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gold-300 hover:text-white transition-colors p-2 rounded-lg hover:bg-blue-800">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gold-300 hover:text-white transition-colors p-2 rounded-lg hover:bg-blue-800">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-base sm:text-lg font-bold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li>
                                <Link href="/" class="text-blue-200 hover:text-gold-300 transition-colors text-sm sm:text-base py-1 block">Home</Link>
                            </li>
                            <li>
                                <Link href="/hotel-rooms" class="text-blue-200 hover:text-gold-300 transition-colors text-sm sm:text-base py-1 block">Rooms & Suites</Link>
                            </li>
                            <li>
                                <Link href="/gallery" class="text-blue-200 hover:text-gold-300 transition-colors text-sm sm:text-base py-1 block">Gallery</Link>
                            </li>
                            <li>
                                <Link href="/about" class="text-blue-200 hover:text-gold-300 transition-colors text-sm sm:text-base py-1 block">About Us</Link>
                            </li>
                            <li>
                                <Link href="/contact" class="text-blue-200 hover:text-gold-300 transition-colors text-sm sm:text-base py-1 block">Contact</Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div>
                        <h3 class="text-base sm:text-lg font-bold mb-4">Contact Us</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gold-300 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-blue-200 text-sm sm:text-base">123 Ocean Drive, Colombo, Sri Lanka</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gold-300 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="text-blue-200 text-sm sm:text-base">+94 11 234 5678</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gold-300 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="text-blue-200 text-sm sm:text-base">reservations@duminduhotel.com</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Newsletter -->
                    <div>
                        <h3 class="text-base sm:text-lg font-bold mb-4">Newsletter</h3>
                        <p class="text-blue-200 mb-4 text-sm sm:text-base">Subscribe for special offers and updates</p>
                        <form class="space-y-3">
                            <input
                                type="email"
                                placeholder="Your email address"
                                class="w-full px-3 sm:px-4 py-2 rounded-lg bg-blue-800 text-white placeholder-blue-400 focus:outline-none focus:ring-2 focus:ring-gold-500 text-sm border border-blue-700"
                            >
                            <button
                                type="submit"
                                class="w-full bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-2 px-4 rounded-lg transition duration-300 text-sm shadow-lg hover:shadow-xl transform hover:scale-105"
                            >
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-t border-blue-800 mt-8 sm:mt-12 pt-4 sm:pt-6 text-center text-blue-300">
                    <p class="text-sm">&copy; {{ new Date().getFullYear() }} Dumindu Hotel. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
    /* Custom gold color palette */
    .bg-gold-500 { background-color: #D4AF37; }
    .bg-gold-600 { background-color: #B8860B; }
    .text-gold-300 { color: #F0D9A3; }
    .text-gold-400 { color: #E6BF67; }
    .text-gold-500 { color: #D4AF37; }
    .border-gold-500 { border-color: #D4AF37; }

    /* Custom blue color palette */
    .bg-blue-900 { background-color: #0C2D6A; }
    .bg-blue-800 { background-color: #1A4480; }
    .text-blue-900 { color: #0C2D6A; }
    .text-blue-200 { color: #C1D0F0; }
    .text-blue-300 { color: #A3B9E6; }
    .border-blue-100 { border-color: #E1E8F7; }
    .border-blue-800 { border-color: #1A4480; }

    /* Smooth transitions */
    a, button {
        transition: all 0.3s ease;
    }

    /* Focus styles */
    :focus {
        outline: 2px solid #D4AF37;
        outline-offset: 2px;
    }

    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
