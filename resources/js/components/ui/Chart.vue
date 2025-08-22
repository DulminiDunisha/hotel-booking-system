<template>
  <div class="relative">
    <div v-if="title" class="mb-4">
      <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
      <p v-if="subtitle" class="text-sm text-gray-600">{{ subtitle }}</p>
    </div>
    
    <div class="relative" :style="{ height: height + 'px' }">
      <canvas ref="chartRef"></canvas>
      
      <!-- Loading State -->
      <div
        v-if="loading"
        class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75"
      >
        <div class="flex items-center space-x-2">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
          <span class="text-sm text-gray-600">Loading chart...</span>
        </div>
      </div>
      
      <!-- Error State -->
      <div
        v-if="error"
        class="absolute inset-0 flex items-center justify-center bg-red-50"
      >
        <div class="text-center">
          <svg
            class="mx-auto h-12 w-12 text-red-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-red-800">Chart Error</h3>
          <p class="mt-1 text-sm text-red-700">{{ error }}</p>
          <button
            v-if="retryable"
            @click="retry"
            class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Retry
          </button>
        </div>
      </div>
    </div>
    
    <!-- Legend -->
    <div v-if="showLegend && chartData?.datasets" class="mt-4">
      <div class="flex flex-wrap gap-4 justify-center">
        <div
          v-for="(dataset, index) in chartData.datasets"
          :key="index"
          class="flex items-center space-x-2"
        >
          <div
            class="w-4 h-4 rounded"
            :style="{ backgroundColor: dataset.backgroundColor || dataset.borderColor }"
          ></div>
          <span class="text-sm text-gray-700">{{ dataset.label }}</span>
        </div>
      </div>
    </div>
    
    <!-- Chart Controls -->
    <div v-if="showControls" class="mt-4 flex justify-center space-x-2">
      <button
        v-for="control in controls"
        :key="control.key"
        @click="control.action"
        class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        :class="{ 'bg-blue-50 border-blue-300': control.active }"
      >
        {{ control.label }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, nextTick, computed } from 'vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  BarElement,
  RadialLinearScale,
  Filler,
} from 'chart.js';

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  BarElement,
  RadialLinearScale,
  Filler
);

interface ChartControl {
  key: string;
  label: string;
  action: () => void;
  active?: boolean;
}

interface Props {
  type: 'line' | 'bar' | 'doughnut' | 'pie' | 'radar' | 'polarArea' | 'bubble' | 'scatter';
  data: any;
  options?: any;
  title?: string;
  subtitle?: string;
  height?: number;
  loading?: boolean;
  error?: string;
  retryable?: boolean;
  showLegend?: boolean;
  showControls?: boolean;
  controls?: ChartControl[];
  responsive?: boolean;
  maintainAspectRatio?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  height: 400,
  loading: false,
  retryable: true,
  showLegend: true,
  showControls: false,
  responsive: true,
  maintainAspectRatio: false,
});

const emit = defineEmits<{
  'chart-click': [event: any, elements: any];
  'chart-hover': [event: any, elements: any];
  'chart-zoom': [event: any];
  'chart-pan': [event: any];
  'retry': [];
}>();

const chartRef = ref<HTMLCanvasElement>();
let chart: ChartJS | null = null;

// Chart data computed property
const chartData = computed(() => {
  if (typeof props.data === 'function') {
    return props.data();
  }
  return props.data;
});

// Initialize chart
const initChart = async () => {
  if (!chartRef.value) return;

  // Destroy existing chart
  if (chart) {
    chart.destroy();
  }

  // Create new chart
  chart = new ChartJS(chartRef.value, {
    type: props.type,
    data: chartData.value,
    options: {
      responsive: props.responsive,
      maintainAspectRatio: props.maintainAspectRatio,
      plugins: {
        legend: {
          display: false, // We handle legend manually
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        },
      },
      interaction: {
        mode: 'nearest',
        axis: 'x',
        intersect: false,
      },
      scales: {
        x: {
          display: true,
          title: {
            display: true,
          },
        },
        y: {
          display: true,
          title: {
            display: true,
          },
        },
      },
      ...props.options,
    },
  });

  // Add event listeners
  chart.canvas.addEventListener('click', handleChartClick);
  chart.canvas.addEventListener('mousemove', handleChartHover);
};

// Event handlers
const handleChartClick = (event: any) => {
  if (!chart) return;
  
  const elements = chart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, true);
  emit('chart-click', event, elements);
};

const handleChartHover = (event: any) => {
  if (!chart) return;
  
  const elements = chart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, true);
  emit('chart-hover', event, elements);
};

// Retry function
const retry = () => {
  emit('retry');
};

// Watch for data changes
watch(
  () => chartData.value,
  () => {
    if (chart) {
      chart.data = chartData.value;
      chart.update();
    }
  },
  { deep: true }
);

// Watch for options changes
watch(
  () => props.options,
  () => {
    if (chart) {
      chart.options = {
        ...chart.options,
        ...props.options,
      };
      chart.update();
    }
  },
  { deep: true }
);

// Watch for type changes
watch(
  () => props.type,
  () => {
    nextTick(() => {
      initChart();
    });
  }
);

// Lifecycle hooks
onMounted(() => {
  nextTick(() => {
    initChart();
  });
});

onUnmounted(() => {
  if (chart) {
    chart.destroy();
  }
});

// Expose chart instance
defineExpose({
  chart: computed(() => chart),
  updateChart: () => chart?.update(),
  destroyChart: () => {
    if (chart) {
      chart.destroy();
      chart = null;
    }
  },
});
</script>

<style scoped>
/* Custom chart styles */
canvas {
  max-width: 100%;
}
</style>
