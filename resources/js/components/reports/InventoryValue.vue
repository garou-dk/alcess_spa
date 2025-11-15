<template>
    <BoxShadow>
        <div v-if="!loadService.request.loading" class="p-4 w-full">
            <div class="p-4 w-full">
                <div class="flex items-center justify-between">
                    <div class="grow">
                        <p class="text-sm text-gray-600">Inventory Value</p>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ CurrencyUtil.formatCurrency(data.total_inventory_value) }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            <span>Across {{ data.total_products }} items</span>
                        </p>
                    </div>
                    <div class="rounded-full bg-purple-100 p-3 shrink">
                        <i class="pi pi-warehouse text-2xl text-purple-600" />
                    </div>
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
import CurrencyUtil from '@/utils/CurrencyUtil';
import { onMounted, reactive } from 'vue';
import { useToast } from 'vue-toastification';

interface InventoryValueInterface {
    total_inventory_value: number;
    total_products: number;
}

const loadService = useAxiosUtil<null, InventoryValueInterface>();
const toast = useToast();
const data : InventoryValueInterface = reactive({
    total_inventory_value: 0,
    total_products: 0
});

const load = async () => {
    await loadService.get('admin/dashboard/inventory-value').then(() => {
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