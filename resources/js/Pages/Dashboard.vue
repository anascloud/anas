<script setup>
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const { t } = useI18n();

const props = defineProps({
    user: { type: Object, required: true },
});

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header class="flex items-center justify-between px-8 py-6 bg-white border-b border-gray-100">
            <span class="text-lg font-bold text-gray-800">{{ t('common.appName') }}</span>
            <div class="flex items-center gap-3">
                <LanguageSwitcher />
                <button
                    @click="logout"
                    class="rounded-lg border border-gray-300 hover:bg-gray-50 text-sm font-medium text-gray-700 px-4 py-2"
                >
                    {{ t('dashboard.logout') }}
                </button>
            </div>
        </header>

        <main class="flex flex-col items-center justify-center px-6 py-24 text-center">
            <h1 class="text-5xl font-bold text-[#5B93EF] mb-4">{{ t('dashboard.greeting') }}</h1>
            <p class="text-gray-500">{{ t('dashboard.welcome', { name: props.user.name, email: props.user.email }) }}</p>
        </main>
    </div>
</template>
