<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-4">Modal Backdrop Test</h1>
        
        <div class="space-y-4">
            <Button @click="showModal = true">Open Modal with Backdrop</Button>
            
            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="text-sm text-gray-600">This page should be completely covered by a dark backdrop when the modal is open.</p>
                <p class="text-sm text-gray-600">The backdrop should be visible and clickable to close the modal.</p>
            </div>
        </div>

        <!-- Test Modal -->
        <Modal 
            :show="showModal" 
            title="Backdrop Test"
            description="Testing modal backdrop visibility"
            @close="showModal = false"
        >
            <div class="text-center py-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Modal Content</h3>
                <p class="text-gray-600 mb-6">
                    This modal should have a visible dark backdrop that covers the entire page.
                </p>
                <p class="text-gray-600 mb-6">
                    You should see a dark gray overlay behind this modal.
                </p>
                <Button @click="showModal = false">Close Modal</Button>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import Modal from './Modal.vue'
import Button from './ui/button/Button.vue'

const showModal = ref(false)
</script>
