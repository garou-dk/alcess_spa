<template>
    <div v-if="loadService.request.loading" class="animate-pulse">
        <div class="h-6 bg-gray-200 rounded w-8 ps-2"></div>
    </div>
    <p v-else class="ps-2 text-xl font-bold text-red-600">{{ result }}</p>
</template>
<script setup lang="ts">
import useAxiosUtil from "@/utils/AxiosUtil";
import { useEcho } from "@laravel/echo-vue";
import { onMounted, ref } from "vue";

const loadService = useAxiosUtil<null, { result: number }>();
const result = ref<number>(0);

const load = async () => {
    await loadService.get("admin/results/out-of-stock").then(() => {
        if (loadService.request.status === 200) {
            result.value = loadService.request.data.result;
        }
    });
};

useEcho("product", [".product.out.count"], (value: { count: number }) => {
    result.value = value.count;
});

onMounted(() => {
    load();
});
</script>
