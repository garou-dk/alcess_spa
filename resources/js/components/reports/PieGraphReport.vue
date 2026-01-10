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
                        Sales Distribution
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
                <p class="text-xs text-gray-500 mt-1">Revenue breakdown by channel</p>
                
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
            <div class="flex w-full justify-center">
                <VueApexCharts 
                    v-if="series[0] > 0" 
                    type="pie" 
                    :options="chartOptions" 
                    :series="series" 
                    width="100%" 
                    @click="openModal"
                    style="cursor: pointer;"
                />
                <div v-else class="text-center py-8">
                    <i class="pi pi-chart-pie text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 font-medium">No sales data</p>
                    <p class="text-xs text-gray-400 mt-1">Start making sales to see distribution</p>
                </div>
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
            tablet: '{ width: \'90vw\', maxWidth: \'700px\' }',
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
                    mobile: 'pi pi-chart-pie text-lg',
                    tablet: 'pi pi-chart-pie text-xl',
                    desktop: 'pi pi-chart-pie text-2xl'
                })"></i>
                <div>
                    <h3 :class="responsive.getResponsiveClasses({
                        mobile: 'text-base font-semibold',
                        tablet: 'text-lg font-semibold',
                        desktop: 'text-lg font-semibold'
                    })">Sales Distribution Details</h3>
                    <p :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs text-blue-100 mt-0.5',
                        tablet: 'text-xs text-blue-100 mt-0.5',
                        desktop: 'text-xs text-blue-100 mt-0.5'
                    })">Detailed breakdown of sales channels</p>
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
                <Column field="type" header="Sales Type" :sortable="true">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2 py-2">
                            <div 
                                class="w-3 h-3 rounded-full"
                                :class="slotProps.data.type === 'Online' ? 'bg-blue-600' : 'bg-green-600'"
                            ></div>
                            <span class="font-medium text-gray-900">{{ slotProps.data.type }}</span>
                        </div>
                    </template>
                </Column>
                <Column field="count" header="Count" :sortable="true">
                    <template #body="slotProps">
                        <span class="font-semibold text-gray-900">{{ slotProps.data.count }}</span>
                    </template>
                </Column>
                <Column field="revenue" header="Revenue" :sortable="true">
                    <template #body="slotProps">
                        <span class="font-semibold text-blue-600">
                            {{ CurrencyUtil.formatCurrency(slotProps.data.revenue) }}
                        </span>
                    </template>
                </Column>
                <Column field="percentage" header="Percentage" :sortable="true">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[100px]">
                                <div 
                                    class="h-2 rounded-full transition-all"
                                    :class="slotProps.data.type === 'Online' ? 'bg-blue-600' : 'bg-green-600'"
                                    :style="{ width: slotProps.data.percentage + '%' }"
                                ></div>
                            </div>
                            <span class="font-semibold text-gray-900 min-w-[50px]">
                                {{ slotProps.data.percentage }}%
                            </span>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Mobile/Tablet Card Layout -->
        <div class="block lg:hidden" :class="responsive.getResponsiveClasses({ mobile: 'p-3', tablet: 'p-4' })">
            <div v-if="tableData.length === 0" class="text-center py-8">
                <i class="pi pi-chart-pie text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">No sales data</p>
                <p class="text-xs text-gray-400 mt-1">Start making sales to see distribution</p>
            </div>
            <div v-else class="space-y-3">
                <div 
                    v-for="item in tableData" 
                    :key="item.type"
                    class="bg-gray-50 rounded-lg p-3 border border-gray-200"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div 
                                class="w-4 h-4 rounded-full"
                                :class="item.type === 'Online' ? 'bg-blue-600' : 'bg-green-600'"
                            ></div>
                            <div>
                                <div :class="responsive.getResponsiveTextSize('base') + ' font-medium text-gray-900'">
                                    {{ item.type }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ item.count }} {{ item.count === 1 ? 'sale' : 'sales' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div :class="responsive.getResponsiveTextSize('base') + ' font-semibold text-blue-600'">
                                {{ CurrencyUtil.formatCurrency(item.revenue) }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ item.percentage }}%
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div 
                            class="h-2 rounded-full transition-all"
                            :class="item.type === 'Online' ? 'bg-blue-600' : 'bg-green-600'"
                            :style="{ width: item.percentage + '%' }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </Dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import Dialog from 'primevue/dialog'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import DatePicker from 'primevue/datepicker'
import { useToast } from 'vue-toastification'
import CurrencyUtil from '@/utils/CurrencyUtil'
import useAxiosUtil from '@/utils/AxiosUtil'
import { useDashboardData } from '@/composables/useDashboardData'
import BoxShadow from '@/components/BoxShadow.vue'

import { useResponsive } from '@/composables/useResponsive';

interface SalesDistributionRow {
    type: string
    count: number
    revenue: number
    percentage: string
}

// Reactive variables
const showModal = ref(false)
const showDateFilter = ref(false)
const dateRange = ref<Date[] | null>(null)
const isLoading = ref(false)
const series = ref<number[]>([0, 0])

// Services
const toast = useToast()
const loadService = useAxiosUtil()
const { dashboardData } = useDashboardData()
const responsive = useResponsive()

// Local data state for filtered results
const filteredData = ref({
    orders_count: 0,
    sales_count: 0,
    product_orders_revenue: 0,
    sale_items_revenue: 0,
    total_revenue: 0
})

// Chart configuration
const chartOptions = ref({
    labels: ["Online", "Walk-In"],
    colors: ["#3B82F6", "#10B981"],
    tooltip: {
        y: {
            formatter: (val: number) => `${CurrencyUtil.formatCurrency(val)}`,
        },
    },
    legend: {
        position: "bottom" as const,
    },
    responsive: [
        {
            breakpoint: 768,
            options: {
                chart: { width: "100%" },
                legend: { position: "bottom" as const },
            },
        },
    ],
    chart: {
        events: {
            click: function() {
                showModal.value = true
            }
        }
    }
})

// Computed properties
const data = computed(() => {
    // Use filtered data if available, otherwise fall back to dashboard data
    if (filteredData.value.total_revenue > 0) {
        return filteredData.value
    }
    return dashboardData.monthly_report || {
        orders_count: 0,
        sales_count: 0,
        product_orders_revenue: 0,
        sale_items_revenue: 0,
        total_revenue: 0
    }
})

const tableData = computed<SalesDistributionRow[]>(() => {
    const total = data.value.total_revenue || 1 // Avoid division by zero
    return [
        {
            type: 'Online',
            count: data.value.orders_count,
            revenue: data.value.product_orders_revenue,
            percentage: ((data.value.product_orders_revenue / total) * 100).toFixed(2)
        },
        {
            type: 'Walk-In',
            count: data.value.sales_count,
            revenue: data.value.sale_items_revenue,
            percentage: ((data.value.sale_items_revenue / total) * 100).toFixed(2)
        }
    ]
})

// Methods
const toggleDateFilter = () => {
    showDateFilter.value = !showDateFilter.value
}

const openModal = () => {
    showModal.value = true
}

const applyDateFilter = async () => {
    if (!dateRange.value || dateRange.value.length === 0) {
        toast.error('Please select a date range')
        return
    }

    isLoading.value = true
    
    try {
        const [startDate, endDate] = dateRange.value
        const params = {
            start_date: startDate.toISOString().split('T')[0],
            end_date: (endDate || startDate).toISOString().split('T')[0]
        }

        await loadService.get('admin/dashboard/monthly-report', params)
        
        if (loadService.request.status === 200 && loadService.request.data) {
            filteredData.value = loadService.request.data
            toast.success('Date filter applied successfully')
        } else {
            toast.error('Failed to apply date filter')
        }
    } catch (error) {
        toast.error('Error applying date filter')
    } finally {
        isLoading.value = false
    }
}

const clearDateFilter = () => {
    dateRange.value = null
    filteredData.value = {
        orders_count: 0,
        sales_count: 0,
        product_orders_revenue: 0,
        sale_items_revenue: 0,
        total_revenue: 0
    }
    toast.success('Date filter cleared')
}

// Watchers
watch(() => dashboardData.monthly_report, (newData) => {
    if (newData && filteredData.value.total_revenue === 0) {
        series.value = [
            newData.product_orders_revenue,
            newData.sale_items_revenue,
        ]
    }
}, { immediate: true })

watch(() => data.value, (newData) => {
    if (newData) {
        series.value = [
            newData.product_orders_revenue,
            newData.sale_items_revenue,
        ]
    }
}, { immediate: true })

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