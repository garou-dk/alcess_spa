<template>
    <div class="min-h-screen flex flex-col bg-white">
        <!-- Full Width Navbar -->
        <NavBar mode="guest" class="z-50 border-b border-gray-100" :transparent="false" />

        <div class="flex-1 flex">
            <!-- Left Side - Branding (White Theme) -->
            <div class="hidden lg:flex lg:w-1/2 bg-white relative overflow-hidden flex-col items-center justify-center p-12 text-gray-900 border-r border-gray-100">
                <!-- Background Pattern -->
                <div class="absolute inset-0 z-0">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 opacity-50"></div>
                    <!-- Abstract blobs -->
                    <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                    <div class="absolute -bottom-8 right-0 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
                </div>

                <!-- Content -->
                <div class="relative z-10 text-center">
                    <div class="mb-8 flex justify-center transform hover:scale-105 transition-transform duration-500">
                        <img :src="Logo" alt="Alcess Logo" class="h-40 w-auto drop-shadow-xl" />
                    </div>
                    <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 mb-4">Welcome to Alcess</h1>
                    <p class="text-lg text-gray-600 max-w-md mx-auto leading-relaxed">
                        Your premium destination for quality tech. Sign in to access your account and manage your orders.
                    </p>
                    
                    <!-- Trust Indicators -->
                    <div class="mt-12 flex gap-8 justify-center text-gray-500">
                        <div class="text-center group">
                            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:bg-emerald-100 transition-colors">
                                <i class="pi pi-shield text-xl text-emerald-600"></i>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-wider">Secure</span>
                        </div>
                        <div class="text-center group">
                            <div class="w-12 h-12 bg-teal-50 rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:bg-teal-100 transition-colors">
                                <i class="pi pi-bolt text-xl text-teal-600"></i>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-wider">Fast</span>
                        </div>
                        <div class="text-center group">
                            <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:bg-green-100 transition-colors">
                                <i class="pi pi-heart text-xl text-green-600"></i>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-wider">Trusted</span>
                        </div>
                    </div>
                </div>
                
                <div class="absolute bottom-6 text-gray-400 text-xs text-center">
                    &copy; {{ new Date().getFullYear() }} Alcess. All rights reserved.
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
