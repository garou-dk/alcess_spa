<template>
    <BoxShadow>
        <div v-if="!loadService.request.loading" class="w-full">
            <p class="p-3 font-semibold text-gray-700">
                Sales Overview
            </p>
            <div v-if="chartOptions2.xaxis.categories.length > 0 && series2[0].data.length > 0">
                <VueApexCharts
                    type="line"
                    :options="chartOptions2"
                    :series="series2"
                    width="100%"
                    height="350"
                />
            </div>
        </div>
        <div v-else class="w-full flex justify-center p-2">
            <PageLoader />
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
import useAxiosUtil from '@/utils/AxiosUtil';
import { onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';
import VueApexCharts from 'vue3-apexcharts';

interface SalesDataPoint {
    date: string;
    day: string;
    orders_revenue: number;
    sale_items_revenue: number;
    total_revenue: number;
}

const series2 = ref([
    {
        name: "Sales",
        data: [0, 0, 0, 0, 0, 0, 0],
    },
]);
const chartOptions2 = ref({
    chart: {
        id: "weekly-sales-line",
        toolbar: {
            show: false,
        },
    },
    xaxis: {
        categories: [
            
        ],
    },
    stroke: {
        curve: "smooth",
        width: 3,
    },
    colors: ["#3B82F6"],
    markers: {
        size: 5,
        colors: ["#3B82F6"],
        strokeColors: "#fff",
        strokeWidth: 2,
    },
    dataLabels: {
        enabled: false,
    },
    grid: {
        row: {
            colors: ["#f3f3f3", "transparent"],
            opacity: 0.5,
        },
    },
    yaxis: {
        title: {
            text: "Sales (₱)",
        },
        labels: {
            formatter: (val) => `₱${(val / 1000).toFixed(0)}k`,
        },
    },
    tooltip: {
        y: {
            formatter: (val) => `₱${val.toLocaleString("en-PH")}`,
        },
    },
});

const loadService = useAxiosUtil<null, SalesDataPoint[]>();
const toast = useToast();

const load = async () => {
    await loadService.get('admin/dashboard/7-day-sales').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            series2.value[0].data = loadService.request.data.map((item) => item.total_revenue);
            chartOptions2.value.xaxis.categories = loadService.request.data.map((item) => item.day);
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