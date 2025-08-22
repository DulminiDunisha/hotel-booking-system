<template>
  <div class="min-h-screen bg-gradient-to-br from-red-50 to-orange-100 py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
          <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Emergency Assistance</h1>
        <p class="text-gray-600">
          We're here to help in emergency situations. Please fill out this form and our team will respond immediately.
        </p>
      </div>

      <!-- Emergency Form -->
      <div class="bg-white rounded-2xl shadow-xl p-8">
        <form @submit.prevent="submitEmergency" class="space-y-6">
          <!-- Emergency Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Emergency Type <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.emergency_type"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
            >
              <option value="">Select emergency type</option>
              <option value="illness">Illness or Medical Emergency</option>
              <option value="cancellation">Emergency Cancellation</option>
              <option value="early_checkout">Early Checkout Required</option>
            </select>
          </div>

          <!-- Booking Code -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Booking Code <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.booking_code"
              type="text"
              required
              placeholder="Enter your booking code"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
            />
          </div>

          <!-- Guest Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Your Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.guest_name"
              type="text"
              required
              placeholder="Enter your full name"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
            />
          </div>

          <!-- Guest Phone -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Phone Number <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.guest_phone"
              type="tel"
              required
              placeholder="Enter your phone number"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
            />
          </div>

          <!-- Guest Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email Address <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.guest_email"
              type="email"
              required
              placeholder="Enter your email address"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
            />
          </div>

          <!-- Emergency Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Emergency Description <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="form.description"
              required
              rows="4"
              placeholder="Please describe the emergency situation in detail..."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
            ></textarea>
          </div>

          <!-- Additional Information -->
          <div v-if="form.emergency_type === 'illness'" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-medium text-yellow-800 mb-2">Medical Emergency Information</h3>
            <p class="text-sm text-yellow-700 mb-3">
              Please provide additional details about the medical situation:
            </p>
            <textarea
              v-model="form.additional_info"
              rows="3"
              placeholder="Describe symptoms, any medications, allergies, etc."
              class="w-full px-4 py-3 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
            ></textarea>
          </div>

          <!-- Submit Button -->
          <div class="pt-4">
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-red-600 text-white py-4 px-6 rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 font-medium text-lg"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting...
              </span>
              <span v-else>Submit Emergency Report</span>
            </button>
          </div>
        </form>

        <!-- Emergency Contact Information -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <h3 class="font-semibold text-gray-900 mb-3">Immediate Assistance</h3>
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center mb-2">
              <svg class="h-5 w-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              <span class="font-medium text-red-800">For immediate assistance, call:</span>
            </div>
            <div class="text-red-700 space-y-1">
              <p class="text-lg font-semibold">📞 +94 11 234 5678</p>
              <p class="text-sm">Available 24/7 for emergency situations</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Information Box -->
      <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="font-semibold text-blue-900 mb-2">What happens after submission?</h3>
        <ul class="text-sm text-blue-800 space-y-2">
          <li class="flex items-start">
            <svg class="h-4 w-4 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            Our emergency response team will be notified immediately
          </li>
          <li class="flex items-start">
            <svg class="h-4 w-4 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            You will receive a confirmation email and SMS
          </li>
          <li class="flex items-start">
            <svg class="h-4 w-4 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            Our team will contact you within 15 minutes
          </li>
          <li class="flex items-start">
            <svg class="h-4 w-4 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            If medical assistance is needed, we can arrange for a doctor
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const loading = ref(false)

const form = reactive({
  emergency_type: '',
  booking_code: '',
  guest_name: '',
  guest_phone: '',
  guest_email: '',
  description: '',
  additional_info: ''
})

const submitEmergency = async () => {
  loading.value = true

  try {
    await router.post(route('emergency.submit'), form)
    
    // Show success message
    Swal.fire({
      icon: 'success',
      title: 'Emergency Report Submitted',
      text: 'Our team has been notified and will contact you shortly. For immediate assistance, please call +94 11 234 5678.',
      confirmButtonText: 'OK',
      confirmButtonColor: '#dc2626'
    })

    // Reset form
    Object.assign(form, {
      emergency_type: '',
      booking_code: '',
      guest_name: '',
      guest_phone: '',
      guest_email: '',
      description: '',
      additional_info: ''
    })

  } catch (error) {
    console.error('Error submitting emergency:', error)
    
    Swal.fire({
      icon: 'error',
      title: 'Submission Failed',
      text: 'There was an error submitting your emergency report. Please try again or call us directly.',
      confirmButtonText: 'OK',
      confirmButtonColor: '#dc2626'
    })
  } finally {
    loading.value = false
  }
}
</script>
