<script setup>
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const { t } = useI18n();

defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <div class="min-h-screen flex flex-col md:flex-row-reverse" dir="auto">
        <!-- Form panel -->
        <div class="flex-1 flex flex-col bg-white">
            <div class="flex items-center justify-between px-8 py-6">
                <LanguageSwitcher />
                <span class="text-lg font-bold text-gray-800">{{ t('common.appName') }}</span>
            </div>

            <div class="flex-1 flex items-center justify-center px-6 pb-16">
                <div class="w-full max-w-md">
                    <div class="bg-white rounded-2xl shadow-xl shadow-brand-900/5 border-t-4 border-brand-600 p-8">
                        <h1 class="text-2xl font-bold text-brand-700 mb-1">{{ title }}</h1>
                        <p v-if="subtitle" class="text-sm text-gray-500 mb-6">{{ subtitle }}</p>
                        <slot />
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual panel -->
        <div class="hidden md:block md:w-[45%] relative overflow-hidden bg-slate-900">
            <!-- Background image -->
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-[#5F5F60]"
                style="background-image: url('/images/auth-bg.jpg'); background-blend-mode: luminosity;"
            ></div>
            <div class="absolute inset-0 bg-slate-900/55"></div>
        </div>
    </div>
</template>
