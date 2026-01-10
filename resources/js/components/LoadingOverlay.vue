<template>
    <Transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm">
            <div class="flex flex-col items-center space-y-4">
                <!-- Modern spinner -->
                <div class="relative h-16 w-16">
                    <div class="absolute inset-0 rounded-full border-4 border-gray-200"></div>
                    <div class="absolute inset-0 animate-spin rounded-full border-4 border-transparent border-t-blue-600"></div>
                </div>
                
                <!-- Loading text -->
                <div class="text-center">
                    <p class="text-lg font-semibold text-gray-800">{{ title }}</p>
                    <p class="text-sm text-gray-500">{{ subtitle }}</p>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
interface Props {
    show: boolean;
    title?: string;
    subtitle?: string;
}

withDefaults(defineProps<Props>(), {
    title: 'Loading Dashboard',
    subtitle: 'Please wait...',
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
