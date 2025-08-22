<template>
  <div class="relative">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <VueDatePicker
      v-model="dateValue"
      :model-type="modelType"
      :format="format"
      :placeholder="placeholder"
      :disabled="disabled"
      :clearable="clearable"
      :auto-apply="autoApply"
      :enable-time-picker="enableTimePicker"
      :enable-seconds="enableSeconds"
      :enable-minutes="enableMinutes"
      :enable-hours="enableHours"
      :min-date="minDate"
      :max-date="maxDate"
      :range="range"
      :multi-dates="multiDates"
      :week-start="weekStart"
      :locale="locale"
      :teleport="teleport"
      :dark="dark"
      :text-input="textInput"
      :text-input-options="textInputOptions"
      :menu-class-name="menuClassName"
      :input-class-name="inputClassName"
      :calendar-class-name="calendarClassName"
      :action-row-class-name="actionRowClassName"
      :calendar-cell-class-name="calendarCellClassName"
      :calendar-day-class-name="calendarDayClassName"
      :calendar-month-class-name="calendarMonthClassName"
      :calendar-year-class-name="calendarYearClassName"
      :calendar-time-class-name="calendarTimeClassName"
      :calendar-week-class-name="calendarWeekClassName"
      :calendar-weekday-class-name="calendarWeekdayClassName"
      :calendar-week-number-class-name="calendarWeekNumberClassName"
      :calendar-header-class-name="calendarHeaderClassName"
      :calendar-header-title-class-name="calendarHeaderTitleClassName"
      :calendar-header-arrow-class-name="calendarHeaderArrowClassName"
      :calendar-header-arrow-left-class-name="calendarHeaderArrowLeftClassName"
      :calendar-header-arrow-right-class-name="calendarHeaderArrowRightClassName"
      :calendar-header-arrow-disabled-class-name="calendarHeaderArrowDisabledClassName"
      :calendar-header-arrow-hover-class-name="calendarHeaderArrowHoverClassName"
      :calendar-header-arrow-focus-class-name="calendarHeaderArrowFocusClassName"
      :calendar-header-arrow-active-class-name="calendarHeaderArrowActiveClassName"
      :calendar-header-arrow-pressed-class-name="calendarHeaderArrowPressedClassName"
      :calendar-header-arrow-selected-class-name="calendarHeaderArrowSelectedClassName"
      :calendar-header-arrow-current-class-name="calendarHeaderArrowCurrentClassName"
      :calendar-header-arrow-today-class-name="calendarHeaderArrowTodayClassName"
      :calendar-header-arrow-weekend-class-name="calendarHeaderArrowWeekendClassName"
      :calendar-header-arrow-holiday-class-name="calendarHeaderArrowHolidayClassName"
      :calendar-header-arrow-disabled-day-class-name="calendarHeaderArrowDisabledDayClassName"
      :calendar-header-arrow-enabled-day-class-name="calendarHeaderArrowEnabledDayClassName"
      :calendar-header-arrow-selected-day-class-name="calendarHeaderArrowSelectedDayClassName"
      :calendar-header-arrow-current-day-class-name="calendarHeaderArrowCurrentDayClassName"
      :calendar-header-arrow-today-day-class-name="calendarHeaderArrowTodayDayClassName"
      :calendar-header-arrow-weekend-day-class-name="calendarHeaderArrowWeekendDayClassName"
      :calendar-header-arrow-holiday-day-class-name="calendarHeaderArrowHolidayDayClassName"
      :calendar-header-arrow-disabled-month-class-name="calendarHeaderArrowDisabledMonthClassName"
      :calendar-header-arrow-enabled-month-class-name="calendarHeaderArrowEnabledMonthClassName"
      :calendar-header-arrow-selected-month-class-name="calendarHeaderArrowSelectedMonthClassName"
      :calendar-header-arrow-current-month-class-name="calendarHeaderArrowCurrentMonthClassName"
      :calendar-header-arrow-today-month-class-name="calendarHeaderArrowTodayMonthClassName"
      :calendar-header-arrow-weekend-month-class-name="calendarHeaderArrowWeekendMonthClassName"
      :calendar-header-arrow-holiday-month-class-name="calendarHeaderArrowHolidayMonthClassName"
      :calendar-header-arrow-disabled-year-class-name="calendarHeaderArrowDisabledYearClassName"
      :calendar-header-arrow-enabled-year-class-name="calendarHeaderArrowEnabledYearClassName"
      :calendar-header-arrow-selected-year-class-name="calendarHeaderArrowSelectedYearClassName"
      :calendar-header-arrow-current-year-class-name="calendarHeaderArrowCurrentYearClassName"
      :calendar-header-arrow-today-year-class-name="calendarHeaderArrowTodayYearClassName"
      :calendar-header-arrow-weekend-year-class-name="calendarHeaderArrowWeekendYearClassName"
      :calendar-header-arrow-holiday-year-class-name="calendarHeaderArrowHolidayYearClassName"
      @update:model-value="handleChange"
      @closed="handleClosed"
      @opened="handleOpened"
      @cleared="handleCleared"
      @range-start="handleRangeStart"
      @range-end="handleRangeEnd"
      @flow-step="handleFlowStep"
      @internal-model-change="handleInternalModelChange"
      @keydown="handleKeydown"
      @focus="handleFocus"
      @blur="handleBlur"
      @click="handleClick"
      @input="handleInput"
      @change="handleChange"
    />

    <!-- Error Message -->
    <p v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </p>

    <!-- Helper Text -->
    <p v-if="helperText && !error" class="mt-1 text-sm text-gray-500">
      {{ helperText }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';

interface Props {
  modelValue?: any;
  label?: string;
  required?: boolean;
  placeholder?: string;
  disabled?: boolean;
  error?: string;
  helperText?: string;
  modelType?: 'timestamp' | 'format' | 'iso' | 'object' | 'utc';
  format?: string;
  clearable?: boolean;
  autoApply?: boolean;
  enableTimePicker?: boolean;
  enableSeconds?: boolean;
  enableMinutes?: boolean;
  enableHours?: boolean;
  minDate?: Date | string | number;
  maxDate?: Date | string | number;
  range?: boolean;
  multiDates?: boolean;
  weekStart?: number;
  locale?: string | object;
  teleport?: boolean | string;
  dark?: boolean;
  textInput?: boolean;
  textInputOptions?: object;
  menuClassName?: string;
  inputClassName?: string;
  calendarClassName?: string;
  actionRowClassName?: string;
  calendarCellClassName?: string;
  calendarDayClassName?: string;
  calendarMonthClassName?: string;
  calendarYearClassName?: string;
  calendarTimeClassName?: string;
  calendarWeekClassName?: string;
  calendarWeekdayClassName?: string;
  calendarWeekNumberClassName?: string;
  calendarHeaderClassName?: string;
  calendarHeaderTitleClassName?: string;
  calendarHeaderArrowClassName?: string;
  calendarHeaderArrowLeftClassName?: string;
  calendarHeaderArrowRightClassName?: string;
  calendarHeaderArrowDisabledClassName?: string;
  calendarHeaderArrowHoverClassName?: string;
  calendarHeaderArrowFocusClassName?: string;
  calendarHeaderArrowActiveClassName?: string;
  calendarHeaderArrowPressedClassName?: string;
  calendarHeaderArrowSelectedClassName?: string;
  calendarHeaderArrowCurrentClassName?: string;
  calendarHeaderArrowTodayClassName?: string;
  calendarHeaderArrowWeekendClassName?: string;
  calendarHeaderArrowHolidayClassName?: string;
  calendarHeaderArrowDisabledDayClassName?: string;
  calendarHeaderArrowEnabledDayClassName?: string;
  calendarHeaderArrowSelectedDayClassName?: string;
  calendarHeaderArrowCurrentDayClassName?: string;
  calendarHeaderArrowTodayDayClassName?: string;
  calendarHeaderArrowWeekendDayClassName?: string;
  calendarHeaderArrowHolidayDayClassName?: string;
  calendarHeaderArrowDisabledMonthClassName?: string;
  calendarHeaderArrowEnabledMonthClassName?: string;
  calendarHeaderArrowSelectedMonthClassName?: string;
  calendarHeaderArrowCurrentMonthClassName?: string;
  calendarHeaderArrowTodayMonthClassName?: string;
  calendarHeaderArrowWeekendMonthClassName?: string;
  calendarHeaderArrowHolidayMonthClassName?: string;
  calendarHeaderArrowDisabledYearClassName?: string;
  calendarHeaderArrowEnabledYearClassName?: string;
  calendarHeaderArrowSelectedYearClassName?: string;
  calendarHeaderArrowCurrentYearClassName?: string;
  calendarHeaderArrowTodayYearClassName?: string;
  calendarHeaderArrowWeekendYearClassName?: string;
  calendarHeaderArrowHolidayYearClassName?: string;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Select date...',
  disabled: false,
  required: false,
  clearable: true,
  autoApply: true,
  enableTimePicker: false,
  enableSeconds: false,
  enableMinutes: true,
  enableHours: true,
  range: false,
  multiDates: false,
  weekStart: 1,
  teleport: true,
  dark: false,
  textInput: false,
  modelType: 'timestamp',
  format: 'dd/MM/yyyy',
});

const emit = defineEmits<{
  'update:modelValue': [value: any];
  'change': [value: any];
  'closed': [];
  'opened': [];
  'cleared': [];
  'range-start': [value: any];
  'range-end': [value: any];
  'flow-step': [value: any];
  'internal-model-change': [value: any];
  'keydown': [event: KeyboardEvent];
  'focus': [event: FocusEvent];
  'blur': [event: FocusEvent];
  'click': [event: MouseEvent];
  'input': [event: Event];
}>();

// Computed date value
const dateValue = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
});

// Event handlers
const handleChange = (value: any) => {
  emit('change', value);
};

const handleClosed = () => {
  emit('closed');
};

const handleOpened = () => {
  emit('opened');
};

const handleCleared = () => {
  emit('cleared');
};

const handleRangeStart = (value: any) => {
  emit('range-start', value);
};

const handleRangeEnd = (value: any) => {
  emit('range-end', value);
};

const handleFlowStep = (value: any) => {
  emit('flow-step', value);
};

const handleInternalModelChange = (value: any) => {
  emit('internal-model-change', value);
};

const handleKeydown = (event: KeyboardEvent) => {
  emit('keydown', event);
};

const handleFocus = (event: FocusEvent) => {
  emit('focus', event);
};

const handleBlur = (event: FocusEvent) => {
  emit('blur', event);
};

const handleClick = (event: MouseEvent) => {
  emit('click', event);
};

const handleInput = (event: Event) => {
  emit('input', event);
};
</script>

<style scoped>
/* Custom styles for the date picker */
:deep(.dp__input) {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
}

:deep(.dp__input:focus) {
  outline: none;
  border-color: transparent;
  box-shadow: 0 0 0 2px #3b82f6;
}

:deep(.dp__input.dp__input_error) {
  border-color: #ef4444;
}

:deep(.dp__input.dp__input_disabled) {
  opacity: 0.5;
  cursor: not-allowed;
}

:deep(.dp__main) {
  z-index: 50;
}

:deep(.dp__menu) {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
}

:deep(.dp__calendar_header) {
  background-color: #f9fafb;
}

:deep(.dp__calendar_header_separator) {
  border-color: #e5e7eb;
}

:deep(.dp__arrow_top) {
  border-color: #e5e7eb;
}

:deep(.dp__arrow_bottom) {
  border-color: #e5e7eb;
}

:deep(.dp__today) {
  background-color: #dbeafe;
  color: #1e40af;
}

:deep(.dp__active_date) {
  background-color: #2563eb;
  color: white;
}

:deep(.dp__range_start) {
  background-color: #2563eb;
  color: white;
}

:deep(.dp__range_end) {
  background-color: #2563eb;
  color: white;
}

:deep(.dp__range_between) {
  background-color: #dbeafe;
}

:deep(.dp__disabled) {
  color: #9ca3af;
  cursor: not-allowed;
}

:deep(.dp__hover) {
  background-color: #f3f4f6;
}
</style>
