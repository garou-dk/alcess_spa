<template>
    <form @submit.prevent="handleSubmit()">
        <div class="p-2 mb-4">
            <div v-if="!codeVerified" class="text-sm text-gray-600">
                <p v-if="method === 'email'">A verification code has been sent to your email. Please enter it below.</p>
                <p v-else-if="method === 'recovery_key'">Please enter your account recovery code below.</p>
                <p v-else-if="method === 'security_question'">Please answer your security question below.</p>
            </div>
            <div v-else class="flex flex-col items-center gap-2 p-3 rounded-lg bg-green-50 text-green-700">
                <div class="flex items-center gap-2">
                    <i class="pi pi-check-circle" />
                    <span class="text-sm font-semibold">Identity Verified Successfully!</span>
                </div>
                <p class="text-xs">Now you can set your new password below.</p>
            </div>
        </div>

        <div v-if="!codeVerified">
            <!-- Email Method (Code Verification) -->
            <div v-if="method === 'email'" class="space-y-4">
                <!-- Countdown Timer -->
                <div class="p-2 mb-2">
                    <div class="flex items-center justify-center gap-2 rounded-lg bg-blue-50 p-3">
                        <i class="pi pi-clock text-blue-600" />
                        <span class="text-sm font-semibold" :class="timeRemaining <= 60 ? 'text-red-600' : 'text-blue-600'">
                            Code expires in: {{ formatTime(timeRemaining) }}
                        </span>
                    </div>
                </div>
                
                <div class="p-2">
                    <InputForm :errors="errors.code" id="code" labelName="Verification Code*" tag="label">
                        <InputText v-model="form.code" placeholder="Enter 6-digit code" fluid id="code" autocomplete="off" />
                    </InputForm>
                </div>

                <div class="flex justify-center p-2">
                    <Button type="submit" label="Verify Code" fluid :loading="verifyService.request.loading" pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!" />
                </div>

                <!-- Resend Code Button -->
                <div class="flex flex-col items-center p-2">
                    <Button type="button" :label="resendCooldown > 0 ? `Resend Code (${resendCooldown}s)` : 'Resend Code'" text :loading="resendCodeService.request.loading" :disabled="resendCooldown > 0 || resendAttempts >= maxResendAttempts" @click="resendCode()" pt:root:class="text-blue-600! hover:text-blue-700!" />
                    <span class="text-xs text-gray-500 mt-1">{{ resendAttempts }}/{{ maxResendAttempts }} resend attempts used</span>
                </div>
            </div>

            <!-- Recovery Key Method -->
            <div v-else-if="method === 'recovery_key'" class="space-y-4">
                <div class="p-2">
                    <InputForm :errors="errors.recovery_key" id="recovery_key" labelName="Recovery Code*" tag="label">
                        <InputText v-model="form.recovery_key" placeholder="XXXX-XXXX-XXXX-XXXX" fluid id="recovery_key" autocomplete="off" />
                    </InputForm>
                    <p class="text-[10px] text-gray-500 mt-1 text-center">Enter the recovery code that was provided to you in your profile settings or email.</p>
                </div>
                <div class="flex justify-center p-2">
                    <Button type="submit" label="Verify Recovery Code" fluid :loading="verifyService.request.loading" pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!" />
                </div>
            </div>

            <!-- Security Question Method -->
            <div v-else-if="method === 'security_question'" class="space-y-4">
                <div v-if="loadingQuestion" class="flex flex-col items-center p-4">
                    <i class="pi pi-spin pi-spinner text-2xl text-blue-600 mb-2" />
                    <p class="text-sm text-gray-500">Loading your security question...</p>
                </div>
                <div v-else class="p-2">
                    <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Your Question:</span>
                        <p class="text-sm font-medium text-gray-800">{{ securityQuestion }}</p>
                    </div>
                    <InputForm :errors="errors.answer" id="answer" labelName="Your Answer*" tag="label">
                        <InputText v-model="form.answer" type="password" placeholder="Type your answer here..." fluid id="answer" autocomplete="off" />
                    </InputForm>
                </div>
                <div class="flex justify-center p-2">
                    <Button type="submit" label="Verify Answer" fluid :loading="verifyService.request.loading" pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!" />
                </div>
            </div>
        </div>

        <!-- Password Reset Section (Only shown after successful verification) -->
        <div v-else class="fade-in">
            <div class="p-2">
                <InputForm :errors="errors.new_password" id="new_password" labelName="New Password*" tag="label">
                    <Password v-model="form.new_password" :invalid="errors.new_password.length > 0" placeholder="New Password" fluid :toggleMask="true" input-id="new_password">
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
                <InputForm :errors="errors.new_password_confirmation" id="new_password_confirmation" labelName="Confirm New Password*" tag="label">
                    <Password v-model="form.new_password_confirmation" :invalid="errors.new_password_confirmation.length > 0" placeholder="Confirm New Password" fluid :feedback="false" :toggleMask="true" input-id="new_password_confirmation" />
                </InputForm>
            </div>
            <div class="flex justify-center p-2 mt-4">
                <Button type="submit" label="Reset Password" fluid :loading="resetService.request.loading" pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!" />
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
    method?: 'email' | 'recovery_key' | 'security_question';
}

interface VerifyCodeForm {
    email: string | null;
    current_password: string | null;
    new_password: string | null;
    new_password_confirmation: string | null;
    code: string | null;
    recovery_key: string | null;
    answer: string | null;
}

const props = defineProps<Props>();
const emit = defineEmits(['success']);
const toast = useToast();
const verifyService = useAxiosUtil<any, any>();
const resetService = useAxiosUtil<VerifyCodeForm, any>();
const resendCodeService = useAxiosUtil<any, any>();

const codeVerified = ref(false);
const securityQuestion = ref<string | null>(null);
const loadingQuestion = ref(false);

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
    recovery_key: null,
    answer: null,
});

const errors = reactive({
    code: [] as string[],
    recovery_key: [] as string[],
    answer: [] as string[],
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
    errors.recovery_key = [];
    errors.answer = [];
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

const fetchSecurityQuestion = async () => {
    if (!props.email || props.method !== 'security_question') return;
    
    loadingQuestion.value = true;
    try {
        const response = await verifyService.axios.post("password/fetch-security-question", { email: props.email });
        securityQuestion.value = response.data.question;
    } catch (e: any) {
        toast.error(e.response?.data?.message || "Failed to fetch security question.");
    } finally {
        loadingQuestion.value = false;
    }
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

const handleVerifyCode = async () => {
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

const handleVerifyRecoveryKey = async () => {
    if (!form.recovery_key) {
        errors.recovery_key = ["Recovery code is required"];
        return;
    }

    await verifyService.post("password/verify-recovery-key", { email: props.email, recovery_key: form.recovery_key }).then(() => {
        if (verifyService.request.status === 200) {
            codeVerified.value = true;
        } else {
            toast.error(verifyService.request.message ?? "Invalid recovery code.");
            if (verifyService.request.errors?.recovery_key) {
                errors.recovery_key = verifyService.request.errors.recovery_key;
            }
        }
    });
};

const handleVerifySecurityAnswer = async () => {
    if (!form.answer) {
        errors.answer = ["Answer is required"];
        return;
    }

    await verifyService.post("password/verify-security-answer", { email: props.email, answer: form.answer }).then(() => {
        if (verifyService.request.status === 200) {
            codeVerified.value = true;
        } else {
            toast.error(verifyService.request.message ?? "Incorrect answer.");
            if (verifyService.request.errors?.answer) {
                errors.answer = verifyService.request.errors.answer;
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
    if (!codeVerified.value) {
        if (props.method === 'email') handleVerifyCode();
        else if (props.method === 'recovery_key') handleVerifyRecoveryKey();
        else if (props.method === 'security_question') handleVerifySecurityAnswer();
    } else {
        handleReset();
    }
};

onMounted(() => {
    if (props.method === 'email') {
        startCountdown();
        startResendCooldown();
    } else if (props.method === 'security_question') {
        fetchSecurityQuestion();
    }
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
