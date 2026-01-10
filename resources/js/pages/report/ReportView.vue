<template>
    <div :class="responsive.getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-3 px-3',
        tablet: 'min-h-screen bg-gray-50 py-4 px-4',
        desktop: 'min-h-screen bg-gray-50 py-4 sm:py-6 px-3 sm:px-6'
    })">
        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <!-- Filters Card -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'mb-3 rounded-lg bg-white border border-gray-200 shadow-sm',
            tablet: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm',
            desktop: 'mb-4 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm'
        })">
            <div :class="responsive.getResponsiveClasses({
                mobile: 'px-3 py-3',
                tablet: 'px-4 py-4',
                desktop: 'px-5 py-4'
            })">
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'space-y-4',
                    tablet: 'space-y-4 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end',
                    desktop: 'flex flex-wrap gap-4 items-end'
                })">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'flex-1 min-w-[200px]',
                        desktop: 'flex-1 min-w-[250px]'
                    })">
                        <label :class="responsive.getResponsiveClasses({
                            mobile: 'block text-xs font-medium text-gray-700 mb-2',
                            tablet: 'block text-sm font-medium text-gray-700 mb-2',
                            desktop: 'block text-sm font-medium text-gray-700 mb-2'
                        })">Report Type</label>
                        <Select
                            v-model="selectedReport"
                            :options="[
                                { label: 'Sales Report', value: 'combined-sales' },
                                {
                                    label: 'Inventory Report',
                                    value: 'inventory',
                                },
                                { label: 'Order Status Report', value: 'delivery' },
                                { label: 'List of Customers', value: 'order' },
                                { label: 'Customer Review Report', value: 'customer-review' },
                            ]"
                            placeholder="Select Report"
                            option-label="label"
                            option-value="value"
                            :class="[
                                'w-full',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'flex-1 min-w-[200px]',
                        desktop: 'flex-1 min-w-[250px]'
                    })">
                        <label :class="responsive.getResponsiveClasses({
                            mobile: 'block text-xs font-medium text-gray-700 mb-2',
                            tablet: 'block text-sm font-medium text-gray-700 mb-2',
                            desktop: 'block text-sm font-medium text-gray-700 mb-2'
                        })">Date Range</label>
                        <DatePicker
                            placeholder="Select Date Range"
                            selectionMode="range"
                            :manualInput="false"
                            v-model="dateRange"
                            :class="[
                                'w-full',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                    <div v-if="selectedReport === 'combined-sales'" :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'flex-1 min-w-[150px]',
                        desktop: 'flex-1 min-w-[200px]'
                    })">
                        <label :class="responsive.getResponsiveClasses({
                            mobile: 'block text-xs font-medium text-gray-700 mb-2',
                            tablet: 'block text-sm font-medium text-gray-700 mb-2',
                            desktop: 'block text-sm font-medium text-gray-700 mb-2'
                        })">Sales Type</label>
                        <Select
                            v-model="salesType"
                            :options="[
                                { label: 'All Types', value: 'all' },
                                { label: 'Walk-In Only', value: 'walk-in' },
                                { label: 'Online Only', value: 'online' },
                            ]"
                            placeholder="Select Type"
                            option-label="label"
                            option-value="value"
                            :class="[
                                'w-full',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div :class="responsive.getResponsiveClasses({
                mobile: 'flex items-center justify-between px-3 py-3 border-b border-gray-200 bg-gray-50',
                tablet: 'flex items-center justify-between px-4 py-4 border-b border-gray-200 bg-gray-50',
                desktop: 'flex items-center justify-between px-5 py-4 border-b border-gray-200'
            })">
                <div>
                    <h1 :class="responsive.getResponsiveClasses({
                        mobile: 'text-base font-bold text-gray-800',
                        tablet: 'text-lg font-bold text-gray-800',
                        desktop: 'text-lg font-semibold text-gray-800'
                    })">{{ getReportTitle() }}</h1>
                    <p :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs text-gray-500 mt-0.5',
                        tablet: 'text-xs text-gray-500 mt-0.5',
                        desktop: 'text-xs text-gray-500 mt-0.5'
                    })">{{ getReportDescription() }}</p>
                </div>
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'flex flex-col items-end',
                    tablet: 'flex flex-col items-end',
                    desktop: 'flex items-center gap-2 text-sm text-gray-600'
                })">
                    <i :class="responsive.getResponsiveClasses({
                        mobile: 'hidden',
                        tablet: 'hidden',
                        desktop: 'pi pi-chart-bar text-blue-600'
                    })"></i>
                    <span :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs text-gray-600 font-medium',
                        tablet: 'text-xs text-gray-600 font-medium',
                        desktop: 'font-medium'
                    })">Report Data</span>
                </div>
            </div>
            <div :class="responsive.getResponsiveClasses({
                mobile: 'w-full p-3',
                tablet: 'w-full p-4',
                desktop: 'w-full p-5'
            })">
                <InventoryReport :startDate="startDate" :endDate="endDate" v-if="selectedReport === 'inventory'" :key="`inventory-${startDate}-${endDate}`" />
                <DeliveryReport :startDate="startDate" :endDate="endDate" v-else-if="selectedReport === 'delivery'" :key="`delivery-${startDate}-${endDate}`" />
                <CustomerListReport :startDate="startDate" :endDate="endDate" v-else-if="selectedReport === 'order'" :key="`customers-${startDate}-${endDate}`" />
                <CombinedSalesReport :startDate="startDate" :endDate="endDate" :salesType="salesType" v-else-if="selectedReport === 'combined-sales'" :key="`combined-${startDate}-${endDate}-${salesType}`" />
                <CustomerReviewReport :startDate="startDate" :endDate="endDate" v-else-if="selectedReport === 'customer-review'" :key="`review-${startDate}-${endDate}`" />
            </div>
        </div>

        <!-- <div :class="responsive.getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden mt-3',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden mt-4',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden mt-4'
        })">
            <div :class="responsive.getResponsiveClasses({
                mobile: 'w-full p-3',
                tablet: 'w-full p-4',
                desktop: 'w-full p-5'
            })">
                <h2 :class="responsive.getResponsiveClasses({
                    mobile: 'mb-3 text-lg font-bold text-gray-800',
                    tablet: 'mb-4 text-xl font-bold text-gray-800',
                    desktop: 'mb-4 text-xl font-bold text-gray-800'
                })">
                    Orders vs Sales (Monthly)
                </h2>
                <VueApexCharts
                    type="bar"
                    :options="options"
                    :series="options.series"
                    :height="responsive.getResponsiveClasses({
                        mobile: '300',
                        tablet: '350',
                        desktop: '400'
                    })"
                />
            </div>
        </div> -->
    </div>
</template>
<script setup lang="ts">
import CustomerListReport from "@/components/reports/CustomerListReport.vue";
import DeliveryReport from "@/components/reports/DeliveryReport.vue";
import InventoryReport from "@/components/reports/InventoryReport.vue";
import CombinedSalesReport from "@/components/reports/CombinedSalesReport.vue";
import CustomerReviewReport from "@/components/reports/CustomerReviewReport.vue";
import DateTimeDisplay from "@/components/DateTimeDisplay.vue";
import DateUtil from "@/utils/DateUtil";
import { useResponsive } from "@/composables/useResponsive";
import { ref, watch } from "vue";
import VueApexCharts from "vue3-apexcharts";

const responsive = useResponsive();


const selectedReport = ref<string>("combined-sales");

const dateRange = ref<Date[]>([]);

const startDate = ref<string | null>(null);
const endDate = ref<string | null>(null);
const salesType = ref<string>('all');

watch(dateRange, () => {
    if (dateRange.value.length > 0 && dateRange.value.length === 2 && dateRange.value[0] !== null && dateRange.value[1] !== null) {
        startDate.value = DateUtil.formatYYYYMMDD(dateRange.value[0]);
        endDate.value = DateUtil.formatYYYYMMDD(dateRange.value[1]);
    }
    else {
        startDate.value = null;
        endDate.value = null;
    }
})

const getReportTitle = () => {
    const titles = {
        'inventory': 'Inventory Report',
        'delivery': 'Order Status Report',
        'order': 'List of Customers',
        'combined-sales': 'Sales Report',
        'customer-review': 'Customer Review Report'
    };
    return titles[selectedReport.value] || 'Report';
};

const getReportDescription = () => {
    const descriptions = {
        'inventory': 'Current stock levels and product inventory',
        'delivery': 'Order status and tracking information',
        'order': 'Complete list of registered customers',
        'combined-sales': 'Combined walk-in and online sales data',
        'customer-review': 'Customer feedback and product reviews'
    };
    return descriptions[selectedReport.value] || 'View detailed report data';
};

const printReport = () => {
    window.print();
};

const options = {
    chart: {
        type: "bar",
        height: 400,
        toolbar: { show: false },
    },
    series: [
        {
            name: "Orders",
            data: [30, 40, 35, 50, 49, 60],
        },
        {
            name: "Sales",
            data: [20, 35, 40, 60, 70, 80],
        },
    ],
    colors: ["#3B82F6", "#10B981"], // Tailwind blue & green
    xaxis: {
        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: "45%",
            endingShape: "rounded",
        },
    },
    dataLabels: {
        enabled: false,
    },
    legend: {
        position: "top",
    },
    tooltip: {
        y: {
            formatter: (val) => `₱${val}k`,
        },
    },
};
</script>
