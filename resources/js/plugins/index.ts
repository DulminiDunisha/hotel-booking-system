// Data Table Plugin
export { useVueTable, type ColumnDef } from '@tanstack/vue-table';

// Axios Configuration
import './axios';

// Date Picker Plugin
export { default as VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

// Chart.js Plugin
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

// Flatpickr Plugin
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';

// Export flatpickr for global use
export { flatpickr };

// Headless UI Components
export {
    Listbox,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
    Combobox,
    ComboboxInput,
    ComboboxButton,
    ComboboxOptions,
    ComboboxOption,
    Dialog,
    DialogPanel,
    DialogTitle,
    DialogDescription,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';

// VueUse utilities
export {
    useLocalStorage,
    useSessionStorage,
    useDebounceFn,
    useThrottleFn,
    useAsyncState,
    useToggle,
    useCounter,
    useIntervalFn,
    useTimeoutFn,
} from '@vueuse/core';
