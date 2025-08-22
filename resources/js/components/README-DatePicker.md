# DatePicker Component

A modern, accessible, and feature-rich date picker component built with Vue 3, TypeScript, and Tailwind CSS.

## Features

### 🎯 Core Functionality
- **Single Date Selection**: Choose individual dates with ease
- **Date Range Selection**: Select start and end dates for ranges
- **Calendar Interface**: Beautiful month-based calendar view
- **Month Navigation**: Navigate between months with arrow buttons

### 🎨 User Experience
- **Visual Feedback**: Hover states, focus rings, and smooth transitions
- **Today Highlighting**: Current date is clearly marked
- **Range Visualization**: Selected ranges are visually indicated
- **Responsive Design**: Works seamlessly on mobile and desktop

### ♿ Accessibility
- **Keyboard Navigation**: Full keyboard support (Enter, Space, Escape)
- **ARIA Labels**: Proper accessibility attributes
- **Focus Management**: Clear focus indicators and management
- **Screen Reader Support**: Semantic HTML structure

### ⚙️ Advanced Features
- **Date Constraints**: Min/max date limitations
- **Custom Formatting**: Flexible date display formats using date-fns
- **Disabled State**: Support for disabled inputs
- **Click Outside**: Closes calendar when clicking elsewhere
- **Clear Functionality**: Easy date clearing with clear button

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `modelValue` | `Date \| string \| [Date, Date] \| null` | `null` | The selected date(s) |
| `minDate` | `Date \| string` | `undefined` | Minimum selectable date |
| `maxDate` | `Date \| string` | `undefined` | Maximum selectable date |
| `range` | `boolean` | `false` | Enable date range selection |
| `disabled` | `boolean` | `false` | Disable the date picker |
| `placeholder` | `string` | `'Select date'` | Input placeholder text |
| `format` | `string` | `'MMM dd, yyyy'` | Date display format (date-fns format) |

## Events

| Event | Payload | Description |
|-------|---------|-------------|
| `update:modelValue` | `Date \| string \| [Date, Date] \| null` | Emitted when date selection changes |

## Usage Examples

### Basic Single Date Selection
```vue
<template>
  <DatePicker v-model="selectedDate" placeholder="Choose a date" />
</template>

<script setup>
import { ref } from 'vue'
import DatePicker from '@/components/DatePicker.vue'

const selectedDate = ref(null)
</script>
```

### Date Range Selection
```vue
<template>
  <DatePicker 
    v-model="dateRange" 
    range 
    placeholder="Select date range" 
  />
</template>

<script setup>
import { ref } from 'vue'
import DatePicker from '@/components/DatePicker.vue'

const dateRange = ref([])
</script>
```

### With Date Constraints
```vue
<template>
  <DatePicker 
    v-model="checkInDate" 
    :min-date="new Date()" 
    :max-date="maxDate"
    placeholder="Select check-in date" 
  />
</template>

<script setup>
import { ref, computed } from 'vue'
import { addDays } from 'date-fns'
import DatePicker from '@/components/DatePicker.vue'

const checkInDate = ref(null)
const maxDate = computed(() => addDays(new Date(), 30))
</script>
```

### Custom Date Format
```vue
<template>
  <DatePicker 
    v-model="eventDate" 
    format="EEEE, MMMM do, yyyy"
    placeholder="Select event date" 
  />
</template>

<script setup>
import { ref } from 'vue'
import DatePicker from '@/components/DatePicker.vue'

const eventDate = ref(null)
</script>
```

### Disabled State
```vue
<template>
  <DatePicker 
    v-model="disabledDate" 
    disabled 
    placeholder="Date picker is disabled" 
  />
</template>

<script setup>
import { ref } from 'vue'
import DatePicker from '@/components/DatePicker.vue'

const disabledDate = ref(null)
</script>
```

## Date Format Examples

The component uses [date-fns](https://date-fns.org/) for date formatting. Here are some common format patterns:

| Format | Example Output |
|--------|----------------|
| `'MMM dd, yyyy'` | Jan 15, 2024 |
| `'EEEE, MMMM do, yyyy'` | Monday, January 15th, 2024 |
| `'yyyy-MM-dd'` | 2024-01-15 |
| `'MM/dd/yyyy'` | 01/15/2024 |
| `'do MMM yyyy'` | 15th Jan 2024 |
| `'MMMM yyyy'` | January 2024 |

## Keyboard Navigation

- **Enter/Space**: Open/close calendar
- **Escape**: Close calendar
- **Tab**: Navigate between interactive elements
- **Arrow Keys**: Navigate calendar days (when calendar is open)

## Styling

The component uses Tailwind CSS classes and follows a consistent design system:

- **Primary Colors**: Blue theme (`blue-500`, `blue-600`, `blue-700`)
- **Gray Scale**: Neutral colors for borders and text (`gray-300`, `gray-400`, `gray-600`)
- **Hover States**: Subtle hover effects with `hover:` prefixes
- **Transitions**: Smooth animations with `transition-colors duration-200`
- **Focus States**: Blue focus rings for accessibility

## Browser Support

- **Modern Browsers**: Chrome, Firefox, Safari, Edge (latest versions)
- **Mobile**: iOS Safari, Chrome Mobile, Samsung Internet
- **JavaScript**: ES6+ features required

## Dependencies

- **Vue 3**: Component framework
- **TypeScript**: Type safety
- **date-fns**: Date manipulation and formatting
- **Tailwind CSS**: Utility-first CSS framework

## Demo

Visit `/datepicker-demo` to see all features in action with interactive examples.

## Migration from Old Component

The new DatePicker is a complete rewrite with improved functionality. Key changes:

1. **Props**: Added `maxDate`, `disabled`, `placeholder`, and `format` props
2. **Events**: Same `update:modelValue` event, but with better type safety
3. **Styling**: Enhanced visual design with better accessibility
4. **Functionality**: Calendar interface instead of native date input

## Contributing

When modifying this component:

1. Maintain accessibility standards
2. Test keyboard navigation
3. Ensure responsive design works
4. Update this documentation
5. Test with various date formats and constraints
