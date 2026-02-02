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
