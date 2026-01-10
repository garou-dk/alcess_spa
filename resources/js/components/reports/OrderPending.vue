<template>
    <BoxShadow>
        <div v-if="dashboardData.pending_orders" class="p-4 w-full">
            <div class="p-4 w-full">
                <div class="flex items-center justify-between">
                    <div class="grow">
                        <p class="text-sm text-gray-600">New Orders</p>
                        <p 
                            class="font-bold text-blue-600 truncate"
                            :class="getValueFontSize(String(dashboardData.pending_orders.total_pending))"
                        >
                            {{ dashboardData.pending_orders.total_pending }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            <span>{{ dashboardData.pending_orders.today_pending }} today</span>
                        </p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-3 shrink w-12 h-12 flex items-center justify-center">
                        <i class="pi pi-bell text-xl text-blue-600" />
                    </div>
                </div>
            </div>
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
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