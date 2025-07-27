<template>
    <p class="ps-2 text-yellow-500">{{ result }}</p>
</template>
<script setup lang="ts">
import useAxiosUtil from "@/utils/AxiosUtil";
import { useEcho } from "@laravel/echo-vue";
import { onMounted, ref } from "vue";

const loadService = useAxiosUtil<null, { result: number }>();
const result = ref<number>(0);

const load = async () => {
    await loadService.get("admin/results/low-stock").then(() => {
        if (loadService.request.status === 200) {
            result.value = loadService.request.data.result;
        }
    });
};

useEcho("product", [".product.low.count"], (value: { count: number }) => {
    result.value = value.count;
});

onMounted(() => {
    load();
});
</script>
