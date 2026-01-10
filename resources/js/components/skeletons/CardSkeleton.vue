<template>
    <div :class="['grid gap-4', layout]">
        <div
            v-for="card in count"
            :key="`card-${card}`"
            class="p-4 rounded-lg border bg-white shadow-sm"
        >
            <!-- Card Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="grow space-y-2">
                    <div class="h-4 bg-gray-200 rounded animate-pulse shimmer w-3/4"></div>
                    <div class="h-6 bg-gray-200 rounded animate-pulse shimmer w-1/2"></div>
                </div>
                <div class="rounded-full bg-gray-200 p-3 shrink animate-pulse shimmer">
                    <div class="w-8 h-8"></div>
                </div>
            </div>

            <!-- Card Content -->
            <div class="space-y-2">
                <div class="h-3 bg-gray-200 rounded animate-pulse shimmer w-2/3"></div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
interface CardSkeletonProps {
    count?: number;
    layout?: string;
}

withDefaults(defineProps<CardSkeletonProps>(), {
    count: 4,
    layout: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
});
</script>

<style scoped>
@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: calc(200px + 100%) 0;
    }
}

.shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}
</style>
