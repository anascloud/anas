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
    <div class="min-h-screen flex flex-col md:flex-row-reverse border-b-[23px] border-b-[#5B93EF]" dir="ltr">
        <!-- Form panel -->
        <div class="flex-1 flex flex-col bg-white">
            <div class="flex items-center justify-between px-8 py-6">
                <LanguageSwitcher />
                <span class="text-lg font-bold text-gray-800">{{ t('common.appName') }}</span>
            </div>

            <div class="flex-1 flex items-center justify-center px-6 pb-16">
                <div class="w-full max-w-md">
                    <div class="bg-white rounded-2xl shadow-xl shadow-brand-900/5 border-t-4 border-brand-500 p-8" style="box-shadow: 0px 0px 100px 0px #5B93EF33;">
                        <h1 class="text-2xl text-center font-bold text-[#5B93EF] mb-3">{{ title }}</h1>
                        <p v-if="subtitle" class="text-sm text-gray-500 mb-6">{{ subtitle }}</p>
                        <slot />
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual panel -->
        <div class="hidden md:block md:w-[67%] relative overflow-hidden">
            <!-- Background image -->
            <div class="absolute inset-0 bg-left bg-cover bg-no-repeat bg-[image:url('/images/auth-bg.jpg')]"> </div>
        </div>
    </div>
</template>
