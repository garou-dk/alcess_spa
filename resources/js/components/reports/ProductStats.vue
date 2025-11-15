<template>
    <BoxShadow>
        <div v-if="!loadService.request.loading" class="p-4 w-full">
            <div class="flex items-center justify-between">
                <div class="grow">
                    <p class="text-sm text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ data.total_products }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        <span class="text-orange-600">{{ data.low_stock_products }} low stock</span>
                    </p>
                </div>
                <div class="rounded-full bg-blue-100 p-3 shrink">
                    <i class="pi pi-box text-2xl text-blue-600" />
                </div>
            </div>
        </div>
        <div v-else class="w-full flex justify-center p-2">
            <PageLoader />
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
import useAxiosUtil from '@/utils/AxiosUtil';
import { onMounted, reactive } from 'vue';
import { useToast } from 'vue-toastification';

interface ProductStatsInterface {
    total_products: number;
    low_stock_products: number;
    healthy_stock_products: number;
    low_stock_percentage: number;
}

const loadService = useAxiosUtil<null, ProductStatsInterface>();
const toast = useToast();
const data : ProductStatsInterface = reactive({
    total_products: 0,
    low_stock_products: 0,
    healthy_stock_products: 0,
    low_stock_percentage: 0,
});

const load = async () => {
    await loadService.get('admin/dashboard/product-stats').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            Object.keys(data).forEach((key) => {
                data[key] = loadService.request.data[key];
            });
        }
        else {
            toast.error(loadService.request.message ?? 'Failed to load data');
        }
    });
}

onMounted(() => {
    load();
});
</script>