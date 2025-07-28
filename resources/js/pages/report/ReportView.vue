<template>
    <div>
        <BoxShadow class="mb-3">
            <div class="flex space-x-4 p-4 w-full">
                <div class="grow">
                    <InputForm :errors="[]" id="sales-report" label-name="Sales Report" tag="label">
                        <Select id="sales-report" placeholder="Select Sales Report" show-clear class="w-full select"
                            fluid />
                    </InputForm>
                </div>
                <div class="grow">
                    <InputForm :errors="[]" id="date-range" label-name="Date Range" tag="label">
                        <DatePicker id="date-range" placeholder="Select Date Range" selectionMode="range"
                            :manualInput="false" fluid />
                    </InputForm>
                </div>
                <div class="flex items-end flex-wrap gap-2">
                    <Button label="Print" icon="pi pi-print" class="primary-bg" />
                    <Button label="Export" icon="pi pi-file-export" class="primary-bg" />
                </div>
            </div>
        </BoxShadow>

        <BoxShadow>
            <div class="w-full p-5">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Orders vs Sales (Monthly)</h2>
                <VueApexCharts type="bar" :options="options" :series="options.series" height="400" />
            </div>
        </BoxShadow>
    </div>
</template>
<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts';

const options = {
    chart: {
        type: 'bar',
        height: 400,
        toolbar: { show: false }
    },
    series: [
        {
            name: 'Orders',
            data: [30, 40, 35, 50, 49, 60]
        },
        {
            name: 'Sales',
            data: [20, 35, 40, 60, 70, 80]
        }
    ],
    colors: ['#3B82F6', '#10B981'], // Tailwind blue & green
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '45%',
            endingShape: 'rounded'
        }
    },
    dataLabels: {
        enabled: false
    },
    legend: {
        position: 'top'
    },
    tooltip: {
        y: {
            formatter: (val) => `₱${val}k`
        }
    }
};

</script>