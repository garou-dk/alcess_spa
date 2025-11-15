<template>
    <div>
        <BoxShadow class="mb-3">
            <div class="flex w-full space-x-4 p-4">
                <div class="grow">
                    <InputForm
                        :errors="[]"
                        id="sales-report"
                        label-name="Report"
                        tag="label"
                    >
                        <Select
                            v-model="selectedReport"
                            :options="[
                                {
                                    label: 'Inventory Report',
                                    value: 'inventory',
                                },
                                // { label: 'Delivery Report', value: 'delivery' },
                                { label: 'List of Customers', value: 'order' },
                                { label: 'Sales Report', value: 'sales' },
                            ]"
                            id="sales-report"
                            placeholder="Select Sales Report"
                            option-label="label"
                            option-value="value"
                            fluid
                        />
                    </InputForm>
                </div>
                <div v-if="selectedReport === 'sales'" class="grow">
                    <InputForm
                        :errors="[]"
                        id="date-range"
                        label-name="Date Range"
                        tag="label"
                    >
                        <DatePicker
                            id="date-range"
                            placeholder="Select Date Range"
                            selectionMode="range"
                            :manualInput="false"
                            fluid
                            v-model="dateRange"
                        />
                    </InputForm>
                </div>
                <div class="flex flex-wrap items-end gap-2">
                    <Button
                        label="Print"
                        icon="pi pi-print"
                        class="primary-bg"
                    />
                </div>
            </div>
        </BoxShadow>

        <BoxShadow class="mb-2">
            <div class="w-full p-5">
                <InventoryReport v-if="selectedReport === 'inventory'" />
                <DeliveryReport v-else-if="selectedReport === 'delivery'" />
                <CustomerListReport v-else-if="selectedReport === 'order'" />
                <SalesReport :startDate="startDate" :endDate="endDate" v-else-if="selectedReport === 'sales' && startDate !== null && endDate !== null" />
            </div>
        </BoxShadow>

        <!-- <BoxShadow>
            <div class="w-full p-5">
                <h2 class="mb-4 text-xl font-bold text-gray-800">
                    Orders vs Sales (Monthly)
                </h2>
                <VueApexCharts
                    type="bar"
                    :options="options"
                    :series="options.series"
                    height="400"
                />
            </div>
        </BoxShadow> -->
    </div>
</template>
<script setup lang="ts">
import CustomerListReport from "@/components/reports/CustomerListReport.vue";
import DeliveryReport from "@/components/reports/DeliveryReport.vue";
import InventoryReport from "@/components/reports/InventoryReport.vue";
import SalesReport from "@/components/reports/SalesReport.vue";
import DateUtil from "@/utils/DateUtil";
import { ref, watch } from "vue";
import VueApexCharts from "vue3-apexcharts";

const selectedReport = ref<string>("inventory");

const dateRange = ref<Date[]>([]);

const startDate = ref<string | null>(null);
const endDate = ref<string | null>(null);

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
