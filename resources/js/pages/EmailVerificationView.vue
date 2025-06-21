<template>
    <div class="h-svh flex justify-center items-center">
        <div>
            <div class="flex justify-center mb-2">
                <img :src="ImgLogo" class="w-32 h-32" alt="Store Icon">
            </div>
            <div v-if="submitService.request.loading">
                <div class="mb-2">
                    <h1 class="dots-loader text-2xl font-semibold">Verifying email<span class="dots"><span>.</span><span>.</span><span>.</span></span></h1>
                </div>
                <div class="flex justify-center">
                    <PageLoader />
                </div>
            </div>
            <div v-else-if="submitService.request.status === 200">
                <h1 class="text-2xl font-semibold p-2 text-center">{{ submitService.request.message }}</h1>
            </div>
            <div v-else>
                <h1 class="text-2xl font-semibold p-2 text-center">{{ submitService.request.message }}</h1>
                <p class="text-center">
                    Please try logging in to your account, and we will send you another email verification link.
                </p>
            </div>
            <div v-if="!submitService.request.loading" class="flex justify-center mt-2 gap-2">
                <RouterLink :to="{ name: 'home' }">
                    <Button type="button" label="Home" raised />
                </RouterLink>
                <Button
                    type="button"
                    label="Retry"
                    icon="pi pi-refresh"
                    raised
                    @click="handleSubmit()"
                    :loading="submitService.request.loading"
                />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import useAxiosUtil from '@/utils/AxiosUtil';
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import ImgLogo from '@/../img/logo.png';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const submitService = useAxiosUtil();

const handleSubmit = async() => {
    submitService.axios.defaults.baseURL = decodeURIComponent(route.query['link'] as string);
    await submitService.post('', null, false).then(() => {
        if (submitService.request.status === 200) {
            toast.success(submitService.request.message);
        }
        else {
            toast.error(submitService.request.message ?? 'Failed to verify email.');
        }
    });
}

onMounted(() => {
    if (route.query['link']) {
        handleSubmit();
    }
    else {
        router.push({ name: 'home' });
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