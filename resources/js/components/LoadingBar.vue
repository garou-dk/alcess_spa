<template>
    <Transition name="slide-down">
        <div v-if="show" class="fixed top-0 left-0 right-0 z-50">
            <div class="h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-pulse">
                <div class="h-full bg-gradient-to-r from-blue-600 to-purple-600 animate-loading-bar"></div>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
interface Props {
    show: boolean;
}

defineProps<Props>();
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
    transition: transform 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
    transform: translateY(-100%);
}

@keyframes loading-bar {
    0% {
        width: 0%;
    }
    50% {
        width: 70%;
    }
    100% {
        width: 100%;
    }
}

.animate-loading-bar {
    animation: loading-bar 2s ease-in-out infinite;
}
</style>
