<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';

const { locale } = useI18n();

const open = ref(false);

const languages = [
    {
        code: 'en',
        label: 'English',
        flag: 'us',
    },
    {
        code: 'he',
        label: 'עברית',
        flag: 'il',
    },
];

const currentLanguage = computed(() =>
    languages.find(lang => lang.code === locale.value)
);

function changeLocale(lang) {
    if (lang.code === locale.value) {
        open.value = false;
        return;
    }

    router.post(
        route('locale.update'),
        { locale: lang.code },
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                locale.value = lang.code;
                document.documentElement.lang = lang.code;
                document.documentElement.dir = lang.code === 'he' ? 'rtl' : 'ltr';
                open.value = false;
            },
        }
    );
}

function handleClickOutside(e) {
    if (!e.target.closest('.language-dropdown')) {
        open.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div
        class="language-dropdown relative inline-block"
    >
        <button
            type="button"
            @click.stop="open = !open"
            class="flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50"
        >
            <span :class="`fi fi-${currentLanguage.flag}`"></span>

            <span>{{ currentLanguage.label }}</span>

            <svg
                class="h-4 w-4 transition-transform duration-200"
                :class="{ 'rotate-180': open }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </button>

        <div
            v-if="open"
            class="absolute right-0 mt-2 w-44 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg z-50"
        >
            <button
                v-for="lang in languages"
                :key="lang.code"
                type="button"
                @click="changeLocale(lang)"
                class="flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-gray-100"
                :class="{
                    'bg-blue-50 text-blue-600': locale === lang.code
                }"
            >
                <span :class="`fi fi-${lang.flag}`"></span>

                <span>{{ lang.label }}</span>

                <svg
                    v-if="locale === lang.code"
                    class="ml-auto h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>
            </button>
        </div>
    </div>
</template>