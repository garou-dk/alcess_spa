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
        header="Inventory Movement Data Table" 
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
            <Column field="stock_in" header="Stock In" :sortable="true">
                <template #body="slotProps">
                    {{ slotProps.data.stock_in.toLocaleString() }} units
                </template>
            </Column>
            <Column field="stock_out" header="Stock Out" :sortable="true">
                <template #body="slotProps">
                    {{ slotProps.data.stock_out.toLocaleString() }} units
                </template>
            </Column>
            <Column field="net_change" header="Net Change" :sortable="true">
                <template #body="slotProps">
                    <span :class="slotProps.data.net_change >= 0 ? 'text-green-600' : 'text-red-600'">
                        {{ slotProps.data.net_change >= 0 ? '+' : '' }}{{ slotProps.data.net_change.toLocaleString() }} units
                    </span>
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
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

interface StockInOutDataInterface {
    date: string;
    day: string;
    stock_in: number;
    stock_out: number;
    net_change: number;
}

const showModal = ref(false);
const tableData = ref<StockInOutDataInterface[]>([]);

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
        events: {
            click: function() {
                showModal.value = true;
            }
        }
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
            tableData.value = loadService.request.data;
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