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

const otpDigits = computed(() => {
    const padded = (form.code || '').slice(0, 6).padEnd(6, '');
    return padded.split('');
});

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

    router.post(
        route('two-factor.resend'),
        {},
        {
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
        }
    );
}
</script>

<template>
    <GuestLayout>
        <div class="mx-auto w-full max-w-md">
                <h1 class="text-2xl font-bold text-slate-900">
                    קוד אימות נשלח אל הטלפון שלך
                </h1>

                <p class="mt-3 text-lg font-semibold tracking-wide text-slate-900" dir="ltr">
                    {{ props.phone }}
                </p>

                <p class="mt-4 text-sm text-slate-500">
                    שלחנו את הקוד למספר הטלפון המסתיים ב-
                </p>

                <div class="mt-2 flex items-center justify-center gap-3" dir="ltr">
                    <span
                        v-for="digit in props.phoneEnding.split('')"
                        :key="digit"
                        class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-lg font-bold text-slate-900"
                    >
                        {{ digit }}
                    </span>
                </div>

                <div
                    v-if="status"
                    class="mt-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-center text-sm text-green-700"
                    role="status"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-6" novalidate>
                    <div>
                        <div class="relative">
                            <input
                                ref="codeInput"
                                :value="form.code"
                                type="text"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                maxlength="6"
                                class="absolute inset-0 opacity-0 pointer-events-none"
                                @input="handleCodeInput"
                            />

                            <div
                                class="flex justify-center gap-2 sm:gap-3"
                                @click="focusCodeInput"
                            >
                                <div
                                    v-for="(digit, index) in otpDigits"
                                    :key="index"
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-300 bg-white text-xl font-semibold text-slate-900 shadow-sm transition focus-within:border-blue-500 sm:h-14 sm:w-14"
                                    :class="{
                                        'border-blue-500 ring-2 ring-blue-100': form.code.length === index,
                                        'border-red-300 ring-2 ring-red-100': form.errors.code,
                                    }"
                                >
                                    {{ digit }}
                                </div>
                            </div>
                        </div>

                        <p v-if="form.errors.code" class="mt-3 text-center text-sm text-red-600">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <PrimaryButton
                                :loading="form.processing"
                                :disabled="form.code.length !== 6"
                                class="justify-center rounded-md"
                                label="אימות קוד"
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
                                    class="m-0 p-0 leading-none text-sm font-medium text-blue-600 transition hover:text-blue-700 disabled:cursor-not-allowed disabled:text-slate-400"
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