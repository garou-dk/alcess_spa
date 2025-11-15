<template>
    <BoxShadow>
        <div v-if="!loadService.request.loading" class="w-full">
            <p class="p-3 font-semibold text-gray-700">
                Inventory Movement
            </p>
            <div v-if="inventorySeries[0].data.length > 0 && inventorySeries[1].data.length > 0 && inventoryChartOptions.xaxis.categories.length > 0" class="p-3">
                <VueApexCharts
                    type="area"
                    :options="inventoryChartOptions"
                    :series="inventorySeries"
                    width="100%"
                    height="300"
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
import { onMounted, ref } from 'vue';
import { useToast } from 'vue-toastification';
import VueApexCharts from 'vue3-apexcharts';

interface StockInOutDataInterface {
    date: string;
    day: string;
    stock_in: number;
    stock_out: number;
    net_change: number;
}

const inventorySeries = ref([
    {
        name: "Stock In",
        data: [],
    },
    {
        name: "Stock Out",
        data: [],
    },
]);
const inventoryChartOptions = ref({
    chart: {
        id: "inventory-movement",
        toolbar: {
            show: false,
        },
    },
    stroke: {
        curve: "smooth",
        width: 2,
    },
    colors: ["#10B981", "#EF4444"],
    dataLabels: {
        enabled: false,
    },
    xaxis: {
        categories: [],
    },
    yaxis: {
        title: {
            text: "Units",
        },
    },
    legend: {
        position: "top",
    },
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.3,
        },
    },
});

const loadService = useAxiosUtil<null, StockInOutDataInterface[]>();
const toast = useToast();

const load = async () => {
    await loadService.get('admin/dashboard/stock-in-out').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            inventorySeries.value[0].data = loadService.request.data.map((item) => item.stock_in);
            inventorySeries.value[1].data = loadService.request.data.map((item) => item.stock_out);
            inventoryChartOptions.value.xaxis.categories = loadService.request.data.map((item) => item.day);
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