<template>
    <div class="auth-page">
        <!-- Background Decoration -->
        <div class="bg-decoration">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
        </div>

        <div class="auth-container">
            <div class="auth-header">
                <RouterLink :to="{ name: 'home' }" class="logo-link">
                    <img :src="Logo" alt="Alcess Logo" class="auth-logo" />
                </RouterLink>
                <h1 class="auth-title">Forgot Password?</h1>
                <p class="auth-subtitle">Enter your email to reset your password</p>
            </div>

            <div class="auth-card">
                <form @submit.prevent="handleSubmit">
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

                    <div class="flex justify-center p-2 mt-2">
                        <Button
                            type="submit"
                            label="Send Reset Link"
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
import Logo from "@/../img/logo.png";
import { reactive, ref } from "vue";
import { useToast } from "vue-toastification";
import InputForm from "@/components/InputForm.vue";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import useAxiosUtil from "@/utils/AxiosUtil";

const toast = useToast();
const isLoading = ref(false);
const authService = useAxiosUtil();

const form = reactive({
    email: '',
});

const errors = reactive({
    email: [] as string[],
});

const validate = () => {
    errors.email = [];
    if (!form.email) {
        errors.email.push("Email is required");
        return false;
    }
    return true;
};

const handleSubmit = async () => {
    if (!validate()) return;

    isLoading.value = true;
    try {
        // Assuming standard Laravel password reset endpoint
        await authService.post('forgot-password', { email: form.email });
        
        if (authService.request.status === 200 || authService.request.status === 204) {
            toast.success("Password reset link sent to your email!");
            form.email = '';
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

.logo-link {
    display: inline-block;
    margin-bottom: 1.5rem;
    transition: transform 0.3s ease;
}

.logo-link:hover {
    transform: scale(1.05);
}

.auth-logo {
    height: 80px;
    width: auto;
    object-fit: contain;
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
