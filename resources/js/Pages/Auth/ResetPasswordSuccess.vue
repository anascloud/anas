<script setup>
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const { t } = useI18n();
</script>

<template>
    <GuestLayout>
        <div class="flex flex-col items-center text-center gap-3 px-8 py-3">
            <img src="/images/tick.svg" alt="Success" />
            <h1 class="text-2xl text-center font-bold text-[#5B93EF] mb-0">{{ t('resetPasswordSuccess.title') }}</h1>
            <span class="text-sm text-gray-500 m-0">{{ t('resetPasswordSuccess.subtitle') }}</span>
            <a
                :href="route('login')"
                class="w-full inline-flex items-center justify-center rounded-lg bg-[#5B93EF] hover:bg-[#5B93EF] text-white font-semibold py-3.5 px-4 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#5B93EF] mt-7" style="border-radius: 40px;"
            >
                {{ t('resetPasswordSuccess.loginButton') }}
            </a>
        </div>
    </GuestLayout>
</template>