<template>
    <BoxShadow>
        <div class="w-full">
            
            <div 
                :class="[
                    'flex items-center justify-between border-b border-gray-200',
                    responsive.getResponsiveClasses({
                        mobile: 'px-3 py-3',
                        tablet: 'px-4 py-3',
                        desktop: 'px-5 py-4' // Original desktop padding
                    })
                ]"
            >
                <div>
                    <h1 :class="responsive.getResponsiveTextSize('lg') + ' font-semibold text-gray-800 dashboard-section-title'">
                        New Orders
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">Orders awaiting admin review and acceptance</p>
                </div>
                <div class="flex items-center gap-3">
                    <div :class="'flex items-center gap-2 text-gray-600 ' + responsive.getResponsiveTextSize('sm')">
                        <i class="pi pi-bell text-blue-600"></i>
                        <span class="font-medium">{{ users.length }} New</span>
                    </div>
                    <RouterLink :to="{ name: 'admin.order.index', query: { status: 'Processing' } }">
                        <button 
                            type="button" 
                            :class="[
                                'font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors cursor-pointer',
                                responsive.getResponsiveClasses({
                                    mobile: 'px-3 py-2 text-xs',
                                    tablet: 'px-3 py-2 text-sm',
                                    desktop: 'px-4 py-2 text-sm' // Original desktop button
                                })
                            ]"
                        >
                            View All
                        </button>
                    </RouterLink>
                </div>
            </div>

            <!-- Desktop Table Layout - Hidden on mobile/tablet -->
            <div class="hidden lg:block">
                <DataTable
                    class="w-full"
                    :value="users"
                    columnResizeMode="expand"
                    :paginator="users.length > 5"
                    :rows="5"
                    stripedRows
                >
                    <Column field="full_name" header="Customer" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3 py-1">
                                <Avatar 
                                    shape="circle" 
                                    icon="pi pi-user"
                                    class="bg-orange-100 text-orange-600"
                                    size="large"
                                />
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">
                                        {{ data.user.full_name }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="created_at" header="Date Ordered" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-gray-400 text-sm"></i>
                                <span class="text-sm text-gray-600">
                                    {{ DateUtil.formatToMonthDayYear(data.created_at) }}
                                </span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Status">
                        <template #body>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                <i class="pi pi-bell mr-1"></i>
                                New
                            </span>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-8">
                            <i class="pi pi-check-circle text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">No new orders</p>
                            <p class="text-xs text-gray-400 mt-1">All orders have been reviewed</p>
                        </div>
                    </template>
                </DataTable>
            </div>

            <!-- Mobile/Tablet Card Layout - Shown on mobile/tablet -->
            <div class="block lg:hidden p-3">
                <div v-if="users.length === 0" class="text-center py-8">
                    <i class="pi pi-check-circle text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 font-medium">No new orders</p>
                    <p class="text-xs text-gray-400 mt-1">All orders have been reviewed</p>
                </div>
                <div v-else :class="'space-y-3 ' + (users.length > 5 ? 'max-h-96 overflow-y-auto' : '')">
                    <div 
                        v-for="order in users.slice(0, 5)" 
                        :key="order.order_id"
                        class="bg-gray-50 rounded-lg p-3 border border-gray-200 hover:bg-gray-100 transition-colors"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <Avatar 
                                    shape="circle" 
                                    icon="pi pi-user"
                                    class="w-10 h-10 bg-orange-100 text-orange-600"
                                    size="large"
                                />
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ order.user.full_name }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <i class="pi pi-calendar text-gray-400 text-xs"></i>
                                        <span class="text-xs text-gray-600">
                                            {{ DateUtil.formatToMonthDayYear(order.created_at) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    <i class="pi pi-bell mr-1"></i>
                                    New
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Show more indicator if there are more than 5 items -->
                <div v-if="users.length > 5" class="text-center mt-3 pt-3 border-t border-gray-200">
                    <span class="text-xs text-gray-500">Showing 5 of {{ users.length }} new orders</span>
                </div>
            </div>
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import DateUtil from '@/utils/DateUtil';
import { computed } from 'vue';
import { useDashboardData } from '@/composables/useDashboardData';
import { useResponsive } from '@/composables/useResponsive';

const { dashboardData } = useDashboardData();
const responsive = useResponsive();

const users = computed(() => dashboardData.pending_orders_list || []);
</script>