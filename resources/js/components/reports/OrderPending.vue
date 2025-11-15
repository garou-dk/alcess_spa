<template>
    <BoxShadow>
        <div v-if="!loadService.request.loading" class="p-4 w-full">
            <div class="p-4 w-full">
                <div class="flex items-center justify-between">
                    <div class="grow">
                        <p class="text-sm text-gray-600">Pending Orders</p>
                        <p class="text-2xl font-bold text-orange-600">
                            {{ data.total_pending }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            <span>{{ data.today_pending }} today</span>
                        </p>
                    </div>
                    <div class="rounded-full bg-orange-100 p-3 shrink">
                        <i class="pi pi-shopping-cart text-2xl text-orange-600" />
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
import { onMounted, reactive } from 'vue';
import { useToast } from 'vue-toastification';

interface OrderPendingDataInterface {
    total_pending: number;
    today_pending: number;
}

const loadService = useAxiosUtil<null, OrderPendingDataInterface>();
const toast = useToast();
const data : OrderPendingDataInterface = reactive({
    total_pending: 0,
    today_pending: 0
});

const load = async () => {
    await loadService.get('admin/dashboard/pending-orders').then(() => {
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