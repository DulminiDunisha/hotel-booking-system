<template>
    <Teleport to="body">
        <Transition name="modal" appear>
            <div
                v-show="show"
                class="fixed inset-0 z-[9999]"
                role="dialog"
                :aria-modal="show"
                :aria-labelledby="title ? 'modal-title' : undefined"
                :aria-describedby="description ? 'modal-description' : undefined"
                @click="handleBackdropClick"
            >
                <!-- Background overlay -->
                <Transition name="modal-fade" appear>
                    <div
                        v-if="show"
                        class="absolute inset-0 bg-black/70 backdrop-blur-sm"
                        aria-hidden="true"
                    ></div>
                </Transition>

                <!-- Modal content container -->
                <div class="relative z-10 flex items-center justify-center min-h-screen p-2 sm:p-4 text-center">
                    <!-- Modal panel -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <Transition name="modal-pop" appear>
                        <div
                            v-if="show"
                            :class="[
                                'relative z-20 inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all w-full sm:my-8 sm:align-middle',
                                // Responsive sizing
                                size === 'xs' ? 'sm:max-w-xs' : '',
                                size === 'sm' ? 'max-w-sm sm:max-w-md' : '',
                                size === 'md' ? 'max-w-md sm:max-w-lg' : '',
                                size === 'lg' ? 'max-w-lg sm:max-w-2xl' : '',
                                size === 'xl' ? 'max-w-2xl sm:max-w-4xl' : '',
                                size === '2xl' ? 'max-w-4xl sm:max-w-6xl' : '',
                                size === 'full' ? 'sm:max-w-full sm:m-4' : '',
                                size === 'auto' ? 'sm:max-w-fit' : '',
                                // Mobile optimizations
                                'max-h-[95vh] sm:max-h-[90vh]',
                                customClass
                            ]"
                            @click.stop
                        >
                            <!-- Header -->
                            <div v-if="title || showCloseButton" class="border-b border-gray-200 px-3 sm:px-4 lg:px-6 py-3 sm:py-4 lg:py-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <h3
                                            v-if="title"
                                            id="modal-title"
                                            class="text-base sm:text-lg lg:text-xl font-semibold text-gray-900 truncate"
                                        >
                                            {{ title }}
                                        </h3>
                                        <p v-if="subtitle" class="mt-1 text-xs sm:text-sm text-gray-500 truncate">
                                            {{ subtitle }}
                                        </p>
                                    </div>
                                    <button
                                        v-if="showCloseButton"
                                        @click="handleClose"
                                        type="button"
                                        class="flex-shrink-0 rounded-lg p-1.5 sm:p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors touch-manipulation"
                                        aria-label="Close modal"
                                    >
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Content -->
                            <div
                                class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 lg:py-6 overflow-y-auto"
                                :class="contentClass"
                                :style="{ maxHeight: maxHeight }"
                            >
                                <div v-if="description" id="modal-description" class="sr-only">{{ description }}</div>
                                <slot></slot>
                            </div>

                            <!-- Footer -->
                            <div v-if="$slots.footer" class="border-t border-gray-200 px-3 sm:px-4 lg:px-6 py-3 sm:py-4 lg:py-6 bg-gray-50">
                                <div class="flex flex-col sm:flex-row sm:justify-end gap-2 sm:gap-3">
                                    <slot name="footer"></slot>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, watch, nextTick } from 'vue'

interface Props {
    show: boolean
    title?: string
    subtitle?: string
    description?: string
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl' | 'full' | 'auto'
    showCloseButton?: boolean
    closeOnBackdrop?: boolean
    closeOnEscape?: boolean
    preventScroll?: boolean
    customClass?: string
    contentClass?: string
    maxHeight?: string
}

const props = withDefaults(defineProps<Props>(), {
    show: false,
    title: '',
    subtitle: '',
    description: '',
    size: 'md',
    showCloseButton: true,
    closeOnBackdrop: true,
    closeOnEscape: true,
    preventScroll: true,
    customClass: '',
    contentClass: '',
    maxHeight: '60vh'
})

const emit = defineEmits<{
    close: []
    'update:show': [value: boolean]
}>()

// Close modal on ESC key
const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && props.show && props.closeOnEscape) {
        handleClose()
    }
}

// Close modal when clicking backdrop
const handleBackdropClick = (e: Event) => {
    if (props.show && props.closeOnBackdrop) {
        const target = e.target as HTMLElement
        // Check if the click is on the backdrop
        if (target.classList.contains('bg-black/70') ||
            target.classList.contains('backdrop-blur-sm') ||
            target === e.currentTarget) {
            handleClose()
        }
    }
}

// Handle close
const handleClose = () => {
    emit('close')
    emit('update:show', false)
}

// Focus management
const focusTrap = (e: KeyboardEvent) => {
    if (!props.show) return

    const modal = e.currentTarget as HTMLElement
    const focusableElements = modal.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    )

    if (focusableElements.length === 0) return

    const firstElement = focusableElements[0] as HTMLElement
    const lastElement = focusableElements[focusableElements.length - 1] as HTMLElement

    if (e.key === 'Tab') {
        if (e.shiftKey) {
            if (document.activeElement === firstElement) {
                e.preventDefault()
                lastElement.focus()
            }
        } else {
            if (document.activeElement === lastElement) {
                e.preventDefault()
                firstElement.focus()
            }
        }
    }
}

// Add event listeners
onMounted(() => {
    document.addEventListener('keydown', handleKeydown)
})

// Clean up event listeners
onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown)
})

// Prevent scrolling when modal is open
watch(() => props.show, async (val) => {
    if (props.preventScroll) {
        await nextTick()
        if (val) {
            document.body.style.overflow = 'hidden'
            document.body.style.paddingRight = '0px'
            // Prevent iOS Safari bounce
            document.body.style.position = 'fixed'
            document.body.style.width = '100%'
        } else {
            document.body.style.overflow = 'auto'
            document.body.style.paddingRight = '0px'
            document.body.style.position = ''
            document.body.style.width = ''
        }
    }
})

// Focus management when modal opens
watch(() => props.show, async (val) => {
    if (val) {
        await nextTick()
        const modal = document.querySelector('[role="dialog"]') as HTMLElement
        if (modal) {
            modal.addEventListener('keydown', focusTrap)
            // Focus first focusable element
            const firstFocusable = modal.querySelector(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            ) as HTMLElement
            if (firstFocusable) {
                firstFocusable.focus()
            }
        }
    } else {
        const modal = document.querySelector('[role="dialog"]') as HTMLElement
        if (modal) {
            modal.removeEventListener('keydown', focusTrap)
        }
    }
})

// Expose methods
defineExpose({
    close: handleClose
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-pop-enter-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-pop-leave-active {
    transition: all 0.2s cubic-bezier(0.4, 0, 1, 1);
}

.modal-pop-enter-from {
    transform: scale(0.95) translateY(10px);
    opacity: 0;
}

.modal-pop-leave-to {
    transform: scale(0.95) translateY(10px);
    opacity: 0;
}

/* Focus styles */
:deep(.focus-visible:focus) {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Mobile-specific optimizations */
@media (max-width: 640px) {
    /* Ensure modal doesn't exceed screen height on mobile */
    .max-h-\[95vh\] {
        max-height: 95vh;
    }

    /* Improve touch targets */
    :deep(button) {
        min-height: 44px;
        min-width: 44px;
    }

    /* Better scrolling on mobile */
    :deep(.overflow-y-auto) {
        -webkit-overflow-scrolling: touch;
    }
}

/* Responsive adjustments for different screen sizes */
@media (max-width: 480px) {
    /* Extra small screens */
    .sm\:max-w-xs,
    .sm\:max-w-md,
    .sm\:max-w-lg,
    .sm\:max-w-2xl,
    .sm\:max-w-4xl,
    .sm\:max-w-6xl {
        max-width: calc(100vw - 1rem);
        margin: 0.5rem;
    }
}

@media (min-width: 481px) and (max-width: 640px) {
    /* Small screens */
    .sm\:max-w-xs { max-width: calc(100vw - 2rem); margin: 1rem; }
    .sm\:max-w-md { max-width: calc(100vw - 2rem); margin: 1rem; }
    .sm\:max-w-lg { max-width: calc(100vw - 2rem); margin: 1rem; }
    .sm\:max-w-2xl { max-width: calc(100vw - 2rem); margin: 1rem; }
    .sm\:max-w-4xl { max-width: calc(100vw - 2rem); margin: 1rem; }
    .sm\:max-w-6xl { max-width: calc(100vw - 2rem); margin: 1rem; }
}

@media (min-width: 641px) and (max-width: 768px) {
    /* Medium screens */
    .sm\:max-w-xs { max-width: 20rem; }
    .sm\:max-w-md { max-width: 28rem; }
    .sm\:max-w-lg { max-width: 32rem; }
    .sm\:max-w-2xl { max-width: 42rem; }
    .sm\:max-w-4xl { max-width: 56rem; }
    .sm\:max-w-6xl { max-width: 72rem; }
}

/* Custom scrollbar for modal content */
:deep(.overflow-y-auto) {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

:deep(.overflow-y-auto::-webkit-scrollbar) {
    width: 6px;
}

:deep(.overflow-y-auto::-webkit-scrollbar-track) {
    background: #f1f5f9;
    border-radius: 3px;
}

:deep(.overflow-y-auto::-webkit-scrollbar-thumb) {
    background: #cbd5e1;
    border-radius: 3px;
}

:deep(.overflow-y-auto::-webkit-scrollbar-thumb:hover) {
    background: #94a3b8;
}

/* Touch-friendly improvements */
:deep(button) {
    touch-action: manipulation;
}

:deep(input), :deep(select), :deep(textarea) {
    font-size: 16px; /* Prevents zoom on iOS */
}

/* Safe area support for notched devices */
@supports (padding: max(0px)) {
    .p-2 {
        padding-left: max(0.5rem, env(safe-area-inset-left));
        padding-right: max(0.5rem, env(safe-area-inset-right));
    }

    .sm\:p-4 {
        padding-left: max(1rem, env(safe-area-inset-left));
        padding-right: max(1rem, env(safe-area-inset-right));
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .bg-black\/70 {
        background-color: rgba(0, 0, 0, 0.9);
    }

    .border-gray-200 {
        border-color: #374151;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .modal-enter-active,
    .modal-leave-active,
    .modal-fade-enter-active,
    .modal-fade-leave-active,
    .modal-pop-enter-active,
    .modal-pop-leave-active {
        transition: none;
    }
}
</style>
