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
                    @click="showModal = true"
                    style="cursor: pointer;"
                />
            </div>
        </div>
        <div v-else class="w-full flex justify-center p-2">
            <PageLoader />
        </div>
    </BoxShadow>

    <!-- PrimeVue Dialog Modal -->
    <Dialog 
        v-model:visible="showModal" 
        modal 
        header="Sales Data Table" 
        :style="{ width: '50rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <DataTable 
            :value="tableData" 
            :paginator="false" 
            responsiveLayout="scroll"
            stripedRows
        >
            <Column field="day" header="Day" :sortable="true"></Column>
            <Column field="date" header="Date" :sortable="true"></Column>
            <Column field="orders_revenue" header="Orders Revenue" :sortable="true">
                <template #body="slotProps">
                    {{ CurrencyUtil.formatCurrency(slotProps.data.orders_revenue) }}
                </template>
            </Column>
            <Column field="sale_items_revenue" header="Sale Items Revenue" :sortable="true">
                <template #body="slotProps">
                    {{ CurrencyUtil.formatCurrency(slotProps.data.sale_items_revenue) }}
                </template>
            </Column>
            <Column field="total_revenue" header="Total Revenue" :sortable="true">
                <template #body="slotProps">
                    {{ CurrencyUtil.formatCurrency(slotProps.data.total_revenue) }}
                </template>
            </Column>
        </DataTable>
    </Dialog>
</template>

<script setup lang="ts">
import useAxiosUtil from '@/utils/AxiosUtil';
import { onMounted, ref } from 'vue';
import { useToast } from 'vue-toastification';
import VueApexCharts from 'vue3-apexcharts';
import CurrencyUtil from '@/utils/CurrencyUtil';

interface SalesDataPoint {
    date: string;
    day: string;
    orders_revenue: number;
    sale_items_revenue: number;
    total_revenue: number;
}

const showModal = ref(false);
const tableData = ref<SalesDataPoint[]>([]);

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
        events: {
            click: function() {
                showModal.value = true;
            }
        }
    },
    xaxis: {
        categories: [],
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
            formatter: (val) => CurrencyUtil.formatCurrency(val),
        },
    },
    tooltip: {
        y: {
            formatter: (val) => CurrencyUtil.formatCurrency(val),
        },
    },
});

const loadService = useAxiosUtil<null, SalesDataPoint[]>();
const toast = useToast();

const load = async () => {
    await loadService.get('admin/dashboard/7-day-sales').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            tableData.value = loadService.request.data;
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