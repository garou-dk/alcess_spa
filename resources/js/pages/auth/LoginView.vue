<template>
    <div class="min-h-screen flex flex-col bg-white">
        <!-- Full Width Navbar -->
        <NavBar mode="guest" class="z-50 border-b border-gray-100" :transparent="false" />

        <div class="flex-1 flex">
            <!-- Left Side - Branding (GIF Theme) -->
            <div class="hidden lg:flex lg:w-1/2 bg-white relative overflow-hidden flex-col items-center justify-center border-r border-gray-100">
                <img :src="LoginGif" alt="Login Animation" class="w-full h-full object-cover" />
            </div>

            <!-- Right Side - Content -->
            <div class="w-full lg:w-1/2 flex flex-col h-full overflow-y-auto bg-white">
                <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24 py-12">
                    <div class="w-full max-w-md mx-auto">
                        <div class="text-center lg:text-left mb-10">
                            <h2 class="text-3xl font-bold tracking-tight text-gray-900">Welcome Back</h2>
                            <p class="mt-2 text-sm text-gray-600">
                                Please sign in to your account
                            </p>
                        </div>

                        <div class="bg-white">
                            <LoginForm :admin="props.admin" @success="onLoginSuccess">
                                <template #footer>
                                    <div class="flex items-center justify-between mt-6">
                                        <div class="text-sm">
                                            <RouterLink 
                                                :to="{ name: 'auth.forgot-password' }" 
                                                class="font-medium text-blue-600 hover:text-blue-500 transition-colors"
                                            >
                                                Forgot password?
                                            </RouterLink>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                                        <p class="text-sm text-gray-600">
                                            Don't have an account? 
                                            <RouterLink 
                                                :to="{ name: 'auth.register' }" 
                                                class="font-bold text-blue-600 hover:text-blue-500 transition-colors ml-1"
                                            >
                                                Sign up for free
                                            </RouterLink>
                                        </p>
                                    </div>
                                </template>
                            </LoginForm>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Footer />
    </div>
</template>

<script setup lang="ts">
import LoginGif from "@/../img/auth/login.gif";
import LoginForm from "@/components/forms/LoginForm.vue";
import NavBar from "@/components/NavBar.vue";
import Footer from "@/components/Footer.vue";
import { useRouter } from "vue-router";

const router = useRouter();

const props = defineProps<{
    admin?: boolean;
}>();

const onLoginSuccess = () => {
    // Redirection is handled by LoginForm component logic
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
