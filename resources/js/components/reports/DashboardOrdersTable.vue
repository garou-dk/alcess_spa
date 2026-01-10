<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div 
            :class="[
                'border-b border-gray-200 bg-gray-50',
                responsive.getResponsiveClasses({
                    mobile: 'px-3 py-3',
                    tablet: 'px-4 py-3',
                    desktop: 'px-5 py-4' // Original desktop padding
                })
            ]"
        >
            <div 
                :class="responsive.getResponsiveClasses({
                    mobile: 'flex flex-col gap-3',
                    tablet: 'flex flex-col gap-3',
                    desktop: 'flex items-center justify-between' // Original desktop layout
                })"
            >
                <div>
                    <h2 :class="responsive.getResponsiveTextSize('lg') + ' font-semibold text-gray-800'">Confirmed Orders</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Today's confirmed orders ready for delivery</p>
                </div>
                <div 
                    :class="responsive.getResponsiveClasses({
                        mobile: 'flex flex-col gap-2',
                        tablet: 'flex flex-row gap-3',
                        desktop: 'flex items-center gap-3' // Original desktop layout
                    })"
                >
                    <!-- Order Type Filter -->
                    <Select 
                        v-model="selectedOrderType" 
                        :options="orderTypes" 
                        option-label="label" 
                        option-value="value" 
                        placeholder="Filter by Type"
                        :class="[
                            responsive.getResponsiveClasses({
                                mobile: 'w-full text-sm',
                                tablet: 'w-48 text-sm',
                                desktop: 'w-48' // Original desktop width
                            }),
                            responsive.getResponsiveClasses({
                                mobile: '!h-9',
                                tablet: '!h-10',
                                desktop: '' // Original desktop height
                            })
                        ]"
                    />
                    <Button
                        icon="pi pi-refresh"
                        label="Refresh"
                        :size="responsive.getResponsiveClasses({
                            mobile: 'small',
                            tablet: 'small', 
                            desktop: 'small' // Keep consistent
                        })"
                        @click="loadOrders"
                        :loading="isLoading"
                        :class="[
                            '!bg-blue-600 hover:!bg-blue-700 !border-blue-600',
                            responsive.getResponsiveClasses({
                                mobile: 'w-full !h-9 !text-sm !px-3',
                                tablet: 'w-auto !h-10 !text-sm !px-4',
                                desktop: 'w-auto' // Original desktop width
                            })
                        ]"
                    />
                </div>
            </div>
        </div>

        <!-- Desktop Table Layout - Hidden on mobile/tablet -->
        <div class="hidden lg:block">
            <DataTable
                :value="filteredOrders"
                :loading="isLoading"
                :paginator="filteredOrders.length > 10"
                :rows="10"
                stripedRows
                class="w-full"
                @row-click="onRowClick"
                :rowClass="() => 'cursor-pointer hover:bg-gray-50'"
            >
                <Column field="order_public_id" header="Order ID" :sortable="true">
                    <template #body="{ data }">
                        <span class="text-xs font-medium text-gray-700">{{ data.order_public_id }}</span>
                    </template>
                </Column>
                
                <Column field="user.full_name" header="Customer" :sortable="true">
                    <template #body="{ data }">
                        <div class="flex items-center gap-2">
                            <Avatar 
                                icon="pi pi-user"
                                class="bg-blue-100 text-blue-600"
                                size="small"
                            />
                            <span class="text-sm text-gray-700">{{ data.user?.full_name || 'N/A' }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="order_type" header="Order Type" :sortable="true">
                    <template #body="{ data }">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                            'bg-purple-50 text-purple-700': data.order_type === 'Delivery',
                            'bg-green-50 text-green-700': data.order_type === 'Pickup'
                        }">
                            <i :class="data.order_type === 'Delivery' ? 'pi pi-truck' : 'pi pi-shopping-bag'" class="mr-1"></i>
                            {{ data.order_type }}
                        </span>
                    </template>
                </Column>

                <Column field="total_amount" header="Amount" :sortable="true">
                    <template #body="{ data }">
                        <span class="font-semibold text-gray-900">
                            {{ CurrencyUtil.formatCurrency(data.total_amount || 0) }}
                        </span>
                    </template>
                </Column>

                <Column field="status" header="Status" :sortable="true">
                    <template #body="{ data }">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="getStatusClass(data.status)">
                            {{ data.status }}
                        </span>
                    </template>
                </Column>

                <Column field="created_at" header="Order Date" :sortable="true">
                    <template #body="{ data }">
                        <span class="text-sm text-gray-600">
                            {{ DateUtil.formatToMonthDayYear(data.created_at) }}
                        </span>
                    </template>
                </Column>

                <template #empty>
                    <div class="text-center py-8">
                        <i class="pi pi-shopping-cart text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No orders found</p>
                    </div>
                </template>
            </DataTable>
        </div>

        <!-- Mobile/Tablet Card Layout - Shown on mobile/tablet -->
        <div class="block lg:hidden" :class="responsive.getResponsiveClasses({ mobile: 'p-3', tablet: 'p-4' })">
            <div v-if="filteredOrders.length === 0" class="text-center py-8">
                <i class="pi pi-shopping-cart text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No orders found</p>
            </div>
            <div v-else :class="'space-y-3 ' + (filteredOrders.length > 10 ? 'max-h-96 overflow-y-auto' : '')">
                <div 
                    v-for="order in filteredOrders.slice(0, 10)" 
                    :key="order.order_id"
                    class="bg-gray-50 rounded-lg p-3 border border-gray-200 hover:bg-gray-100 transition-colors cursor-pointer"
                    @click="onRowClick({ data: order })"
                >
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <Avatar 
                                icon="pi pi-user"
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'w-10 h-10',
                                    tablet: 'w-12 h-12'
                                }) + ' bg-blue-100 text-blue-600'"
                                size="small"
                            />
                            <div class="flex-1 min-w-0">
                                <div :class="responsive.getResponsiveTextSize('base') + ' font-medium text-gray-900 truncate'">
                                    {{ order.user?.full_name || 'N/A' }}
                                </div>
                                <div class="text-xs font-medium text-gray-600 mt-0.5">
                                    {{ order.order_public_id }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right ml-3">
                            <div :class="responsive.getResponsiveTextSize('base') + ' font-bold text-gray-900'">
                                {{ CurrencyUtil.formatCurrency(order.total_amount || 0) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="{
                                'bg-purple-50 text-purple-700': order.order_type === 'Delivery',
                                'bg-green-50 text-green-700': order.order_type === 'Pickup'
                            }">
                                <i :class="order.order_type === 'Delivery' ? 'pi pi-truck' : 'pi pi-shopping-bag'" class="mr-1"></i>
                                {{ order.order_type }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(order.status)">
                                {{ order.status }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ DateUtil.formatToMonthDayYear(order.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- Show more indicator if there are more than 10 items -->
            <div v-if="filteredOrders.length > 10" class="text-center mt-3 pt-3 border-t border-gray-200">
                <span class="text-xs text-gray-500">Showing 10 of {{ filteredOrders.length }} orders</span>
            </div>
        </div>

        <!-- Order Details Modal -->
        <Dialog
            v-model:visible="showOrderModal"
            modal
            :style="responsive.getResponsiveClasses({
                mobile: '{ width: \'95vw\', maxWidth: \'100%\' }',
                tablet: '{ width: \'90vw\', maxWidth: \'800px\' }',
                desktop: '{ width: \'90vw\', maxWidth: \'1200px\' }'
            })"
            :dismissableMask="true"
            :breakpoints="{ 
                '960px': '95vw',
                '640px': '98vw'
            }"
        >
            <template #header>
                <div class="flex items-center gap-3">
                    <i :class="responsive.getResponsiveClasses({
                        mobile: 'pi pi-shopping-cart text-lg text-white',
                        tablet: 'pi pi-shopping-cart text-xl text-white',
                        desktop: 'pi pi-shopping-cart text-2xl text-white'
                    })"></i>
                    <div>
                        <h3 :class="responsive.getResponsiveClasses({
                            mobile: 'text-lg font-bold text-white',
                            tablet: 'text-xl font-bold text-white',
                            desktop: 'text-xl font-bold text-white'
                        })">Order Details</h3>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-blue-100',
                            tablet: 'text-sm text-blue-100',
                            desktop: 'text-sm text-blue-100'
                        })">{{ selectedOrder?.order_public_id }}</p>
                    </div>
                </div>
            </template>
            
            <ViewOrderForm 
                v-if="selectedOrder" 
                :data="selectedOrder"
                @close="showOrderModal = false"
            />
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import { useToast } from 'vue-toastification';
import { useResponsive } from '@/composables/useResponsive';
import ViewOrderForm from '@/components/forms/ViewOrderForm.vue';

const toast = useToast();
const responsive = useResponsive();
const loadService = useAxiosUtil<null, IOrder[]>();
const orders = ref<IOrder[]>([]);
const isLoading = ref(false);
const selectedOrderType = ref('');
const showOrderModal = ref(false);
const selectedOrder = ref<IOrder | null>(null);

const orderTypes = ref([
    { label: 'All Types', value: '' },
    { label: 'Pickup', value: 'Pickup' },
    { label: 'Delivery', value: 'Delivery' },
]);

const filteredOrders = computed(() => {
    if (!selectedOrderType.value) {
        return orders.value;
    }
    return orders.value.filter(order => order.order_type === selectedOrderType.value);
});

const getStatusClass = (status: string) => {
    const statusClasses: Record<string, string> = {
        'Processing': 'bg-yellow-50 text-yellow-700',
        'Confirmed': 'bg-blue-50 text-blue-700',
        'Shipped': 'bg-purple-50 text-purple-700',
        'Completed': 'bg-green-50 text-green-700',
        'Cancelled': 'bg-red-50 text-red-700',
    };
    return statusClasses[status] || 'bg-gray-50 text-gray-700';
};

const onRowClick = (event: any) => {
    selectedOrder.value = event.data;
    showOrderModal.value = true;
};

const loadOrders = async () => {
    isLoading.value = true;
    await loadService.get('admin/orders').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            // Filter for Confirmed orders that were confirmed today (based on payment confirmation date)
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Start of today
            
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1); // Start of tomorrow
            
            orders.value = loadService.request.data.filter((order: IOrder) => {
                if (order.status !== 'Confirmed') return false;
                
                // Check if order was confirmed today
                if (!order.date_paid_confirmed) return false;
                
                const confirmDate = new Date(order.date_paid_confirmed);
                return confirmDate >= today && confirmDate < tomorrow;
            });
        } else {
            toast.error(loadService.request.message ?? 'Failed to load orders');
        }
    }).finally(() => {
        isLoading.value = false;
    });
};

onMounted(() => {
    loadOrders();
});
</script>
