<template>
    <AdminLayout>
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Booking Management</h2>
                    <p class="text-gray-600">Manage all hotel bookings and reservations</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
                >
                    <PlusIcon class="h-5 w-5 mr-2" />
                    New Booking
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <Select
                        v-model="filters.status"
                        :options="statusOptions"
                        placeholder="All Statuses"
                        label="Status"
                    />
                </div>
                <div>
                    <DatePicker
                        v-model="filters.dateRange"
                        :range="true"
                        placeholder="Select date range..."
                        label="Date Range"
                        format="dd/MM/yyyy"
                    />
                </div>
                <div>
                    <Select
                        v-model="filters.roomType"
                        :options="roomTypeOptions"
                        placeholder="All Types"
                        label="Room Type"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Search bookings..."
                            class="w-full border border-gray-300 rounded-md p-2 pl-10"
                        >
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="booking in filteredBookings" :key="booking.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ booking.booking_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ booking.guest?.name }}</div>
                                    <div class="text-sm text-gray-500">{{ booking.guest?.email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ booking.room?.name }} ({{ booking.room?.type }})</div>
                            <div class="text-sm text-gray-500">#{{ booking.room?.number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ formatDate(booking.check_in_date) }} - {{ formatDate(booking.check_out_date) }}
                            </div>
                            <div class="text-sm text-gray-500">{{ booking.nights }} nights</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                <span :class="statusBadgeClass(booking.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ booking.status }}
                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ booking.total_amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="editBooking(booking)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                            <button @click="confirmDelete(booking)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button @click="currentPage -= 1" :disabled="currentPage === 1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </button>
                    <button @click="currentPage += 1" :disabled="currentPage === totalPages" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span> to <span class="font-medium">{{ Math.min(currentPage * perPage, bookings.length) }}</span> of <span class="font-medium">{{ bookings.length }}</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button
                                @click="currentPage -= 1"
                                :disabled="currentPage === 1"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                            >
                                <span class="sr-only">Previous</span>
                                <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                            </button>
                            <button
                                v-for="page in pages"
                                :key="page"
                                @click="currentPage = page"
                                :class="{'bg-blue-50 border-blue-500 text-blue-600': currentPage === page, 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': currentPage !== page}"
                                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                            >
                                {{ page }}
                            </button>
                            <button
                                @click="currentPage += 1"
                                :disabled="currentPage === totalPages"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                            >
                                <span class="sr-only">Next</span>
                                <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ isEditing ? 'Edit Booking' : 'Create New Booking' }}
                </h2>

                <form @submit.prevent="isEditing ? updateBooking() : createBooking()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Guest</label>
                            <select
                                v-model="form.guest_id"
                                required
                                class="w-full border border-gray-300 rounded-md p-2"
                            >
                                <option value="">Select Guest</option>
                                <option v-for="guest in guests" :key="guest.id" :value="guest.id">
                                    {{ guest.name }} ({{ guest.email }})
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                            <select
                                v-model="form.room_id"
                                required
                                class="w-full border border-gray-300 rounded-md p-2"
                                @change="updateRoomDetails"
                            >
                                <option value="">Select Room</option>
                                <option v-for="room in availableRooms" :key="room.id" :value="room.id">
                                    {{ room.name }} ({{ room.type }}) - ${{ room.base_price }}/night
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Date</label>
                            <DatePicker
                                v-model="form.check_in_date"
                                required
                                class="w-full"
                                :min-date="new Date()"
                                @update:modelValue="calculateNights"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check-out Date</label>
                            <DatePicker
                                v-model="form.check_out_date"
                                required
                                class="w-full"
                                :min-date="form.check_in_date ? new Date(form.check_in_date) : new Date()"
                                @update:modelValue="calculateNights"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adults</label>
                            <input
                                v-model="form.adults"
                                type="number"
                                min="1"
                                required
                                class="w-full border border-gray-300 rounded-md p-2"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Children</label>
                            <input
                                v-model="form.children"
                                type="number"
                                min="0"
                                class="w-full border border-gray-300 rounded-md p-2"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select
                                v-model="form.payment_method"
                                required
                                class="w-full border border-gray-300 rounded-md p-2"
                            >
                                <option value="">Select Payment Method</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <select
                                v-model="form.payment_status"
                                required
                                class="w-full border border-gray-300 rounded-md p-2"
                            >
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                v-model="form.status"
                                required
                                class="w-full border border-gray-300 rounded-md p-2"
                            >
                                <option value="confirmed">Confirmed</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="checked_in">Checked In</option>
                                <option value="checked_out">Checked Out</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                            <input
                                v-model="form.total_amount"
                                type="number"
                                min="0"
                                step="0.01"
                                required
                                class="w-full border border-gray-300 rounded-md p-2"
                                readonly
                            >
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button
                            type="button"
                            @click="closeModal"
                            class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none"
                        >
                            {{ isEditing ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Delete Booking</h2>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this booking? This action cannot be undone.</p>

                <div class="flex justify-end">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="deleteBooking"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue'
    import { useForm } from '@inertiajs/vue3'
    import AdminLayout from '@/layouts/AdminLayout.vue'
    import Modal from '@/components/Modal.vue'
    import { DatePicker, Select } from '@/components/ui'
    import type { Booking, Guest, Room } from '@/types'
    import {
        ChevronLeftIcon,
        ChevronRightIcon,
        PlusIcon,
        MagnifyingGlassIcon
    } from '@heroicons/vue/24/outline'

    const props = defineProps<{
        bookings: Booking[]
        guests: Guest[]
        rooms: Room[]
    }>()

    // State
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const isEditing = ref(false)
    const currentPage = ref(1)
    const perPage = 10
    const bookingToDelete = ref<number | null>(null)

    // Filters
    const filters = ref({
        status: '',
        dateRange: null as [Date, Date] | null,
        roomType: '',
        search: ''
    })

    // Form
    const form = useForm({
        id: null as number | null,
        guest_id: '',
        room_id: '',
        check_in_date: '',
        check_out_date: '',
        adults: 1,
        children: 0,
        payment_method: '',
        payment_status: 'pending',
        status: 'confirmed',
        total_amount: 0,
        nights: 0
    })

    // Computed
    const statusOptions = [
        { value: '', label: 'All Statuses' },
        { value: 'confirmed', label: 'Confirmed' },
        { value: 'pending', label: 'Pending' },
        { value: 'cancelled', label: 'Cancelled' },
        { value: 'checked_in', label: 'Checked In' },
        { value: 'checked_out', label: 'Checked Out' }
    ]
    
    const roomTypeOptions = computed(() => {
        if (!props.rooms || !props.rooms.length) return [{ value: '', label: 'All Types' }]
        const types = [...new Set(props.rooms.map(room => room.type))]
        return [
            { value: '', label: 'All Types' },
            ...types.map(type => ({ value: type, label: type }))
        ]
    })

    const filteredBookings = computed(() => {
        let result = props.bookings

        if (filters.value.status) {
            result = result.filter(b => b.status === filters.value.status)
        }

        if (filters.value.dateRange && filters.value.dateRange.length === 2) {
            const [start, end] = filters.value.dateRange
            result = result.filter(b => {
                const checkIn = new Date(b.check_in_date)
                const checkOut = new Date(b.check_out_date)
                return (checkIn >= start && checkIn <= end) ||
                    (checkOut >= start && checkOut <= end) ||
                    (checkIn <= start && checkOut >= end)
            })
        }

        if (filters.value.roomType) {
            result = result.filter(b => b.room?.type === filters.value.roomType)
        }

        if (filters.value.search) {
            const search = filters.value.search.toLowerCase()
            result = result.filter(b =>
                b.booking_code.toLowerCase().includes(search) ||
                b.guest?.name.toLowerCase().includes(search) ||
                b.guest?.email.toLowerCase().includes(search) ||
                b.room?.name.toLowerCase().includes(search) ||
                b.room?.number.toLowerCase().includes(search)
            )
        }

        return result
    })

    const availableRooms = computed(() => {
        if (!form.check_in_date || !form.check_out_date) {
            return props.rooms
        }

        // Filter out rooms that are already booked for the selected dates
        const bookedRoomIds = props.bookings
            .filter(booking => {
                // Skip the current booking if we're editing
                if (isEditing.value && booking.id === form.id) return false

                const bookingStart = new Date(booking.check_in_date)
                const bookingEnd = new Date(booking.check_out_date)
                const selectedStart = new Date(form.check_in_date)
                const selectedEnd = new Date(form.check_out_date)

                // Check for date overlap
                return (
                    (selectedStart >= bookingStart && selectedStart < bookingEnd) ||
                    (selectedEnd > bookingStart && selectedEnd <= bookingEnd) ||
                    (selectedStart <= bookingStart && selectedEnd >= bookingEnd)
                )
            })
            .map(booking => booking.room_id)

        return props.rooms.filter(room => !bookedRoomIds.includes(room.id))
    })

    const totalPages = computed(() => Math.ceil(filteredBookings.value.length / perPage))
    const pages = computed(() => {
        const range = []
        const start = Math.max(1, currentPage.value - 2)
        const end = Math.min(totalPages.value, currentPage.value + 2)

        for (let i = start; i <= end; i++) {
            range.push(i)
        }

        return range
    })

    // Methods
    const openCreateModal = () => {
        isEditing.value = false
        form.reset()
        form.adults = 1
        form.status = 'confirmed'
        form.payment_status = 'pending'
        form.nights = 0
        showModal.value = true
    }

    const editBooking = (booking: Booking) => {
        isEditing.value = true
        form.id = booking.id
        form.guest_id = booking.guest_id.toString()
        form.room_id = booking.room_id.toString()
        form.check_in_date = booking.check_in_date
        form.check_out_date = booking.check_out_date
        form.adults = booking.adults
        form.children = booking.children
        form.payment_method = booking.payment_method
        form.payment_status = booking.payment_status
        form.status = booking.status
        form.total_amount = booking.total_amount
        form.nights = booking.nights || 0
        showModal.value = true
    }

    const closeModal = () => {
        showModal.value = false
    }

    const confirmDelete = (booking: Booking) => {
        bookingToDelete.value = booking.id
        showDeleteModal.value = true
    }

    const createBooking = () => {
        // Calculate nights and total amount before submission
        calculateNights()
        
        form.post(route('bookings.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal()
                // Refresh the page to get updated data
                window.location.reload()
            },
            onError: (errors) => {
                console.error('Booking creation errors:', errors)
            }
        })
    }

    const updateBooking = () => {
        if (form.id) {
            // Calculate nights and total amount before submission
            calculateNights()
            
            form.put(route('bookings.update', form.id), {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal()
                    // Refresh the page to get updated data
                    window.location.reload()
                },
                onError: (errors) => {
                    console.error('Booking update errors:', errors)
                }
            })
        }
    }

    const deleteBooking = () => {
        if (bookingToDelete.value) {
            form.delete(route('bookings.destroy', bookingToDelete.value), {
                preserveScroll: true,
                onSuccess: () => {
                    showDeleteModal.value = false
                    // Refresh the page to get updated data
                    window.location.reload()
                }
            })
        }
    }

    const calculateNights = () => {
        if (form.check_in_date && form.check_out_date) {
            const start = new Date(form.check_in_date)
            const end = new Date(form.check_out_date)
            const diffTime = Math.abs(end.getTime() - start.getTime())
            form.nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

            // Calculate total amount if room is selected
            if (form.room_id) {
                const room = props.rooms.find(r => r.id.toString() === form.room_id)
                if (room) {
                    form.total_amount = room.base_price * form.nights
                }
            }
        }
    }

    const updateRoomDetails = () => {
        if (form.room_id && form.nights) {
            const room = props.rooms.find(r => r.id.toString() === form.room_id)
            if (room) {
                form.total_amount = room.base_price * form.nights
            }
        }
    }

    const formatDate = (dateString: string) => {
        if (!dateString) return ''
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    }

    const statusBadgeClass = (status: string) => {
        switch (status) {
            case 'confirmed':
                return 'bg-blue-100 text-blue-800'
            case 'pending':
                return 'bg-yellow-100 text-yellow-800'
            case 'cancelled':
                return 'bg-red-100 text-red-800'
            case 'checked_in':
                return 'bg-green-100 text-green-800'
            case 'checked_out':
                return 'bg-purple-100 text-purple-800'
            default:
                return 'bg-gray-100 text-gray-800'
        }
    }

    // API functions for future use
    const fetchBookings = async (filters: Record<string, any> = {}) => {
        try {
            const params = new URLSearchParams()
            Object.entries(filters).forEach(([key, value]) => {
                if (value) params.append(key, value)
            })
            
            const response = await fetch(`/api/bookings?${params}`)
            const data = await response.json()
            return data
        } catch (error) {
            console.error('Error fetching bookings:', error)
            return null
        }
    }

    const fetchAvailableRooms = async (checkIn: string, checkOut: string, excludeId?: number) => {
        try {
            const params = new URLSearchParams({
                check_in_date: checkIn,
                check_out_date: checkOut
            })
            if (excludeId) params.append('exclude_booking_id', excludeId.toString())
            
            const response = await fetch(`/api/bookings/available-rooms?${params}`)
            const data = await response.json()
            return data
        } catch (error) {
            console.error('Error fetching available rooms:', error)
            return []
        }
    }

    const fetchStats = async () => {
        try {
            const response = await fetch('/api/bookings/stats')
            const data = await response.json()
            return data
        } catch (error) {
            console.error('Error fetching stats:', error)
            return null
        }
    }

    onMounted(() => {
        // Initialize any data if needed
    })
</script>
