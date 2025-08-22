# Hotel Booking System - Plugin Documentation

This document provides comprehensive guidance on using the plugins implemented in the hotel booking system for data tables, selects, date pickers, and charts.

## Table of Contents

1. [Overview](#overview)
2. [DataTable Plugin](#datatable-plugin)
3. [Select Plugin](#select-plugin)
4. [DatePicker Plugin](#datepicker-plugin)
5. [Chart Plugin](#chart-plugin)
6. [Installation & Setup](#installation--setup)
7. [Examples](#examples)
8. [Best Practices](#best-practices)

## Overview

The hotel booking system includes four main plugins:

- **DataTable**: Advanced table component with sorting, filtering, and pagination
- **Select**: Enhanced dropdown/select component with search and custom styling
- **DatePicker**: Date and date range picker with various formats and options
- **Chart**: Chart.js integration for data visualization

## DataTable Plugin

### Features
- Sorting (single and multi-column)
- Global search
- Pagination
- Custom cell rendering
- Responsive design
- Empty state handling

### Basic Usage

```vue
<template>
  <DataTable
    :data="users"
    :columns="tableColumns"
    :show-search="true"
    :show-pagination="true"
    search-placeholder="Search users..."
    :page-size="10"
  >
    <!-- Custom cell templates -->
    <template #cell-role="{ value }">
      <span class="badge">{{ value }}</span>
    </template>
    
    <template #cell-actions="{ row }">
      <button @click="editUser(row.original)">Edit</button>
    </template>
  </DataTable>
</template>

<script setup>
import { DataTable } from '@/components/ui'
import { type ColumnDef } from '@tanstack/vue-table'

const tableColumns: ColumnDef<User>[] = [
  {
    accessorKey: 'name',
    header: 'Name',
  },
  {
    accessorKey: 'email',
    header: 'Email',
  },
  {
    accessorKey: 'role',
    header: 'Role',
  },
  {
    id: 'actions',
    header: 'Actions',
  },
]
</script>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `data` | `any[]` | `[]` | Array of data to display |
| `columns` | `ColumnDef[]` | `[]` | Column definitions |
| `showSearch` | `boolean` | `true` | Show search input |
| `showFilters` | `boolean` | `false` | Show filter section |
| `showPagination` | `boolean` | `true` | Show pagination |
| `searchPlaceholder` | `string` | `'Search...'` | Search input placeholder |
| `pageSize` | `number` | `10` | Number of items per page |
| `enableSorting` | `boolean` | `true` | Enable column sorting |
| `enableGlobalFilter` | `boolean` | `true` | Enable global search |
| `enablePagination` | `boolean` | `true` | Enable pagination |

### Slots

- `#cell-{columnId}`: Custom cell rendering
- `#header-{columnId}`: Custom header rendering
- `#filters`: Custom filter controls
- `#empty`: Empty state content

## Select Plugin

### Features
- Search functionality
- Custom styling
- Error states
- Helper text
- Keyboard navigation
- Multiple selection support

### Basic Usage

```vue
<template>
  <Select
    v-model="selectedRole"
    :options="roleOptions"
    placeholder="Select a role..."
    label="User Role"
    :error="errors.role"
    helper-text="Choose the user's role in the system"
  />
</template>

<script setup>
import { Select } from '@/components/ui'

const roleOptions = [
  { value: 'admin', label: 'Administrator' },
  { value: 'user', label: 'Regular User' },
  { value: 'guest', label: 'Guest' },
]

const selectedRole = ref('')
</script>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `modelValue` | `any` | `undefined` | Selected value |
| `options` | `Option[]` | `[]` | Array of options |
| `placeholder` | `string` | `'Select an option...'` | Placeholder text |
| `label` | `string` | `undefined` | Label text |
| `disabled` | `boolean` | `false` | Disable the select |
| `error` | `string` | `undefined` | Error message |
| `helperText` | `string` | `undefined` | Helper text |
| `valueKey` | `string` | `'value'` | Key for option value |
| `labelKey` | `string` | `'label'` | Key for option label |

### Option Interface

```typescript
interface Option {
  value: any;
  label: string;
  disabled?: boolean;
}
```

## DatePicker Plugin

### Features
- Single date selection
- Date range selection
- Multiple date selection
- Time picker support
- Custom formats
- Min/max date constraints
- Localization support

### Basic Usage

```vue
<template>
  <!-- Single date -->
  <DatePicker
    v-model="selectedDate"
    label="Check-in Date"
    format="dd/MM/yyyy"
    :min-date="new Date()"
  />
  
  <!-- Date range -->
  <DatePicker
    v-model="dateRange"
    :range="true"
    label="Stay Duration"
    format="dd/MM/yyyy"
  />
  
  <!-- With time picker -->
  <DatePicker
    v-model="meetingTime"
    :enable-time-picker="true"
    label="Meeting Time"
    format="dd/MM/yyyy HH:mm"
  />
</template>

<script setup>
import { DatePicker } from '@/components/ui'

const selectedDate = ref(null)
const dateRange = ref(null)
const meetingTime = ref(null)
</script>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `modelValue` | `any` | `undefined` | Selected date(s) |
| `label` | `string` | `undefined` | Label text |
| `required` | `boolean` | `false` | Required field |
| `placeholder` | `string` | `'Select date...'` | Placeholder text |
| `disabled` | `boolean` | `false` | Disable the picker |
| `error` | `string` | `undefined` | Error message |
| `helperText` | `string` | `undefined` | Helper text |
| `modelType` | `string` | `'timestamp'` | Data model type |
| `format` | `string` | `'dd/MM/yyyy'` | Display format |
| `clearable` | `boolean` | `true` | Show clear button |
| `autoApply` | `boolean` | `true` | Auto apply selection |
| `enableTimePicker` | `boolean` | `false` | Enable time selection |
| `range` | `boolean` | `false` | Enable range selection |
| `minDate` | `Date` | `undefined` | Minimum selectable date |
| `maxDate` | `Date` | `undefined` | Maximum selectable date |

## Chart Plugin

### Features
- Multiple chart types (line, bar, doughnut, pie, etc.)
- Responsive design
- Custom styling
- Loading states
- Error handling
- Interactive legends
- Custom options

### Basic Usage

```vue
<template>
  <!-- Line Chart -->
  <Chart
    type="line"
    :data="revenueData"
    :options="chartOptions"
    title="Revenue Trend"
    height="300"
  />
  
  <!-- Doughnut Chart -->
  <Chart
    type="doughnut"
    :data="roomTypeData"
    :options="doughnutOptions"
    title="Room Type Distribution"
    height="300"
  />
</template>

<script setup>
import { Chart } from '@/components/ui'

const revenueData = computed(() => ({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
  datasets: [{
    label: 'Revenue',
    data: [45000, 52000, 48000, 61000, 55000, 68000],
    borderColor: '#3B82F6',
    backgroundColor: 'rgba(59, 130, 246, 0.1)',
    tension: 0.4,
    fill: true,
  }],
}))

const chartOptions = {
  plugins: {
    legend: { display: false },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { callback: (value) => `$${value.toLocaleString()}` },
    },
  },
}
</script>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | `string` | `'line'` | Chart type |
| `data` | `object` | `{}` | Chart data |
| `options` | `object` | `{}` | Chart options |
| `title` | `string` | `undefined` | Chart title |
| `subtitle` | `string` | `undefined` | Chart subtitle |
| `height` | `number` | `400` | Chart height |
| `loading` | `boolean` | `false` | Show loading state |
| `error` | `string` | `undefined` | Error message |
| `retryable` | `boolean` | `true` | Show retry button |
| `showLegend` | `boolean` | `true` | Show legend |
| `showControls` | `boolean` | `false` | Show chart controls |

### Supported Chart Types

- `line`: Line chart
- `bar`: Bar chart
- `doughnut`: Doughnut chart
- `pie`: Pie chart
- `radar`: Radar chart
- `polarArea`: Polar area chart
- `bubble`: Bubble chart
- `scatter`: Scatter chart

## Installation & Setup

### 1. Install Dependencies

```bash
npm install @tanstack/vue-table @headlessui/vue chart.js vue-chartjs flatpickr vue-flatpickr-component @vuepic/vue-datepicker
```

### 2. Import Plugins

In your main app file (`resources/js/app.ts`):

```typescript
// Import plugins
import './plugins';
```

### 3. Use Components

Import and use the components in your Vue files:

```vue
<script setup>
import { DataTable, Select, DatePicker, Chart } from '@/components/ui'
</script>
```

## Examples

### Complete User Management Example

```vue
<template>
  <div>
    <!-- Filters -->
    <div class="filters">
      <Select
        v-model="roleFilter"
        :options="roleOptions"
        label="Filter by Role"
      />
      <DatePicker
        v-model="dateRange"
        :range="true"
        label="Date Range"
      />
    </div>

    <!-- Data Table -->
    <DataTable
      :data="filteredUsers"
      :columns="userColumns"
      :show-search="true"
      search-placeholder="Search users..."
    >
      <template #cell-role="{ value }">
        <span class="badge">{{ value }}</span>
      </template>
    </DataTable>

    <!-- Statistics Chart -->
    <Chart
      type="doughnut"
      :data="userStatsData"
      title="User Distribution"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { DataTable, Select, DatePicker, Chart } from '@/components/ui'

const roleFilter = ref('')
const dateRange = ref(null)

const roleOptions = [
  { value: '', label: 'All Roles' },
  { value: 'admin', label: 'Admin' },
  { value: 'user', label: 'User' },
]

const userColumns = [
  { accessorKey: 'name', header: 'Name' },
  { accessorKey: 'email', header: 'Email' },
  { accessorKey: 'role', header: 'Role' },
]

const filteredUsers = computed(() => {
  // Filter logic here
  return users.value
})

const userStatsData = computed(() => ({
  labels: ['Admin', 'User', 'Guest'],
  datasets: [{
    data: [10, 50, 20],
    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
  }],
}))
</script>
```

### Booking Management Example

```vue
<template>
  <div>
    <!-- Advanced Filters -->
    <div class="filters-grid">
      <Select
        v-model="filters.status"
        :options="statusOptions"
        label="Booking Status"
      />
      <DatePicker
        v-model="filters.dateRange"
        :range="true"
        label="Check-in/Check-out"
      />
      <Select
        v-model="filters.roomType"
        :options="roomTypeOptions"
        label="Room Type"
      />
    </div>

    <!-- Bookings Table -->
    <DataTable
      :data="filteredBookings"
      :columns="bookingColumns"
      :show-search="true"
      :show-pagination="true"
    >
      <template #cell-status="{ value }">
        <span :class="`status-badge ${value}`">{{ value }}</span>
      </template>
    </DataTable>

    <!-- Revenue Chart -->
    <Chart
      type="line"
      :data="revenueData"
      :options="revenueOptions"
      title="Monthly Revenue"
    />
  </div>
</template>
```

## Best Practices

### 1. Performance Optimization

- Use `computed` properties for filtered data
- Implement debouncing for search inputs
- Lazy load chart data when possible
- Use `v-memo` for expensive computations

### 2. Accessibility

- Always provide labels for form controls
- Use proper ARIA attributes
- Ensure keyboard navigation works
- Provide alternative text for charts

### 3. Error Handling

- Implement proper error states for all components
- Provide meaningful error messages
- Add retry mechanisms for failed operations
- Handle loading states gracefully

### 4. Responsive Design

- Test components on different screen sizes
- Use responsive breakpoints appropriately
- Ensure tables are scrollable on mobile
- Optimize chart sizes for mobile devices

### 5. Data Management

- Validate data before passing to components
- Handle empty states properly
- Implement proper data transformations
- Use TypeScript interfaces for type safety

### 6. Styling

- Use consistent color schemes
- Follow design system guidelines
- Implement dark mode support
- Ensure proper contrast ratios

## Troubleshooting

### Common Issues

1. **Charts not rendering**: Ensure Chart.js is properly registered
2. **Date picker not working**: Check if date format matches expected format
3. **Table sorting issues**: Verify column definitions are correct
4. **Select dropdown not opening**: Check z-index and positioning

### Debug Tips

- Use Vue DevTools to inspect component state
- Check browser console for errors
- Verify data structure matches component expectations
- Test components in isolation

## Support

For issues and questions:

1. Check the component documentation
2. Review the examples in this guide
3. Check the browser console for errors
4. Verify all dependencies are installed correctly

---

This documentation covers the main plugins used in the hotel booking system. For more detailed information about specific components, refer to their individual documentation files.
