<template>
    <div class="flex h-svh items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50 to-white">
        <div class="max-w-md w-full mx-4">
            <!-- Verifying State -->
            <div v-if="submitService.request.loading" class="text-center">
                <div class="mb-6 flex justify-center">
                    <div class="w-24 h-24 rounded-full bg-blue-50 flex items-center justify-center animate-pulse">
                        <img :src="ImgLogo" class="h-16 w-16" alt="Store Icon" />
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    Verifying your email<span class="dots"><span>.</span><span>.</span><span>.</span></span>
                </h1>
                <p class="text-gray-500 text-sm">Please wait while we confirm your email address</p>
                <div class="flex justify-center mt-6">
                    <PageLoader />
                </div>
            </div>

            <!-- Success State: Welcome Modal -->
            <div v-else-if="submitService.request.status === 200" class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                <!-- Confetti-like header -->
                <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-6 pt-8 pb-10 text-center relative overflow-hidden">
                    <!-- Decorative circles -->
                    <div class="absolute top-4 left-6 w-3 h-3 rounded-full bg-yellow-300 animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="absolute top-8 right-10 w-2 h-2 rounded-full bg-green-300 animate-bounce" style="animation-delay: 0.3s"></div>
                    <div class="absolute bottom-6 left-12 w-2.5 h-2.5 rounded-full bg-pink-300 animate-bounce" style="animation-delay: 0.5s"></div>
                    <div class="absolute top-12 left-1/3 w-2 h-2 rounded-full bg-purple-300 animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="absolute bottom-4 right-8 w-3 h-3 rounded-full bg-yellow-200 animate-bounce" style="animation-delay: 0.4s"></div>
                    
                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 ring-4 ring-white/30">
                            <i class="pi pi-check-circle text-white text-4xl"></i>
                        </div>
                        <h1 class="text-2xl font-extrabold text-white">Welcome to Alcess!</h1>
                        <p class="text-blue-100 text-sm mt-2 font-medium">Your email has been verified successfully</p>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-6 -mt-4 relative z-20">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-4 border border-blue-100 mb-6">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="pi pi-map-marker text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 mb-1">Set up your delivery address</h3>
                                <p class="text-xs text-gray-600 leading-relaxed">
                                    To start shopping and placing orders, you'll need to set up your delivery address first. We'll redirect you to your profile page to complete this step.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Steps overview -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="pi pi-check text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-600 line-through">Create your account</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="pi pi-check text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-600 line-through">Verify your email</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 animate-pulse">
                                <span class="text-white text-xs font-bold">3</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-900">Set up your delivery address</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <Button
                            @click="goToLogin"
                            type="button"
                            label="Continue to Login"
                            icon="pi pi-arrow-right"
                            iconPos="right"
                            class="w-full font-bold text-base"
                            pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-xl! py-3! shadow-lg! hover:shadow-xl! transition-all!"
                        />
                        <p class="text-center text-xs text-gray-400 font-medium">
                            Login and set up your profile to start shopping
                        </p>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div v-else class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-red-500 to-rose-600 px-6 py-8 text-center">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="pi pi-times-circle text-white text-4xl"></i>
                    </div>
                    <h1 class="text-xl font-bold text-white">Verification Failed</h1>
                    <p class="text-red-100 text-sm mt-2 font-medium">{{ submitService.request.message }}</p>
                </div>
                <div class="px-6 py-6 -mt-2 relative z-10">
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-6 flex items-start gap-3">
                        <i class="pi pi-info-circle text-amber-600 mt-0.5"></i>
                        <p class="text-sm text-amber-800 leading-relaxed">
                            Please try logging in to your account, and we will send you another email verification link.
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <RouterLink :to="{ name: 'home' }" class="flex-1">
                            <Button type="button" label="Home" icon="pi pi-home" severity="secondary" outlined class="w-full rounded-xl font-bold" />
                        </RouterLink>
                        <Button
                            type="button"
                            label="Retry"
                            icon="pi pi-refresh"
                            @click="handleSubmit()"
                            :loading="submitService.request.loading"
                            class="flex-1 rounded-xl font-bold"
                            pt:root:class="bg-blue-600! hover:bg-blue-700!"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import useAxiosUtil from "@/utils/AxiosUtil";
import { onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import ImgLogo from "@/../img/logo.png";

const route = useRoute();
const router = useRouter();
const toast = useToast();

const submitService = useAxiosUtil();

const handleSubmit = async () => {
    submitService.axios.defaults.baseURL = decodeURIComponent(
        route.query["link"] as string,
    );
    await submitService.post("", null, false).then(() => {
        if (submitService.request.status === 200) {
            toast.success(submitService.request.message);
        } else {
            toast.error(
                submitService.request.message ?? "Failed to verify email.",
            );
        }
    });
};

const goToLogin = () => {
    router.push({ name: 'auth.login' });
};

onMounted(() => {
    if (route.query["link"]) {
        handleSubmit();
    } else {
        router.push({ name: "home" });
    }
});
</script>
<style scoped>
.dots span {
    opacity: 0;
    animation: blinkDot 1.5s infinite;
}

.dots span:nth-child(1) {
    animation-delay: 0s;
}
.dots span:nth-child(2) {
    animation-delay: 0.2s;
}
.dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes blinkDot {
    0% {
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}
</style>
