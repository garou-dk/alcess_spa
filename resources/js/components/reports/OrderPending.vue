<template>
    <StatCard 
        v-if="dashboardData.pending_orders"
        label="New Orders" 
        :value="dashboardData.pending_orders.total_pending" 
        :valueClass="getValueFontSize(String(dashboardData.pending_orders.total_pending))"
        :subtitle="dashboardData.pending_orders.today_pending + ' received today'"
        icon="pi pi-bell"
        iconBgColor="bg-blue-50"
        iconColor="#2563EB"
    />
</template>
<script setup lang="ts">
import { useDashboardData } from '@/composables/useDashboardData';
import StatCard from '@/components/StatCard.vue';

const { dashboardData } = useDashboardData();

const getValueFontSize = (value: string) => {
    const length = value.length;
    if (length > 15) return 'text-base';
    if (length > 12) return 'text-lg';
    if (length > 9) return 'text-xl';
    return 'text-2xl';
};
</script>