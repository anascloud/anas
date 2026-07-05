<script setup>
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';

const { locale, t } = useI18n();

function toggleLocale() {
    const next = locale.value === 'en' ? 'he' : 'en';

    router.post(
        route('locale.update'),
        { locale: next },
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                locale.value = next;
                document.documentElement.setAttribute('lang', next);
                document.documentElement.setAttribute('dir', next === 'he' ? 'rtl' : 'ltr');
            },
        }
    );
}
</script>

<template>
    <button
        type="button"
        @click="toggleLocale"
        class="inline-flex items-center gap-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors px-4 py-2 text-sm font-medium text-gray-700"
    >
        <span aria-hidden="true">🌐</span>
        <span>{{ t('common.language') }}</span>
        <svg class="w-3 h-3 text-gray-500" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</template>
