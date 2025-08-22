<script setup lang="ts">
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed, ref } from 'vue';

    const page = usePage();
    const user = computed(() => page.props.auth?.user || {});
    const userInitials = computed(() => {
        if (!user.value.name) return 'U';
        return user.value.name.substring(0, 2).toUpperCase();
    });

    // Mobile sidebar state
    const isSidebarOpen = ref(false);

    const toggleSidebar = () => {
        isSidebarOpen.value = !isSidebarOpen.value;
    };

    const closeSidebar = () => {
        isSidebarOpen.value = false;
    };
</script>

<template>
    <div class="flex min-h-screen bg-gray-50">
        <!-- Mobile overlay -->
        <div 
            v-if="isSidebarOpen" 
            @click="closeSidebar"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center space-x-2">
                    <img src="/images/logo.png" alt="Hotel Logo" class="h-8 w-auto">
                    <span class="text-lg font-bold text-blue-900 hidden sm:block">Admin</span>
                </div>
                <button 
                    @click="closeSidebar"
                    class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <nav class="mt-6 px-4 lg:px-6">
                <ul class="space-y-1">
                    <li>
                        <Link 
                            :href="route('admin.dashboard')" 
                            @click="closeSidebar"
                            class="flex items-center px-4 py-3 text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200 group"
                        >
                            <svg class="h-5 w-5 mr-3 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                            </svg>
                            <span class="font-medium">Dashboard</span>
                        </Link>
                    </li>
                    <li>
                        <Link 
                            :href="route('admin.users.management')" 
                            @click="closeSidebar"
                            class="flex items-center px-4 py-3 text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200 group"
                        >
                            <svg class="h-5 w-5 mr-3 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="font-medium">Users</span>
                        </Link>
                    </li>
                    <li>
                        <Link 
                            :href="route('admin.room.management')" 
                            @click="closeSidebar"
                            class="flex items-center px-4 py-3 text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200 group"
                        >
                            <svg class="h-5 w-5 mr-3 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="font-medium">Rooms</span>
                        </Link>
                    </li>
                    <li>
                        <Link 
                            :href="route('admin.booking.management')" 
                            @click="closeSidebar"
                            class="flex items-center px-4 py-3 text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200 group"
                        >
                            <svg class="h-5 w-5 mr-3 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">Bookings</span>
                        </Link>
                    </li>
                    <li>
                        <Link 
                            :href="route('admin.payment.hub')" 
                            @click="closeSidebar"
                            class="flex items-center px-4 py-3 text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200 group"
                        >
                            <svg class="h-5 w-5 mr-3 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="font-medium">Payments</span>
                        </Link>
                    </li>
                    <li>
                        <Link 
                            :href="route('admin.gallery.management')" 
                            @click="closeSidebar"
                            class="flex items-center px-4 py-3 text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200 group"
                        >
                            <svg class="h-5 w-5 mr-3 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">Gallery</span>
                        </Link>
                    </li>
                    <li>
                        <Link 
                            :href="route('admin.emergency.cases')" 
                            @click="closeSidebar"
                            class="flex items-center px-4 py-3 text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200 group"
                        >
                            <svg class="h-5 w-5 mr-3 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="font-medium">Emergency Cases</span>
                        </Link>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main content area -->
        <div class="flex-1 flex flex-col lg:ml-0">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 lg:px-6 py-4 flex items-center justify-between">
                    <!-- Mobile menu button -->
                    <button 
                        @click="toggleSidebar"
                        class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Page title for mobile -->
                    <h1 class="text-lg font-semibold text-blue-900 lg:hidden">Admin Panel</h1>

                    <!-- Right side actions -->
                    <div class="flex items-center space-x-3 lg:space-x-6">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                            </button>
                        </div>

                        <!-- User menu -->
                        <div class="flex items-center space-x-3">
                            <div class="hidden sm:flex items-center">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <span class="text-blue-900 font-bold text-sm lg:text-base">{{ userInitials }}</span>
                                </div>
                                <div class="hidden lg:block">
                                    <p class="text-sm font-medium text-blue-900 truncate max-w-32">
                                        {{ user.name ? user.name.toUpperCase() : 'User' }}
                                    </p>
                                    <p class="text-xs text-gray-500">Administrator</p>
                                </div>
                            </div>
                            
                            <!-- Mobile user avatar -->
                            <div class="sm:hidden w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-900 font-bold text-sm">{{ userInitials }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="flex-1 p-4 lg:p-6 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
