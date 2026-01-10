<template>
    <div
        class="rounded-lg border bg-white p-6 shadow-sm"
        :style="{ height: height }"
    >
        <!-- Chart Title -->
        <div class="mb-4 space-y-2">
            <div class="h-6 bg-gray-200 rounded animate-pulse shimmer w-1/3"></div>
            <div class="h-4 bg-gray-200 rounded animate-pulse shimmer w-1/2"></div>
        </div>

        <!-- Chart Content -->
        <div class="flex-1 flex items-end justify-center space-x-2 h-full">
            <!-- Bar Chart -->
            <template v-if="type === 'bar'">
                <div
                    v-for="bar in 8"
                    :key="`bar-${bar}`"
                    class="bg-gray-200 rounded-t animate-pulse shimmer flex-1 max-w-8"
                    :style="{ height: `${Math.random() * 60 + 20}%` }"
                ></div>
            </template>

            <!-- Line Chart -->
            <template v-else-if="type === 'line'">
                <div class="w-full h-full relative">
                    <!-- Grid lines -->
                    <div class="absolute inset-0 flex flex-col justify-between">
                        <div
                            v-for="line in 5"
                            :key="`line-${line}`"
                            class="h-px bg-gray-200"
                        ></div>
                    </div>
                    <!-- Line path placeholder -->
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full h-px bg-gray-200 rounded animate-pulse shimmer"></div>
                    </div>
                    <!-- Data points -->
                    <div class="absolute inset-0 flex items-center justify-between px-4">
                        <div
                            v-for="point in 6"
                            :key="`point-${point}`"
                            class="w-2 h-2 bg-gray-200 rounded-full animate-pulse shimmer"
                        ></div>
                    </div>
                </div>
            </template>

            <!-- Pie Chart -->
            <template v-else-if="type === 'pie'">
                <div class="w-32 h-32 rounded-full bg-gray-200 animate-pulse shimmer relative">
                    <div class="absolute inset-2 rounded-full bg-white"></div>
                </div>
            </template>
        </div>

        <!-- Chart Legend -->
        <div class="mt-4 flex flex-wrap gap-4">
            <div
                v-for="legend in 3"
                :key="`legend-${legend}`"
                class="flex items-center space-x-2"
            >
                <div class="w-3 h-3 bg-gray-200 rounded animate-pulse shimmer"></div>
                <div class="h-4 bg-gray-200 rounded animate-pulse shimmer w-16"></div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
interface ChartSkeletonProps {
    type?: 'bar' | 'line' | 'pie';
    height?: string;
}

withDefaults(defineProps<ChartSkeletonProps>(), {
    type: 'bar',
    height: '300px',
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
