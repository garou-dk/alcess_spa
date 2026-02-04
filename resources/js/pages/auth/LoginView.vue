<template>
    <div class="min-h-screen flex flex-col bg-white">
        <!-- Full Width Navbar -->
        <NavBar mode="guest" class="z-50 border-b border-gray-100" :transparent="false" />

        <div class="flex-1 flex">
            <!-- Left Side - Branding (Creative Shapes Theme) -->
            <div class="hidden lg:flex lg:w-1/2 bg-slate-50 relative overflow-hidden flex-col items-center justify-center border-r border-gray-100">
                <!-- Abstract Shapes -->
                <div class="absolute inset-0 z-0">
                    <div class="absolute top-10 left-10 w-32 h-32 bg-emerald-100 rounded-3xl rotate-12 opacity-40 animate-pulse"></div>
                    <div class="absolute bottom-20 right-10 w-48 h-48 bg-teal-100 rounded-full opacity-30 animate-bounce" style="animation-duration: 4s"></div>
                    <div class="absolute top-1/2 left-1/4 w-12 h-12 bg-green-200 rounded-lg -rotate-45 opacity-20"></div>
                    <div class="absolute bottom-1/4 left-10 w-24 h-24 border-4 border-emerald-50 rounded-full opacity-50"></div>
                    <div class="absolute top-1/4 right-1/4 w-8 h-8 bg-teal-300 rounded-full opacity-10"></div>
                </div>

                <!-- Branding Content -->
                <div class="relative z-10 text-center px-12">
                    <div class="mb-8 flex justify-center transform hover:scale-110 transition-transform duration-700">
                        <img :src="Logo" alt="Alcess Logo" class="h-48 w-auto drop-shadow-2xl" />
                    </div>
                    <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Elevate Your Tech</h1>
                    <p class="text-slate-600 max-w-sm mx-auto leading-relaxed">
                        Join the future of retail with Alcess. Premium products, seamless experience.
                    </p>
                </div>

                <div class="absolute bottom-10 text-slate-400 text-xs font-medium tracking-widest uppercase">
                    Innovating Since 2024
                </div>
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
import Logo from "@/../img/logo.png";
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
