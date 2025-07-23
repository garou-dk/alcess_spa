<template>
    <p class="ps-2 text-pink-500">{{ result }}</p>
</template>
<script setup lang="ts">
import useAxiosUtil from '@/utils/AxiosUtil';
import { onMounted, ref } from 'vue';

const loadService = useAxiosUtil<null, { result: number }>();
const result = ref<number>(0);


const load = async () => {
    await loadService.get('admin/results/active-product').then(() => {
        if (loadService.request.status === 200) {
            result.value = loadService.request.data.result;
        }
    });
}

onMounted(() => {
    load();
});
</script>