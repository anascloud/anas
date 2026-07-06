<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    email: { type: String, default: '' },
    phone: { type: String, default: '5971-***-***' },
    phoneEnding: { type: String, default: '123456' },
    canResendIn: { type: Number, default: 20 },
});

const form = useForm({ code: '' });
const secondsLeft = ref(props.canResendIn);
const resending = ref(false);
const status = ref('');
const codeInput = ref(null);

let timer = null;

const codeChars = computed(() => form.code.split(''));

const formattedTime = computed(() => {
    const minutes = String(Math.floor(secondsLeft.value / 60)).padStart(2, '0');
    const seconds = String(secondsLeft.value % 60).padStart(2, '0');
    return `${minutes}:${seconds}`;
});

function startTimer() {
    clearInterval(timer);
    timer = setInterval(() => {
        if (secondsLeft.value > 0) {
            secondsLeft.value -= 1;
        } else {
            clearInterval(timer);
        }
    }, 1000);
}

onMounted(() => {
    startTimer();
    nextTick(() => codeInput.value?.focus());
});

onUnmounted(() => clearInterval(timer));

function handleCodeInput(event) {
    form.code = event.target.value.replace(/\D/g, '').slice(0, 6);
}

function focusCodeInput() {
    codeInput.value?.focus();
}

function submit() {
    if (form.processing || form.code.length !== 6) return;

    form.post(route('two-factor.verify'), {
        onFinish: () => {
            form.reset('code');
            nextTick(() => codeInput.value?.focus());
        },
    });
}

function resend() {
    if (resending.value || secondsLeft.value > 0) return;

    resending.value = true;
    status.value = '';

    router.post(route('two-factor.resend'), {}, {
        preserveScroll: true,
        onSuccess: (page) => {
            secondsLeft.value = 20;
            status.value = page.props.flash?.status || 'הקוד נשלח שוב';
            startTimer();
            nextTick(() => codeInput.value?.focus());
        },
        onFinish: () => {
            resending.value = false;
        },
    });
}
</script>

<template>
    <GuestLayout :title="t('twoFactor.title')">
        <div class="mx-auto w-full max-w-md gap-4">
            <p class="mt-3 text-sm tracking-wide text-slate-900" dir="ltr">
                {{ t('twoFactor.subtitle') }} {{ props.email }}
            </p>

            <div
                v-if="status"
                class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-center text-sm text-green-700"
                role="status"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="mt-6 space-y-6" novalidate>
                <div>
                    <div class="relative" @click="focusCodeInput">
                        <input
                            ref="codeInput"
                            :value="form.code"
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            maxlength="6"
                            dir="ltr"
                            class="absolute inset-0 z-10 h-full w-full cursor-text opacity-0"
                            @input="handleCodeInput"
                        />

                        <div class="flex justify-center gap-2 sm:gap-3">
                            <div
                                v-for="index in 6"
                                :key="index"
                                class="flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-300 bg-white text-xl font-semibold text-slate-900 shadow-sm transition sm:h-14 sm:w-14"
                                :class="{
                                    'border-blue-500 ring-2 ring-blue-100': form.code.length === index - 1,
                                    'border-red-300 ring-2 ring-red-100': form.errors.code,
                                }"
                            >
                                {{ codeChars[index - 1] || '' }}
                            </div>
                        </div>
                    </div>

                    <p v-if="form.errors.code" class="mt-3 text-center text-sm text-red-600">
                        {{ form.errors.code }}
                    </p>
                </div>

                <div class="flex items-center justify-between gap-4 py-4">
                    <div class="flex-1">
                        <PrimaryButton
                            :loading="form.processing"
                            :disabled="form.code.length !== 6"
                            class="justify-center rounded-md"
                            :label="t('twoFactor.submit')"
                            loading-label="מאמת..."
                        />
                    </div>

                    <div class="flex flex-1 items-center justify-center">
                        <div class="flex items-center gap-3">
                            <p class="m-0 text-base font-semibold text-slate-900" dir="ltr">
                                {{ formattedTime }}
                            </p>

                            <button
                                type="button"
                                @click="resend"
                                :disabled="resending || secondsLeft > 0"
                                class="m-0 p-0 leading-none text-sm font-medium text-blue-600 transition hover:text-blue-700 disabled:cursor-not-allowed disabled:text-slate-400 underline"
                            >
                                {{ t('twoFactor.resend') }}
                            </button>
                        </div>
                    </div>
                </div>

                <p class="text-center text-sm">
                    <a
                        :href="route('login')"
                        class="text-slate-400 transition hover:text-slate-600"
                    >
                        {{ t('twoFactor.backToLogin') }}
                    </a>
                </p>
            </form>
        </div>
    </GuestLayout>
</template>