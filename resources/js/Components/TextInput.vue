<script setup>
defineProps({
    modelValue: String,
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { type: String, default: 'text' },
    autocomplete: { type: String, default: 'off' },
    error: { type: String, default: '' },
    required: { type: Boolean, default: false },
    placeholder: { type: String, default: '' },
    icon: { type: [Object, Function], default: null },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label
            :for="id"
            class="mb-1 block text-sm font-medium text-gray-700"
        >
            {{ label }}
        </label>

        <div class="relative">
            <span
                v-if="$slots.icon || icon"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
            >
                <slot name="icon">
                    <component :is="icon" class="h-5 w-5" />
                </slot>
            </span>

            <input
                :id="id"
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :autocomplete="autocomplete"
                :required="required"
                :placeholder="placeholder"
                :aria-invalid="!!error"
                :aria-describedby="error ? `${id}-error` : undefined"
                class="block w-full rounded-full border bg-gray-100 px-4 py-3.5 text-sm transition-colors focus:outline-none focus:ring-1 focus:bg-white focus:border-[#5B93EF] focus:ring-[#5B93EF]"
                :class="[
                    ($slots.icon || icon) ? 'pl-12' : 'pl-4',
                    error
                        ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
                        : 'border-gray-50'
                ]"
            />

            <p
                v-if="error"
                :id="`${id}-error`"
                class="mt-1.5 text-sm text-red-600"
                role="alert"
            >
                {{ error }}
            </p>
        </div>
    </div>
</template>