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

interface CategoryRevenueInterface {
    category_id: number;
    category_name: string;
    orders_revenue: number;
    sales_revenue: number;
    total_revenue: number;
}

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
            formatter: (val) => `₱${(val / 1000).toFixed(0)}k`,
        },
    },
    tooltip: {
        y: {
            formatter: (val) => `₱${val.toLocaleString("en-PH")}`,
        },
    },
});

const loadService = useAxiosUtil<null, CategoryRevenueInterface[]>();
const toast = useToast();

const load = async () => {
    await loadService.get('admin/dashboard/category-sales').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
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