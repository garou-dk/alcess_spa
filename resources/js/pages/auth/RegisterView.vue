<template>
    <div class="min-h-screen flex bg-white">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-slate-900 relative overflow-hidden flex-col items-center justify-center p-12 text-white">
            <!-- Background Pattern -->
            <div class="absolute inset-0 z-0 opacity-20">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/30 to-purple-600/30"></div>
                <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute -bottom-8 right-0 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 text-center">
                <div class="mb-8 flex justify-center transform hover:scale-105 transition-transform duration-500">
                    <img :src="Logo" alt="Alcess Logo" class="h-32 w-auto drop-shadow-2xl" />
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-white mb-4">Join the Community</h1>
                <p class="text-lg text-slate-300 max-w-md mx-auto leading-relaxed">
                    Create your account today and unlock exclusive features, faster checkout, and order tracking.
                </p>
                
                <!-- Feature List -->
                <div class="mt-12 space-y-4 text-left inline-block">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <i class="pi pi-check text-blue-400"></i>
                        </div>
                        <span class="text-slate-300">Exclusive member discounts</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <i class="pi pi-check text-purple-400"></i>
                        </div>
                        <span class="text-slate-300">Track your order history</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                            <i class="pi pi-check text-green-400"></i>
                        </div>
                        <span class="text-slate-300">Fast and secure checkout</span>
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
                        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Create Account</h2>
                        <p class="mt-2 text-sm text-gray-600">
                            Join us to start shopping
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl">
                        <RegisterForm @success="onRegisterSuccess">
                            <template #footer>
                                <div class="mt-6 text-xs text-center text-gray-500">
                                    By signing up, you agree to our 
                                    <RouterLink :to="{ name: 'terms-of-service' }" target="_blank" class="text-blue-600 hover:underline">Terms of Service</RouterLink>
                                    and 
                                    <RouterLink :to="{ name: 'privacy-policy' }" target="_blank" class="text-blue-600 hover:underline">Privacy Policy</RouterLink>.
                                </div>

                                <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                                    <p class="text-sm text-gray-600">
                                        Already have an account? 
                                        <RouterLink 
                                            :to="{ name: 'auth.login' }" 
                                            class="font-bold text-blue-600 hover:text-blue-500 transition-colors ml-1"
                                        >
                                            Sign in
                                        </RouterLink>
                                    </p>
                                </div>
                            </template>
                        </RegisterForm>
                    </div>
                </div>
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
    router.push({ name: 'home' });
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
