<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    if (form.processing) return;

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <GuestLayout :title="t('register.title')" :subtitle="t('register.subtitle')">
        <form @submit.prevent="submit" class="space-y-5" novalidate>
            <TextInput
                id="name"
                v-model="form.name"
                :label="t('common.name')"
                autocomplete="name"
                required
                :error="form.errors.name"
            />

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
                :label="t('register.submit')"
                :loading-label="t('register.submitting')"
            />

            <p class="text-center text-sm text-gray-500">
                {{ t('register.hasAccount') }}
                <a :href="route('login')" class="text-[#5B93EF] hover:text-[#5B93EF] font-medium">
                    {{ t('register.login') }}
                </a>
            </p>
        </form>
    </GuestLayout>
</template>
