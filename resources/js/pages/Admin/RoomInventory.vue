<template>
    <AdminLayout>
        <!-- Filters and Controls -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-blue-900">Room Inventory Management</h2>
                    <p class="text-blue-700">Manage room types, pricing, and availability</p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        @click="fetchRooms"
                        :disabled="loading"
                        class="bg-blue-100 hover:bg-blue-200 text-blue-900 font-medium py-2 px-4 rounded-lg transition flex items-center"
                        :class="{ 'opacity-50 cursor-not-allowed': loading }"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                    <button
                        @click="openRoomModal(null)"
                        class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-2 px-4 rounded-lg transition flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Room
                    </button>
                </div>

                <div class="flex flex-wrap gap-3">
                    <!-- Room Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-1">Room Type</label>
                        <select
                            v-model="roomTypeFilter"
                            class="border border-blue-200 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-gold-500 text-blue-900 w-full"
                        >
                            <option value="all">All Types</option>
                            <option value="Standard">Standard</option>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Suite">Suite</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-1">Status</label>
                        <select
                            v-model="statusFilter"
                            class="border border-blue-200 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-gold-500 text-blue-900 w-full"
                        >
                            <option value="all">All Statuses</option>
                            <option value="Available">Available</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Renovation">Renovation</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-1">Search</label>
                        <div class="flex">
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Room number or name..."
                                class="border border-blue-200 rounded-l-lg p-2 focus:outline-none focus:ring-2 focus:ring-gold-500 text-blue-900 w-full"
                                @keyup.enter="fetchRooms"
                            >
                            <button 
                                @click="fetchRooms"
                                class="bg-blue-100 hover:bg-blue-200 text-blue-900 font-medium p-2 rounded-r-lg transition"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <div class="flex justify-end mt-4">
                        <button
                            @click="clearFilters"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Inventory Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="bg-blue-50 text-blue-900">
                        <th class="text-left py-3 px-4">Room</th>
                        <th class="text-left py-3 px-4">Image</th>
                        <th class="text-left py-3 px-4">Type</th>
                        <th class="text-left py-3 px-4">Base Price</th>
                        <th class="text-left py-3 px-4">Capacity</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Seasonal Pricing</th>
                        <th class="text-right py-3 px-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Loading State -->
                    <tr v-if="loading" class="border-b border-blue-100">
                        <td colspan="8" class="py-8 px-4 text-center">
                            <div class="flex items-center justify-center">
                                <svg class="animate-spin h-6 w-6 text-blue-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-blue-700">Loading rooms...</span>
                            </div>
                        </td>
                    </tr>
                    
                    <tr
                        v-for="room in filteredRooms"
                        :key="room.id || `room-${room.number}`"
                        class="border-b border-blue-100 hover:bg-blue-50"
                    >
                        <td class="py-4 px-4">
                            <div class="font-medium text-blue-900">{{ room.name }}</div>
                            <div class="text-sm text-blue-700">#{{ room.number }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div v-if="room.image" class="w-16 h-12 rounded-lg overflow-hidden">
                                <img 
                                    :src="room.image" 
                                    :alt="room.name"
                                    class="w-full h-full object-cover"
                                    @error="handleTableImageError"
                                >
                            </div>
                            <div v-else class="w-16 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                <span class="bg-blue-100 text-blue-900 text-xs font-semibold px-2 py-1 rounded">
                  {{ room.type }}
                </span>
                        </td>
                                                    <td class="py-4 px-4 font-medium">${{ room.base_price || 0 }}</td>
                                                    <td class="py-4 px-4">{{ room.capacity || 0 }} Guests</td>
                        <td class="py-4 px-4">
                <span
                    class="text-xs font-bold py-1 px-2 rounded-full"
                    :class="statusClass(room.status)"
                >
                  {{ room.status }}
                </span>
                        </td>
                        <td class="py-4 px-4">
                            <div v-if="room.seasonalRates && room.seasonalRates.length > 0" class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm">{{ room.seasonalRates.length }} seasonal rates</span>
                            </div>
                            <div v-else class="text-sm text-gray-500">None</div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex justify-end space-x-2">
                                <button
                                    @click="openRoomModal(room)"
                                    class="text-blue-600 hover:text-blue-800 p-1"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    @click="openPricingModal(room)"
                                    class="text-gold-600 hover:text-gold-800 p-1"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0114 0z" />
                                    </svg>
                                </button>
                                <button
                                    @click="openAvailabilityModal(room)"
                                    class="text-green-600 hover:text-green-800 p-1"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <button
                                    @click="confirmDeleteRoom(room)"
                                    class="text-red-600 hover:text-red-800 p-1"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="filteredRooms.length === 0 && !loading" class="text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-blue-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <h3 class="text-2xl font-bold text-blue-900 mb-2">
                    {{ rooms.length === 0 ? 'No Rooms Available' : 'No Rooms Match Your Filters' }}
                </h3>
                <p class="text-blue-700 mb-6">
                    {{ rooms.length === 0 
                        ? 'Get started by adding your first room to the inventory' 
                        : 'Try adjusting your search criteria or clear the filters' 
                    }}
                </p>
                <div class="flex justify-center gap-4">
                    <button
                        @click="openRoomModal(null)"
                        class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-8 rounded-full transition"
                    >
                        Add New Room
                    </button>
                    <button
                        v-if="rooms.length > 0"
                        @click="clearFilters"
                        class="bg-blue-100 hover:bg-blue-200 text-blue-900 font-bold py-3 px-8 rounded-full transition"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-blue-500">
                <div class="text-3xl font-bold text-blue-900 mb-2">{{ rooms.length }}</div>
                <div class="text-blue-700">Total Rooms</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-green-500">
                <div class="text-3xl font-bold text-blue-900 mb-2">{{ availableRoomsCount }}</div>
                <div class="text-blue-700">Available Now</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-gold-500">
                <div class="text-3xl font-bold text-blue-900 mb-2">{{ roomTypesCount.Suite || 0 }}</div>
                <div class="text-blue-700">Suites</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-purple-500">
                <div class="text-3xl font-bold text-blue-900 mb-2">${{ avgRoomPrice }}</div>
                <div class="text-blue-700">Avg. Room Rate</div>
            </div>
        </div>

        <!-- Room Modal -->
        <Modal
            v-model:show="showRoomModal"
            :title="currentRoom.id ? 'Edit Room' : 'Add New Room'"
            size="lg"
            @close="closeRoomModal"
        >
            <form @submit.prevent novalidate>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Room Name</label>
                        <input
                            type="text"
                            v-model="currentRoom.name"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500"
                            placeholder="Ocean View Deluxe"
                            required
                            minlength="1"
                            maxlength="100"

                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Room Number</label>
                        <input
                            type="text"
                            v-model="currentRoom.number"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500"
                            placeholder="305"
                            required
                            minlength="1"
                            maxlength="10"

                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Room Type</label>
                        <select
                            v-model="currentRoom.type"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500"
                            required
                        >
                            <option value="">Select Room Type</option>
                            <option value="Standard">Standard</option>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Suite">Suite</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Base Price ($)</label>
                        <input
                            type="number"
                            v-model="currentRoom.base_price"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500"
                            placeholder="189"
                            min="1"
                            step="0.01"
                            required
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Capacity</label>
                        <select
                            v-model="currentRoom.capacity"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500"
                            required
                        >
                            <option value="">Select Capacity</option>
                            <option v-for="n in 6" :value="n">{{ n }} Guest{{ n > 1 ? 's' : '' }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Status</label>
                        <select
                            v-model="currentRoom.status"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500"
                            required
                        >
                            <option value="">Select Status</option>
                            <option value="Available">Available</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Renovation">Renovation</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-blue-900 mb-2">Description</label>
                    <textarea
                        v-model="currentRoom.description"
                        class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500 h-32"
                        placeholder="Describe the room features and amenities..."
                    ></textarea>
                </div>

                <!-- Image Upload Section -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-blue-900 mb-2">Room Image</label>
                    <div class="space-y-4">
                        <!-- Current Image Preview -->
                        <div v-if="currentRoom.image || imagePreview" class="relative">
                            <img 
                                :src="imagePreview || currentRoom.image" 
                                :alt="currentRoom.name"
                                class="w-full h-48 object-cover rounded-lg border border-blue-200"
                                @error="handleImageError"
                            >
                            <button
                                @click="removeImage"
                                type="button"
                                class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- File Upload Input -->
                        <div class="border-2 border-dashed border-blue-200 rounded-lg p-6 text-center hover:border-blue-300 transition-colors">
                            <input
                                type="file"
                                @change="handleImageUpload"
                                accept="image/*"
                                class="hidden"
                                id="room-image-upload"
                            >
                            <label for="room-image-upload" class="cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-blue-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-blue-700 font-medium">Click to upload room image</p>
                                <p class="text-blue-500 text-sm mt-1">PNG, JPG, GIF up to 2MB</p>
                            </label>
                        </div>
                    </div>
                </div>
            </form>

            <template #footer>
                <button
                    type="button"
                    @click="closeRoomModal"
                    :disabled="loading"
                    class="bg-white border border-blue-300 text-blue-700 hover:bg-blue-50 font-bold py-3 px-6 rounded-lg transition"
                    :class="{ 'opacity-50 cursor-not-allowed': loading }"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    @click="saveRoom"
                    :disabled="loading"
                    class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-lg transition flex items-center"
                    :class="{ 'opacity-50 cursor-not-allowed': loading }"
                >
                    <svg v-if="loading" class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ currentRoom.id ? 'Update Room' : 'Create Room' }}
                </button>
            </template>
        </Modal>

        <!-- Pricing Modal -->
        <Modal
            v-model:show="showPricingModal"
            :title="`Seasonal Pricing for ${currentRoom.name} (#${currentRoom.number})`"
            size="xl"
            @close="closePricingModal"
        >
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                                                <h4 class="font-medium text-blue-900">Base Price: ${{ currentRoom.base_price || 0 }}</h4>
                    <button
                        @click="addSeasonalRate"
                        class="bg-blue-100 hover:bg-blue-200 text-blue-900 font-medium py-2 px-4 rounded-lg transition flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Seasonal Rate
                    </button>
                </div>

                <div v-if="currentRoom.seasonalRates && currentRoom.seasonalRates.length > 0" class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                        <tr class="bg-blue-50 text-blue-900">
                            <th class="text-left py-3 px-4">Season Name</th>
                            <th class="text-left py-3 px-4">Date Range</th>
                            <th class="text-left py-3 px-4">Price Multiplier</th>
                            <th class="text-left py-3 px-4">Calculated Price</th>
                            <th class="text-right py-3 px-4">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="(rate, index) in currentRoom.seasonalRates"
                            :key="index"
                            class="border-b border-blue-100"
                        >
                            <td class="py-4 px-4">
                                <input
                                    type="text"
                                    v-model="rate.name"
                                    class="w-full border border-blue-200 rounded p-2 focus:outline-none focus:ring-2 focus:ring-gold-500"
                                    placeholder="Peak Season"
                                >
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <input
                                        type="date"
                                        v-model="rate.start_date"
                                        class="border border-blue-200 rounded p-2 focus:outline-none focus:ring-2 focus:ring-gold-500"
                                    >
                                    <span class="mx-2 text-blue-300">→</span>
                                    <input
                                        type="date"
                                        v-model="rate.end_date"
                                        class="border border-blue-200 rounded p-2 focus:outline-none focus:ring-2 focus:ring-gold-500"
                                    >
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <input
                                        type="number"
                                        v-model="rate.multiplier"
                                        min="0.5"
                                        max="3"
                                        step="0.1"
                                        class="w-20 border border-blue-200 rounded p-2 focus:outline-none focus:ring-2 focus:ring-gold-500"
                                    >
                                    <span class="ml-2 text-blue-700">x base price</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-medium">
                                ${{ ((currentRoom.base_price || 0) * rate.multiplier).toFixed(2) }}
                            </td>
                            <td class="py-4 px-4 text-right">
                                <button
                                    @click="removeSeasonalRate(index)"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="bg-blue-50 rounded-lg p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-blue-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0114 0z" />
                    </svg>
                    <h4 class="text-lg font-medium text-blue-900 mb-2">No Seasonal Pricing</h4>
                    <p class="text-blue-700">Add seasonal rates to adjust prices during peak periods</p>
                </div>
            </div>

            <template #footer>
                <button
                    @click="closePricingModal"
                    :disabled="loading"
                    class="bg-white border border-blue-300 text-blue-700 hover:bg-blue-50 font-bold py-3 px-6 rounded-lg transition"
                    :class="{ 'opacity-50 cursor-not-allowed': loading }"
                >
                    Cancel
                </button>
                <button
                    @click="saveSeasonalPricing"
                    :disabled="loading"
                    class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-lg transition flex items-center"
                    :class="{ 'opacity-50 cursor-not-allowed': loading }"
                >
                    <svg v-if="loading" class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Save Pricing
                </button>
            </template>
        </Modal>

        <!-- Availability Modal -->
        <Modal
            v-model:show="showAvailabilityModal"
            :title="`Availability for ${currentRoom.name} (#${currentRoom.number})`"
            size="2xl"
            @close="closeAvailabilityModal"
        >
            <div class="mb-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="font-medium text-blue-900 mb-1">Current Status:
                            <span :class="statusClass(currentRoom.status)" class="text-xs font-bold py-1 px-2 rounded-full">
                                {{ currentRoom.status }}
                            </span>
                        </h4>
                        <p class="text-sm text-blue-700">Manage availability dates below</p>
                    </div>
                    <div class="flex">
                        <button 
                            @click="previousMonth"
                            class="p-2 rounded-lg border border-blue-200 mr-2 hover:bg-blue-50 transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="bg-blue-100 text-blue-900 font-medium py-1 px-4 rounded-lg mr-2">
                            {{ currentMonthYear }}
                        </button>
                        <button 
                            @click="nextMonth"
                            class="p-2 rounded-lg border border-blue-200 hover:bg-blue-50 transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="bg-white border border-blue-200 rounded-lg overflow-hidden">
                    <!-- Weekday Headers -->
                    <div class="grid grid-cols-7 bg-blue-50">
                        <div class="text-center text-sm font-medium text-blue-900 p-2">Sun</div>
                        <div class="text-center text-sm font-medium text-blue-900 p-2">Mon</div>
                        <div class="text-center text-sm font-medium text-blue-900 p-2">Tue</div>
                        <div class="text-center text-sm font-medium text-blue-900 p-2">Wed</div>
                        <div class="text-center text-sm font-medium text-blue-900 p-2">Thu</div>
                        <div class="text-center text-sm font-medium text-blue-900 p-2">Fri</div>
                        <div class="text-center text-sm font-medium text-blue-900 p-2">Sat</div>
                    </div>

                    <!-- Calendar Days -->
                    <div class="grid grid-cols-7">
                        <div
                            v-for="day in calendarDays"
                            :key="day.date"
                            class="min-h-24 border border-blue-100 p-2 relative"
                            :class="{
                                'bg-blue-50': day.isCurrentMonth,
                                'bg-gray-50 text-gray-400': !day.isCurrentMonth
                            }"
                        >
                            <div class="text-right text-sm font-medium mb-1">{{ day.day }}</div>

                            <!-- Availability Status -->
                            <div
                                v-if="day.isCurrentMonth"
                                class="absolute bottom-2 left-2 right-2"
                            >
                                <div class="flex justify-center">
                                    <div
                                        class="text-xs px-2 py-1 rounded-full cursor-pointer"
                                        :class="dayAvailabilityClass(day.date)"
                                        @click="toggleAvailability(day.date)"
                                    >
                                        {{ getAvailabilityStatus(day.date) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-100 border border-green-300 rounded mr-2"></div>
                    <span class="text-sm text-blue-700">Available</span>
                    <div class="w-3 h-3 bg-red-100 border border-red-300 rounded mx-4 mr-2"></div>
                    <span class="text-sm text-blue-700">Booked</span>
                    <div class="w-3 h-3 bg-yellow-100 border border-yellow-300 rounded mx-4 mr-2"></div>
                    <span class="text-sm text-blue-700">Blocked</span>
                </div>
            </div>

            <template #footer>
                <button
                    @click="closeAvailabilityModal"
                    :disabled="loading"
                    class="bg-white border border-blue-300 text-blue-700 hover:bg-blue-50 font-bold py-3 px-6 rounded-lg transition"
                    :class="{ 'opacity-50 cursor-not-allowed': loading }"
                >
                    Close
                </button>
                <button
                    @click="saveAvailability"
                    :disabled="loading"
                    class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-lg transition flex items-center"
                    :class="{ 'opacity-50 cursor-not-allowed': loading }"
                >
                    <svg v-if="loading" class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Save Changes
                </button>
            </template>
        </Modal>
    </AdminLayout>
</template>

<script setup lang="ts">
    import { ref, computed, reactive, onMounted } from 'vue'
    import axios from 'axios'
    import Swal from 'sweetalert2'
    import AdminLayout from '@/layouts/AdminLayout.vue'
    import Modal from '@/components/Modal.vue'

    interface Room {
        id: number | null
        number: string
        name: string
        type: string
        base_price: number | null
        capacity: number | null
        status: string
        description: string
        image?: string
        seasonalRates: SeasonalRate[]
        availabilities: Availability[]
    }

    interface SeasonalRate {
        id?: number
        name: string
        start_date: string
        end_date: string
        multiplier: number
    }

    interface Availability {
        id?: number
        date: string
        status: string
    }

    interface CalendarDay {
        day: number
        date: string
        isCurrentMonth: boolean
    }

    // Room data
    const rooms = ref<Room[]>([])
    const loading = ref(false)

    // Filters
    const roomTypeFilter = ref('all')
    const statusFilter = ref('all')
    const searchQuery = ref('')

    // Modals
    const showRoomModal = ref(false)
    const showPricingModal = ref(false)
    const showAvailabilityModal = ref(false)

    // Current room for editing
    const currentRoom = reactive<Room>({
        id: null,
        number: '',
        name: '',
        type: '',
        base_price: null,
        capacity: null,
        status: '',
        description: '',
        image: '',
        seasonalRates: [],
        availabilities: []
    })

    // Image handling
    const imagePreview = ref<string | null>(null)
    const selectedImageFile = ref<File | null>(null)

    // Calendar
    const currentMonth = ref(new Date().getMonth())
    const currentYear = ref(new Date().getFullYear())
    const currentMonthYear = computed(() =>
        new Date(currentYear.value, currentMonth.value).toLocaleDateString('en-US', {
            month: 'long',
            year: 'numeric'
        })
    )

    // Fetch rooms on mount
    onMounted(async () => {
        await fetchRooms()
    })

    const fetchRooms = async () => {
        try {
            loading.value = true
            const response = await axios.get('/api/rooms')
            rooms.value = response.data.data || response.data
        } catch (error) {
            console.error('Failed to fetch rooms', error)
            Swal.fire('Error', 'Failed to load rooms', 'error')
        } finally {
            loading.value = false
        }
    }

    const clearFilters = () => {
        roomTypeFilter.value = 'all'
        statusFilter.value = 'all'
        searchQuery.value = ''
    }

    const previousMonth = () => {
        if (currentMonth.value === 0) {
            currentMonth.value = 11
            currentYear.value--
        } else {
            currentMonth.value--
        }
    }

    const nextMonth = () => {
        if (currentMonth.value === 11) {
            currentMonth.value = 0
            currentYear.value++
        } else {
            currentMonth.value++
        }
    }

    const filteredRooms = computed(() =>
        rooms.value.filter(room => {
            const matchesType = roomTypeFilter.value === 'all' || room.type === roomTypeFilter.value
            const matchesStatus = statusFilter.value === 'all' || room.status === statusFilter.value
            const matchesSearch = searchQuery.value === '' ||
                room.number.includes(searchQuery.value) ||
                room.name.toLowerCase().includes(searchQuery.value.toLowerCase())
            return matchesType && matchesStatus && matchesSearch
        })
    )

    const availableRoomsCount = computed(() =>
        rooms.value.filter(r => r.status === 'Available').length
    )

    const roomTypesCount = computed(() =>
        rooms.value.reduce((acc: Record<string, number>, room) => {
            acc[room.type] = (acc[room.type] || 0) + 1
            return acc
        }, {})
    )

    const avgRoomPrice = computed(() => {
        if (rooms.value.length === 0) return '0.00'
        const total = rooms.value.reduce((sum, room) => sum + (room.base_price || 0), 0)
        return (total / rooms.value.length).toFixed(2)
    })

    const calendarDays = computed<CalendarDay[]>(() => {
        const days: CalendarDay[] = []
        const firstDayOfMonth = new Date(currentYear.value, currentMonth.value, 1)
        const lastDayOfMonth = new Date(currentYear.value, currentMonth.value + 1, 0)

        const prevMonthLastDay = new Date(currentYear.value, currentMonth.value, 0).getDate()
        const firstDayOfWeek = firstDayOfMonth.getDay()

        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const day = prevMonthLastDay - i
            const date = new Date(currentYear.value, currentMonth.value - 1, day).toISOString().split('T')[0]
            days.push({ day, date, isCurrentMonth: false })
        }

        for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
            const date = new Date(currentYear.value, currentMonth.value, i).toISOString().split('T')[0]
            days.push({ day: i, date, isCurrentMonth: true })
        }

        const daysToAdd = 42 - days.length
        for (let i = 1; i <= daysToAdd; i++) {
            const date = new Date(currentYear.value, currentMonth.value + 1, i).toISOString().split('T')[0]
            days.push({ day: i, date, isCurrentMonth: false })
        }

        return days
    })

    const statusClass = (status: string) => {
        switch (status) {
            case 'Available': return 'bg-green-100 text-green-800'
            case 'Maintenance': return 'bg-yellow-100 text-yellow-800'
            case 'Renovation': return 'bg-red-100 text-red-800'
            default: return 'bg-gray-100 text-gray-800'
        }
    }

    const openRoomModal = (room: Room | null) => {
        if (room) {
            // When editing an existing room, ensure all fields are properly set
            currentRoom.id = room.id
            currentRoom.number = room.number || ''
            currentRoom.name = room.name || ''
            currentRoom.type = room.type || ''
            currentRoom.base_price = room.base_price || null
            currentRoom.capacity = room.capacity || null
            currentRoom.status = room.status || ''
            currentRoom.description = room.description || ''
            currentRoom.image = room.image || ''
            currentRoom.seasonalRates = room.seasonalRates || []
            currentRoom.availabilities = room.availabilities || []
        } else {
            resetCurrentRoom()
        }
        
        showRoomModal.value = true
    }
    const closeRoomModal = () => showRoomModal.value = false
    const openPricingModal = (room: Room) => { Object.assign(currentRoom, JSON.parse(JSON.stringify(room))); showPricingModal.value = true }
    const closePricingModal = () => showPricingModal.value = false
    const openAvailabilityModal = (room: Room) => { Object.assign(currentRoom, JSON.parse(JSON.stringify(room))); showAvailabilityModal.value = true }
    const closeAvailabilityModal = () => showAvailabilityModal.value = false

    const resetCurrentRoom = () => {
        currentRoom.id = null
        currentRoom.number = ''
        currentRoom.name = ''
        currentRoom.type = ''
        currentRoom.base_price = null
        currentRoom.capacity = null
        currentRoom.status = ''
        currentRoom.description = ''
        currentRoom.image = ''
        currentRoom.seasonalRates = []
        currentRoom.availabilities = []
        
        // Reset image preview
        imagePreview.value = null
        selectedImageFile.value = null
    }

    // Image handling functions
    const handleImageUpload = (event: Event) => {
        const target = event.target as HTMLInputElement
        const file = target.files?.[0]
        
        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                Swal.fire('Error', 'Please select a valid image file (PNG, JPG, GIF)', 'error')
                return
            }
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('Error', 'Image size must be less than 2MB', 'error')
                return
            }
            
            selectedImageFile.value = file
            
            // Create preview
            const reader = new FileReader()
            reader.onload = (e) => {
                imagePreview.value = e.target?.result as string
            }
            reader.onerror = () => {
                Swal.fire('Error', 'Failed to read image file', 'error')
            }
            reader.readAsDataURL(file)
        }
    }

    const removeImage = async () => {
        const result = await Swal.fire({
            title: 'Remove Image?',
            text: 'Are you sure you want to remove this image?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
        })

        if (result.isConfirmed) {
            imagePreview.value = null
            selectedImageFile.value = null
            currentRoom.image = ''
            
            // Clear file input
            const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement
            if (fileInput) {
                fileInput.value = ''
            }
        }
    }

    const handleImageError = (event: Event) => {
        const target = event.target as HTMLImageElement
        target.style.display = 'none'
        // You could also set a fallback image here
    }

    const handleTableImageError = (event: Event) => {
        const target = event.target as HTMLImageElement
        const parent = target.parentElement
        if (parent) {
            parent.innerHTML = `
                <div class="w-16 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            `
        }
    }

    const saveRoom = async () => {
        // Client-side validation
        const validationErrors = []
        
        if (!currentRoom.number?.trim()) {
            validationErrors.push('Room number is required')
        }
        if (!currentRoom.name?.trim()) {
            validationErrors.push('Room name is required')
        }
        if (!currentRoom.type?.trim()) {
            validationErrors.push('Room type is required')
        }
        if (!currentRoom.base_price || currentRoom.base_price <= 0) {
            validationErrors.push('Base price must be greater than 0')
        }
        if (!currentRoom.capacity || currentRoom.capacity <= 0) {
            validationErrors.push('Capacity must be greater than 0')
        }
        if (!currentRoom.status?.trim()) {
            validationErrors.push('Room status is required')
        }
        
        if (validationErrors.length > 0) {
            Swal.fire('Validation Error', validationErrors.join('\n'), 'error')
            return
        }

        try {
            loading.value = true
            
            // Create FormData for file upload
            const formData = new FormData()
            formData.append('number', currentRoom.number.trim())
            formData.append('name', currentRoom.name.trim())
            formData.append('type', currentRoom.type.trim())
            formData.append('base_price', Number(currentRoom.base_price).toString())
            formData.append('capacity', Number(currentRoom.capacity).toString())
            formData.append('status', currentRoom.status) // Keep original case, backend will normalize
            formData.append('description', currentRoom.description?.trim() || '')
            
            // Add image if selected
            if (selectedImageFile.value) {
                formData.append('image', selectedImageFile.value)
            }
            
            if (currentRoom.id) {
                // Use the custom FormData update route
                const response = await axios.post(`/rooms/${currentRoom.id}/update`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                const index = rooms.value.findIndex(r => r.id === currentRoom.id)
                if (index !== -1) rooms.value[index] = response.data
                Swal.fire('Success', 'Room updated successfully', 'success')
            } else {
                const response = await axios.post('/rooms', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                rooms.value.push(response.data)
                Swal.fire('Success', 'Room created successfully', 'success')
            }
            
            // Reset image preview
            imagePreview.value = null
            selectedImageFile.value = null
            showRoomModal.value = false
        } catch (error: any) {
            console.error('Failed to save room', error)
            if (error.response?.data?.errors) {
                // Handle validation errors from backend
                const errorMessages = Object.values(error.response.data.errors).flat().join('\n')
                Swal.fire('Validation Error', errorMessages, 'error')
            } else {
                const errorMessage = error.response?.data?.message || 'Failed to save room'
                Swal.fire('Error', errorMessage, 'error')
            }
        } finally {
            loading.value = false
        }
    }

    const confirmDeleteRoom = async (room: Room) => {
        const result = await Swal.fire({
            title: 'Confirm Deletion',
            text: `Delete ${room.name} (#${room.number})? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        })

        if (result.isConfirmed) {
            deleteRoom(room.id!)
        }
    }

    const deleteRoom = async (id: number) => {
        try {
            loading.value = true
            await axios.delete(`/rooms/${id}`)
            rooms.value = rooms.value.filter(room => room.id !== id)
            Swal.fire('Success', 'Room deleted successfully', 'success')
        } catch (error: any) {
            console.error('Failed to delete room', error)
            const errorMessage = error.response?.data?.message || 'Failed to delete room'
            Swal.fire('Error', errorMessage, 'error')
        } finally {
            loading.value = false
        }
    }

    const addSeasonalRate = () => {
        currentRoom.seasonalRates.push({
            name: '',
            start_date: new Date().toISOString().split('T')[0],
            end_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            multiplier: 1.2
        })
    }

    const removeSeasonalRate = (index: number) => {
        currentRoom.seasonalRates.splice(index, 1)
    }

    const saveSeasonalPricing = async () => {
        try {
            loading.value = true
            await axios.post(`/rooms/${currentRoom.id}/seasonal-rates`, {
                seasonalRates: currentRoom.seasonalRates
            })
            const index = rooms.value.findIndex(r => r.id === currentRoom.id)
            if (index !== -1) {
                const response = await axios.get(`/api/rooms/${currentRoom.id}`)
                rooms.value[index] = response.data
            }
            Swal.fire('Success', 'Seasonal rates saved successfully', 'success')
            showPricingModal.value = false
        } catch (error: any) {
            console.error('Failed to save seasonal rates', error)
            const errorMessage = error.response?.data?.message || 'Failed to save seasonal rates'
            Swal.fire('Error', errorMessage, 'error')
        } finally {
            loading.value = false
        }
    }

    const getAvailabilityStatus = (date: string) => {
        if (currentRoom.status !== 'Available') return currentRoom.status
        const availability = currentRoom.availabilities.find(a => a.date === date)
        if (!availability) return 'Available'
        return availability.status.charAt(0).toUpperCase() + availability.status.slice(1)
    }

    const dayAvailabilityClass = (date: string) => {
        if (currentRoom.status !== 'Available') return 'bg-yellow-100 text-yellow-800 border border-yellow-300'
        const availability = currentRoom.availabilities.find(a => a.date === date)
        if (!availability) return 'bg-green-100 text-green-800 border border-green-300'
        return availability.status === 'booked'
            ? 'bg-red-100 text-red-800 border border-red-300'
            : 'bg-yellow-100 text-yellow-800 border border-yellow-300'
    }

    const toggleAvailability = (date: string) => {
        if (currentRoom.status !== 'Available') return

        const index = currentRoom.availabilities.findIndex(a => a.date === date)
        if (index === -1) {
            currentRoom.availabilities.push({ date, status: 'blocked' })
        } else {
            const currentStatus = currentRoom.availabilities[index].status
            if (currentStatus === 'blocked') {
                currentRoom.availabilities.splice(index, 1)
            } else if (currentStatus === 'booked') {
                Swal.fire('Warning', 'This date is already booked and cannot be changed.', 'warning')
            }
        }
    }

    const saveAvailability = async () => {
        try {
            loading.value = true
            await axios.post(`/rooms/${currentRoom.id}/availabilities`, {
                availability: currentRoom.availabilities
            })
            const index = rooms.value.findIndex(r => r.id === currentRoom.id)
            if (index !== -1) {
                const response = await axios.get(`/api/rooms/${currentRoom.id}`)
                rooms.value[index] = response.data
            }
            Swal.fire('Success', 'Availability saved successfully', 'success')
            showAvailabilityModal.value = false
        } catch (error: any) {
            console.error('Failed to save availability', error)
            const errorMessage = error.response?.data?.message || 'Failed to save availability'
            Swal.fire('Error', errorMessage, 'error')
        } finally {
            loading.value = false
        }
    }
</script>


<style scoped>
    /* Custom color palette */
    .bg-gold-500 { background-color: #D4AF37; }
    .bg-gold-600 { background-color: #B8860B; }
    .text-gold-500 { color: #D4AF37; }
    .bg-blue-900 { background-color: #0C2D6A; }
    .text-blue-900 { color: #0C2D6A; }
    .text-blue-700 { color: #1A4480; }
    .bg-blue-50 { background-color: #F0F5FF; }

    /* Table styling */
    table {
        border-collapse: separate;
        border-spacing: 0;
    }

    th {
        font-weight: 600;
    }

    tr:last-child td {
        border-bottom: none;
    }

    /* Calendar day styling */
    .min-h-24 {
        min-height: 6rem;
    }

    /* Loading animation */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Hover effects */
    .hover\:bg-blue-50:hover {
        background-color: #F0F5FF;
    }

    /* Transition effects */
    .transition {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Focus states */
    .focus\:outline-none:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
    }

    .focus\:ring-2:focus {
        --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
        --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
        box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
    }

    .focus\:ring-gold-500:focus {
        --tw-ring-color: #D4AF37;
    }
</style>
