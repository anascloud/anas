<script setup>
defineProps({
    modelValue: String,
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { type: String, default: 'text' },
    autocomplete: { type: String, default: 'off' },
    error: { type: String, default: '' },
    required: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
        </label>
        <div class="relative">
            <input
                :id="id"
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :autocomplete="autocomplete"
                :required="required"
                :aria-invalid="!!error"
                :aria-describedby="error ? `${id}-error` : undefined"
                class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 py-2.5 px-4 text-sm transition-colors"
                :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-500': error }"
            />
        </div>
        <p v-if="error" :id="`${id}-error`" class="mt-1.5 text-sm text-red-600" role="alert">
            {{ error }}
        </p>
    </div>
</template>
