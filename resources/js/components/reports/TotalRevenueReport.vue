<template>
    <StatCard 
        v-if="dashboardData.revenue"
        label="Total Revenue" 
        :value="CurrencyUtil.formatCurrency(dashboardData.revenue.current_month.revenue)" 
        :subtitle="dashboardData.revenue.comparison.percentage_change + '% vs last month'"
        icon="pi pi-dollar"
        iconBgColor="bg-green-50"
        iconColor="#059669"
    />
</template>
<script setup lang="ts">
import CurrencyUtil from '@/utils/CurrencyUtil';
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