<template>
    <StatCard 
        v-if="dashboardData.inventory_value"
        label="Inventory Value" 
        :value="CurrencyUtil.formatCurrency(dashboardData.inventory_value.total_inventory_value)" 
        :subtitle="'Across ' + dashboardData.inventory_value.total_products + ' items'"
        icon="pi pi-warehouse"
        iconBgColor="bg-purple-50"
        iconColor="#9333EA"
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