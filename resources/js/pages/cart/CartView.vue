<template>
    <div>

    </div>
</template>
<script setup lang="ts">
import { CartInterface } from '@/interfaces/CartInterface';
import useAxiosUtil from '@/utils/AxiosUtil';
import { onMounted, ref } from 'vue';
import { useToast } from 'vue-toastification';

const loadService = useAxiosUtil<null, CartInterface[]>();
const carts = ref<CartInterface[]>([]);
const toast = useToast();

const load = async () => {
    await loadService.get("cart").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            carts.value = loadService.request.data;
        }
        else {
            toast.error(loadService.request.message ?? 'Failed to load carts');
        }
    });
}

onMounted(() => {
    load();
});
</script>