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
                        Top Categories by Revenue
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
            <div v-if="categorySeries[0].data.length > 0 && categoryChartOptions.xaxis.categories.length > 0">
                <VueApexCharts
                    type="bar"
                    :options="categoryChartOptions"
                    :series="categorySeries"
                    width="100%"
                    height="350"
                    @click="() => showModal = true"
                    style="cursor: pointer;"
                />
            </div>
            <div v-else class="text-center py-8">
                <i class="pi pi-chart-bar text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">No category data</p>
                <p class="text-xs text-gray-400 mt-1">Category revenue will appear here</p>
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
                    mobile: 'pi pi-chart-bar text-lg',
                    tablet: 'pi pi-chart-bar text-xl',
                    desktop: 'pi pi-chart-bar text-2xl'
                })"></i>
                <div>
                    <h3 :class="responsive.getResponsiveClasses({
                        mobile: 'text-base font-semibold',
                        tablet: 'text-lg font-semibold',
                        desktop: 'text-lg font-semibold'
                    })">Category Revenue Details</h3>
                    <p :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs text-blue-100 mt-0.5',
                        tablet: 'text-xs text-blue-100 mt-0.5',
                        desktop: 'text-xs text-blue-100 mt-0.5'
                    })">Revenue breakdown by category</p>
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
                <Column field="category_name" header="Category" :sortable="true">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2 py-2">
                            <div class="w-3 h-3 rounded-full bg-purple-600"></div>
                            <span class="font-medium text-gray-900">{{ slotProps.data.category_name }}</span>
                        </div>
                    </template>
                </Column>
                <Column field="orders_revenue" header="Online Revenue" :sortable="true">
                    <template #body="slotProps">
                        <span class="font-semibold text-blue-600">
                            {{ CurrencyUtil.formatCurrency(slotProps.data.orders_revenue) }}
                        </span>
                    </template>
                </Column>
                <Column field="sales_revenue" header="POS Revenue" :sortable="true">
                    <template #body="slotProps">
                        <span class="font-semibold text-green-600">
                            {{ CurrencyUtil.formatCurrency(slotProps.data.sales_revenue) }}
                        </span>
                    </template>
                </Column>
                <Column field="total_revenue" header="Total Revenue" :sortable="true">
                    <template #body="slotProps">
                        <span class="font-bold text-gray-900">
                            {{ CurrencyUtil.formatCurrency(slotProps.data.total_revenue) }}
                        </span>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Mobile/Tablet Card Layout -->
        <div class="block lg:hidden" :class="responsive.getResponsiveClasses({ mobile: 'p-3', tablet: 'p-4' })">
            <div v-if="tableData.length === 0" class="text-center py-8">
                <i class="pi pi-chart-bar text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">No category data</p>
                <p class="text-xs text-gray-400 mt-1">Category revenue will appear here</p>
            </div>
            <div v-else :class="'space-y-3 ' + (tableData.length > 8 ? 'max-h-96 overflow-y-auto' : '')">
                <div 
                    v-for="category in tableData" 
                    :key="category.category_id"
                    class="bg-gray-50 rounded-lg p-3 border border-gray-200"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full bg-purple-600"></div>
                            <div>
                                <div :class="responsive.getResponsiveTextSize('base') + ' font-medium text-gray-900'">
                                    {{ category.category_name }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div :class="responsive.getResponsiveTextSize('base') + ' font-bold text-gray-900'">
                                {{ CurrencyUtil.formatCurrency(category.total_revenue) }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Total Revenue
                            </div>
                        </div>
                    </div>
                    
                    <!-- Revenue Breakdown -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="text-center p-2 bg-blue-50 rounded-lg">
                            <div class="text-xs text-blue-600 font-medium mb-1">Online</div>
                            <div class="font-semibold text-blue-600 text-sm">
                                {{ CurrencyUtil.formatCurrency(category.orders_revenue) }}
                            </div>
                        </div>
                        <div class="text-center p-2 bg-green-50 rounded-lg">
                            <div class="text-xs text-green-600 font-medium mb-1">POS</div>
                            <div class="font-semibold text-green-600 text-sm">
                                {{ CurrencyUtil.formatCurrency(category.sales_revenue) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Show more indicator if there are many items -->
            <div v-if="tableData.length > 8" class="text-center mt-3 pt-3 border-t border-gray-200">
                <span class="text-xs text-gray-500">Showing category revenue data</span>
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
import CurrencyUtil from '@/utils/CurrencyUtil';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useDashboardData } from '@/composables/useDashboardData';
import BoxShadow from '@/components/BoxShadow.vue';

import { useResponsive } from '@/composables/useResponsive';

interface CategoryRevenueInterface {
    category_id: number;
    category_name: string;
    orders_revenue: number;
    sales_revenue: number;
    total_revenue: number;
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
const filteredData = ref<CategoryRevenueInterface[]>([]);

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
            formatter: (val: number) => CurrencyUtil.formatCurrency(val),
        },
    },
    tooltip: {
        y: {
            formatter: (val: number) => CurrencyUtil.formatCurrency(val),
        },
    },
});

// Computed properties
const tableData = computed(() => {
    // Use filtered data if available, otherwise fall back to dashboard data
    let data = [];
    if (filteredData.value.length > 0) {
        data = filteredData.value;
    } else {
        data = dashboardData.category_sales || [];
    }
    
    // Always filter out categories with zero or negative revenue
    return data.filter(item => item.total_revenue > 0);
});

// Methods
const toggleDateFilter = () => {
    showDateFilter.value = !showDateFilter.value;
};

const openModal = () => {
    showModal.value = true;
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

        await loadService.get('admin/dashboard/category-sales', params);
        
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
watch(() => dashboardData.category_sales, (newData) => {
    if (newData && Array.isArray(newData) && filteredData.value.length === 0) {
        // Filter out categories with zero or negative revenue
        const validCategories = newData.filter(item => item.total_revenue > 0);
        console.log('Dashboard data - Original:', newData);
        console.log('Dashboard data - Filtered:', validCategories);
        
        // Update chart data
        categorySeries.value[0].data = validCategories.map((item) => item.total_revenue);
        
        // Force update chart options to refresh categories
        categoryChartOptions.value = {
            ...categoryChartOptions.value,
            xaxis: {
                ...categoryChartOptions.value.xaxis,
                categories: validCategories.map((item) => item.category_name)
            }
        };
    }
}, { immediate: true });

watch(() => tableData.value, (newData) => {
    if (newData && Array.isArray(newData)) {
        // Filter out categories with zero or negative revenue
        const validCategories = newData.filter(item => item.total_revenue > 0);
        console.log('Table data - Original:', newData);
        console.log('Table data - Filtered:', validCategories);
        
        // Update chart data
        categorySeries.value[0].data = validCategories.map((item) => item.total_revenue);
        
        // Force update chart options to refresh categories
        categoryChartOptions.value = {
            ...categoryChartOptions.value,
            xaxis: {
                ...categoryChartOptions.value.xaxis,
                categories: validCategories.map((item) => item.category_name)
            }
        };
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