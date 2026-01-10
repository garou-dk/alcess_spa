<template>
    <BoxShadow>
        <div class="w-full" :class="responsive.getResponsiveClasses({
            mobile: 'p-3',
            tablet: 'p-4',
            desktop: 'p-6'
        })">
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 :class="responsive.getResponsiveTextSize('lg') + ' font-semibold text-gray-800'">
                        Inventory Movement
                    </h3>
                    <Button
                        @click="toggleDateFilter"
                        :icon="showDateFilter ? 'pi pi-chevron-up' : 'pi pi-filter'"
                        :label="showDateFilter ? 'Hide Filter' : 'Filter'"
                        size="small"
                        severity="secondary"
                        text
                        :class="responsive.getResponsiveClasses({
                            mobile: '!text-xs',
                            tablet: '!text-sm',
                            desktop: ''
                        })"
                    />
                </div>
                
                <!-- Date Range Filter -->
                <div v-if="showDateFilter" :class="responsive.getResponsiveClasses({
                    mobile: 'mt-3 p-3 bg-gray-50 rounded-lg border',
                    tablet: 'mt-4 p-4 bg-gray-50 rounded-lg border',
                    desktop: 'mt-4 p-4 bg-gray-50 rounded-lg border'
                })">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'flex flex-col gap-3',
                        tablet: 'flex items-end gap-4',
                        desktop: 'flex items-end gap-4'
                    })">
                        <div class="flex-1">
                            <label :class="responsive.getResponsiveClasses({
                                mobile: 'block text-xs font-medium text-gray-700 mb-2',
                                tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                desktop: 'block text-sm font-medium text-gray-700 mb-2'
                            })">Date Range</label>
                            <DatePicker 
                                v-model="dateRange" 
                                selectionMode="range" 
                                placeholder="Select Date Range"
                                class="w-full"
                                dateFormat="mm/dd/yy"
                            />
                        </div>
                        <div class="flex gap-2">
                            <Button
                                @click="applyDateFilter"
                                label="Apply"
                                size="small"
                                :loading="isLoading"
                                :class="[
                                    '!bg-blue-600 hover:!bg-blue-700 !border-blue-600',
                                    responsive.getResponsiveClasses({
                                        mobile: 'flex-1 !text-xs',
                                        tablet: 'w-auto !text-sm',
                                        desktop: 'w-auto'
                                    })
                                ]"
                            />
                            <Button
                                @click="clearDateFilter"
                                label="Clear"
                                size="small"
                                severity="secondary"
                                outlined
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'flex-1 !text-xs',
                                    tablet: 'w-auto !text-sm',
                                    desktop: 'w-auto'
                                })"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="tableData.length > 0" class="p-3">
                <VueApexCharts
                    type="area"
                    :options="inventoryChartOptions"
                    :series="inventorySeries"
                    width="100%"
                    height="300"
                    @click="() => showModal = true"
                    style="cursor: pointer;"
                />
            </div>
            <div v-else class="text-center py-8">
                <i class="pi pi-arrows-h text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">No inventory movement data</p>
                <p class="text-xs text-gray-400 mt-1">Stock movement will appear here</p>
            </div>
        </div>
    </BoxShadow>

    <!-- PrimeVue Dialog Modal -->
    <Dialog 
        v-model:visible="showModal" 
        modal 
        dismissableMask
        :style="responsive.getResponsiveClasses({
            mobile: '{ width: \'95vw\', maxWidth: \'100%\' }',
            tablet: '{ width: \'90vw\', maxWidth: \'800px\' }',
            desktop: '{ width: \'50rem\' }'
        })"
        :breakpoints="{ 
            '960px': '90vw',
            '640px': '95vw'
        }"
        :pt="{
            root: { class: 'rounded-xl' },
            header: { 
                class: responsive.getResponsiveClasses({
                    mobile: 'bg-blue-600 text-white rounded-t-xl px-3 py-3',
                    tablet: 'bg-blue-600 text-white rounded-t-xl px-4 py-3',
                    desktop: 'bg-blue-600 text-white rounded-t-xl px-6 py-4'
                })
            },
            content: { class: 'p-0' },
            mask: { class: 'backdrop-blur-sm' }
        }"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <i :class="responsive.getResponsiveClasses({
                    mobile: 'pi pi-arrows-h text-lg',
                    tablet: 'pi pi-arrows-h text-xl',
                    desktop: 'pi pi-arrows-h text-2xl'
                })"></i>
                <div>
                    <h3 :class="responsive.getResponsiveClasses({
                        mobile: 'text-base font-semibold',
                        tablet: 'text-lg font-semibold',
                        desktop: 'text-lg font-semibold'
                    })">Inventory Movement Details</h3>
                    <p :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs text-blue-100 mt-0.5',
                        tablet: 'text-xs text-blue-100 mt-0.5',
                        desktop: 'text-xs text-blue-100 mt-0.5'
                    })">Stock in/out tracking</p>
                </div>
            </div>
        </template>
        
        <!-- Desktop Table Layout -->
        <div class="hidden lg:block">
            <DataTable 
                :value="tableData" 
                :paginator="false" 
                responsiveLayout="scroll"
                stripedRows
                class="rounded-b-xl overflow-hidden"
            >
                <Column field="day" header="Day" :sortable="true">
                    <template #body="slotProps">
                        <span class="font-medium text-gray-900">{{ slotProps.data.day }}</span>
                    </template>
                </Column>
                <Column field="date" header="Date" :sortable="true">
                    <template #body="slotProps">
                        <span class="text-sm text-gray-600">{{ slotProps.data.date }}</span>
                    </template>
                </Column>
                <Column field="stock_in" header="Stock In" :sortable="true">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-arrow-down text-green-600"></i>
                            <span class="font-semibold text-green-600">
                                {{ slotProps.data.stock_in.toLocaleString() }} units
                            </span>
                        </div>
                    </template>
                </Column>
                <Column field="stock_out" header="Stock Out" :sortable="true">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-arrow-up text-red-600"></i>
                            <span class="font-semibold text-red-600">
                                {{ slotProps.data.stock_out.toLocaleString() }} units
                            </span>
                        </div>
                    </template>
                </Column>
                <Column field="net_change" header="Net Change" :sortable="true">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div 
                                class="px-2.5 py-1 rounded-full text-xs font-semibold"
                                :class="slotProps.data.net_change >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                            >
                                {{ slotProps.data.net_change >= 0 ? '+' : '' }}{{ slotProps.data.net_change.toLocaleString() }} units
                            </div>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Mobile/Tablet Card Layout -->
        <div class="block lg:hidden" :class="responsive.getResponsiveClasses({ mobile: 'p-3', tablet: 'p-4' })">
            <div v-if="tableData.length === 0" class="text-center py-8">
                <i class="pi pi-arrows-h text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">No inventory movement data</p>
                <p class="text-xs text-gray-400 mt-1">Stock movement will appear here</p>
            </div>
            <div v-else :class="'space-y-3 ' + (tableData.length > 10 ? 'max-h-96 overflow-y-auto' : '')">
                <div 
                    v-for="item in tableData" 
                    :key="item.date"
                    class="bg-gray-50 rounded-lg p-3 border border-gray-200"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <div :class="responsive.getResponsiveTextSize('base') + ' font-medium text-gray-900'">
                                {{ item.day }}
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                {{ item.date }}
                            </div>
                        </div>
                        <div 
                            class="px-2.5 py-1 rounded-full text-xs font-semibold"
                            :class="item.net_change >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                        >
                            {{ item.net_change >= 0 ? '+' : '' }}{{ item.net_change.toLocaleString() }}
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-arrow-down text-green-600 text-sm"></i>
                            <div>
                                <div class="text-xs text-gray-600">Stock In</div>
                                <div class="font-semibold text-green-600 text-sm">
                                    {{ item.stock_in.toLocaleString() }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-arrow-up text-red-600 text-sm"></i>
                            <div>
                                <div class="text-xs text-gray-600">Stock Out</div>
                                <div class="font-semibold text-red-600 text-sm">
                                    {{ item.stock_out.toLocaleString() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Show more indicator if there are many items -->
            <div v-if="tableData.length > 10" class="text-center mt-3 pt-3 border-t border-gray-200">
                <span class="text-xs text-gray-500">Showing inventory movement data</span>
            </div>
        </div>
    </Dialog>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import DatePicker from 'primevue/datepicker';
import { useToast } from 'vue-toastification';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useDashboardData } from '@/composables/useDashboardData';
import BoxShadow from '@/components/BoxShadow.vue';

import { useResponsive } from '@/composables/useResponsive';

interface StockInOutDataInterface {
    date: string;
    day: string;
    stock_in: number;
    stock_out: number;
    net_change: number;
}

// Reactive variables
const showModal = ref(false);
const showDateFilter = ref(false);
const dateRange = ref<Date[] | null>(null);
const isLoading = ref(false);

// Services
const toast = useToast();
const loadService = useAxiosUtil();
const { dashboardData } = useDashboardData();
const responsive = useResponsive();

// Local data state for filtered results
const filteredData = ref<StockInOutDataInterface[]>([]);

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

// Computed properties
const tableData = computed(() => {
    // Use filtered data if available, otherwise fall back to dashboard data
    if (filteredData.value.length > 0) {
        return filteredData.value;
    }
    return dashboardData.stock_in_out || [];
});

// Methods
const toggleDateFilter = () => {
    showDateFilter.value = !showDateFilter.value;
};

const applyDateFilter = async () => {
    if (!dateRange.value || dateRange.value.length === 0) {
        toast.error('Please select a date range');
        return;
    }

    isLoading.value = true;
    
    try {
        const [startDate, endDate] = dateRange.value;
        const params = {
            start_date: startDate.toISOString().split('T')[0],
            end_date: (endDate || startDate).toISOString().split('T')[0]
        };

        await loadService.get('admin/dashboard/stock-in-out', params);
        
        if (loadService.request.status === 200 && loadService.request.data) {
            filteredData.value = loadService.request.data;
            toast.success('Date filter applied successfully');
        } else {
            toast.error('Failed to apply date filter');
        }
    } catch (error) {
        toast.error('Error applying date filter');
    } finally {
        isLoading.value = false;
    }
};

const clearDateFilter = () => {
    dateRange.value = null;
    filteredData.value = [];
    toast.success('Date filter cleared');
};

// Watchers
watch(() => dashboardData.stock_in_out, (newData) => {
    if (newData && Array.isArray(newData) && filteredData.value.length === 0) {
        console.log('Dashboard stock data:', newData);
        inventorySeries.value[0].data = newData.map((item) => item.stock_in);
        inventorySeries.value[1].data = newData.map((item) => item.stock_out);
        
        console.log('Stock In series:', inventorySeries.value[0].data);
        console.log('Stock Out series:', inventorySeries.value[1].data);
        
        // Force update chart options to refresh categories
        inventoryChartOptions.value = {
            ...inventoryChartOptions.value,
            xaxis: {
                ...inventoryChartOptions.value.xaxis,
                categories: newData.map((item) => item.day)
            }
        };
        
        console.log('Chart categories:', inventoryChartOptions.value.xaxis.categories);
    }
}, { immediate: true });

watch(() => tableData.value, (newData) => {
    if (newData && Array.isArray(newData)) {
        console.log('Table stock data:', newData);
        inventorySeries.value[0].data = newData.map((item) => item.stock_in);
        inventorySeries.value[1].data = newData.map((item) => item.stock_out);
        
        console.log('Stock In series (filtered):', inventorySeries.value[0].data);
        console.log('Stock Out series (filtered):', inventorySeries.value[1].data);
        
        // Force update chart options to refresh categories
        inventoryChartOptions.value = {
            ...inventoryChartOptions.value,
            xaxis: {
                ...inventoryChartOptions.value.xaxis,
                categories: newData.map((item) => item.day)
            }
        };
        
        console.log('Chart categories (filtered):', inventoryChartOptions.value.xaxis.categories);
    }
}, { immediate: true });

// Scroll position preservation for mobile/tablet
let savedScrollPosition = 0

watch(showModal, (newValue) => {
    if (responsive.isMobile || responsive.isTablet) {
        if (newValue) {
            // Save scroll position when modal opens
            savedScrollPosition = window.pageYOffset || document.documentElement.scrollTop
        } else {
            // Restore scroll position when modal closes
            setTimeout(() => {
                window.scrollTo(0, savedScrollPosition)
            }, 100) // Small delay to ensure modal is fully closed
        }
    }
})
</script>