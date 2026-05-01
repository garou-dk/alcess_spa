<template>
    <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100 kpi-card dashboard-card h-full">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Revenue</p>
                </div>
                <p v-if="!isLoadingRevenue" class="font-bold stat-value leading-tight text-gray-900" :class="getValueFontSize(displayRevenue)">
                    {{ displayRevenue }}
                </p>
                <div v-else class="h-8 w-32 bg-gray-100 rounded animate-pulse"></div>
            </div>
            <div class="p-3 rounded-xl flex-shrink-0 ml-3 shadow-sm bg-green-50">
                <i class="pi pi-dollar text-xl" style="color: #059669"></i>
            </div>
        </div>

        <!-- Month Selector -->
        <div class="mt-3 flex items-center gap-2">
            <select
                id="revenue-month-selector"
                v-model="selectedMonth"
                @change="fetchRevenueForMonth"
                class="text-xs bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-gray-600 font-medium focus:outline-none focus:ring-1 focus:ring-green-400 focus:border-green-400 cursor-pointer transition-all hover:bg-gray-100"
            >
                <option v-for="month in monthOptions" :key="month.value" :value="month.value">
                    {{ month.label }}
                </option>
            </select>
        </div>

        <!-- Comparison Subtitle -->
        <div v-if="revenueData && !isLoadingRevenue" class="mt-2 flex items-center gap-1.5">
            <span class="inline-block w-1 h-1 rounded-full" :class="trendDotColor"></span>
            <span class="text-xs font-medium" :class="trendTextColor">
                {{ trendIcon }} {{ Math.abs(revenueData.comparison.percentage_change) }}% vs {{ revenueData.last_month.period }}
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import { useDashboardData } from '@/composables/useDashboardData';

const { dashboardData } = useDashboardData();

interface RevenueResponse {
    current_month: {
        period: string;
        revenue: number;
        formatted_revenue: string;
        breakdown: { orders: number; sales: number };
    };
    last_month: {
        period: string;
        revenue: number;
        formatted_revenue: string;
        breakdown: { orders: number; sales: number };
    };
    comparison: {
        percentage_change: number;
        trend: string;
        difference: number;
        formatted_difference: string;
    };
}

const revenueData = ref<RevenueResponse | null>(null);
const isLoadingRevenue = ref(false);
const selectedMonth = ref('yearly');

// Generate month options: Full Year + current month + previous 11 months
const monthOptions = computed(() => {
    const options = [];
    const now = new Date();

    // Full year option (default)
    options.push({
        value: 'yearly',
        label: `Full Year ${now.getFullYear()}`
    });

    // Current month
    options.push({
        value: 'current',
        label: `${now.toLocaleString('en-US', { month: 'long' })} ${now.getFullYear()} (Current)`
    });

    for (let i = 1; i <= 11; i++) {
        const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
        options.push({
            value: `${date.getMonth() + 1}-${date.getFullYear()}`,
            label: `${date.toLocaleString('en-US', { month: 'long' })} ${date.getFullYear()}`
        });
    }

    return options;
});

const displayRevenue = computed(() => {
    if (revenueData.value) {
        return CurrencyUtil.formatCurrency(revenueData.value.current_month.revenue);
    }
    if (dashboardData.revenue) {
        return CurrencyUtil.formatCurrency(dashboardData.revenue.current_month.revenue);
    }
    return '₱0.00';
});

const trendDotColor = computed(() => {
    const data = revenueData.value || dashboardData.revenue;
    if (!data) return 'bg-blue-400';
    return data.comparison.trend === 'increase' ? 'bg-green-400' : data.comparison.trend === 'decrease' ? 'bg-red-400' : 'bg-blue-400';
});

const trendTextColor = computed(() => {
    const data = revenueData.value || dashboardData.revenue;
    if (!data) return 'text-gray-500';
    return data.comparison.trend === 'increase' ? 'text-green-600' : data.comparison.trend === 'decrease' ? 'text-red-500' : 'text-gray-500';
});

const trendIcon = computed(() => {
    const data = revenueData.value || dashboardData.revenue;
    if (!data) return '';
    return data.comparison.trend === 'increase' ? '↑' : data.comparison.trend === 'decrease' ? '↓' : '→';
});

const getValueFontSize = (value: string) => {
    const length = value.length;
    if (length > 15) return 'text-base';
    if (length > 12) return 'text-lg';
    if (length > 9) return 'text-xl';
    return 'text-2xl';
};

const fetchRevenueForMonth = async () => {
    if (selectedMonth.value === 'yearly') {
        // Reset to dashboard data (which now returns yearly by default)
        revenueData.value = dashboardData.revenue;
        return;
    }

    if (selectedMonth.value === 'current') {
        // Fetch current month specifically
        isLoadingRevenue.value = true;
        const now = new Date();
        const month = now.getMonth() + 1;
        const year = now.getFullYear();

        try {
            const service = useAxiosUtil<null, RevenueResponse>();
            await service.get(`admin/dashboard/monthly-revenue?month=${month}&year=${year}`);

            if (service.request.status === 200 && service.request.data) {
                revenueData.value = service.request.data;
            }
        } catch (err) {
            console.error('Failed to fetch revenue data:', err);
        } finally {
            isLoadingRevenue.value = false;
        }
        return;
    }

    isLoadingRevenue.value = true;
    const [month, year] = selectedMonth.value.split('-').map(Number);

    try {
        const service = useAxiosUtil<null, RevenueResponse>();
        await service.get(`admin/dashboard/monthly-revenue?month=${month}&year=${year}`);

        if (service.request.status === 200 && service.request.data) {
            revenueData.value = service.request.data;
        }
    } catch (err) {
        console.error('Failed to fetch revenue data:', err);
    } finally {
        isLoadingRevenue.value = false;
    }
};

// Initialize with dashboard data when it loads
onMounted(() => {
    if (dashboardData.revenue) {
        revenueData.value = dashboardData.revenue;
    }
});
</script>

<style scoped>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>