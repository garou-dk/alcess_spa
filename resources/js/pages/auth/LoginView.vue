<template>
    <div class="auth-page">
        <NavBar mode="guest" />
        
        <!-- Background Decoration -->
        <div class="bg-decoration">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
        </div>

        <div class="auth-container mt-16">
            <div class="auth-header">
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to your account to continue</p>
            </div>

            <div class="auth-card">
                <LoginForm :admin="props.admin" @success="onLoginSuccess">
                    <template #footer>
                        <div class="text-center mt-6">
                            <RouterLink :to="{ name: 'auth.forgot-password' }" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">
                                Forgot password?
                            </RouterLink>
                        </div>
                        <div class="text-center mt-4 pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500">
                                Don't have an account? 
                                <RouterLink :to="{ name: 'auth.register' }" class="text-blue-600 hover:text-blue-700 font-bold hover:underline">
                                    Sign up
                                </RouterLink>
                            </p>
                        </div>
                    </template>
                </LoginForm>
            </div>

            <div class="auth-footer">
                <p>&copy; {{ new Date().getFullYear() }} Alcess. All rights reserved.</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Logo from "@/../img/logo.png";
import LoginForm from "@/components/forms/LoginForm.vue";
import NavBar from "@/components/NavBar.vue";
import { useRouter } from "vue-router";

const router = useRouter();

const props = defineProps<{
    admin?: boolean;
}>();

const onLoginSuccess = () => {
    // Redirection is handled by LoginForm component logic
    // But we can add any page-specific post-login logic here if needed
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.auth-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    /* align-items: center; Removed to allow full width children */
    justify-content: flex-start; /* Changed from center to allow spacer or margin auto to handle centering */
    background-color: #f8fafc;
    background-image: 
        radial-gradient(at 0% 0%, hsla(253,16%,7%,0) 0, hsla(253,16%,7%,0) 50%), 
        radial-gradient(at 50% 0%, hsla(225,39%,30%,0.1) 0, hsla(225,39%,30%,0) 50%), 
        radial-gradient(at 100% 0%, hsla(339,49%,30%,0) 0, hsla(339,49%,30%,0) 50%);
    font-family: 'Inter', sans-serif;
    /* padding: 1.5rem; Removed padding from parent to let navbar touch edges */
    position: relative;
    overflow: hidden;
}

/* Background Decoration */
.bg-decoration {
    position: absolute;
    inset: 0;
    z-index: 0;
    opacity: 0.6;
    background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
    background-size: 32px 32px;
    mask-image: radial-gradient(circle at center, black 40%, transparent 100%);
}

.auth-container {
    width: 100%;
    max-width: 440px;
    margin: auto; /* Center vertically and horizontally */
    padding: 1.5rem; 
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 2rem;
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.auth-header {
    text-align: center;
}

.auth-title {
    font-family: 'Inter', sans-serif;
    font-size: 1.875rem;
    font-weight: 800;
    letter-spacing: -0.025em;
    color: #0f172a;
    margin-bottom: 0.5rem;
}

.auth-subtitle {
    color: #64748b;
    font-size: 0.9375rem;
    font-weight: 500;
}

.auth-card {
    background: white;
    padding: 2.5rem;
    border-radius: 1.5rem;
    box-shadow: 
        0 4px 6px -1px rgba(0, 0, 0, 0.02),
        0 20px 25px -5px rgba(0, 0, 0, 0.05),
        0 0 0 1px rgba(0, 0, 0, 0.03); 
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.auth-card:hover {
    box-shadow: 
        0 10px 15px -3px rgba(0, 0, 0, 0.02),
        0 25px 30px -5px rgba(0, 0, 0, 0.06),
        0 0 0 1px rgba(0, 0, 0, 0.03); 
    transform: translateY(-2px);
}

.auth-footer {
    text-align: center;
    color: #94a3b8;
    font-size: 0.8125rem;
    font-weight: 500;
}
</style>
