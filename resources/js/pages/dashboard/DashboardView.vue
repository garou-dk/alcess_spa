<template>
    <div>
        <BoxShadow class="mb-5">
            <div class="w-full">
                <h1 class="text-center w-full text-gray-700 text-2xl mb-1">
                    Best Selling Products
                </h1>
                <DataTable class="w-full" :value="paginate.data" :loading="loadProductService.request.loading"
                    columnResizeMode="expand">
                    <Column field="product_name" header="Product Name">
                        <template #body="{ data }">
                            <div class="flex items-center">
                                <Avatar v-if="data.product_image" shape="circle" :image="UrlUtil.getBaseAppUrl(
                                    `storage/images/product/${data.product_image}`,
                                )
                                    " class="aspect-square!" />
                                <Avatar v-else shape="circle" icon="pi pi-camera" />
                                <div class="ml-2 shrink">
                                    {{ data.product_name }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="category.category_name" header="Category" />
                    <Column field="product_quantity" header="Product Sold" />
                    <Column field="product_price" header="Price">
                        <template #body="{ data }">
                            {{ CurrencyUtil.formatCurrency(data.product_price) }}
                        </template>
                    </Column>
                    <template #empty>
                        <p>No result found</p>
                    </template>
                </DataTable>
            </div>
        </BoxShadow>
        <BoxShadow class="mb-5">
            <div class="flex flex-wrap w-full p-5">
                <div class="flex flex-wrap max-lg:w-full lg:w-1/2">
                    <div
                        class="p-2 flex items-center justify-center max-lg:w-full lg:w-1/2 lg:border-r lg:border-gray-500 lg:border-b">
                        <div>
                            <p class="text-center text-gray-700 font-semibold">Total Orders</p>
                            <p class="text-center font-bold text-green-600 text-xl">388</p>
                            <p class="text-center space-x-2">
                                <i class="pi pi-arrow-up text-green-600" />
                                <span class="text-gray-600">16.52% Previous Day 333</span>
                            </p>
                        </div>
                    </div>
                    <div
                        class="p-2 flex items-center justify-center max-lg:w-full lg:w-1/2 lg:border-b lg:border-gray-500">
                        <div>
                            <p class="text-center text-gray-700 font-semibold">Total Shipping Orders</p>
                            <p class="text-center font-bold text-green-600 text-xl">537</p>
                            <p class="text-center space-x-2">
                                <i class="pi pi-arrow-up text-green-600" />
                                <span class="text-gray-600">21.77% Previous Day 441</span>
                            </p>
                        </div>
                    </div>
                    <div
                        class="p-2 flex items-center justify-center max-lg:w-full lg:w-1/2 lg:border-r lg:border-gray-500">
                        <div>
                            <p class="text-center text-gray-700 font-semibold">Items Ordered</p>
                            <p class="text-center font-bold text-red-600 text-xl">996</p>
                            <p class="text-center space-x-2">
                                <i class="pi pi-arrow-down text-red-600" />
                                <span class="text-gray-600">1.00% Previous Day 1006</span>
                            </p>
                        </div>
                    </div>
                    <div class="p-2 flex items-center justify-center max-lg:w-full lg:w-1/2">
                        <div>
                            <p class="text-center text-gray-700 font-semibold">Items Shipped</p>
                            <p class="text-center font-bold text-red-600 text-xl">672</p>
                            <p class="text-center space-x-2">
                                <i class="pi pi-arrow-down text-red-600" />
                                <span class="text-gray-600">16.21% Previous Day 802</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="max-lg:w-full lg:w-1/2">
                    <p class="text-center text-gray-700 font-semibold">Current Shipping Status</p>
                    <div class="w-full flex justify-center p-5">
                        <VueApexCharts type="pie" :options="chartOptions" :series="series" width="100%" />
                    </div>
                </div>
            </div>
        </BoxShadow>
        <div class="mb-5 flex flex-wrap">
            <div class="lg:w-1/2 max-lg:w-full p-2">
                <BoxShadow>
                    <div class="w-full">
                        <p class="text-gray-600 font-semibold p-3">Sales Overview</p>
                        <div>
                            <VueApexCharts type="line" :options="chartOptions2" :series="series2" width="100%"
                                height="350" />
                        </div>
                    </div>
                </BoxShadow>
            </div>
            <div class="lg:w-1/2 max-lg:w-full p-2">
                <BoxShadow>
                    <div class="w-full">
                        <p class="text-gray-600 font-semibold p-3">Low Stock</p>
                        <DataTable class="w-full" :value="paginate.data" :loading="loadProductService.request.loading"
                            columnResizeMode="expand">
                            <Column field="product_name" header="Product Name">
                                <template #body="{ data }">
                                    <div class="flex items-center">
                                        <Avatar v-if="data.product_image" shape="circle" :image="UrlUtil.getBaseAppUrl(
                                            `storage/images/product/${data.product_image}`,
                                        )
                                            " class="aspect-square!" />
                                        <Avatar v-else shape="circle" icon="pi pi-camera" />
                                        <div class="ml-2 shrink">
                                            {{ data.product_name }}
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column field="category.category_name" header="Category" />
                            <Column field="low_stock_threshold" header="In Stock" />
                            <template #empty>
                                <p>No result found</p>
                            </template>
                        </DataTable>
                    </div>
                </BoxShadow>
            </div>
        </div>
        <BoxShadow class="mb-3">
            <div class="w-full">
                <p class="text-gray-600 font-semibold p-3">Pending Orders</p>
                <DataTable class="w-full" :value="users" :loading="loadProductService.request.loading"
                    columnResizeMode="expand">
                    <Column field="full_name" header="Name">
                        <template #body="{ data }">
                            <div class="flex items-center">
                                <Avatar shape="circle" icon="pi pi-camera" />
                                <div class="ml-2 shrink">
                                    {{ data.full_name }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="date_ordered" header="Date Ordered">
                        <template #body="{ data }">
                            {{ DateUtil.formatToMonthDayYear(data.date_ordered) }}
                        </template>
                    </Column>
                    <template #empty>
                        <p>No result found</p>
                    </template>
                </DataTable>
            </div>
        </BoxShadow>
    </div>
</template>
<script setup lang="ts">
import DataTableInterface from '@/interfaces/DataTableInterface';
import { ProductInterface, ProductSearchErrorInterface, ProductSearchInterface } from '@/interfaces/ProductInterface';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';
import { onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';
import VueApexCharts from 'vue3-apexcharts';

const loadProductService = useAxiosUtil<ProductSearchInterface, DataTableInterface<ProductInterface>>();
const toast = useToast();
const series = ref<number[]>([70, 30]);
const chartOptions = ref({
    labels: ['Ordered', 'Shipped'],
    colors: ['#fa625e', '#03b5a9'],
    legend: {
        position: 'bottom'
    },
    responsive: [
        {
            breakpoint: 768,
            options: {
                chart: {
                    width: '100%'
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    ]
});

const series2 = ref([
    {
        name: 'Sales',
        data: [26668, 33335, 22223, 40001, 44446, 28890, 20001]
    }
]);
const chartOptions2 = ref({
    chart: {
        id: 'weekly-sales-line',
        toolbar: {
            show: false
        }
    },
    xaxis: {
        categories: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
    },
    stroke: {
        curve: 'smooth',
        width: 3
    },
    colors: ['#00CED1'],
    markers: {
        size: 5,
        colors: ['#00CED1'],
        strokeColors: '#fff',
        strokeWidth: 2
    },
    dataLabels: {
        enabled: false
    },
    grid: {
        row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.5
        }
    },
    yaxis: {
        title: {
            text: 'Sales'
        }
    },
    tooltip: {
        y: {
            formatter: (val) => `₱${val.toLocaleString('en-PH')}`
        }
    }
});

const users: {
    full_name: string;
    date_ordered: string;
}[] = [
        {
            full_name: "Alice Johnson",
            date_ordered: "2025-07-20"
        },
        {
            full_name: "Bob Smith",
            date_ordered: "2025-07-21"
        },
        {
            full_name: "Charlie Davis",
            date_ordered: "2025-07-22"
        }
    ];

const form: ProductSearchInterface = reactive({
    search: null,
    limit: 3,
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
                toast.error(loadProductService.request.message ?? "Please try again");
                if (loadProductService.request.errors) {
                    Object.keys(loadProductService.request.errors).forEach((key) => {
                        errors[key] = loadProductService.request.errors[key];
                    });
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
