# Modal Component

A highly accessible, feature-rich modal component built with Vue 3 and Tailwind CSS.

## Features

- ✨ **Accessibility First**: ARIA attributes, focus management, and keyboard navigation
- 🎨 **Flexible Sizing**: Multiple size options from small to full-width
- 🔒 **Customizable Behavior**: Control how the modal can be closed
- 📱 **Responsive Design**: Optimized for all screen sizes
- 🎭 **Smooth Animations**: Beautiful enter/exit transitions
- 🎯 **Focus Trapping**: Prevents focus from leaving the modal
- 📜 **Scroll Prevention**: Prevents background scrolling when open
- 🚀 **Teleport Support**: Renders to body for proper z-index handling

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `show` | `Boolean` | `false` | Controls modal visibility |
| `title` | `String` | `''` | Modal title (optional) |
| `description` | `String` | `''` | Modal description for screen readers |
| `size` | `String` | `'md'` | Modal size: `sm`, `md`, `lg`, `xl`, `full`, `auto` |
| `showCloseButton` | `Boolean` | `true` | Show/hide close button in header |
| `closeOnBackdrop` | `Boolean` | `true` | Allow closing by clicking backdrop |
| `closeOnEscape` | `Boolean` | `true` | Allow closing with Escape key |
| `preventScroll` | `Boolean` | `true` | Prevent background scrolling |

## Events

| Event | Payload | Description |
|-------|---------|-------------|
| `close` | - | Emitted when modal should close |
| `update:show` | `Boolean` | Emitted for v-model support |

## Slots

| Slot | Description |
|------|-------------|
| `default` | Main modal content |
| `footer` | Footer content (buttons, actions) |

## Size Options

- **`sm`**: Small modal (max-width: 28rem)
- **`md`**: Medium modal (max-width: 32rem) - Default
- **`lg`**: Large modal (max-width: 42rem)
- **`xl`**: Extra large modal (max-width: 56rem)
- **`full`**: Full width modal with margins
- **`auto`**: Auto-width based on content

## Basic Usage

```vue
<template>
    <Button @click="showModal = true">Open Modal</Button>
    
    <Modal :show="showModal" @close="showModal = false">
        <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Hello World</h3>
            <p class="text-gray-600">This is a basic modal.</p>
        </div>
    </Modal>
</template>

<script setup>
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'

const showModal = ref(false)
</script>
```

## Modal with Title and Footer

```vue
<Modal 
    :show="showModal" 
    title="Confirmation Required"
    description="Please confirm your action"
    @close="showModal = false"
>
    <div class="text-center">
        <p class="text-gray-600 mb-6">Are you sure you want to proceed?</p>
    </div>
    
    <template #footer>
        <Button variant="secondary" @click="showModal = false">Cancel</Button>
        <Button @click="handleConfirm">Confirm</Button>
    </template>
</Modal>
```

## Different Sizes

```vue
<!-- Small Modal -->
<Modal :show="showModal" size="sm" title="Small Modal">
    <p>This is a small modal.</p>
</Modal>

<!-- Large Modal -->
<Modal :show="showModal" size="lg" title="Large Modal">
    <p>This is a large modal with more space.</p>
</Modal>

<!-- Full Width Modal -->
<Modal :show="showModal" size="full" title="Full Width Modal">
    <p>This modal takes the full width of the screen.</p>
</Modal>
```

## Custom Close Behavior

```vue
<Modal 
    :show="showModal" 
    title="Important Notice"
    :close-on-backdrop="false"
    :close-on-escape="false"
    :show-close-button="false"
    @close="showModal = false"
>
    <p>This modal cannot be closed by clicking outside or pressing ESC.</p>
    <Button @click="showModal = false">I Understand</Button>
</Modal>
```

## Form Modal

```vue
<Modal 
    :show="showFormModal" 
    title="User Information"
    size="lg"
    @close="showFormModal = false"
>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input 
                id="name"
                v-model="form.name"
                type="text" 
                class="mt-1 block w-full rounded-md border-gray-300"
                required
            />
        </div>
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input 
                id="email"
                v-model="form.email"
                type="email" 
                class="mt-1 block w-full rounded-md border-gray-300"
                required
            />
        </div>
    </form>
    
    <template #footer>
        <Button variant="secondary" @click="showFormModal = false">Cancel</Button>
        <Button @click="handleSubmit">Submit</Button>
    </template>
</Modal>
```

## v-model Usage

```vue
<Modal v-model:show="showModal" title="v-model Modal">
    <p>This modal uses v-model for two-way binding.</p>
</Modal>
```

## Accessibility Features

- **ARIA Attributes**: Proper `role="dialog"`, `aria-modal`, `aria-labelledby`, and `aria-describedby`
- **Focus Management**: Automatically focuses first focusable element when opened
- **Focus Trapping**: Tab navigation stays within the modal
- **Keyboard Support**: Escape key to close (configurable)
- **Screen Reader Support**: Hidden description for better context

## Styling

The modal uses Tailwind CSS classes and includes:

- Responsive design for all screen sizes
- Smooth transitions and animations
- Proper shadows and borders
- Consistent spacing and typography
- Focus indicators for keyboard navigation

## Best Practices

1. **Always provide a title** for better accessibility
2. **Use appropriate sizes** for your content
3. **Include a footer** for action buttons when needed
4. **Handle form submissions** properly in form modals
5. **Provide clear close actions** for users
6. **Test keyboard navigation** and screen reader compatibility

## Migration from Old Version

If you're upgrading from the previous version:

1. **Props**: The `show` prop behavior remains the same
2. **Events**: The `close` event is still emitted
3. **Slots**: The default slot and footer slot work the same way
4. **New Features**: All new props are optional with sensible defaults

## Browser Support

- Modern browsers with ES6+ support
- Vue 3.0+
- Tailwind CSS 3.0+

## Examples

See `ModalExample.vue` for comprehensive usage examples demonstrating all features.
