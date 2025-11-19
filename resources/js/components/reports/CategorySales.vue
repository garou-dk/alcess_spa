<template>
    <BoxShadow>
        <div v-if="!loadService.request.loading" class="w-full">
            <p class="p-3 font-semibold text-gray-700">
                Top Categories by Revenue
            </p>
            <div v-if="categorySeries[0].data.length > 0 && categoryChartOptions.xaxis.categories.length > 0">
                <VueApexCharts
                    type="bar"
                    :options="categoryChartOptions"
                    :series="categorySeries"
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
        header="Category Revenue Data Table" 
        :style="{ width: '50rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <DataTable 
            :value="tableData" 
            :paginator="false" 
            responsiveLayout="scroll"
            stripedRows
        >
            <Column field="category_name" header="Category" :sortable="true"></Column>
            <Column field="orders_revenue" header="Orders Revenue" :sortable="true">
                <template #body="slotProps">
                    {{ CurrencyUtil.formatCurrency(slotProps.data.orders_revenue) }}
                </template>
            </Column>
            <Column field="sales_revenue" header="Sales Revenue" :sortable="true">
                <template #body="slotProps">
                    {{ CurrencyUtil.formatCurrency(slotProps.data.sales_revenue) }}
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
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import CurrencyUtil from '@/utils/CurrencyUtil';

interface CategoryRevenueInterface {
    category_id: number;
    category_name: string;
    orders_revenue: number;
    sales_revenue: number;
    total_revenue: number;
}

const showModal = ref(false);
const tableData = ref<CategoryRevenueInterface[]>([]);

const categorySeries = ref([
    {
        name: "Revenue",
        data: [],
    },
]);

const categoryChartOptions = ref({
    chart: {
        id: "category-revenue-bar",
        toolbar: {
            show: false,
        },
        events: {
            click: function() {
                showModal.value = true;
            }
        }
    },
    plotOptions: {
        bar: {
            borderRadius: 8,
            horizontal: false,
            columnWidth: "60%",
        },
    },
    dataLabels: {
        enabled: false,
    },
    xaxis: {
        categories: [],
    },
    colors: ["#8B5CF6"],
    yaxis: {
        title: {
            text: "Revenue (₱)",
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

const loadService = useAxiosUtil<null, CategoryRevenueInterface[]>();
const toast = useToast();

const load = async () => {
    await loadService.get('admin/dashboard/category-sales').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            tableData.value = loadService.request.data;
            categorySeries.value[0].data = loadService.request.data.map((item) => item.total_revenue);
            categoryChartOptions.value.xaxis.categories = loadService.request.data.map((item) => item.category_name);
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