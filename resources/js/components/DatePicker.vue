<template>
    <div class="relative">
        <!-- Input Field -->
        <div class="relative">
            <input
                ref="inputRef"
                type="text"
                :value="displayValue"
                @click="toggleCalendar"
                @keydown="handleKeydown"
                @blur="handleBlur"
                :placeholder="placeholder"
                :disabled="disabled"
                :class="[
                    'block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-3 pr-10 cursor-pointer',
                    'bg-white hover:bg-gray-50 transition-colors duration-200',
                    disabled && 'bg-gray-100 cursor-not-allowed opacity-60'
                ]"
                readonly
            />
            
            <!-- Calendar Icon -->
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>

            <!-- Clear Button -->
            <button
                v-if="modelValue && !disabled"
                @click="clearDate"
                type="button"
                class="absolute inset-y-0 right-8 pr-2 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200"
                :aria-label="'Clear ' + (range ? 'date range' : 'date')"
            >
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Calendar Dropdown -->
        <div
            v-if="isOpen"
            class="absolute z-50 mt-1 w-80 bg-white border border-gray-200 rounded-lg shadow-lg"
            @click.stop
        >
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <button
                    @click="previousMonth"
                    type="button"
                    class="p-2 hover:bg-gray-100 rounded-md transition-colors duration-200"
                    :aria-label="'Previous month'"
                >
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ currentMonthYear }}
                </h2>
                
                <button
                    @click="nextMonth"
                    type="button"
                    class="p-2 hover:bg-gray-100 rounded-md transition-colors duration-200"
                    :aria-label="'Next month'"
                >
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Weekday Headers -->
            <div class="grid grid-cols-7 gap-px bg-gray-200">
                <div
                    v-for="day in weekdays"
                    :key="day"
                    class="bg-gray-50 px-3 py-2 text-center text-sm font-medium text-gray-700"
                >
                    {{ day }}
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-px bg-gray-200">
                <button
                    v-for="day in calendarDays"
                    :key="day.date"
                    @click="selectDate(day.date)"
                    type="button"
                    :disabled="!day.isEnabled"
                    :class="[
                        'px-3 py-2 text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                        day.isCurrentMonth ? 'bg-white hover:bg-blue-50' : 'bg-gray-50 text-gray-400',
                        day.isToday && 'font-semibold text-blue-600',
                        day.isSelected && 'bg-blue-600 text-white hover:bg-blue-700',
                        day.isInRange && !day.isSelected && 'bg-blue-100 text-blue-900',
                        day.isStartDate && 'bg-blue-600 text-white',
                        day.isEndDate && 'bg-blue-600 text-white',
                        !day.isEnabled && 'text-gray-300 cursor-not-allowed hover:bg-transparent'
                    ]"
                    :aria-label="`${day.date.toDateString()}`"
                >
                    {{ day.dayNumber }}
                </button>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between p-4 border-t border-gray-200">
                <button
                    @click="goToToday"
                    type="button"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200"
                >
                    Today
                </button>
                
                <div v-if="range && selectedRange.start && selectedRange.end" class="text-sm text-gray-600">
                    {{ formatRangeDisplay(selectedRange.start, selectedRange.end) }}
                </div>
                
                <div class="flex space-x-2">
                    <button
                        @click="closeCalendar"
                        type="button"
                        class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 transition-colors duration-200"
                    >
                        Cancel
                    </button>
                    <button
                        @click="applySelection"
                        type="button"
                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200"
                    >
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { format, addMonths, subMonths, startOfMonth, endOfMonth, eachDayOfInterval, isSameMonth, isSameDay, isToday, isBefore, isAfter, addDays, differenceInDays, startOfDay } from 'date-fns'

interface Props {
    modelValue?: Date | string | [Date, Date] | null
    minDate?: Date | string
    maxDate?: Date | string
    range?: boolean
    disabled?: boolean
    placeholder?: string
    format?: string
}

const props = withDefaults(defineProps<Props>(), {
    range: false,
    disabled: false,
    placeholder: 'Select date',
    format: 'MMM dd, yyyy'
})

const emit = defineEmits<{
    'update:modelValue': [value: Date | string | [Date, Date] | null]
}>()

// Refs
const inputRef = ref<HTMLInputElement>()
const isOpen = ref(false)
const currentMonth = ref(new Date())
const selectedRange = ref<{ start: Date | null; end: Date | null }>({ start: null, end: null })

// Computed
const weekdays = computed(() => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'])

const currentMonthYear = computed(() => {
    return format(currentMonth.value, 'MMMM yyyy')
})

const displayValue = computed(() => {
    if (!props.modelValue) return ''
    
    if (props.range && Array.isArray(props.modelValue)) {
        if (props.modelValue.length === 2) {
            return `${format(props.modelValue[0], props.format)} - ${format(props.modelValue[1], props.format)}`
        }
        return ''
    }
    
    if (props.modelValue instanceof Date || typeof props.modelValue === 'string') {
        const date = new Date(props.modelValue)
        return format(date, props.format)
    }
    
    return ''
})

const calendarDays = computed(() => {
    const start = startOfMonth(currentMonth.value)
    const end = endOfMonth(currentMonth.value)
    const days = eachDayOfInterval({ start, end })
    
    // Add padding days from previous month
    const firstDayOfWeek = start.getDay()
    const paddingStart = Array.from({ length: firstDayOfWeek }, (_, i) => {
        const date = subDays(start, firstDayOfWeek - i)
        return date
    })
    
    // Add padding days from next month
    const lastDayOfWeek = end.getDay()
    const paddingEnd = Array.from({ length: 6 - lastDayOfWeek }, (_, i) => {
        const date = addDays(end, i + 1)
        return date
    })
    
    const allDays = [...paddingStart, ...days, ...paddingEnd]
    
    return allDays.map(date => {
        const isCurrentMonth = isSameMonth(date, currentMonth.value)
        const isTodayDate = isToday(date)
        const isSelected = isSelectedDate(date)
        const isInRange = isInSelectedRange(date)
        const isStartDate = isStartDateOfRange(date)
        const isEndDate = isEndDateOfRange(date)
        const isEnabled = isDateEnabled(date)
        
        return {
            date,
            dayNumber: date.getDate(),
            isCurrentMonth,
            isToday: isTodayDate,
            isSelected,
            isInRange,
            isStartDate,
            isEndDate,
            isEnabled
        }
    })
})

// Methods
const isSelectedDate = (date: Date): boolean => {
    if (props.range) {
        return selectedRange.value.start && selectedRange.value.end &&
               isSameDay(date, selectedRange.value.start) || isSameDay(date, selectedRange.value.end)
    }
    
    if (props.modelValue instanceof Date) {
        return isSameDay(date, props.modelValue)
    }
    
    if (typeof props.modelValue === 'string') {
        return isSameDay(date, new Date(props.modelValue))
    }
    
    return false
}

const isInSelectedRange = (date: Date): boolean => {
    if (!props.range || !selectedRange.value.start || !selectedRange.value.end) return false
    
    return isAfter(date, selectedRange.value.start) && isBefore(date, selectedRange.value.end)
}

const isStartDateOfRange = (date: Date): boolean => {
    if (!props.range || !selectedRange.value.start) return false
    return isSameDay(date, selectedRange.value.start)
}

const isEndDateOfRange = (date: Date): boolean => {
    if (!props.range || !selectedRange.value.end) return false
    return isSameDay(date, selectedRange.value.end)
}

const isDateEnabled = (date: Date): boolean => {
    const minDate = props.minDate ? new Date(props.minDate) : null
    const maxDate = props.maxDate ? new Date(props.maxDate) : null
    
    if (minDate && isBefore(date, startOfDay(minDate))) return false
    if (maxDate && isAfter(date, startOfDay(maxDate))) return false
    
    return true
}

const toggleCalendar = () => {
    if (props.disabled) return
    isOpen.value = !isOpen.value
}

const closeCalendar = () => {
    isOpen.value = false
}

const selectDate = (date: Date) => {
    if (!isDateEnabled(date)) return
    
    if (props.range) {
        if (!selectedRange.value.start || (selectedRange.value.start && selectedRange.value.end)) {
            // Start new range
            selectedRange.value = { start: date, end: null }
        } else {
            // Complete range
            if (isBefore(date, selectedRange.value.start)) {
                selectedRange.value = { start: date, end: selectedRange.value.start }
            } else {
                selectedRange.value = { start: selectedRange.value.start, end: date }
            }
        }
    } else {
        emit('update:modelValue', date)
        closeCalendar()
    }
}

const applySelection = () => {
    if (props.range && selectedRange.value.start && selectedRange.value.end) {
        emit('update:modelValue', [selectedRange.value.start, selectedRange.value.end])
    }
    closeCalendar()
}

const clearDate = () => {
    if (props.range) {
        selectedRange.value = { start: null, end: null }
        emit('update:modelValue', [])
    } else {
        emit('update:modelValue', null)
    }
}

const previousMonth = () => {
    currentMonth.value = subMonths(currentMonth.value, 1)
}

const nextMonth = () => {
    currentMonth.value = addMonths(currentMonth.value, 1)
}

const goToToday = () => {
    currentMonth.value = new Date()
    if (!props.range) {
        emit('update:modelValue', new Date())
        closeCalendar()
    }
}

const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault()
        toggleCalendar()
    } else if (event.key === 'Escape') {
        closeCalendar()
    }
}

const handleBlur = () => {
    // Delay closing to allow button clicks
    setTimeout(() => {
        isOpen.value = false
    }, 150)
}

const formatRangeDisplay = (start: Date, end: Date): string => {
    const days = differenceInDays(end, start) + 1
    return `${days} day${days !== 1 ? 's' : ''} selected`
}

// Watchers
watch(() => props.modelValue, (newValue) => {
    if (props.range && Array.isArray(newValue) && newValue.length === 2) {
        selectedRange.value = { start: new Date(newValue[0]), end: new Date(newValue[1]) }
        if (selectedRange.value.start) {
            currentMonth.value = selectedRange.value.start
        }
    } else if (!props.range && newValue) {
        currentMonth.value = new Date(newValue)
    }
}, { immediate: true })

// Click outside to close
const handleClickOutside = (event: Event) => {
    if (inputRef.value && !inputRef.value.contains(event.target as Node)) {
        isOpen.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

// Helper functions
const subDays = (date: Date, days: number): Date => {
    const result = new Date(date)
    result.setDate(result.getDate() - days)
    return result
}

const addDays = (date: Date, days: number): Date => {
    const result = new Date(date)
    result.setDate(result.getDate() + days)
    return result
}
</script>
