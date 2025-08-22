<template>
  <div class="min-h-screen bg-gradient-to-br from-red-50 to-pink-100 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
      <!-- Error Icon -->
      <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </div>

      <!-- Error Message -->
      <h1 class="text-2xl font-bold text-gray-900 mb-4">Payment Failed</h1>
      <p class="text-gray-600 mb-6">
        {{ errorMessage || 'Your payment could not be processed. Please try again or contact support if the problem persists.' }}
      </p>

      <!-- Common Issues -->
      <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
        <h3 class="font-semibold text-gray-900 mb-3">Common issues:</h3>
        <ul class="text-sm text-gray-600 space-y-2">
          <li class="flex items-center">
            <svg class="h-4 w-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            Insufficient funds in your account
          </li>
          <li class="flex items-center">
            <svg class="h-4 w-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            Card expired or invalid
          </li>
          <li class="flex items-center">
            <svg class="h-4 w-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            Network connection issues
          </li>
        </ul>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <button
          @click="retryPayment"
          class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200 font-medium"
        >
          Try Again
        </button>
        <Link
          :href="route('home')"
          class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition duration-200 font-medium"
        >
          Return to Home
        </Link>
        <Link
          :href="route('user.dashboard')"
          class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition duration-200 font-medium"
        >
          View My Bookings
        </Link>
      </div>

      <!-- Contact Information -->
      <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-sm text-gray-500 mb-2">Need help? Contact us:</p>
        <div class="text-sm text-gray-600">
          <p>📞 +94 11 234 5678</p>
          <p>📧 support@dumiduhotel.com</p>
        </div>
        <p class="text-xs text-gray-500 mt-2">
          Our support team is available 24/7 to assist you.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const errorMessage = computed(() => page.props.flash?.error || '')

const retryPayment = () => {
  // Get the last booking data from localStorage
  const bookingData = localStorage.getItem('booking_data')
  if (bookingData) {
    // Redirect back to payment form
    window.history.back()
  } else {
    // If no booking data, go to home
    window.location.href = route('home')
  }
}
</script>
