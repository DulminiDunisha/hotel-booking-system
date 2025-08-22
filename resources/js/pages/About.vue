<script setup lang="ts">
    import { ref } from 'vue';
    import WelcomeLayout from '@/layouts/WelcomeLayout.vue';
    import { Award, Users, MapPin, Clock, Star, Heart } from 'lucide-vue-next';

    // Hotel statistics
    const stats = ref([
        { icon: Award, number: '15+', label: 'Years of Excellence' },
        { icon: Users, number: '50,000+', label: 'Happy Guests' },
        { icon: Star, number: '4.8', label: 'Average Rating' },
        { icon: Heart, number: '95%', label: 'Guest Satisfaction' },
    ]);

    // Team members
    const team = ref([
        {
            name: 'Sarah Johnson',
            position: 'General Manager',
            image: '/images/team/manager.jpg',
            description: 'With over 15 years of hospitality experience, Sarah ensures every guest receives exceptional service.'
        },
        {
            name: 'Michael Chen',
            position: 'Head Chef',
            image: '/images/team/chef.jpg',
            description: 'Award-winning chef with expertise in international cuisine and local flavors.'
        },
        {
            name: 'Emily Rodriguez',
            position: 'Guest Relations Manager',
            image: '/images/team/guest-relations.jpg',
            description: 'Dedicated to creating memorable experiences and exceeding guest expectations.'
        },
        {
            name: 'David Thompson',
            position: 'Operations Director',
            image: '/images/team/operations.jpg',
            description: 'Ensures smooth operations and maintains the highest standards of quality.'
        }
    ]);

    // Awards and recognition
    const awards = ref([
        { year: '2023', title: 'Best Luxury Hotel', organization: 'Travel Awards' },
        { year: '2022', title: 'Excellence in Service', organization: 'Hospitality Institute' },
        { year: '2021', title: 'Green Hotel Certification', organization: 'Environmental Council' },
        { year: '2020', title: 'Customer Choice Award', organization: 'Guest Reviews' }
    ]);
</script>

<template>
    <WelcomeLayout>
        <div class="min-h-screen bg-white">
            <!-- Hero Section -->
            <section class="relative h-[60vh] bg-gradient-to-r from-blue-900 to-gold-600">
                <div class="absolute inset-0 bg-black/30"></div>
                <div class="relative z-10 h-full flex items-center">
                    <div class="container mx-auto px-6 max-w-6xl">
                        <div class="max-w-3xl">
                            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">About Dumindu Hotel</h1>
                            <p class="text-xl text-blue-100 mb-8">Discover our story of luxury, excellence, and unwavering commitment to providing exceptional hospitality experiences.</p>
                            <div class="flex items-center">
                                <div class="h-1 w-16 bg-gold-500 mr-4"></div>
                                <p class="text-gold-300">Where luxury meets tradition</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Our Story -->
            <section class="py-20">
                <div class="container mx-auto px-6 max-w-6xl">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <h2 class="text-3xl font-bold text-blue-900 mb-6">Our Story</h2>
                            <div class="space-y-4 text-blue-700">
                                <p>Founded in 2008, Dumindu Hotel began as a vision to create the ultimate luxury hospitality experience in Sri Lanka. What started as a small boutique hotel has grown into one of the most prestigious destinations in the region.</p>
                                <p>Our name "Dumindu" represents the essence of our philosophy - combining traditional Sri Lankan hospitality with modern luxury amenities. We believe in creating not just accommodations, but memorable experiences that last a lifetime.</p>
                                <p>Over the years, we have welcomed guests from around the world, each with their own unique stories and expectations. Our commitment to excellence has remained unwavering, earning us numerous awards and the loyalty of returning guests.</p>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="bg-gradient-to-br from-blue-100 to-gold-100 rounded-xl p-8">
                                <div class="text-center">
                                    <div class="bg-blue-900 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                                        <span class="text-3xl font-bold text-gold-500">DH</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-blue-900 mb-2">Our Mission</h3>
                                    <p class="text-blue-700">To provide exceptional luxury hospitality experiences that exceed expectations and create lasting memories for our guests.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Statistics -->
            <section class="py-20 bg-blue-50">
                <div class="container mx-auto px-6 max-w-6xl">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-blue-900 mb-2">Our Achievements</h2>
                        <div class="w-24 h-1 bg-gold-500 mx-auto"></div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div v-for="stat in stats" :key="stat.label" class="text-center">
                            <div class="bg-white rounded-xl p-6 shadow-lg">
                                <div class="flex justify-center mb-4">
                                    <component :is="stat.icon" class="h-8 w-8 text-gold-500" />
                                </div>
                                <div class="text-3xl font-bold text-blue-900 mb-2">{{ stat.number }}</div>
                                <div class="text-blue-700">{{ stat.label }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Values -->
            <section class="py-20">
                <div class="container mx-auto px-6 max-w-6xl">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-blue-900 mb-2">Our Values</h2>
                        <div class="w-24 h-1 bg-gold-500 mx-auto"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center p-6">
                            <div class="bg-gold-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <Heart class="h-8 w-8 text-gold-600" />
                            </div>
                            <h3 class="text-xl font-bold text-blue-900 mb-3">Excellence</h3>
                            <p class="text-blue-700">We strive for excellence in every aspect of our service, from the smallest detail to the grandest gesture.</p>
                        </div>
                        <div class="text-center p-6">
                            <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <Users class="h-8 w-8 text-blue-600" />
                            </div>
                            <h3 class="text-xl font-bold text-blue-900 mb-3">Hospitality</h3>
                            <p class="text-blue-700">Genuine warmth and care for our guests, treating each person as a valued member of our extended family.</p>
                        </div>
                        <div class="text-center p-6">
                            <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <Award class="h-8 w-8 text-green-600" />
                            </div>
                            <h3 class="text-xl font-bold text-blue-900 mb-3">Innovation</h3>
                            <p class="text-blue-700">Continuously improving and innovating to provide the best possible experience for our guests.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Team -->
            <section class="py-20 bg-blue-50">
                <div class="container mx-auto px-6 max-w-6xl">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-blue-900 mb-2">Our Leadership Team</h2>
                        <div class="w-24 h-1 bg-gold-500 mx-auto"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div v-for="member in team" :key="member.name" class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="h-48 bg-gradient-to-br from-blue-100 to-gold-100 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="bg-blue-900 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2">
                                        <span class="text-lg font-bold text-gold-500">{{ member.name.split(' ').map(n => n[0]).join('') }}</span>
                                    </div>
                                    <p class="text-blue-600 text-sm">Photo</p>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-blue-900 mb-1">{{ member.name }}</h3>
                                <p class="text-gold-600 font-medium mb-3">{{ member.position }}</p>
                                <p class="text-blue-700 text-sm">{{ member.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Awards -->
            <section class="py-20">
                <div class="container mx-auto px-6 max-w-6xl">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-blue-900 mb-2">Awards & Recognition</h2>
                        <div class="w-24 h-1 bg-gold-500 mx-auto"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="award in awards" :key="award.year" class="bg-white rounded-xl shadow-lg p-6 border border-gold-200">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gold-600 mb-2">{{ award.year }}</div>
                                <h3 class="text-lg font-bold text-blue-900 mb-2">{{ award.title }}</h3>
                                <p class="text-blue-700 text-sm">{{ award.organization }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Call to Action -->
            <section class="py-20 bg-gradient-to-r from-blue-900 to-gold-600">
                <div class="container mx-auto px-6 max-w-4xl text-center">
                    <h2 class="text-3xl font-bold text-white mb-4">Experience Luxury Like Never Before</h2>
                    <p class="text-xl text-blue-100 mb-8">Join thousands of satisfied guests who have experienced the magic of Dumindu Hotel.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/hotel-rooms" class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-8 rounded-full transition duration-300">
                            Book Your Stay
                        </a>
                        <a href="/contact" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-900 font-bold py-3 px-8 rounded-full transition duration-300">
                            Contact Us
                        </a>
                    </div>
                </div>
            </section>
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
    .bg-blue-100 { background-color: #E1E8F7; }
    .border-gold-200 { border-color: #F5E7C1; }
</style>

