<template>
    <div class="min-h-screen flex flex-col bg-white">
        <!-- Full Width Navbar -->
        <NavBar mode="guest" class="z-50 border-b border-gray-100" :transparent="false" />

        <div class="flex-1 flex">
            <!-- Left Side - Branding (Creative Shapes Theme) -->
            <div class="hidden lg:flex lg:w-1/2 bg-slate-50 relative overflow-hidden flex-col items-center justify-center border-r border-gray-100">
                <!-- Abstract Shapes -->
                <div class="absolute inset-0 z-0">
                    <div class="absolute top-20 right-20 w-40 h-40 bg-blue-100 rounded-full opacity-40 animate-pulse"></div>
                    <div class="absolute top-1/3 left-10 w-32 h-32 bg-indigo-100 rounded-2xl rotate-45 opacity-30 animate-spin" style="animation-duration: 10s"></div>
                    <div class="absolute bottom-10 left-1/4 w-20 h-20 bg-blue-200 rounded-full opacity-20"></div>
                    <div class="absolute top-10 left-1/2 w-48 h-12 bg-slate-100 -rotate-12 opacity-50"></div>
                </div>

                <!-- Branding Content -->
                <div class="relative z-10 text-center px-12">
                    <div class="mb-8 flex justify-center transform hover:scale-110 transition-transform duration-700">
                        <img :src="Logo" alt="Alcess Logo" class="h-48 w-auto drop-shadow-2xl" />
                    </div>
                    <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Start Your Journey</h1>
                    <p class="text-slate-600 max-w-sm mx-auto leading-relaxed">
                        Become a part of our growing community and access exclusive tech deals.
                    </p>
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
        <Footer />
    </div>
</template>

<script setup lang="ts">
import Logo from "@/../img/logo.png";
import RegisterForm from "@/components/forms/RegisterForm.vue";
import NavBar from "@/components/NavBar.vue";
import Footer from "@/components/Footer.vue";
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
