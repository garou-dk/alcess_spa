<template>
    <form @submit.prevent="handleSubmit()">
        <div class="p-2 mb-4">
            <p class="text-sm text-gray-600">
                A verification code has been sent to your email. Please enter it below to complete the password reset.
            </p>
        </div>

        <!-- Countdown Timer -->
        <div class="p-2 mb-2">
            <div class="flex items-center justify-center gap-2 rounded-lg bg-blue-50 p-3">
                <i class="pi pi-clock text-blue-600" />
                <span class="text-sm font-semibold" :class="timeRemaining <= 60 ? 'text-red-600' : 'text-blue-600'">
                    Code expires in: {{ formatTime(timeRemaining) }}
                </span>
            </div>
            <div v-if="timeRemaining === 0" class="mt-2 text-center text-sm text-red-600">
                The verification code has expired. Please request a new one.
            </div>
        </div>
        
        <div class="p-2">
            <InputForm
                :errors="errors.code"
                id="code"
                labelName="Verification Code*"
                tag="label"
            >
                <InputText
                    type="text"
                    v-model="form.code"
                    :invalid="errors.code.length > 0"
                    placeholder="Enter verification code"
                    fluid
                    id="code"
                    autocomplete="off"
                    :disabled="timeRemaining === 0 || verificationAttempts >= maxVerificationAttempts"
                />
            </InputForm>
            <div v-if="verificationAttempts > 0" class="mt-2 text-center">
                <span class="text-xs" :class="verificationAttempts >= maxVerificationAttempts ? 'text-red-600' : 'text-orange-600'">
                    {{ verificationAttempts }}/{{ maxVerificationAttempts }} verification attempts used
                </span>
            </div>
        </div>

        <div class="flex justify-center p-2">
            <Button
                type="submit"
                label="Confirm"
                fluid
                :loading="resetService.request.loading"
                :disabled="timeRemaining === 0 || verificationAttempts >= maxVerificationAttempts"
                pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!"
            />
        </div>

        <!-- Resend Code Button -->
        <div class="flex flex-col items-center p-2">
            <Button
                type="button"
                :label="resendCooldown > 0 ? `Resend Code (${resendCooldown}s)` : 'Resend Code'"
                text
                :loading="resendCodeService.request.loading"
                :disabled="resendCooldown > 0 || resendCodeService.request.loading || resendAttempts >= maxResendAttempts"
                @click="resendCode()"
                pt:root:class="text-blue-600! hover:text-blue-700!"
            />
            <span class="text-xs text-gray-500 mt-1">
                {{ resendAttempts }}/{{ maxResendAttempts }} resend attempts used
            </span>
            <span v-if="resendAttempts >= maxResendAttempts" class="text-xs text-red-600 mt-1">
                Maximum resend attempts reached
            </span>
        </div>
    </form>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted, onUnmounted } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";

interface Props {
    email: string | null;
    currentPassword: string | null;
    newPassword: string | null;
    newPasswordConfirmation: string | null;
}

interface VerifyCodeForm {
    email: string | null;
    current_password: string | null;
    new_password: string | null;
    new_password_confirmation: string | null;
    code: string | null;
}

interface VerifyCodeErrors {
    code: string[];
}

const props = defineProps<Props>();
const emit = defineEmits(['success']);
const toast = useToast();
const resetService = useAxiosUtil<VerifyCodeForm, any>();
const resendCodeService = useAxiosUtil<any, any>();

// Countdown timer (15 minutes = 900 seconds)
const timeRemaining = ref(900);
let countdownInterval: number | null = null;

// Resend cooldown timer (60 seconds)
const resendCooldown = ref(60);
let resendCooldownInterval: number | null = null;

// Resend attempts tracking (max 3 attempts)
const resendAttempts = ref(0);
const maxResendAttempts = 3;

// Verification attempts tracking (max 3 attempts)
const verificationAttempts = ref(0);
const maxVerificationAttempts = 3;

const form: VerifyCodeForm = reactive({
    email: props.email,
    current_password: props.currentPassword,
    new_password: props.newPassword,
    new_password_confirmation: props.newPasswordConfirmation,
    code: null,
});

const errors: VerifyCodeErrors = reactive({
    code: [],
});

const clearErrors = () => {
    errors.code = [];
};

const formatTime = (seconds: number): string => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const startCountdown = () => {
    countdownInterval = window.setInterval(() => {
        if (timeRemaining.value > 0) {
            timeRemaining.value--;
        } else {
            stopCountdown();
            toast.error("Verification code has expired. Please request a new one.");
        }
    }, 1000);
};

const stopCountdown = () => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
        countdownInterval = null;
    }
};

const startResendCooldown = () => {
    resendCooldown.value = 60;
    resendCooldownInterval = window.setInterval(() => {
        if (resendCooldown.value > 0) {
            resendCooldown.value--;
        } else {
            stopResendCooldown();
        }
    }, 1000);
};

const stopResendCooldown = () => {
    if (resendCooldownInterval) {
        clearInterval(resendCooldownInterval);
        resendCooldownInterval = null;
    }
};

const resendCode = async () => {
    if (resendAttempts.value >= maxResendAttempts) {
        toast.error("Maximum resend attempts reached. Please try again later.");
        return;
    }

    const data = {
        email: props.email,
        current_password: props.currentPassword,
        new_password: props.newPassword,
        new_password_confirmation: props.newPasswordConfirmation,
    };

    await resendCodeService.post("password/send-code", data).then(() => {
        if (resendCodeService.request.status === 200) {
            resendAttempts.value++;
            toast.success(`Verification code resent to your email! (${resendAttempts.value}/${maxResendAttempts} attempts)`);
            // Reset the main countdown timer
            stopCountdown();
            timeRemaining.value = 900;
            startCountdown();
            // Start resend cooldown
            startResendCooldown();
            // Reset verification attempts when new code is sent
            verificationAttempts.value = 0;
            // Clear the code input
            form.code = null;
            clearErrors();
        } else {
            toast.error(resendCodeService.request.message ?? "Failed to resend code.");
        }
    });
};

const handleSubmit = async () => {
    clearErrors();
    
    if (timeRemaining.value === 0) {
        toast.error("Verification code has expired. Please request a new one.");
        return;
    }

    if (verificationAttempts.value >= maxVerificationAttempts) {
        toast.error("Maximum verification attempts exceeded. Please request a new code.");
        return;
    }
    
    if (!form.code) {
        errors.code.push("Verification code is required");
        toast.error("Please enter the verification code.");
        return;
    }

    await resetService.post("password/reset", form).then(() => {
        if (resetService.request.status === 200) {
            stopCountdown();
            toast.success("Password reset successfully!");
            emit('success');
        } else {
            // Increment verification attempts on failure
            if (resetService.request.status === 401 || resetService.request.status === 429) {
                verificationAttempts.value++;
            }
            
            toast.error(resetService.request.message ?? "Failed to reset password.");
            if (resetService.request.errors) {
                Object.keys(resetService.request.errors).forEach((key) => {
                    if (key === 'code') {
                        errors.code = resetService.request.errors[key];
                    }
                });
            }
        }
    });
};

onMounted(() => {
    startCountdown();
    startResendCooldown();
});

onUnmounted(() => {
    stopCountdown();
    stopResendCooldown();
});
</script>
