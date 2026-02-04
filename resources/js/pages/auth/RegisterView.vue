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
                    <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                    <div class="absolute -bottom-8 right-0 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
                </div>

                <!-- Content -->
                <div class="relative z-10 text-center">
                    <div class="mb-8 flex justify-center transform hover:scale-105 transition-transform duration-500">
                        <img :src="Logo" alt="Alcess Logo" class="h-40 w-auto drop-shadow-xl" />
                    </div>
                    <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 mb-4">Join the Community</h1>
                    <p class="text-lg text-gray-600 max-w-md mx-auto leading-relaxed">
                        Create your account today and unlock exclusive features, faster checkout, and order tracking.
                    </p>
                    
                    <!-- Feature List -->
                    <div class="mt-12 space-y-4 text-left inline-block">
                        <div class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                                <i class="pi pi-check text-emerald-600"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Exclusive member discounts</span>
                        </div>
                        <div class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-teal-50 flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                                <i class="pi pi-check text-teal-600"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Track your order history</span>
                        </div>
                        <div class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                                <i class="pi pi-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Fast and secure checkout</span>
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
                            <h2 class="text-3xl font-bold tracking-tight text-gray-900">Create Account</h2>
                            <p class="mt-2 text-sm text-gray-600">
                                Join us to start shopping
                            </p>
                        </div>

                        <div class="bg-white">
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
