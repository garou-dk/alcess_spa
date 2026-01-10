<template>
    <BoxShadow>
        <div v-if="dashboardData.revenue" class="p-4 w-full">
            <div class="p-4 w-full">
                <div class="flex items-center justify-between">
                <div class="grow">
                    <p class="text-sm text-gray-600">Total Revenue</p>
                    <p 
                        class="font-bold text-green-600 truncate"
                        :class="getValueFontSize(CurrencyUtil.formatCurrency(dashboardData.revenue.current_month.revenue))"
                    >
                        {{ CurrencyUtil.formatCurrency(dashboardData.revenue.current_month.revenue) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="pi pi-arrow-up text-green-600" />
                        <span>{{ dashboardData.revenue.comparison.percentage_change }}% vs last month</span>
                    </p>
                </div>
                <div class="rounded-full bg-green-100 p-3 shrink w-12 h-12 flex items-center justify-center">
                    <i class="pi pi-dollar text-xl text-green-600" />
                </div>
            </div>
        </div>
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
import CurrencyUtil from '@/utils/CurrencyUtil';
import { useDashboardData } from '@/composables/useDashboardData';

const { dashboardData } = useDashboardData();

const getValueFontSize = (value: string) => {
    const length = value.length;
    if (length > 15) return 'text-base';
    if (length > 12) return 'text-lg';
    if (length > 9) return 'text-xl';
    return 'text-2xl';
};
</script>