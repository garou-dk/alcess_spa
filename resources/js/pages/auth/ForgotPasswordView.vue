<template>
    <div class="auth-page">
        <NavBar mode="guest" :transparent="true" />
        
        <!-- Background Decoration -->
        <div class="bg-decoration">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
        </div>

        <div class="auth-container mt-16">
            <div class="auth-header">
                <h1 class="auth-title">Account Recovery</h1>
                <p class="auth-subtitle">{{ subtitle }}</p>
            </div>

            <div class="auth-card">
                <!-- Step 1: Email Input -->
                <form v-if="step === 'email-input'" @submit.prevent="handleEmailSubmit">
                    <div class="p-2">
                        <InputForm
                            :errors="errors.email"
                            id="email"
                            labelName="Email"
                            tag="label"
                        >
                            <InputText
                                type="email"
                                v-model="form.email"
                                :invalid="errors.email.length > 0"
                                placeholder="Enter your email"
                                fluid
                                id="email"
                                autocomplete="off"
                            />
                        </InputForm>
                    </div>

                    <div class="flex flex-col gap-3 p-2 mt-2">
                        <Button
                            type="submit"
                            label="Send Reset Link"
                            :loading="isLoading"
                            fluid
                            pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!"
                        />
                        <Button
                            type="button"
                            label="Try another way"
                            severity="secondary"
                            outlined
                            fluid
                            pt:root:class="rounded-3xl!"
                            @click="loadRecoveryOptions"
                        />
                    </div>
                </form>

                <!-- Step 2: Method Selection -->
                <div v-else-if="step === 'method-selection'" class="flex flex-col gap-3">
                    <Button
                        label="Use Recovery Code"
                        icon="pi pi-key"
                        severity="secondary"
                        outlined
                        class="p-4 text-left justify-start"
                        @click="step = 'recovery-code'"
                    />
                    <Button
                        label="Answer Security Question"
                        icon="pi pi-question-circle"
                        severity="secondary"
                        outlined
                        class="p-4 text-left justify-start"
                        @click="fetchSecurityQuestion"
                    />
                     <Button
                        label="Go Back"
                        link
                        class="mt-2"
                        @click="step = 'email-input'"
                    />
                </div>

                <!-- Step 3: Recovery Code -->
                <form v-else-if="step === 'recovery-code'" @submit.prevent="verifyRecoveryCode">
                    <div class="p-2">
                        <InputForm
                            :errors="errors.code"
                            id="code"
                            labelName="Recovery Code"
                            tag="label"
                        >
                            <InputText
                                type="text"
                                v-model="form.code"
                                :invalid="errors.code.length > 0"
                                placeholder="Enter recovery code"
                                fluid
                                id="code"
                                autocomplete="off"
                            />
                        </InputForm>
                    </div>
                    <div class="flex flex-col gap-3 p-2 mt-2">
                        <Button
                            type="submit"
                            label="Verify Code"
                            :loading="isLoading"
                            fluid
                            pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!"
                        />
                         <Button
                            type="button"
                            label="Back"
                            link
                            @click="step = 'method-selection'"
                        />
                    </div>
                </form>

                <!-- Step 4: Security Question -->
                <form v-else-if="step === 'security-question'" @submit.prevent="verifySecurityAnswer">
                    <div class="p-2">
                         <div class="mb-4 text-sm font-medium text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-200">
                            {{ securityQuestion }}
                        </div>
                        <InputForm
                            :errors="errors.answer"
                            id="answer"
                            labelName="Answer"
                            tag="label"
                        >
                            <InputText
                                type="text"
                                v-model="form.answer"
                                :invalid="errors.answer.length > 0"
                                placeholder="Your answer"
                                fluid
                                id="answer"
                                autocomplete="off"
                            />
                        </InputForm>
                    </div>
                    <div class="flex flex-col gap-3 p-2 mt-2">
                        <Button
                            type="submit"
                            label="Verify Answer"
                            :loading="isLoading"
                            fluid
                            pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!"
                        />
                         <Button
                            type="button"
                            label="Back"
                            link
                            @click="step = 'method-selection'"
                        />
                    </div>
                </form>

                <!-- Step 5: Reset Password (New Password Input) -->
                <form v-else-if="step === 'reset-password'" @submit.prevent="resetPassword">
                     <div class="p-2 flex flex-col gap-3">
                        <InputForm
                            :errors="errors.password"
                            id="password"
                            labelName="New Password"
                            tag="label"
                        >
                            <InputText
                                type="password"
                                v-model="form.password"
                                :invalid="errors.password.length > 0"
                                placeholder="New password"
                                fluid
                                id="password"
                            />
                        </InputForm>
                         <InputForm
                            :errors="errors.password_confirmation"
                            id="password_confirmation"
                            labelName="Confirm Password"
                            tag="label"
                        >
                            <InputText
                                type="password"
                                v-model="form.password_confirmation"
                                :invalid="errors.password_confirmation.length > 0"
                                placeholder="Confirm new password"
                                fluid
                                id="password_confirmation"
                            />
                        </InputForm>
                    </div>
                    <div class="flex justify-center p-2 mt-2">
                        <Button
                            type="submit"
                            label="Reset Password"
                            :loading="isLoading"
                            fluid
                            pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!"
                        />
                    </div>
                </form>

                <div class="text-center mt-6 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500">
                        Remember your password? 
                        <RouterLink :to="{ name: 'auth.login' }" class="text-blue-600 hover:text-blue-700 font-bold hover:underline">
                            Back to Login
                        </RouterLink>
                    </p>
                </div>
            </div>

            <div class="auth-footer">
                <p>&copy; {{ new Date().getFullYear() }} Alcess. All rights reserved.</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref, computed } from "vue";
import { useToast } from "vue-toastification";
import { useRouter } from "vue-router";
import InputForm from "@/components/InputForm.vue";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import useAxiosUtil from "@/utils/AxiosUtil";
import NavBar from "@/components/NavBar.vue";

const toast = useToast();
const router = useRouter();
const isLoading = ref(false);
const authService = useAxiosUtil();

// Steps: 'email-input', 'method-selection', 'recovery-code', 'security-question', 'reset-password'
const step = ref('email-input');
const securityQuestion = ref('');
const resetToken = ref(''); // Token received after verification

const form = reactive({
    email: '',
    code: '',
    answer: '',
    password: '',
    password_confirmation: ''
});

const errors = reactive({
    email: [] as string[],
    code: [] as string[],
    answer: [] as string[],
    password: [] as string[],
    password_confirmation: [] as string[]
});

const subtitle = computed(() => {
    switch (step.value) {
        case 'email-input': return 'Enter your email to reset your password';
        case 'method-selection': return 'Choose a recovery method';
        case 'recovery-code': return 'Enter your recovery code';
        case 'security-question': return 'Answer your security question';
        case 'reset-password': return 'Create a new password';
        default: return 'Account Recovery';
    }
});

const validateEmail = () => {
    errors.email = [];
    if (!form.email) {
        errors.email.push("Email is required");
        return false;
    }
    return true;
};

// Handle "Send Reset Link"
const handleEmailSubmit = async () => {
    if (!validateEmail()) return;

    isLoading.value = true;
    try {
        await authService.post('password/send-code', { email: form.email });
        
        if (authService.request.status === 200 || authService.request.status === 204) {
            toast.success("Password reset link sent to your email!");
            // form.email = ''; // Keep email in case they want to try another way
        } else {
            toast.error(authService.request.message || "Failed to send reset link.");
            if (authService.request.errors?.email) {
                errors.email = authService.request.errors.email;
            }
        }
    } catch (e) {
        toast.error("An error occurred. Please try again.");
    } finally {
        isLoading.value = false;
    }
};

const loadRecoveryOptions = () => {
    if (!validateEmail()) return;
    step.value = 'method-selection';
};

// Fetch Question
const fetchSecurityQuestion = async () => {
    isLoading.value = true;
    try {
        await authService.post('recovery/question', { email: form.email });
        if (authService.request.status === 200 && authService.request.data?.question) {
            securityQuestion.value = authService.request.data.question;
            step.value = 'security-question';
        } else {
            toast.error("No security question found for this email.");
        }
    } catch (e) {
        toast.error("Failed to retrieve security question.");
    } finally {
        isLoading.value = false;
    }
};

// Verify Code
const verifyRecoveryCode = async () => {
    errors.code = [];
    if (!form.code) {
        errors.code.push("Code is required");
        return;
    }

    isLoading.value = true;
    try {
        // 'recovery/by-code' endpoint should verify and return a token for reset
        await authService.post('recovery/by-code', { email: form.email, code: form.code });
        
        if (authService.request.status === 200 && authService.request.data?.token) {
            resetToken.value = authService.request.data.token;
            step.value = 'reset-password';
            toast.success("Code verified! Please set a new password.");
        } else {
            toast.error(authService.request.message || "Invalid recovery code.");
            if (authService.request.errors?.code) errors.code = authService.request.errors.code;
        }
    } catch (e) {
        toast.error("Verification failed.");
    } finally {
        isLoading.value = false;
    }
};

// Verify Answer
const verifySecurityAnswer = async () => {
     errors.answer = [];
    if (!form.answer) {
        errors.answer.push("Answer is required");
        return;
    }

    isLoading.value = true;
    try {
        await authService.post('recovery/verify-answer', { email: form.email, answer: form.answer });
        
        if (authService.request.status === 200 && authService.request.data?.token) {
            resetToken.value = authService.request.data.token;
            step.value = 'reset-password';
             toast.success("Answer verified! Please set a new password.");
        } else {
            toast.error(authService.request.message || "Incorrect answer.");
            if (authService.request.errors?.answer) errors.answer = authService.request.errors.answer;
        }
    } catch (e) {
         toast.error("Verification failed.");
    } finally {
        isLoading.value = false;
    }
};

// Reset Password
const resetPassword = async () => {
    errors.password = [];
    errors.password_confirmation = [];
    if (!form.password) {
        errors.password.push("Password is required");
        return;
    }
    if (form.password !== form.password_confirmation) {
        errors.password_confirmation.push("Passwords do not match");
        return;
    }

    isLoading.value = true;
    try {
        await authService.post('password/reset', { 
            email: form.email, 
            password: form.password, 
            password_confirmation: form.password_confirmation,
            token: resetToken.value 
        });
        
        if (authService.request.status === 200) {
            toast.success("Password reset successfully! Please login.");
            router.push({ name: 'auth.login' });
        } else {
            toast.error(authService.request.message || "Failed to reset password.");
             if (authService.request.errors?.password) errors.password = authService.request.errors.password;
        }
    } catch (e) {
        toast.error("An error occurred during reset.");
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap');

.auth-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #f8fafc;
    font-family: 'Inter', sans-serif;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

/* Background Decoration */
.bg-decoration {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
}

.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.4;
}

.blob-1 {
    width: 500px;
    height: 500px;
    background-color: #bfdbfe;
    top: -100px;
    right: -100px;
    animation: move 20s infinite alternate;
}

.blob-2 {
    width: 400px;
    height: 400px;
    background-color: #dbeafe;
    bottom: -50px;
    left: -50px;
    animation: move 15s infinite alternate-reverse;
}

@keyframes move {
    from { transform: translate(0, 0); }
    to { transform: translate(50px, 50px); }
}

.auth-container {
    width: 100%;
    max-width: 450px;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.auth-header {
    text-align: center;
}

.auth-title {
    font-family: 'Poppins', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.auth-subtitle {
    color: #64748b;
    font-size: 0.9375rem;
}

.auth-card {
    background: white;
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.auth-footer {
    text-align: center;
    color: #94a3b8;
    font-size: 0.8125rem;
}
</style>
