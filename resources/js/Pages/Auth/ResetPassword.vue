<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    if (form.processing) return;
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <GuestLayout :title="t('resetPassword.title')" :subtitle="t('resetPassword.subtitle')">
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

            <div>
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    :label="t('common.password')"
                    autocomplete="new-password"
                    required
                    :error="form.errors.password"
                />
                <p class="mt-1.5 text-xs text-gray-400">{{ t('validation.passwordHint') }}</p>
            </div>

            <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                :label="t('common.confirmPassword')"
                autocomplete="new-password"
                required
                :error="form.errors.password_confirmation"
            />

            <PrimaryButton
                :loading="form.processing"
                :label="t('resetPassword.submit')"
                :loading-label="t('resetPassword.submitting')"
            />
        </form>
    </GuestLayout>
</template>
