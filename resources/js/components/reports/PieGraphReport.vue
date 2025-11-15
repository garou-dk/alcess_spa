<template>
    <BoxShadow class="mb-5">
        <div class="flex w-full flex-wrap p-5">
            <div class="flex flex-wrap max-lg:w-full lg:w-1/2">
                <div
                    class="flex items-center justify-center p-2 max-lg:w-full lg:w-1/2 lg:border-r lg:border-b lg:border-gray-300">
                    <div>
                        <p class="text-center font-semibold text-gray-700">
                            Total Orders
                        </p>
                        <p class="text-center text-xl font-bold text-blue-600">
                            {{ data.orders_count }}
                        </p>
                        <p class="mt-1 text-center text-xs text-gray-600">
                            <span>Total orders for this month</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-center p-2 max-lg:w-full lg:w-1/2 lg:border-b lg:border-gray-300">
                    <div>
                        <p class="text-center font-semibold text-gray-700">
                            In-Store Sale Count
                        </p>
                        <p class="text-center text-xl font-bold text-green-600">
                            {{ data.sales_count }}
                        </p>
                        <p class="mt-1 text-center text-xs text-gray-600">
                            <span>Total sales count for this month</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-center p-2 max-lg:w-full lg:w-1/2 lg:border-r lg:border-gray-300">
                    <div>
                        <p class="text-center font-semibold text-gray-700">
                            Order Revenue
                        </p>
                        <p class="text-center text-xl font-bold text-blue-600">
                            {{ CurrencyUtil.formatCurrency(data.product_orders_revenue) }}
                        </p>
                        <p class="mt-1 text-center text-xs text-gray-600">
                            <span>Total revenue for this month from orders</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-center p-2 max-lg:w-full lg:w-1/2">
                    <div>
                        <p class="text-center font-semibold text-gray-700">
                            In-Store Sales Revenue
                        </p>
                        <p class="text-center text-xl font-bold text-green-600">
                            {{ CurrencyUtil.formatCurrency(data.sale_items_revenue) }}
                        </p>
                        <p class="mt-1 text-center text-xs text-gray-600">
                            <span>Total revenue for this month from in-store sales</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="max-lg:w-full lg:w-1/2">
                <p class="text-center font-semibold text-gray-700">
                    Sales Distribution
                </p>
                <div class="flex w-full justify-center p-5">
                    <VueApexCharts v-if="!loadService.request.loading && series[0] > 0" type="pie" :options="chartOptions" :series="series" width="100%" />
                    <div v-else class="w-full flex justify-center p-2">
                        <PageLoader />
                    </div>
                </div>
            </div>
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import { onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';
import VueApexCharts from 'vue3-apexcharts';

interface TotalRevenueInterface {
    orders_count: number;
    sales_count: number;
    product_orders_revenue: number;
    sale_items_revenue: number;
    total_revenue: number;
}

const series = ref<number[]>([0, 0]);
const chartOptions = ref({
    labels: ["Online Orders", "POS/In-Store Sales"],
    colors: ["#3B82F6", "#10B981"],
    tooltip: {
        y: {
            formatter: (val) => `${CurrencyUtil.formatCurrency(val)}`,
        },
    },
    legend: {
        position: "bottom",
    },
    responsive: [
        {
            breakpoint: 768,
            options: {
                chart: { width: "100%" },
                legend: { position: "bottom" },
            },
        },
    ],
});

const loadService = useAxiosUtil<null, TotalRevenueInterface>();
const toast = useToast();
const data: TotalRevenueInterface = reactive({
    orders_count: 0,
    sales_count: 0,
    product_orders_revenue: 0,
    sale_items_revenue: 0,
    total_revenue: 0
});

const load = async () => {
    await loadService.get('admin/dashboard/monthly-report').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            series.value = [
                loadService.request.data.product_orders_revenue,
                loadService.request.data.sale_items_revenue,
            ];
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