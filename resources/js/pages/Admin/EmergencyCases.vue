<template>
  <AdminLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Emergency Cases Management</h1>
              <p class="text-gray-600">Monitor and resolve emergency situations</p>
            </div>
            <div class="flex space-x-3">
              <button
                @click="refreshData"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200"
              >
                <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Total Emergencies -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Emergencies</p>
                <p class="text-2xl font-semibold text-gray-900">{{ statistics.total_emergencies }}</p>
              </div>
            </div>
          </div>

          <!-- Open Cases -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Open Cases</p>
                <p class="text-2xl font-semibold text-yellow-600">{{ statistics.open_emergencies }}</p>
              </div>
            </div>
          </div>

          <!-- Resolved Cases -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Resolved Cases</p>
                <p class="text-2xl font-semibold text-green-600">{{ statistics.resolved_emergencies }}</p>
              </div>
            </div>
          </div>

          <!-- Total Refunds -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Refunds</p>
                <p class="text-2xl font-semibold text-blue-600">LKR {{ statistics.total_refunds?.toFixed(2) || '0.00' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Emergency Cases Table -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Emergency Cases</h3>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Case ID
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Guest
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Refund Amount
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Created
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="emergencyCase in emergencyCases.data" :key="emergencyCase.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    #{{ emergencyCase.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        {{ emergencyCase.booking?.guest?.name }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ emergencyCase.booking?.booking_code }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getTypeBadgeClass(emergencyCase.type)">
                      {{ getTypeLabel(emergencyCase.type) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(emergencyCase.status)">
                      {{ getStatusLabel(emergencyCase.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span v-if="emergencyCase.refund_amount">
                      LKR {{ emergencyCase.refund_amount.toFixed(2) }}
                    </span>
                    <span v-else class="text-gray-400">-</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(emergencyCase.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button
                        @click="viewCase(emergencyCase)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        View
                      </button>
                      <button
                        v-if="emergencyCase.status === 'open'"
                        @click="resolveCase(emergencyCase)"
                        class="text-green-600 hover:text-green-900"
                      >
                        Resolve
                      </button>
                      <button
                        v-if="emergencyCase.refund_amount && emergencyCase.refund_status === 'pending'"
                        @click="processRefund(emergencyCase)"
                        class="text-purple-600 hover:text-purple-900"
                      >
                        Process Refund
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="emergencyCases.links && emergencyCases.links.length > 3" class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ emergencyCases.from }} to {{ emergencyCases.to }} of {{ emergencyCases.total }} results
              </div>
              <div class="flex space-x-2">
                               <Link
                 v-for="link in emergencyCases.links"
                 :key="link.label"
                 :href="link.url || '#'"
                 :class="[
                   'px-3 py-2 text-sm font-medium rounded-md',
                   link.active
                     ? 'bg-blue-600 text-white'
                     : link.url
                     ? 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'
                     : 'text-gray-400 cursor-not-allowed'
                 ]"
               >
                 <span v-html="link.label"></span>
               </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Resolve Case Modal -->
      <Modal
          v-model:show="showResolveModal"
          title="Resolve Emergency Case"
          size="sm"
          @close="showResolveModal = false"
      >
          <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                  Resolution Notes
              </label>
              <textarea
                  v-model="resolveNotes"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter resolution details..."
              ></textarea>
          </div>

          <template #footer>
              <button
                  @click="showResolveModal = false"
                  class="bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400"
              >
                  Cancel
              </button>
              <button
                  @click="confirmResolve"
                  class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700"
              >
                  Resolve Case
              </button>
          </template>
      </Modal>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Modal from '@/components/Modal.vue'

interface EmergencyCase {
  id: number
  type: string
  description: string
  status: string
  refund_amount: number | null
  refund_status: string
  created_at: string
  booking: {
    id: number
    booking_code: string
    guest: {
      name: string
      email: string
      phone: string
    }
    room: {
      name: string
    }
  }
}

interface Statistics {
  total_emergencies: number
  open_emergencies: number
  resolved_emergencies: number
  total_refunds: number
  emergency_types: Array<{ type: string; count: number }>
}

interface Props {
  emergencyCases: {
    data: EmergencyCase[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
    links: Array<{ url: string | null; label: string; active: boolean }>
  }
  statistics: Statistics
}

const props = defineProps<Props>()

const showResolveModal = ref(false)
const resolveNotes = ref('')
const selectedCase = ref<EmergencyCase | null>(null)

const getTypeLabel = (type: string) => {
  const labels = {
    illness: 'Illness',
    cancellation: 'Cancellation',
    early_checkout: 'Early Checkout'
  }
  return labels[type] || type
}

const getStatusLabel = (status: string) => {
  const labels = {
    open: 'Open',
    resolved: 'Resolved',
    closed: 'Closed'
  }
  return labels[status] || status
}

const getTypeBadgeClass = (type: string) => {
  const classes = {
    illness: 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800',
    cancellation: 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800',
    early_checkout: 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800'
  }
  return classes[type] || 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800'
}

const getStatusBadgeClass = (status: string) => {
  const classes = {
    open: 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800',
    resolved: 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800',
    closed: 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800'
  }
  return classes[status] || 'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800'
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const viewCase = (emergencyCase: EmergencyCase) => {
  router.visit(route('admin.emergency.show', emergencyCase.id))
}

const resolveCase = (emergencyCase: EmergencyCase) => {
  selectedCase.value = emergencyCase
  showResolveModal.value = true
}

const confirmResolve = async () => {
  if (!selectedCase.value) return

  try {
    await router.put(route('admin.emergency.resolve', selectedCase.value.id), {
      resolution_notes: resolveNotes.value
    })

    Swal.fire({
      icon: 'success',
      title: 'Case Resolved',
      text: 'Emergency case has been resolved successfully.',
      confirmButtonText: 'OK'
    })

    showResolveModal.value = false
    resolveNotes.value = ''
    selectedCase.value = null

    // Refresh the page to update data
    window.location.reload()

  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to resolve the case. Please try again.',
      confirmButtonText: 'OK'
    })
  }
}

const processRefund = async (emergencyCase: EmergencyCase) => {
  try {
    await router.put(route('admin.emergency.refund', emergencyCase.id), {
      refund_amount: emergencyCase.refund_amount
    })

    Swal.fire({
      icon: 'success',
      title: 'Refund Processed',
      text: 'Refund has been processed successfully.',
      confirmButtonText: 'OK'
    })

    // Refresh the page to update data
    window.location.reload()

  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to process refund. Please try again.',
      confirmButtonText: 'OK'
    })
  }
}

const refreshData = () => {
  window.location.reload()
}
</script>

<style>
    /* Custom gold color for Tailwind */
    .bg-gold-100 { background-color: #fff9e6; }
    .bg-gold-200 { background-color: #ffedb3; }
    .bg-gold-500 { background-color: #ffd700; }
    .bg-gold-600 { background-color: #e6c200; }
    .bg-gold-700 { background-color: #ccad00; }
    .text-gold-600 { color: #e6c200; }
    .text-gold-800 { color: #997a00; }
    .border-gold-500 { border-color: #ffd700; }
    .border-gold-600 { border-color: #e6c200; }
    .hover\:bg-gold-700:hover { background-color: #ccad00; }
    .hover\:text-gold-800:hover { color: #997a00; }
    .focus\:ring-gold-500:focus { --tw-ring-color: #ffd700; }
</style>
