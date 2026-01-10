<template>
    <div class="w-full overflow-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th
                        v-for="col in columns"
                        :key="`header-${col}`"
                        class="p-4 text-left"
                    >
                        <div class="h-4 bg-gray-200 rounded animate-pulse shimmer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in rows"
                    :key="`row-${row}`"
                    class="border-b"
                >
                    <td
                        v-for="col in columns"
                        :key="`cell-${row}-${col}`"
                        class="p-4"
                    >
                        <div class="h-4 bg-gray-200 rounded animate-pulse shimmer"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
interface TableSkeletonProps {
    rows?: number;
    columns?: number;
}

withDefaults(defineProps<TableSkeletonProps>(), {
    rows: 5,
    columns: 4,
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
