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

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    if (form.processing) return;

    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <GuestLayout :title="t('login.title')" :subtitle="t('login.subtitle')">
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

            <TextInput
                id="password"
                v-model="form.password"
                type="password"
                :label="t('common.password')"
                autocomplete="current-password"
                required
                :error="form.errors.password"
            />

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-gray-600">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                    />
                    {{ t('login.remember') }}
                </label>
                <a :href="route('password.request')" class="text-brand-600 hover:text-brand-700 font-medium">
                    {{ t('login.forgot') }}
                </a>
            </div>

            <PrimaryButton
                :loading="form.processing"
                :label="t('login.submit')"
                :loading-label="t('login.submitting')"
            />

            <p class="text-center text-sm text-gray-500">
                {{ t('login.noAccount') }}
                <a :href="route('register')" class="text-brand-600 hover:text-brand-700 font-medium">
                    {{ t('login.register') }}
                </a>
            </p>
        </form>
    </GuestLayout>
</template>
