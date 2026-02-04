<template>
    <div class="min-h-screen flex bg-white">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-slate-900 relative overflow-hidden flex-col items-center justify-center p-12 text-white">
            <!-- Background Pattern -->
            <div class="absolute inset-0 z-0 opacity-20">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/30 to-purple-600/30"></div>
                <!-- Abstract blobs similar to original design but larger -->
                <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute -bottom-8 right-0 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 text-center">
                <div class="mb-8 flex justify-center transform hover:scale-105 transition-transform duration-500">
                    <img :src="Logo" alt="Alcess Logo" class="h-32 w-auto drop-shadow-2xl" />
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-white mb-4">Welcome to Alcess</h1>
                <p class="text-lg text-slate-300 max-w-md mx-auto leading-relaxed">
                    Your premium destination for quality tech. Sign in to access your account and manage your orders.
                </p>
                
                <!-- Trust Indicators -->
                <div class="mt-12 flex gap-8 justify-center text-slate-400">
                    <div class="text-center">
                        <i class="pi pi-shield text-2xl mb-2 block text-blue-400"></i>
                        <span class="text-xs font-medium uppercase tracking-wider">Secure</span>
                    </div>
                    <div class="text-center">
                        <i class="pi pi-bolt text-2xl mb-2 block text-yellow-400"></i>
                        <span class="text-xs font-medium uppercase tracking-wider">Fast</span>
                    </div>
                    <div class="text-center">
                        <i class="pi pi-heart text-2xl mb-2 block text-red-400"></i>
                        <span class="text-xs font-medium uppercase tracking-wider">Trusted</span>
                    </div>
                </div>
            </div>
            
            <div class="absolute bottom-6 text-slate-500 text-xs text-center">
                &copy; {{ new Date().getFullYear() }} Alcess. All rights reserved.
            </div>
        </div>

        <!-- Right Side - Content -->
        <div class="w-full lg:w-1/2 flex flex-col h-screen overflow-y-auto">
            <NavBar mode="guest" class="bg-white/80 backdrop-blur-md sticky top-0 z-20" :transparent="false" />
            
            <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24 py-12">
                <div class="w-full max-w-md mx-auto">
                    <div class="text-center lg:text-left mb-10">
                        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Welcome Back</h2>
                        <p class="mt-2 text-sm text-gray-600">
                            Please sign in to your account
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl">
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
