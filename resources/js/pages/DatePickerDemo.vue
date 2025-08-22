<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">DatePicker Component Demo</h1>
                <p class="text-gray-600">Showcasing the improved DatePicker with various configurations</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Single Date Selection -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Single Date Selection</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Basic Date Picker</label>
                            <DatePicker v-model="singleDate" placeholder="Select a date" />
                            <p class="mt-2 text-sm text-gray-600">Selected: {{ singleDate ? formatDate(singleDate) : 'None' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">With Min Date (Today)</label>
                            <DatePicker v-model="singleDateWithMin" :min-date="new Date()" placeholder="Select future date" />
                            <p class="mt-2 text-sm text-gray-600">Selected: {{ singleDateWithMin ? formatDate(singleDateWithMin) : 'None' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">With Custom Format</label>
                            <DatePicker v-model="singleDateCustomFormat" format="EEEE, MMMM do, yyyy" placeholder="Select date" />
                            <p class="mt-2 text-sm text-gray-600">Selected: {{ singleDateCustomFormat ? formatDate(singleDateCustomFormat) : 'None' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Disabled State</label>
                            <DatePicker v-model="singleDateDisabled" disabled placeholder="Disabled picker" />
                        </div>
                    </div>
                </div>

                <!-- Date Range Selection -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Date Range Selection</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date Range Picker</label>
                            <DatePicker v-model="dateRange" range placeholder="Select date range" />
                            <p class="mt-2 text-sm text-gray-600">
                                Selected: {{ dateRange.length === 2 ? `${formatDate(dateRange[0])} - ${formatDate(dateRange[1])}` : 'None' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Range with Min Date</label>
                            <DatePicker v-model="dateRangeWithMin" range :min-date="new Date()" placeholder="Select future range" />
                            <p class="mt-2 text-sm text-gray-600">
                                Selected: {{ dateRangeWithMin.length === 2 ? `${formatDate(dateRangeWithMin[0])} - ${formatDate(dateRangeWithMin[1])}` : 'None' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Range with Max Date (30 days from now)</label>
                            <DatePicker v-model="dateRangeWithMax" range :max-date="maxDate" placeholder="Select range within 30 days" />
                            <p class="mt-2 text-sm text-gray-600">
                                Selected: {{ dateRangeWithMax.length === 2 ? `${formatDate(dateRangeWithMax[0])} - ${formatDate(dateRangeWithMax[1])}` : 'None' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Advanced Features -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Advanced Features</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date with Min/Max Constraints</label>
                            <DatePicker v-model="constrainedDate" :min-date="minDate" :max-date="maxDate" placeholder="Select date within range" />
                            <p class="mt-2 text-sm text-gray-600">Selected: {{ constrainedDate ? formatDate(constrainedDate) : 'None' }}</p>
                            <p class="mt-1 text-xs text-gray-500">Min: {{ formatDate(minDate) }} | Max: {{ formatDate(maxDate) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Custom Placeholder</label>
                            <DatePicker v-model="customPlaceholderDate" placeholder="🎯 Pick your lucky date!" />
                            <p class="mt-2 text-sm text-gray-600">Selected: {{ customPlaceholderDate ? formatDate(customPlaceholderDate) : 'None' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Long Format Date</label>
                            <DatePicker v-model="longFormatDate" format="'The' do 'day of' MMMM, yyyy" placeholder="Select date" />
                            <p class="mt-2 text-sm text-gray-600">Selected: {{ longFormatDate ? formatDate(longFormatDate) : 'None' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Interactive Demo -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Interactive Demo</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                            <DatePicker v-model="checkInDate" :min-date="new Date()" placeholder="Select check-in date" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                            <DatePicker v-model="checkOutDate" :min-date="checkInDate || new Date()" placeholder="Select check-out date" />
                        </div>

                        <div class="bg-blue-50 p-4 rounded-md">
                            <h3 class="font-medium text-blue-900 mb-2">Booking Summary</h3>
                            <div class="text-sm text-blue-800">
                                <p v-if="checkInDate && checkOutDate">
                                    <strong>Check-in:</strong> {{ formatDate(checkInDate) }}<br>
                                    <strong>Check-out:</strong> {{ formatDate(checkOutDate) }}<br>
                                    <strong>Duration:</strong> {{ calculateNights(checkInDate, checkOutDate) }} nights
                                </p>
                                <p v-else class="text-blue-600">Please select both check-in and check-out dates</p>
                            </div>
                        </div>

                        <div class="flex space-x-2">
                            <button @click="clearAllDates" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                                Clear All
                            </button>
                            <button @click="setRandomDates" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                Random Dates
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Component Information -->
            <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Component Features</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Core Features</h3>
                        <ul class="space-y-1">
                            <li>• Single date and date range selection</li>
                            <li>• Beautiful calendar interface with month navigation</li>
                            <li>• Keyboard navigation support (Enter, Space, Escape)</li>
                            <li>• Click outside to close functionality</li>
                            <li>• Responsive design for mobile and desktop</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Advanced Features</h3>
                        <ul class="space-y-1">
                            <li>• Min/max date constraints</li>
                            <li>• Custom date formatting with date-fns</li>
                            <li>• Disabled state support</li>
                            <li>• Today button for quick navigation</li>
                            <li>• Clear button for easy reset</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { format, addDays, differenceInDays } from 'date-fns'
import DatePicker from '@/components/DatePicker.vue'

// Single date states
const singleDate = ref<Date | null>(null)
const singleDateWithMin = ref<Date | null>(null)
const singleDateCustomFormat = ref<Date | null>(null)
const singleDateDisabled = ref<Date | null>(null)
const constrainedDate = ref<Date | null>(null)
const customPlaceholderDate = ref<Date | null>(null)
const longFormatDate = ref<Date | null>(null)

// Date range states
const dateRange = ref<[Date, Date] | []>([])
const dateRangeWithMin = ref<[Date, Date] | []>([])
const dateRangeWithMax = ref<[Date, Date] | []>([])

// Interactive demo states
const checkInDate = ref<Date | null>(null)
const checkOutDate = ref<Date | null>(null)

// Computed values
const minDate = computed(() => new Date())
const maxDate = computed(() => addDays(new Date(), 30))

// Methods
const formatDate = (date: Date): string => {
    return format(date, 'MMM dd, yyyy')
}

const calculateNights = (checkIn: Date, checkOut: Date): number => {
    return differenceInDays(checkOut, checkIn)
}

const clearAllDates = () => {
    singleDate.value = null
    singleDateWithMin.value = null
    singleDateCustomFormat.value = null
    singleDateDisabled.value = null
    constrainedDate.value = null
    customPlaceholderDate.value = null
    longFormatDate.value = null
    dateRange.value = []
    dateRangeWithMin.value = []
    dateRangeWithMax.value = []
    checkInDate.value = null
    checkOutDate.value = null
}

const setRandomDates = () => {
    const today = new Date()
    const randomCheckIn = addDays(today, Math.floor(Math.random() * 7) + 1)
    const randomCheckOut = addDays(randomCheckIn, Math.floor(Math.random() * 7) + 1)
    
    checkInDate.value = randomCheckIn
    checkOutDate.value = randomCheckOut
}
</script>
