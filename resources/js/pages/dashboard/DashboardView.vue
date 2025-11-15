<template>
    <div>
        <div class="mb-5 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <TotalRevenueReport />
            <ProductStats />
            <OrderPending />
            <InventoryValue />
        </div>

        <TopSoldProducts />

        <PieGraphReport />

        <!-- Charts Section -->
        <div class="mb-5 flex flex-wrap">
            <div class="p-2 max-lg:w-full lg:w-1/2">
                <SalesPrevious7Days />
            </div>
            <div class="p-2 max-lg:w-full lg:w-1/2">
                <CategorySales />
            </div>
        </div>

        <!-- Inventory Alerts & Recent Activity -->
        <div class="mb-5 flex flex-wrap">
            <div class="p-2 max-lg:w-full lg:w-1/2">
                <BatchForm />
            </div>
            <div class="p-2 max-lg:w-full lg:w-1/2">
                <BoxShadow>
                    <div class="w-full">
                        <p class="p-3 font-semibold text-gray-700">
                            Inventory Movement
                        </p>
                        <div class="p-3">
                            <VueApexCharts
                                type="area"
                                :options="inventoryChartOptions"
                                :series="inventorySeries"
                                width="100%"
                                height="300"
                            />
                        </div>
                    </div>
                </BoxShadow>
            </div>
        </div>

        <!-- Pending Orders -->
        <BoxShadow class="mb-3">
            <div class="w-full">
                <div class="flex items-center justify-between p-3">
                    <p class="font-semibold text-gray-700">
                        Recent Pending Orders
                    </p>
                    <button class="text-sm text-blue-600 hover:underline">
                        View All
                    </button>
                </div>
                <DataTable
                    class="w-full"
                    :value="users"
                    :loading="loadProductService.request.loading"
                    columnResizeMode="expand"
                >
                    <Column field="full_name" header="Customer">
                        <template #body="{ data }">
                            <div class="flex items-center">
                                <Avatar shape="circle" icon="pi pi-user" />
                                <div class="ml-2 shrink">
                                    {{ data.full_name }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="date_ordered" header="Date Ordered">
                        <template #body="{ data }">
                            {{
                                DateUtil.formatToMonthDayYear(data.date_ordered)
                            }}
                        </template>
                    </Column>
                    <Column field="order_amount" header="Amount">
                        <template #body="{ data }">
                            {{ CurrencyUtil.formatCurrency(data.order_amount) }}
                        </template>
                    </Column>
                    <Column header="Status">
                        <template #body="{ data }">
                            <span
                                :class="[
                                    'rounded-full px-2 py-1 text-xs',
                                    data.status === 'pending'
                                        ? 'bg-orange-100 text-orange-600'
                                        : data.status === 'processing'
                                          ? 'bg-blue-100 text-blue-600'
                                          : 'bg-green-100 text-green-600',
                                ]"
                            >
                                {{ data.status }}
                            </span>
                        </template>
                    </Column>
                    <template #empty>
                        <p>No pending orders</p>
                    </template>
                </DataTable>
            </div>
        </BoxShadow>
    </div>
</template>
<script setup lang="ts">
import BatchForm from "@/components/forms/BatchForm.vue";
import CategorySales from "@/components/reports/CategorySales.vue";
import InventoryValue from "@/components/reports/InventoryValue.vue";
import OrderPending from "@/components/reports/OrderPending.vue";
import PieGraphReport from "@/components/reports/PieGraphReport.vue";
import ProductStats from "@/components/reports/ProductStats.vue";
import SalesPrevious7Days from "@/components/reports/SalesPrevious7Days.vue";
import TopSoldProducts from "@/components/reports/TopSoldProducts.vue";
import TotalRevenueReport from "@/components/reports/TotalRevenueReport.vue";
import DataTableInterface from "@/interfaces/DataTableInterface";
import {
    ProductInterface,
    ProductSearchErrorInterface,
    ProductSearchInterface,
} from "@/interfaces/ProductInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import CurrencyUtil from "@/utils/CurrencyUtil";
import DateUtil from "@/utils/DateUtil";
import UrlUtil from "@/utils/UrlUtil";
import { onMounted, reactive, ref } from "vue";
import { useToast } from "vue-toastification";
import VueApexCharts from "vue3-apexcharts";

const loadProductService = useAxiosUtil<
    ProductSearchInterface,
    DataTableInterface<ProductInterface>
>();
const toast = useToast();

// Dashboard Metrics
const totalProducts = ref(245);
const lowStockCount = ref(12);
const pendingOrdersCount = ref(28);
const todayOrdersCount = ref(8);
const inventoryValue = ref(3456780);
const totalOrders = ref(156);
const shippingOrders = ref(42);
const itemsOrdered = ref(534);
const itemsShipped = ref(489);

// Period filter
const period = ref("week");

const changePeriod = (newPeriod: string) => {
    period.value = newPeriod;
    // Reload data based on period
    load();
};

// Sales Distribution Chart
const series = ref<number[]>([65, 35]);
const chartOptions = ref({
    labels: ["Online Orders", "POS/In-Store Sales"],
    colors: ["#3B82F6", "#10B981"],
    legend: {
        position: "bottom",
    },
    responsive: [
        {
            breakpoint: 768,
            options: {
                chart: {
                    width: "100%",
                },
                legend: {
                    position: "bottom",
                },
            },
        },
    ],
});

// Sales Overview Chart
const series2 = ref([
    {
        name: "Sales",
        data: [26668, 33335, 22223, 40001, 44446, 28890, 35420],
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
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
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

// Category Revenue Chart
const categorySeries = ref([
    {
        name: "Revenue",
        data: [125000, 98000, 87000, 72000, 65000],
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
        categories: ["Electronics", "Clothing", "Food", "Books", "Toys"],
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

// Inventory Movement Chart
const inventorySeries = ref([
    {
        name: "Stock In",
        data: [120, 95, 150, 110, 135, 125, 140],
    },
    {
        name: "Stock Out",
        data: [85, 110, 95, 125, 100, 115, 105],
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
        categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
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

const users: {
    full_name: string;
    date_ordered: string;
    order_amount: number;
    status: string;
}[] = [
    {
        full_name: "Alice Johnson",
        date_ordered: "2025-11-14",
        order_amount: 5240,
        status: "pending",
    },
    {
        full_name: "Bob Smith",
        date_ordered: "2025-11-14",
        order_amount: 3150,
        status: "processing",
    },
    {
        full_name: "Charlie Davis",
        date_ordered: "2025-11-13",
        order_amount: 8900,
        status: "pending",
    },
];

const form: ProductSearchInterface = reactive({
    search: null,
    limit: 5,
    page: 1,
    category_id: null,
    status: null,
    available_online: null,
    low_stock: null,
});

const errors: ProductSearchErrorInterface = reactive({
    search: [],
    limit: [],
    page: [],
    category_id: [],
    status: [],
    available_online: [],
    low_stock: [],
});

const paginate: DataTableInterface<ProductInterface> = reactive({
    data: [],
    current_page: 0,
    from: 0,
    to: 0,
    total: 0,
    last_page: 0,
});

const clearError = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearError();
    if (!form.limit) {
        errors.limit.push("Limit is required");
    }
    if (!form.page) {
        errors.page.push("Page is required");
    }

    const hasErrors = [errors.limit.length > 0, errors.page.length > 0];
    return hasErrors.includes(true) ? false : form;
};

const load = async (resetPage: boolean = false) => {
    if (resetPage) {
        form.page = 1;
    }
    if (!form.limit) {
        form.limit = 5;
    }

    const data = validate();

    if (data) {
        await loadProductService.get("admin/products", data).then(() => {
            if (
                loadProductService.request.status === 200 &&
                loadProductService.request.data
            ) {
                Object.keys(paginate).forEach((key) => {
                    paginate[key] = loadProductService.request.data[key];
                });
            } else {
                toast.error(
                    loadProductService.request.message ?? "Please try again",
                );
                if (loadProductService.request.errors) {
                    Object.keys(loadProductService.request.errors).forEach(
                        (key) => {
                            errors[key] =
                                loadProductService.request.errors[key];
                        },
                    );
                }
            }
        });
    } else {
        toast.error("Please fill in the required fields.");
    }
};

onMounted(() => {
    load();
});
</script>