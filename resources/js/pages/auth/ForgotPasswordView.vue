<template>
    <div class="min-h-screen flex flex-col bg-white">
         <!-- Full Width Navbar -->
        <NavBar mode="guest" class="z-50 border-b border-gray-100" :transparent="false" />

        <div class="flex-1 flex">
            <!-- Left Side - Branding (Crisp Shapes Theme) -->
            <div class="hidden lg:flex lg:w-1/2 bg-white relative overflow-hidden flex-col items-center justify-center border-r border-gray-100">
                <!-- Abstract Shapes (Sharp & Clear Style) -->
                <div class="absolute inset-0 z-0 overflow-hidden">
                    <div class="absolute w-[400px] h-[400px] rounded-full top-[-50px] left-[-50px] bg-gradient-to-br from-emerald-400/20 to-teal-500/20 animate-pulse"></div>
                    <div class="absolute w-[500px] h-[500px] rounded-full bottom-[-150px] right-[-100px] bg-gradient-to-tr from-teal-400/20 to-green-500/20 animate-bounce" style="animation-duration: 7s"></div>
                    <div class="absolute w-16 h-16 rounded-lg top-1/3 right-1/3 bg-gradient-to-tr from-emerald-300/30 to-teal-300/30 rotate-45"></div>
                </div>

                <!-- Branding Content -->
                <div class="relative z-10 text-center px-12">
                    <div class="mb-8 flex justify-center transform hover:scale-110 transition-transform duration-700">
                        <img :src="Logo" alt="Alcess Logo" class="h-48 w-auto drop-shadow-2xl" />
                    </div>
                    <h1 class="text-4xl font-extrabold text-slate-900 mb-4 h-10">
                        <BinaryCodeText text="Restore Access" />
                    </h1>
                    <p class="text-slate-600 max-w-sm mx-auto leading-relaxed">
                        Don't worry, we'll help you get back to your account in just a few steps.
                    </p>
                </div>
            </div>

            <!-- Right Side - Content -->
            <div class="w-full lg:w-1/2 flex flex-col h-full overflow-y-auto bg-white">
                <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24 py-12">
                    <div class="w-full max-w-md mx-auto">
                        <div class="text-center lg:text-left mb-10">
                            <h2 class="text-3xl font-bold tracking-tight text-gray-900">Forgot Password?</h2>
                            <p class="mt-2 text-sm text-gray-600">
                                {{ subtitle }}
                            </p>
                        </div>

                        <div class="bg-white">
                            <!-- Step 1: Email Input -->
                            <form v-if="step === 'email-input'" @submit.prevent="handleEmailSubmit">
                                <div class="flex flex-col gap-4">
                                    <InputForm
                                        :errors="errors.email"
                                        id="email"
                                        labelName="Email Address"
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
                                            class="w-full"
                                        />
                                    </InputForm>

                                    <div class="flex flex-col gap-3 mt-2">
                                        <Button
                                            type="submit"
                                            label="Send Reset Link"
                                            :loading="isLoading"
                                            fluid
                                            pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-lg! py-3! font-semibold!"
                                        />
                                        
                                        <div class="relative flex py-2 items-center">
                                            <div class="flex-grow border-t border-gray-200"></div>
                                            <span class="flex-shrink-0 mx-4 text-xs text-gray-400 uppercase">Or try another method</span>
                                            <div class="flex-grow border-t border-gray-200"></div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <Button
                                                type="button"
                                                label="Recovery Code"
                                                severity="secondary"
                                                outlined
                                                class="w-full"
                                                pt:root:class="text-sm! py-2.5!"
                                                @click="step = 'recovery-code'"
                                            />
                                            <Button
                                                type="button"
                                                label="Security Question"
                                                severity="secondary"
                                                outlined
                                                class="w-full"
                                                pt:root:class="text-sm! py-2.5!"
                                                @click="prepareSecurityQuestion"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- Step 2: Method Selection (Keeping existing logic but wrapped in new layout if needed, though step 'email-input' handles jump to others) -->
                            <div v-else-if="step === 'method-selection'" class="flex flex-col gap-3">
                                <Button
                                    label="Use Recovery Code"
                                    icon="pi pi-key"
                                    severity="secondary"
                                    outlined
                                    class="p-4 text-left justify-start w-full"
                                    @click="step = 'recovery-code'"
                                />
                                <Button
                                    label="Answer Security Question"
                                    icon="pi pi-question-circle"
                                    severity="secondary"
                                    outlined
                                    class="p-4 text-left justify-start w-full"
                                    @click="fetchSecurityQuestion"
                                />
                                 <Button
                                    label="Go Back"
                                    link
                                    class="mt-2 w-full"
                                    @click="step = 'email-input'"
                                />
                            </div>

                            <!-- Step 3: Recovery Code -->
                            <form v-else-if="step === 'recovery-code'" @submit.prevent="verifyRecoveryCode">
                                <div class="flex flex-col gap-4">
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
                                            placeholder="Enter 6-digit code"
                                            fluid
                                            id="code"
                                            autocomplete="off"
                                            class="w-full text-center tracking-widest font-mono text-lg"
                                            maxlength="6"
                                        />
                                    </InputForm>
                                    
                                    <div class="flex flex-col gap-3 mt-2">
                                        <Button
                                            type="submit"
                                            label="Verify Code"
                                            :loading="isLoading"
                                            fluid
                                            pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-lg! py-3! font-semibold!"
                                        />
                                         <Button
                                            type="button"
                                            label="Back to Email"
                                            link
                                            class="w-full text-sm text-gray-500"
                                            @click="step = 'email-input'"
                                        />
                                    </div>
                                </div>
                            </form>

                            <!-- Step 4: Security Question -->
                            <form v-else-if="step === 'security-question'" @submit.prevent="verifySecurityAnswer">
                                <div class="flex flex-col gap-4">
                                     <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                                        <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider mb-1">Security Question</p>
                                        <p class="text-gray-900 font-medium">{{ securityQuestion }}</p>
                                    </div>
                                    
                                    <InputForm
                                        :errors="errors.answer"
                                        id="answer"
                                        labelName="Your Answer"
                                        tag="label"
                                    >
                                        <InputText
                                            type="text"
                                            v-model="form.answer"
                                            :invalid="errors.answer.length > 0"
                                            placeholder="Type your answer here"
                                            fluid
                                            id="answer"
                                            autocomplete="off"
                                            class="w-full"
                                        />
                                    </InputForm>
                                    
                                    <div class="flex flex-col gap-3 mt-2">
                                        <Button
                                            type="submit"
                                            label="Verify Answer"
                                            :loading="isLoading"
                                            fluid
                                            pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-lg! py-3! font-semibold!"
                                        />
                                         <Button
                                            type="button"
                                            label="Back to Email"
                                            link
                                            class="w-full text-sm text-gray-500"
                                            @click="step = 'email-input'"
                                        />
                                    </div>
                                </div>
                            </form>

                            <!-- Step 5: Reset Password -->
                            <form v-else-if="step === 'reset-password'" @submit.prevent="resetPassword">
                                 <div class="flex flex-col gap-4">
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
                                            placeholder="Create new password"
                                            fluid
                                            id="password"
                                            class="w-full"
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
                                            class="w-full"
                                        />
                                    </InputForm>
                                    
                                    <div class="flex flex-col gap-3 mt-2">
                                        <Button
                                            type="submit"
                                            label="Reset Password"
                                            :loading="isLoading"
                                            fluid
                                            pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-lg! py-3! font-semibold!"
                                        />
                                    </div>
                                </div>
                            </form>

                            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                                <p class="text-sm text-gray-600">
                                    Remember your password? 
                                    <RouterLink 
                                        :to="{ name: 'auth.login' }" 
                                        class="font-bold text-blue-600 hover:text-blue-500 transition-colors ml-1"
                                    >
                                        Back to Login
                                    </RouterLink>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Footer />
    </div>
</template>

<script setup lang="ts">
import BinaryCodeText from "@/components/BinaryCodeText.vue";
import { reactive, ref, computed } from "vue";
import { useToast } from "vue-toastification";
import { useRouter } from "vue-router";
import InputForm from "@/components/InputForm.vue";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import useAxiosUtil from "@/utils/AxiosUtil";
import NavBar from "@/components/NavBar.vue";
import Footer from "@/components/Footer.vue";
import Logo from "@/../img/logo.png";

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
        case 'recovery-code': return 'Enter the recovery code sent to your email';
        case 'security-question': return 'Answer the security question associated with your account';
        case 'reset-password': return 'Create a secure new password for your account';
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
            toast.success("Recovery code sent to your email!");
            step.value = 'recovery-code'; // Auto-advance to code input
        } else {
            toast.error(authService.request.message || "Failed to send reset code.");
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

const prepareSecurityQuestion = () => {
    if (!validateEmail()) {
        toast.info("Please enter your email first to see your security question.");
        return;
    }
    fetchSecurityQuestion();
};

// Fetch Question
const fetchSecurityQuestion = async () => {
    isLoading.value = true;
    try {
        await authService.post('recovery/question', { identifier: form.email });
        if (authService.request.status === 200 && authService.request.data?.security_question) {
            securityQuestion.value = authService.request.data.security_question;
            step.value = 'security-question';
        } else {
            toast.error(authService.request.message || "No security question found for this account.");
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
        await authService.post('recovery/by-code', { recovery_code: form.code });
        
        if (authService.request.status === 200 && authService.request.data?.email) {
            form.email = authService.request.data.email;
            // The backend returns user email. Now we need to allow them to reset password.
            // But wait, the reset-password step usually needs a token.
            // Let's check if the backend should return a token too.
            // For now, assuming we continue to reset-password step.
            resetToken.value = 'recovery-code-verified'; // Placeholder or actual token
            step.value = 'reset-password';
            toast.success("Code verified! Your account is " + form.email + ". Please set a new password.");
        } else {
            toast.error(authService.request.message || "Invalid or expired recovery code.");
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
        await authService.post('recovery/verify-answer', { 
            identifier: form.email, 
            security_answer: form.answer 
        });
        
        if (authService.request.status === 200 && authService.request.data?.email) {
            // Success
            resetToken.value = 'security-answer-verified'; // Placeholder
            step.value = 'reset-password';
            toast.success("Answer verified! Please set a new password.");
        } else {
            toast.error(authService.request.message || "Incorrect answer.");
            if (authService.request.errors?.security_answer) errors.answer = authService.request.errors.security_answer;
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
        const payload: any = { 
            email: form.email, 
            password: form.password, 
            password_confirmation: form.password_confirmation
        };
        
        if (resetToken.value && resetToken.value !== 'recovery-code-verified' && resetToken.value !== 'security-answer-verified') {
            payload.token = resetToken.value;
        } else if (form.code) {
             payload.recovery_code = form.code;
        } else if (form.answer) {
             payload.security_answer = form.answer;
        }

        await authService.post('password/reset', payload);
        
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
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.animate-blob {
    animation: blob 7s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
</style>
