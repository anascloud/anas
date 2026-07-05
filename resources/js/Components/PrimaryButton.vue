<script setup>
defineProps({
    loading: { type: Boolean, default: false },
    label: { type: String, required: true },
    loadingLabel: { type: String, required: true },
});
</script>

<template>
    <button
        type="submit"
        :disabled="loading"
        style="border-radius: 30px;"
        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-[#5B93EF] hover:bg-[#5B93EF] disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-3.5 px-4 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#5B93EF]"
    >
        <svg
            v-if="loading"
            class="animate-spin h-4 w-4"
            viewBox="0 0 24 24"
            fill="none"
            aria-hidden="true"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        <span>{{ loading ? loadingLabel : label }}</span>
    </button>
</template>
