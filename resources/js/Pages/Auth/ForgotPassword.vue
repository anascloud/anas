<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

defineProps({
    status: { type: String, default: null },
});

const form = useForm({ email: '' });

function submit() {
    if (form.processing) return;
    form.post(route('password.email'));
}
</script>

<template>
    <GuestLayout :title="t('forgotPassword.title')" :subtitle="t('forgotPassword.subtitle')">
        <div
            v-if="status"
            class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3"
            role="status"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-7" novalidate>
            <TextInput
                id="email"
                v-model="form.email"
                type="email"
                :label="t('common.email')"
                autocomplete="username"
                required
                :error="form.errors.email"
                :placeholder="t('common.emailPlaceholder')"
            >
                <template #icon>
                    <img src="/images/envelope.svg" class="w-5 h-5" />
                </template>
            </TextInput>

            <div class="flex items-center gap-4">
                <div class="w-40 shrink-0">
                    <PrimaryButton
                        :loading="form.processing"
                        :label="t('forgotPassword.submit')"
                        :loading-label="t('forgotPassword.submitting')"
                    />
                </div>

                <p class="text-sm text-gray-500">
                    {{ t('forgotPassword.rememberPassword') }}
                    <a
                        :href="route('login')"
                        class="text-[#5B93EF] hover:text-[#5B93EF] font-medium underline"
                    >
                        {{ t('forgotPassword.login') }}
                    </a>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
