<script setup lang="ts">
    import { ref } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import WelcomeLayout from '@/layouts/WelcomeLayout.vue';
    import { MapPin, Phone, Mail, Clock, Send } from 'lucide-vue-next';

    // Contact form
    const form = useForm({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: '',
    });

    // Contact information
    const contactInfo = ref([
        {
            icon: MapPin,
            title: 'Address',
            details: '123 Ocean Drive, Colombo, Sri Lanka',
            description: 'Located in the heart of Colombo with stunning ocean views'
        },
        {
            icon: Phone,
            title: 'Phone',
            details: '+94 11 234 5678',
            description: 'Available 24/7 for reservations and inquiries'
        },
        {
            icon: Mail,
            title: 'Email',
            details: 'reservations@duminduhotel.com',
            description: 'We respond within 2 hours during business hours'
        },
        {
            icon: Clock,
            title: 'Check-in/Check-out',
            details: '3:00 PM / 11:00 AM',
            description: 'Early check-in and late check-out available upon request'
        }
    ]);

    // Submit form
    const submitForm = () => {
        form.post('/contact', {
            onSuccess: () => {
                form.reset();
                // Show success message
            },
            onError: (errors) => {
                console.error('Form errors:', errors);
            }
        });
    };

    // Map coordinates (Colombo, Sri Lanka)
    const mapUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.63162500000!2d79.78616400000000!3d6.92707900000000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae253d10f7a7003%3A0x320b2e4d32d3838d!2sColombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2sus!4v1234567890';
</script>

<template>
    <WelcomeLayout>
        <div class="min-h-screen bg-white">
            <!-- Hero Section -->
            <section class="relative h-[50vh] bg-gradient-to-r from-blue-900 to-gold-600">
                <div class="absolute inset-0 bg-black/30"></div>
                <div class="relative z-10 h-full flex items-center">
                    <div class="container mx-auto px-6 max-w-6xl">
                        <div class="max-w-3xl">
                            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Contact Us</h1>
                            <p class="text-xl text-blue-100 mb-8">Get in touch with us for reservations, inquiries, or any assistance you may need.</p>
                            <div class="flex items-center">
                                <div class="h-1 w-16 bg-gold-500 mr-4"></div>
                                <p class="text-gold-300">We're here to help</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Information -->
            <section class="py-20">
                <div class="container mx-auto px-6 max-w-6xl">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-blue-900 mb-2">Get In Touch</h2>
                        <div class="w-24 h-1 bg-gold-500 mx-auto"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                        <div v-for="info in contactInfo" :key="info.title" class="text-center p-6 bg-white rounded-xl shadow-lg border border-gold-100">
                            <div class="flex justify-center mb-4">
                                <div class="bg-gold-100 p-3 rounded-full">
                                    <component :is="info.icon" class="h-6 w-6 text-gold-600" />
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">{{ info.title }}</h3>
                            <p class="text-blue-700 font-medium mb-2">{{ info.details }}</p>
                            <p class="text-blue-600 text-sm">{{ info.description }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Form and Map -->
            <section class="py-20 bg-blue-50">
                <div class="container mx-auto px-6 max-w-6xl">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <!-- Contact Form -->
                        <div class="bg-white rounded-xl shadow-lg p-8">
                            <h3 class="text-2xl font-bold text-blue-900 mb-6">Send Us a Message</h3>
                            <form @submit.prevent="submitForm" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-2">Full Name</label>
                                        <input 
                                            type="text" 
                                            v-model="form.name"
                                            class="w-full border border-blue-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-2">Email Address</label>
                                        <input 
                                            type="email" 
                                            v-model="form.email"
                                            class="w-full border border-blue-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                            required
                                        >
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-2">Phone Number</label>
                                        <input 
                                            type="tel" 
                                            v-model="form.phone"
                                            class="w-full border border-blue-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-2">Subject</label>
                                        <select 
                                            v-model="form.subject"
                                            class="w-full border border-blue-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                            required
                                        >
                                            <option value="">Select a subject</option>
                                            <option value="Reservation">Reservation Inquiry</option>
                                            <option value="General">General Inquiry</option>
                                            <option value="Feedback">Feedback</option>
                                            <option value="Complaint">Complaint</option>
                                            <option value="Partnership">Partnership</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Message</label>
                                    <textarea 
                                        v-model="form.message"
                                        rows="6"
                                        class="w-full border border-blue-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                        placeholder="Tell us how we can help you..."
                                        required
                                    ></textarea>
                                </div>
                                
                                <button 
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full bg-gold-500 hover:bg-gold-600 disabled:bg-gray-400 text-blue-900 font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center"
                                >
                                    <Send class="h-5 w-5 mr-2" />
                                    <span v-if="form.processing">Sending...</span>
                                    <span v-else>Send Message</span>
                                </button>
                            </form>
                        </div>

                        <!-- Map -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="h-full">
                                <iframe 
                                    :src="mapUrl"
                                    width="100%" 
                                    height="100%" 
                                    style="border:0; min-height: 400px;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ Section -->
            <section class="py-20">
                <div class="container mx-auto px-6 max-w-4xl">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-blue-900 mb-2">Frequently Asked Questions</h2>
                        <div class="w-24 h-1 bg-gold-500 mx-auto"></div>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-lg p-6 border border-gold-100">
                            <h3 class="text-lg font-bold text-blue-900 mb-2">What are your check-in and check-out times?</h3>
                            <p class="text-blue-700">Check-in time is 3:00 PM and check-out time is 11:00 AM. Early check-in and late check-out can be arranged based on availability.</p>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-6 border border-gold-100">
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Do you offer airport transfers?</h3>
                            <p class="text-blue-700">Yes, we provide airport transfer services. Please contact us at least 24 hours in advance to arrange your transfer.</p>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-6 border border-gold-100">
                            <h3 class="text-lg font-bold text-blue-900 mb-2">What is your cancellation policy?</h3>
                            <p class="text-blue-700">Free cancellation up to 24 hours before check-in. Late cancellations may incur charges. Please refer to your booking confirmation for specific terms.</p>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-6 border border-gold-100">
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Do you have parking facilities?</h3>
                            <p class="text-blue-700">Yes, we offer complimentary parking for all guests. Valet parking service is also available for an additional fee.</p>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg p-6 border border-gold-100">
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Are pets allowed?</h3>
                            <p class="text-blue-700">We welcome well-behaved pets with prior arrangement. Please contact us for our pet policy and any additional charges.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Call to Action -->
            <section class="py-20 bg-gradient-to-r from-blue-900 to-gold-600">
                <div class="container mx-auto px-6 max-w-4xl text-center">
                    <h2 class="text-3xl font-bold text-white mb-4">Ready to Experience Luxury?</h2>
                    <p class="text-xl text-blue-100 mb-8">Book your stay with us and discover the perfect blend of comfort and elegance.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/hotel-rooms" class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-8 rounded-full transition duration-300">
                            Browse Rooms
                        </a>
                        <a href="tel:+94112345678" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-900 font-bold py-3 px-8 rounded-full transition duration-300">
                            Call Now
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
    .border-gold-100 { border-color: #FAF3E0; }
</style>

