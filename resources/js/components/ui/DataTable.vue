<template>
  <div class="w-full">
    <!-- Search and Filters -->
    <div v-if="showSearch || showFilters" class="mb-4 p-4 bg-white rounded-lg shadow-sm border">
      <div class="flex flex-col lg:flex-row gap-4">
        <!-- Search Input -->
        <div v-if="showSearch" class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input
            v-model="searchValue"
            type="text"
            :placeholder="searchPlaceholder"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
          />
        </div>
        
        <!-- Filters -->
        <div v-if="showFilters" class="flex flex-wrap gap-2">
          <slot name="filters" />
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-full">
          <thead class="bg-gray-50">
            <tr>
              <th
                v-for="column in table.getFlatHeaders()"
                :key="column.id"
                class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap"
                :class="column.column.getCanSort() ? 'cursor-pointer select-none' : ''"
                @click="column.column.getToggleSortingHandler()?.()"
              >
                <div class="flex items-center gap-1 lg:gap-2">
                  <slot
                    :name="`header-${column.id}`"
                    :column="column"
                    :sorting="column.column.getIsSorted()"
                  >
                    <span class="truncate">{{ column.column.columnDef.header }}</span>
                  </slot>
                  <span v-if="column.column.getCanSort()" class="text-gray-400 flex-shrink-0">
                    <svg
                      v-if="column.column.getIsSorted() === 'asc'"
                      class="w-3 h-3 lg:w-4 lg:h-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <svg
                      v-else-if="column.column.getIsSorted() === 'desc'"
                      class="w-3 h-3 lg:w-4 lg:h-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <svg
                      v-else
                      class="w-3 h-3 lg:w-4 lg:h-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </span>
                </div>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="row in table.getRowModel().rows"
              :key="row.id"
              class="hover:bg-gray-50"
            >
              <td
                v-for="cell in row.getVisibleCells()"
                :key="cell.id"
                class="px-3 lg:px-6 py-3 lg:py-4 text-sm text-gray-900"
              >
                <slot
                  :name="`cell-${cell.column.id}`"
                  :cell="cell"
                  :row="row"
                  :value="cell.getValue()"
                >
                  <div class="truncate">{{ cell.getValue() }}</div>
                </slot>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div
        v-if="table.getRowModel().rows.length === 0"
        class="text-center py-8 lg:py-12 px-4"
      >
        <slot name="empty">
          <svg
            class="mx-auto h-8 w-8 lg:h-12 lg:w-12 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
          <h3 class="mt-2 text-sm lg:text-base font-medium text-gray-900">No data found</h3>
          <p class="mt-1 text-xs lg:text-sm text-gray-500">
            Try adjusting your search or filter criteria.
          </p>
        </slot>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="showPagination" class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-2 text-sm text-gray-700">
        <span class="hidden sm:inline">
          Showing {{ table.getState().pagination.pageIndex * table.getState().pagination.pageSize + 1 }} to
          {{ Math.min((table.getState().pagination.pageIndex + 1) * table.getState().pagination.pageSize, table.getFilteredRowModel().rows.length) }} of
          {{ table.getFilteredRowModel().rows.length }} results
        </span>
        <span class="sm:hidden">
          {{ table.getFilteredRowModel().rows.length }} results
        </span>
      </div>
      
      <div class="flex items-center gap-2">
        <button
          :disabled="!table.getCanPreviousPage()"
          @click="table.previousPage()"
          class="px-3 py-2 text-sm border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors"
        >
          <span class="hidden sm:inline">Previous</span>
          <span class="sm:hidden">←</span>
        </button>
        
        <span class="text-sm text-gray-700 px-2">
          <span class="hidden sm:inline">Page {{ table.getState().pagination.pageIndex + 1 }} of {{ table.getPageCount() }}</span>
          <span class="sm:hidden">{{ table.getState().pagination.pageIndex + 1 }}/{{ table.getPageCount() }}</span>
        </span>
        
        <button
          :disabled="!table.getCanNextPage()"
          @click="table.nextPage()"
          class="px-3 py-2 text-sm border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors"
        >
          <span class="hidden sm:inline">Next</span>
          <span class="sm:hidden">→</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, watch, ref } from 'vue';
import { 
  useVueTable, 
  type ColumnDef, 
  type SortingState, 
  type PaginationState,
  getCoreRowModel,
  getSortedRowModel,
  getFilteredRowModel,
  getPaginationRowModel
} from '@tanstack/vue-table';

interface Props {
  data: any[];
  columns: ColumnDef<any>[];
  showSearch?: boolean;
  showFilters?: boolean;
  showPagination?: boolean;
  searchPlaceholder?: string;
  pageSize?: number;
  enableSorting?: boolean;
  enableGlobalFilter?: boolean;
  enablePagination?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  showSearch: true,
  showFilters: false,
  showPagination: true,
  searchPlaceholder: 'Search...',
  pageSize: 10,
  enableSorting: true,
  enableGlobalFilter: true,
  enablePagination: true,
});

const emit = defineEmits<{
  'update:data': [value: any[]];
  'row-click': [row: any];
}>();

// Reactive state
const sorting = ref<SortingState>([]);
const globalFilter = ref('');
const pagination = ref<PaginationState>({
  pageIndex: 0,
  pageSize: props.pageSize,
});

// Search value for external control
const searchValue = ref('');

// Watch for search value changes
watch(searchValue, (newValue) => {
  globalFilter.value = newValue;
});

// Table instance
const table = useVueTable({
  get data() {
    return props.data;
  },
  get columns() {
    return props.columns;
  },
  get state() {
    return {
      sorting: sorting.value,
      globalFilter: globalFilter.value,
      pagination: pagination.value,
    };
  },
  onSortingChange: (updater) => {
    sorting.value = typeof updater === 'function' ? updater(sorting.value) : updater;
  },
  onGlobalFilterChange: (updater) => {
    globalFilter.value = typeof updater === 'function' ? updater(globalFilter.value) : updater;
  },
  onPaginationChange: (updater) => {
    pagination.value = typeof updater === 'function' ? updater(pagination.value) : updater;
  },
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  enableSorting: props.enableSorting,
  enableGlobalFilter: props.enableGlobalFilter,
  enablePagination: props.enablePagination,
});

// Expose table instance
defineExpose({
  table,
});
</script>
