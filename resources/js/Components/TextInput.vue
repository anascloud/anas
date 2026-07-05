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
    // NEW: icon support (optional)
    icon: { type: [Object, Function], default: null },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <!-- Label -->
        <label
            :for="id"
            class="block text-sm font-medium text-gray-700 mb-1"
        >
            {{ label }}
        </label>

        <!-- Input wrapper -->
        <div class="relative">
            <!-- Icon -->
            <span
                v-if="$slots.icon"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
            >
                <slot name="icon" />
            </span>

            <!-- Input -->
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
                class="block w-full rounded-full border bg-gray-50 focus:bg-white focus:ring-1 focus:outline-none py-3.5 px-4 text-sm transition-colors
                       focus:border-[#5B93EF] focus:ring-[#5B93EF]"
                :class="[
    $slots.icon ? 'pl-12' : 'pl-4',
                    error
                        ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
                        : 'border-gray-200'
                ]"
            />
        </div>

        <!-- Error -->
        <p
            v-if="error"
            :id="`${id}-error`"
            class="mt-1.5 text-sm text-red-600"
            role="alert"
        >
            {{ error }}
        </p>
    </div>
</template>