<template>
    <BoxShadow>
        <div v-if="!loadService.request.loading" class="p-4 w-full">
            <div class="flex items-center justify-between">
                <div class="grow">
                    <p class="text-sm text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ CurrencyUtil.formatCurrency(data.current_month.revenue) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="pi pi-arrow-up text-green-600" />
                        <span>{{ data.comparison.percentage_change }}% vs last month</span>
                    </p>
                </div>
                <div class="rounded-full bg-green-100 p-3 shrink">
                    <i class="pi pi-dollar text-2xl text-green-600" />
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
import { onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';

interface TotalRevenueDataInterface {
    current_month: {
        period: string;
        revenue: number;
        formatted_revenue: string;
    };
    last_month: {
        period: string;
        revenue: number;
        formatted_revenue: string;
    };
    comparison: {
        percentage_change: number;
        trend: string;
        difference: number;
        formatted_difference: string;
    };
}

const data : TotalRevenueDataInterface = reactive({
    current_month: {
        period: '',
        revenue: 0,
        formatted_revenue: '',
    },
    last_month: {
        period: '',
        revenue: 0,
        formatted_revenue: '',
    },
    comparison: {
        percentage_change: 0,
        trend: '',
        difference: 0,
        formatted_difference: '',
    },
});

const loadService = useAxiosUtil<null, TotalRevenueDataInterface>();
const toast = useToast();

const load = async () => {
    await loadService.get('admin/dashboard/monthly-revenue').then(() => {
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