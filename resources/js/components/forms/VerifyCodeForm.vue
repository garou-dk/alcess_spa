<template>
    <form @submit.prevent="handleSubmit()">
        <div class="p-2 mb-4">
            <p v-if="!codeVerified" class="text-sm text-gray-600">
                A verification code has been sent to your email. Please enter it below to verify your account.
            </p>
            <div v-else class="flex flex-col items-center gap-2 p-3 rounded-lg bg-green-50 text-green-700">
                <div class="flex items-center gap-2">
                    <i class="pi pi-check-circle" />
                    <span class="text-sm font-semibold">Code Verified Successfully!</span>
                </div>
                <p class="text-xs">Now you can set your new password below.</p>
            </div>
        </div>

        <div v-if="!codeVerified">
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
                        placeholder="Enter 6-digit code"
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
                    label="Verify Code"
                    fluid
                    :loading="verifyService.request.loading"
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
            </div>
        </div>

        <!-- Password Reset Section (Only shown after code verification) -->
        <div v-else class="fade-in">
            <div class="p-2">
                <InputForm
                    :errors="errors.new_password"
                    id="new_password"
                    labelName="New Password*"
                    tag="label"
                >
                    <Password
                        v-model="form.new_password"
                        :invalid="errors.new_password.length > 0"
                        placeholder="New Password"
                        fluid
                        :toggleMask="true"
                        input-id="new_password"
                    >
                        <template #footer>
                            <div class="flex flex-col gap-1">
                                <span v-if="!hasLowercase" class="text-sm text-red-500">At least one lowercase letter</span>
                                <span v-if="!hasUppercase" class="text-sm text-red-500">At least one uppercase letter</span>
                                <span v-if="!hasNumber" class="text-sm text-red-500">At least one number</span>
                                <span v-if="!hasSymbol" class="text-sm text-red-500">At least one special character</span>
                                <span v-if="!hasEightLength" class="text-sm text-red-500">At least 8 characters</span>
                            </div>
                        </template>
                    </Password>
                </InputForm>
            </div>
            <div class="p-2">
                <InputForm
                    :errors="errors.new_password_confirmation"
                    id="new_password_confirmation"
                    labelName="Confirm New Password*"
                    tag="label"
                >
                    <Password
                        v-model="form.new_password_confirmation"
                        :invalid="errors.new_password_confirmation.length > 0"
                        placeholder="Confirm New Password"
                        fluid
                        :feedback="false"
                        :toggleMask="true"
                        input-id="new_password_confirmation"
                    />
                </InputForm>
            </div>
            <div class="flex justify-center p-2 mt-4">
                <Button
                    type="submit"
                    label="Reset Password"
                    fluid
                    :loading="resetService.request.loading"
                    pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!"
                />
            </div>
        </div>
    </form>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted, onUnmounted, computed } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";

interface Props {
    email: string | null;
    currentPassword: string | null;
}

interface VerifyCodeForm {
    email: string | null;
    current_password: string | null;
    new_password: string | null;
    new_password_confirmation: string | null;
    code: string | null;
}

const props = defineProps<Props>();
const emit = defineEmits(['success']);
const toast = useToast();
const verifyService = useAxiosUtil<any, any>();
const resetService = useAxiosUtil<VerifyCodeForm, any>();
const resendCodeService = useAxiosUtil<any, any>();

const codeVerified = ref(false);

// Countdown timer (15 minutes = 900 seconds)
const timeRemaining = ref(900);
let countdownInterval: number | null = null;

// Resend cooldown timer (60 seconds)
const resendCooldown = ref(60);
let resendCooldownInterval: number | null = null;

// Resend attempts tracking
const resendAttempts = ref(0);
const maxResendAttempts = 3;

// Verification attempts tracking
const verificationAttempts = ref(0);
const maxVerificationAttempts = 3;

const form: VerifyCodeForm = reactive({
    email: props.email,
    current_password: props.currentPassword,
    new_password: null,
    new_password_confirmation: null,
    code: null,
});

const errors = reactive({
    code: [] as string[],
    new_password: [] as string[],
    new_password_confirmation: [] as string[],
});

const hasLowercase = computed(() => /[a-z]/.test(form.new_password || ''));
const hasUppercase = computed(() => /[A-Z]/.test(form.new_password || ''));
const hasNumber = computed(() => /\d/.test(form.new_password || ''));
const hasSymbol = computed(() => /[\W_]/.test(form.new_password || ''));
const hasEightLength = computed(() => form.new_password && form.new_password.length >= 8);

const clearErrors = () => {
    errors.code = [];
    errors.new_password = [];
    errors.new_password_confirmation = [];
};

const formatTime = (seconds: number): string => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const startCountdown = () => {
    countdownInterval = window.setInterval(() => {
        if (timeRemaining.value > 0) timeRemaining.value--;
        else stopCountdown();
    }, 1000);
};

const stopCountdown = () => {
    if (countdownInterval) { clearInterval(countdownInterval); countdownInterval = null; }
};

const startResendCooldown = () => {
    resendCooldown.value = 60;
    resendCooldownInterval = window.setInterval(() => {
        if (resendCooldown.value > 0) resendCooldown.value--;
        else stopResendCooldown();
    }, 1000);
};

const stopResendCooldown = () => {
    if (resendCooldownInterval) { clearInterval(resendCooldownInterval); resendCooldownInterval = null; }
};

const resendCode = async () => {
    if (resendAttempts.value >= maxResendAttempts) {
        toast.error("Maximum resend attempts reached.");
        return;
    }

    const data = {
        email: props.email,
        current_password: props.currentPassword,
    };

    await resendCodeService.post("password/send-code", data).then(() => {
        if (resendCodeService.request.status === 200) {
            resendAttempts.value++;
            toast.success("Verification code resent!");
            stopCountdown();
            timeRemaining.value = 900;
            startCountdown();
            startResendCooldown();
            verificationAttempts.value = 0;
            form.code = null;
            clearErrors();
        } else {
            toast.error(resendCodeService.request.message ?? "Failed to resend code.");
        }
    });
};

const handleVerify = async () => {
    if (!form.code) {
        errors.code = ["Verification code is required"];
        return;
    }

    await verifyService.post("password/verify-code", { email: props.email, code: form.code }).then(() => {
        if (verifyService.request.status === 200) {
            codeVerified.value = true;
            stopCountdown();
        } else {
            verificationAttempts.value++;
            toast.error(verifyService.request.message ?? "Invalid code.");
            if (verifyService.request.errors?.code) {
                errors.code = verifyService.request.errors.code;
            }
        }
    });
};

const handleReset = async () => {
    clearErrors();
    if (!form.new_password) errors.new_password.push("New password is required");
    else if (form.new_password.length < 8) errors.new_password.push("Min 8 characters required");
    
    if (form.new_password !== form.new_password_confirmation) {
        errors.new_password_confirmation.push("Passwords do not match");
    }

    if (errors.new_password.length || errors.new_password_confirmation.length) return;

    await resetService.post("password/reset", form).then(() => {
        if (resetService.request.status === 200) {
            toast.success("Password reset successfully!");
            emit('success');
        } else {
            toast.error(resetService.request.message ?? "Failed to reset password.");
            if (resetService.request.errors) {
                Object.keys(resetService.request.errors).forEach(key => {
                    if (key in errors) errors[key] = resetService.request.errors[key];
                });
            }
        }
    });
};

const handleSubmit = () => {
    if (!codeVerified.value) handleVerify();
    else handleReset();
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

<style scoped>
.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
