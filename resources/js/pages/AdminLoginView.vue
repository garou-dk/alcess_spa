<template>
    <div class="flex min-h-screen items-center bg-gradient-to-br from-sky-50 via-white to-sky-100">
        <div class="container mx-auto">
            <div class="mx-auto max-w-6xl overflow-hidden rounded-3xl bg-white shadow-2xl">
                <div class="flex flex-wrap">
                    <div class="relative max-lg:w-full lg:w-1/2">
                        <div class="flex h-full flex-col items-center justify-center bg-gradient-to-br from-sky-600 to-sky-800 p-12 lg:min-h-[600px]">
                            <div class="absolute left-0 top-0 h-32 w-32 rounded-full bg-white/10 blur-3xl"></div>
                            <div class="absolute bottom-0 right-0 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
                            
                            <div class="relative z-10 text-center">
                                <div class="mb-8 flex justify-center">
                                    <div class="group relative">
                                        <div class="absolute inset-0 animate-pulse rounded-full bg-white/20 blur-xl"></div>
                                        <div class="relative overflow-hidden rounded-full border-4 border-white/30 bg-white shadow-2xl transition-transform duration-500 hover:scale-105">
                                            <img
                                                :src="Logo"
                                                class="h-40 w-40 object-cover p-2 lg:h-48 lg:w-48"
                                                alt="Store Icon"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h2 class="text-4xl font-bold text-white lg:text-5xl">
                                        Welcome Back
                                    </h2>
                                    <p class="text-xl font-semibold text-sky-100">
                                        Gear Up for Greatness
                                    </p>
                                    <div class="mx-auto mt-6 h-1 w-20 rounded-full bg-white/50"></div>
                                    <p class="mt-6 text-sky-100/90">
                                        Sign in to access your admin dashboard and manage your store with powerful tools
                                    </p>
                                </div>

                                <div class="mt-12 space-y-4 text-left">
                                    <div class="flex items-center gap-3 text-white/90">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                                            <i class="pi pi-check text-lg"></i>
                                        </div>
                                        <span class="text-sm">Comprehensive dashboard analytics</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="max-lg:w-full lg:w-1/2">
                        <div class="flex h-full flex-col justify-center p-8 lg:p-12 lg:min-h-[600px]">
                            <div class="mb-8">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100">
                                        <i class="pi pi-lock text-xl text-sky-600"></i>
                                    </div>
                                    <div>
                                        <h1 class="text-3xl font-bold text-gray-800">
                                            Admin Login
                                        </h1>
                                    </div>
                                </div>
                                <p class="text-gray-600">
                                    Enter your credentials to access the admin panel
                                </p>
                            </div>

                            <div class="space-y-6">
                                <LoginForm :admin="true" />
                            </div>

                            <div class="mt-8 rounded-xl bg-sky-50 p-4">
                                <div class="flex items-start gap-3">
                                    <i class="pi pi-info-circle mt-0.5 text-sky-600"></i>
                                    <div class="text-sm text-gray-700">
                                        <p class="font-semibold">Security Notice</p>
                                        <p class="mt-1 text-gray-600">
                                            Make sure you're on the official admin portal. Never share your login credentials.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 text-center">
                                <p class="text-sm text-gray-500">
                                    Need help? 
                                    <a href="#" class="font-semibold text-sky-600 hover:underline">
                                        Contact Support
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center text-sm text-gray-500">
                <p>&copy; 2025 All rights reserved. Powered by your store management system.</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Logo from "@/../img/logo.png";
import LoginForm from "@/components/forms/LoginForm.vue";
</script>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.group:hover img {
    animation: float 3s ease-in-out infinite;
}
</style>