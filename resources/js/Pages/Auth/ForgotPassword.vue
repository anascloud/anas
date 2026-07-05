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

        <form @submit.prevent="submit" class="space-y-5" novalidate>
            <TextInput
                id="email"
                v-model="form.email"
                type="email"
                :label="t('common.email')"
                autocomplete="username"
                required
                :error="form.errors.email"
            />

            <PrimaryButton
                :loading="form.processing"
                :label="t('forgotPassword.submit')"
                :loading-label="t('forgotPassword.submitting')"
            />

            <p class="text-center text-sm">
                <a :href="route('login')" class="text-gray-400 hover:text-gray-600">
                    {{ t('forgotPassword.backToLogin') }}
                </a>
            </p>
        </form>
    </GuestLayout>
</template>
