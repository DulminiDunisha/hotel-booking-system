<template>
    <div class="p-8 space-y-8">
        <h1 class="text-3xl font-bold text-gray-900">Modal Component Examples</h1>
        
        <!-- Basic Modal -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Basic Modal</h2>
            <Button @click="showBasicModal = true">Open Basic Modal</Button>
            
            <Modal :show="showBasicModal" @close="showBasicModal = false">
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Modal</h3>
                    <p class="text-gray-600 mb-6">This is a simple modal with basic content.</p>
                    <Button @click="showBasicModal = false">Close</Button>
                </div>
            </Modal>
        </div>

        <!-- Modal with Title and Footer -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Modal with Title and Footer</h2>
            <Button @click="showTitledModal = true">Open Titled Modal</Button>
            
            <Modal 
                :show="showTitledModal" 
                title="Confirmation Required"
                description="Please confirm your action before proceeding"
                @close="showTitledModal = false"
            >
                <div class="text-center">
                    <p class="text-gray-600 mb-6">Are you sure you want to proceed with this action?</p>
                </div>
                
                <template #footer>
                    <Button variant="secondary" @click="showTitledModal = false">Cancel</Button>
                    <Button @click="handleConfirm">Confirm</Button>
                </template>
            </Modal>
        </div>

        <!-- Different Sizes -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Modal Sizes</h2>
            <div class="flex flex-wrap gap-2">
                <Button @click="openSizeModal('sm')">Small</Button>
                <Button @click="openSizeModal('md')">Medium</Button>
                <Button @click="openSizeModal('lg')">Large</Button>
                <Button @click="openSizeModal('xl')">Extra Large</Button>
                <Button @click="openSizeModal('full')">Full Width</Button>
                <Button @click="openSizeModal('auto')">Auto Width</Button>
            </div>
            
            <Modal 
                :show="showSizeModal" 
                :size="currentSize"
                :title="`${currentSize.toUpperCase()} Modal`"
                @close="showSizeModal = false"
            >
                <div class="text-center">
                    <p class="text-gray-600 mb-4">This modal demonstrates the {{ currentSize }} size option.</p>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Modal size: {{ currentSize }}</p>
                        <p class="text-sm text-gray-500">Max width varies based on size prop</p>
                    </div>
                </div>
                
                <template #footer>
                    <Button @click="showSizeModal = false">Close</Button>
                </template>
            </Modal>
        </div>

        <!-- Modal without Close Button -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Modal without Close Button</h2>
            <Button @click="showNoCloseModal = true">Open Modal (No Close Button)</Button>
            
            <Modal 
                :show="showNoCloseModal" 
                title="Important Notice"
                :show-close-button="false"
                @close="showNoCloseModal = false"
            >
                <div class="text-center">
                    <p class="text-gray-600 mb-6">This modal doesn't have a close button in the header.</p>
                    <p class="text-gray-600 mb-6">You must use the button below to close it.</p>
                    <Button @click="showNoCloseModal = false">I Understand</Button>
                </div>
            </Modal>
        </div>

        <!-- Modal with Custom Close Behavior -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Modal with Custom Close Behavior</h2>
            <Button @click="showCustomCloseModal = true">Open Custom Close Modal</Button>
            
            <Modal 
                :show="showCustomCloseModal" 
                title="Custom Close Behavior"
                :close-on-backdrop="false"
                :close-on-escape="false"
                @close="showCustomCloseModal = false"
            >
                <div class="text-center">
                    <p class="text-gray-600 mb-6">This modal cannot be closed by clicking outside or pressing ESC.</p>
                    <p class="text-gray-600 mb-6">You must use the specific close button below.</p>
                    <Button @click="showCustomCloseModal = false">Close Modal</Button>
                </div>
            </Modal>
        </div>

        <!-- Form Modal -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Form Modal</h2>
            <Button @click="showFormModal = true">Open Form Modal</Button>
            
            <Modal 
                :show="showFormModal" 
                title="User Information"
                size="lg"
                @close="showFormModal = false"
            >
                <form @submit.prevent="handleFormSubmit" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input 
                            id="name"
                            v-model="formData.name"
                            type="text" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input 
                            id="email"
                            v-model="formData.email"
                            type="email" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea 
                            id="message"
                            v-model="formData.message"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        ></textarea>
                    </div>
                </form>
                
                <template #footer>
                    <Button variant="secondary" @click="showFormModal = false">Cancel</Button>
                    <Button @click="handleFormSubmit">Submit</Button>
                </template>
            </Modal>
        </div>

        <!-- v-model Usage -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">v-model Usage</h2>
            <Button @click="showVModelModal = true">Open v-model Modal</Button>
            
            <Modal 
                v-model:show="showVModelModal" 
                title="v-model Modal"
                @close="handleVModelClose"
            >
                <div class="text-center">
                    <p class="text-gray-600 mb-6">This modal uses v-model for two-way binding.</p>
                    <p class="text-gray-600 mb-6">Current state: {{ showVModelModal ? 'Open' : 'Closed' }}</p>
                    <Button @click="showVModelModal = false">Close</Button>
                </div>
            </Modal>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import Modal from './Modal.vue'
import Button from './ui/button/Button.vue'

// Modal states
const showBasicModal = ref(false)
const showTitledModal = ref(false)
const showSizeModal = ref(false)
const showNoCloseModal = ref(false)
const showCustomCloseModal = ref(false)
const showFormModal = ref(false)
const showVModelModal = ref(false)

// Size modal
const currentSize = ref('md')

const openSizeModal = (size) => {
    currentSize.value = size
    showSizeModal.value = true
}

// Form data
const formData = ref({
    name: '',
    email: '',
    message: ''
})

// Event handlers
const handleConfirm = () => {
    alert('Action confirmed!')
    showTitledModal.value = false
}

const handleFormSubmit = () => {
    alert(`Form submitted: ${JSON.stringify(formData.value, null, 2)}`)
    showFormModal.value = false
    // Reset form
    formData.value = { name: '', email: '', message: '' }
}

const handleVModelClose = () => {
    console.log('Modal closed via v-model')
}
</script>
