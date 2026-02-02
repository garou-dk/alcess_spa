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
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join us to start shopping</p>
            </div>

            <div class="auth-card">
                <RegisterForm @success="onRegisterSuccess">
                    <template #footer>
                        <div class="text-center mt-6 pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500">
                                Already have an account? 
                                <RouterLink :to="{ name: 'auth.login' }" class="text-blue-600 hover:text-blue-700 font-bold hover:underline">
                                    Sign in
                                </RouterLink>
                            </p>
                        </div>
                    </template>
                </RegisterForm>
            </div>

            <div class="auth-footer">
                <p>&copy; {{ new Date().getFullYear() }} Alcess. All rights reserved.</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Logo from "@/../img/logo.png";
import RegisterForm from "@/components/forms/RegisterForm.vue";
import NavBar from "@/components/NavBar.vue";
import { useRouter } from "vue-router";

const router = useRouter();

const onRegisterSuccess = () => {
    // Redirect to home or logic happens inside RegisterForm mostly, 
    // but usually we want to go straight to login or home if auto-logged in.
    // RegisterForm usually clears form.
    // Let's assume we want to redirect to login or auto-login.
    // If backend auto-logs in, we go to home.
    router.push({ name: 'home' });
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.auth-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    background-color: #f8fafc;
    background-image: 
        radial-gradient(at 0% 0%, hsla(253,16%,7%,0) 0, hsla(253,16%,7%,0) 50%), 
        radial-gradient(at 50% 0%, hsla(225,39%,30%,0.1) 0, hsla(225,39%,30%,0) 50%), 
        radial-gradient(at 100% 0%, hsla(339,49%,30%,0) 0, hsla(339,49%,30%,0) 50%);
    font-family: 'Inter', sans-serif;
    position: relative;
    overflow: hidden;
}

/* Subtle modern background pattern */
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
    margin: auto;
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
