<template>
    <div :class="getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-3 px-3',
        tablet: 'min-h-screen bg-gray-50 py-4 px-4',
        desktop: 'min-h-screen bg-gray-50 py-4 sm:py-6 px-3 sm:px-6'
    })">
        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <!-- Filters Card -->
        <div :class="getResponsiveClasses({
            mobile: 'mb-3 rounded-lg bg-white border border-gray-200 shadow-sm',
            tablet: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm',
            desktop: 'mb-4 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm'
        })">
            <div :class="getResponsiveClasses({
                mobile: 'border-b border-gray-200 px-3 py-3',
                tablet: 'border-b border-gray-200 px-4 py-3',
                desktop: 'border-b border-gray-200 px-4 py-3'
            })">
                <div class="flex items-center gap-2">
                    <i :class="getResponsiveClasses({
                        mobile: 'pi pi-filter text-blue-600 text-sm',
                        tablet: 'pi pi-filter text-blue-600',
                        desktop: 'pi pi-filter text-blue-600'
                    })"></i>
                    <h2 :class="getResponsiveClasses({
                        mobile: 'text-sm font-semibold text-gray-800',
                        tablet: 'text-base font-semibold text-gray-800',
                        desktop: 'font-semibold text-gray-800'
                    })">Filter Online Sales</h2>
                </div>
            </div>
            <div :class="getResponsiveClasses({
                mobile: 'p-3',
                tablet: 'p-4',
                desktop: 'p-4'
            })">
                <div :class="getResponsiveClasses({
                    mobile: 'space-y-4',
                    tablet: 'space-y-4 md:space-y-0 md:grid md:grid-cols-11 md:gap-4 md:items-end',
                    desktop: 'grid grid-cols-1 md:grid-cols-11 gap-4 items-end'
                })">
                    <div :class="getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full md:col-span-5',
                        desktop: 'md:col-span-5'
                    })">
                        <label :class="getResponsiveClasses({
                            mobile: 'block text-xs font-semibold text-gray-700 mb-2',
                            tablet: 'block text-sm font-semibold text-gray-700 mb-2',
                            desktop: 'block text-sm font-semibold text-gray-700 mb-2'
                        })">Search</label>
                        <InputGroup>
                            <InputGroupAddon>
                                <i :class="getResponsiveClasses({
                                    mobile: 'pi pi-search text-gray-400 text-sm',
                                    tablet: 'pi pi-search text-gray-400',
                                    desktop: 'pi pi-search text-gray-400'
                                })" />
                            </InputGroupAddon>
                            <InputText
                                v-model="filters.global.value"
                                id="search"
                                :placeholder="getResponsiveClasses({
                                    mobile: 'Search by Sale ID',
                                    tablet: 'Search by Sale ID or Customer Name',
                                    desktop: 'Search by Sale ID or Customer Name'
                                })"
                                :class="[
                                    'w-full',
                                    getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                            />
                        </InputGroup>
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full md:col-span-3',
                        desktop: 'md:col-span-3'
                    })">
                        <label :class="getResponsiveClasses({
                            mobile: 'block text-xs font-semibold text-gray-700 mb-2',
                            tablet: 'block text-sm font-semibold text-gray-700 mb-2',
                            desktop: 'block text-sm font-semibold text-gray-700 mb-2'
                        })">Order Type</label>
                        <Select 
                            v-model="filters.order_type.value" 
                            :options="orderTypes" 
                            option-label="label" 
                            option-value="value" 
                            placeholder="Select"
                            :class="[
                                'w-full !border !border-gray-300 !rounded-md',
                                getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full md:col-span-3',
                        desktop: 'md:col-span-3'
                    })">
                        <label :class="getResponsiveClasses({
                            mobile: 'block text-xs font-semibold text-gray-700 mb-2',
                            tablet: 'block text-sm font-semibold text-gray-700 mb-2',
                            desktop: 'block text-sm font-semibold text-gray-700 mb-2'
                        })">Date Range</label>
                        <DatePicker 
                            v-model="dateRange" 
                            selectionMode="range" 
                            placeholder="Select Date Range"
                            :class="[
                                'w-full',
                                getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                            dateFormat="mm/dd/yy"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Table Card -->
        <div :class="getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div
                v-if="progressiveLoad.error.value"
                :class="getResponsiveClasses({
                    mobile: 'p-6 text-center',
                    tablet: 'p-8 text-center',
                    desktop: 'p-8 text-center'
                })"
            >
                <div class="mb-4">
                    <div :class="getResponsiveClasses({
                        mobile: 'mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center',
                        tablet: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center',
                        desktop: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center'
                    })">
                        <i :class="getResponsiveClasses({
                            mobile: 'pi pi-exclamation-triangle text-2xl text-red-600',
                            tablet: 'pi pi-exclamation-triangle text-3xl text-red-600',
                            desktop: 'pi pi-exclamation-triangle text-3xl text-red-600'
                        })"></i>
                    </div>
                </div>
                <h3 :class="getResponsiveClasses({
                    mobile: 'mb-2 text-base font-semibold text-red-800',
                    tablet: 'mb-2 text-lg font-semibold text-red-800',
                    desktop: 'mb-2 text-lg font-semibold text-red-800'
                })">
                    Failed to Load Orders
                </h3>
                <p :class="getResponsiveClasses({
                    mobile: 'mb-4 text-sm text-red-600',
                    tablet: 'mb-4 text-sm text-red-600',
                    desktop: 'mb-4 text-sm text-red-600'
                })">
                    {{ progressiveLoad.error.value }}
                </p>
                <Button
                    @click="handleRetry"
                    :disabled="progressiveLoad.isLoading.value"
                    :loading="progressiveLoad.isLoading.value"
                    label="Retry"
                    icon="pi pi-refresh"
                    class="!bg-red-600 hover:!bg-red-700 !border-red-600 !transition-colors"
                />
            </div>
            <div v-else class="w-full">
                <div :class="getResponsiveClasses({
                    mobile: 'flex items-center justify-between px-3 py-3 border-b border-gray-200 bg-gray-50',
                    tablet: 'flex items-center justify-between px-4 py-4 border-b border-gray-200 bg-gray-50',
                    desktop: 'flex items-center justify-between px-4 sm:px-5 py-4 border-b border-gray-200 bg-gray-50'
                })">
                    <div>
                        <h2 :class="getResponsiveClasses({
                            mobile: 'text-base font-bold text-gray-800',
                            tablet: 'text-lg font-bold text-gray-800',
                            desktop: 'text-base sm:text-lg font-bold text-gray-800'
                        })">
                            Online Sales History
                        </h2>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">View all completed online orders</p>
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'flex flex-col items-end',
                        tablet: 'flex flex-col items-end',
                        desktop: 'flex flex-col items-end'
                    })">
                        <span :class="getResponsiveClasses({
                            mobile: 'text-xs text-gray-600 font-medium',
                            tablet: 'text-xs text-gray-600 font-medium',
                            desktop: 'text-xs text-gray-600 font-medium'
                        })">Total Sales</span>
                        <span :class="getResponsiveClasses({
                            mobile: 'text-base font-bold text-gray-800',
                            tablet: 'text-lg font-bold text-gray-800',
                            desktop: 'text-lg font-bold text-gray-800'
                        })">{{ CurrencyUtil.formatCurrency(totalAmount) }}</span>
                    </div>
                </div>
                
                <!-- Desktop DataTable -->
                <DataTable 
                    v-if="isDesktop"
                    class="w-full"
                    :value="paginatedData" 
                    :globalFilterFields="['order_public_id', 'user.full_name']"
                    columnResizeMode="expand"
                    stripedRows
                    @row-click="onRowClick"
                    :pt="{
                        bodyRow: { class: 'cursor-pointer hover:bg-blue-50 transition-colors' }
                    }"
                >
                    <Column field="created_at" header="Sale Date" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-gray-400 text-sm"></i>
                                <span class="text-sm text-gray-600">
                                    {{ DateUtil.formatToMonthDayYear(data.created_at) }}
                                </span>
                            </div>
                        </template>
                    </Column>
                    <Column field="order_public_id" header="Sale ID" :sortable="true">
                        <template #body="{ data }">
                            <span class="text-xs font-medium text-gray-700">{{ data.order_public_id }}</span>
                        </template>
                    </Column>
                    <Column header="Product Name">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-1">
                                <span v-for="(item, index) in data.product_orders" :key="index" class="text-sm text-gray-700">
                                    {{ item.product.product_name }} <span class="text-xs text-gray-500">(x{{ item.quantity }})</span>
                                </span>
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
                                {{ 
                                    data.total_amount ?
                                        CurrencyUtil.formatCurrency(data.total_amount)
                                        : CurrencyUtil.formatCurrency(data.product_orders.reduce((total, item) => total + item.price * item.quantity, 0))
                                }}
                            </span>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-12">
                            <div class="mb-4">
                                <div class="mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="pi pi-globe text-4xl text-gray-400"></i>
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Online Sales Yet</h3>
                            <p class="text-sm text-gray-500">Completed online orders will appear here</p>
                        </div>
                    </template>
                </DataTable>

                <!-- Mobile/Tablet Card Layout -->
                <div v-else>
                    <div v-if="filteredData.length === 0" :class="getResponsiveClasses({
                        mobile: 'text-center py-8',
                        tablet: 'text-center py-12',
                        desktop: 'text-center py-12'
                    })">
                        <div class="mb-4">
                            <div :class="getResponsiveClasses({
                                mobile: 'mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center',
                                tablet: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center',
                                desktop: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center'
                            })">
                                <i :class="getResponsiveClasses({
                                    mobile: 'pi pi-globe text-3xl text-gray-400',
                                    tablet: 'pi pi-globe text-4xl text-gray-400',
                                    desktop: 'pi pi-globe text-4xl text-gray-400'
                                })"></i>
                            </div>
                        </div>
                        <h3 :class="getResponsiveClasses({
                            mobile: 'text-base font-semibold text-gray-700 mb-2',
                            tablet: 'text-lg font-semibold text-gray-700 mb-2',
                            desktop: 'text-lg font-semibold text-gray-700 mb-2'
                        })">No Online Sales Yet</h3>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-sm text-gray-500',
                            tablet: 'text-sm text-gray-500',
                            desktop: 'text-sm text-gray-500'
                        })">Completed online orders will appear here</p>
                    </div>
                    <div v-else>
                        <!-- Cards Container -->
                        <div :class="getResponsiveClasses({
                            mobile: 'space-y-3 p-3',
                            tablet: 'space-y-4 p-4',
                            desktop: 'space-y-4 p-4'
                        })">
                            <div 
                                v-for="(order, index) in paginatedData" 
                                :key="order.order_id"
                                :class="getResponsiveClasses({
                                    mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm cursor-pointer hover:bg-blue-50 transition-colors',
                                    tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm cursor-pointer hover:bg-blue-50 transition-colors',
                                    desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm cursor-pointer hover:bg-blue-50 transition-colors'
                                })"
                                @click="onRowClick({ data: order })"
                            >
                                <!-- Order Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i class="pi pi-calendar text-gray-400 text-sm"></i>
                                            <span :class="getResponsiveClasses({
                                                mobile: 'text-sm text-gray-600',
                                                tablet: 'text-sm text-gray-600',
                                                desktop: 'text-sm text-gray-600'
                                            })">
                                                {{ DateUtil.formatToMonthDayYear(order.created_at) }}
                                            </span>
                                        </div>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs font-medium text-gray-700',
                                            tablet: 'text-sm font-medium text-gray-700',
                                            desktop: 'text-sm font-medium text-gray-700'
                                        })">{{ order.order_public_id }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                                        'bg-purple-50 text-purple-700': order.order_type === 'Delivery',
                                        'bg-green-50 text-green-700': order.order_type === 'Pickup'
                                    }">
                                        <i :class="order.order_type === 'Delivery' ? 'pi pi-truck' : 'pi pi-shopping-bag'" class="mr-1"></i>
                                        {{ order.order_type }}
                                    </span>
                                </div>

                                <!-- Customer Info -->
                                <div class="flex items-center gap-3 mb-3">
                                    <Avatar 
                                        shape="circle" 
                                        icon="pi pi-user"
                                        :class="getResponsiveClasses({
                                            mobile: 'bg-blue-100 text-blue-600 w-8 h-8',
                                            tablet: 'bg-blue-100 text-blue-600 w-10 h-10',
                                            desktop: 'bg-blue-100 text-blue-600 w-10 h-10'
                                        })"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <div :class="getResponsiveClasses({
                                            mobile: 'font-medium text-gray-900 truncate text-sm',
                                            tablet: 'font-medium text-gray-900 truncate text-base',
                                            desktop: 'font-medium text-gray-900 truncate text-base'
                                        })">
                                            {{ order.user.full_name }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Products -->
                                <div class="mb-3">
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-xs font-medium text-gray-700 mb-2',
                                        tablet: 'text-sm font-medium text-gray-700 mb-2',
                                        desktop: 'text-sm font-medium text-gray-700 mb-2'
                                    })">Products:</p>
                                    <div class="space-y-1">
                                        <span v-for="(item, index) in order.product_orders" :key="index" :class="getResponsiveClasses({
                                            mobile: 'block text-sm text-gray-700',
                                            tablet: 'block text-sm text-gray-700',
                                            desktop: 'block text-sm text-gray-700'
                                        })">
                                            {{ item.product.product_name }} <span class="text-xs text-gray-500">(x{{ item.quantity }})</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Details Footer -->
                                <div :class="getResponsiveClasses({
                                    mobile: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                    tablet: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                    desktop: 'flex items-center justify-between pt-3 border-t border-gray-100'
                                })">
                                    <div class="text-left">
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600',
                                            tablet: 'text-sm text-gray-600',
                                            desktop: 'text-sm text-gray-600'
                                        })">Total Amount:</p>
                                    </div>
                                    <div class="text-right">
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-base font-bold text-blue-600',
                                            tablet: 'text-lg font-bold text-blue-600',
                                            desktop: 'text-lg font-bold text-blue-600'
                                        })">
                                            {{ 
                                                order.total_amount ?
                                                    CurrencyUtil.formatCurrency(order.total_amount)
                                                    : CurrencyUtil.formatCurrency(order.product_orders.reduce((total, item) => total + item.price * item.quantity, 0))
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile/Tablet Pagination Controls -->
                        <div v-if="filteredData.length > rowsPerPage" :class="getResponsiveClasses({
                            mobile: 'flex items-center justify-between px-3 py-3 border-t border-gray-200 bg-gray-50',
                            tablet: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50',
                            desktop: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50'
                        })">
                            <div :class="getResponsiveClasses({
                                mobile: 'text-xs text-gray-600',
                                tablet: 'text-sm text-gray-600',
                                desktop: 'text-sm text-gray-600'
                            })">
                                Showing {{ ((currentPage - 1) * rowsPerPage) + 1 }} to {{ Math.min(currentPage * rowsPerPage, filteredData.length) }} of {{ filteredData.length }} orders
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    icon="pi pi-chevron-left"
                                    :disabled="currentPage === 1"
                                    @click="currentPage--"
                                    :class="[
                                        'p-button-text p-button-sm',
                                        getResponsiveClasses({
                                            mobile: '!text-xs !p-1',
                                            tablet: '!text-sm !p-2',
                                            desktop: '!text-sm !p-2'
                                        })
                                    ]"
                                />
                                <span :class="getResponsiveClasses({
                                    mobile: 'text-xs font-medium text-gray-700 px-2',
                                    tablet: 'text-sm font-medium text-gray-700 px-3',
                                    desktop: 'text-sm font-medium text-gray-700 px-3'
                                })">
                                    {{ currentPage }} of {{ totalPages }}
                                </span>
                                <Button
                                    icon="pi pi-chevron-right"
                                    :disabled="currentPage === totalPages"
                                    @click="currentPage++"
                                    :class="[
                                        'p-button-text p-button-sm',
                                        getResponsiveClasses({
                                            mobile: '!text-xs !p-1',
                                            tablet: '!text-sm !p-2',
                                            desktop: '!text-sm !p-2'
                                        })
                                    ]"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details Modal -->
        <Dialog
            v-model:visible="viewOrderModal"
            header="Order Details"
            :style="{ width: '90%' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <ViewOrderForm 
                v-if="selectedOrder"
                :data="selectedOrder"
                @viewPayment="handleViewPayment"
            />
        </Dialog>

        <!-- View Payment Modal -->
        <Dialog
            v-model:visible="viewPaymentModal"
            header="View Payment Details"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <ViewPaymentForm 
                v-if="paymentOrder"
                :data="paymentOrder"
            />
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, computed, watch } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { IOrder } from "@/interfaces/IOrder";
import { useToast } from "vue-toastification";
import DateUtil from "@/utils/DateUtil";
import CurrencyUtil from "@/utils/CurrencyUtil";
import DateTimeDisplay from "@/components/DateTimeDisplay.vue";
import { FilterMatchMode } from '@primevue/core/api';
import { useProgressiveLoad } from "@/composables/useProgressiveLoad";
import { useResponsive } from "@/composables/useResponsive";
import { useRouter } from "vue-router";
import ViewOrderForm from "@/components/forms/ViewOrderForm.vue";
import ViewPaymentForm from "@/components/forms/ViewPaymentForm.vue";

// Use responsive composable
const { getResponsiveClasses, isDesktop } = useResponsive();

const toast = useToast();
const router = useRouter();

const filters = ref({
    global: { value: '', matchMode: FilterMatchMode.CONTAINS },
    order_type: { value: '', matchMode: FilterMatchMode.EQUALS },
});

const dateRange = ref<Date[] | null>(null);
const data = ref<IOrder[]>([]);
const loadService = useAxiosUtil<null, IOrder[]>();

// Pagination for mobile/tablet card layout
const currentPage = ref(1);
const rowsPerPage = ref(10);

const orderTypes = ref([
    { label: 'All', value: '' },
    { label: 'Delivery', value: 'Delivery' },
    { label: 'Pickup', value: 'Pickup' },
]);

// Computed property to filter data by date range and status
const filteredData = computed(() => {
    let filtered = data.value;

    // Apply global filter (search)
    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase();
        filtered = filtered.filter(order => 
            order.order_public_id.toLowerCase().includes(searchTerm) ||
            (order.user && order.user.full_name && order.user.full_name.toLowerCase().includes(searchTerm))
        );
    }

    // Apply date range filter
    if (dateRange.value && dateRange.value.length > 0) {
        const [startDate, endDate] = dateRange.value;
        
        if (startDate) {
            filtered = filtered.filter((order) => {
                const orderDate = new Date(order.created_at);
                orderDate.setHours(0, 0, 0, 0);
                
                const start = new Date(startDate);
                start.setHours(0, 0, 0, 0);
                
                if (endDate) {
                    const end = new Date(endDate);
                    end.setHours(23, 59, 59, 999);
                    return orderDate >= start && orderDate <= end;
                }
                
                return orderDate.getTime() === start.getTime();
            });
        }
    }

    // Apply order type filter
    if (filters.value.order_type.value) {
        filtered = filtered.filter((order) => order.order_type === filters.value.order_type.value);
    }

    return filtered;
});

const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / rowsPerPage.value);
});

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * rowsPerPage.value;
    const end = start + rowsPerPage.value;
    return filteredData.value.slice(start, end);
});

// Reset pagination when filters change
watch([() => filters.value.global.value, () => filters.value.order_type.value, () => dateRange.value], () => {
    currentPage.value = 1;
});

// Computed property to calculate total amount from filtered data
const totalAmount = computed(() => {
    return filteredData.value.reduce((sum, order) => sum + (order.total_amount || 0), 0);
});

const progressiveLoad = useProgressiveLoad();

const loadPrimaryData = async () => {
    await loadService.get("admin/orders").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            // Filter for Completed orders (which are displayed as "Delivered" to customers)
            data.value = loadService.request.data.filter((order: IOrder) => 
                order.status === 'Completed'
            );
        } else {
            toast.error(loadService.request.message ?? "Failed to load orders");
        }
    });
};

const loadSecondaryData = async () => {
    return Promise.resolve();
};

const load = () => {
    progressiveLoad.loadData({
        primaryLoader: loadPrimaryData,
        secondaryLoader: loadSecondaryData,
    });
};

const handleRetry = async () => {
    try {
        await progressiveLoad.retry();
        toast.success("Order data reloaded successfully");
    } catch (error) {
        toast.error("Retry failed. Please try again later.");
    }
};

const getStatusClass = (order: IOrder) => {
    const statusClasses: Record<string, string> = {
        'Delivered': 'bg-green-50 text-green-700',
        'Released': 'bg-blue-50 text-blue-700',
        'Completed': 'bg-green-50 text-green-700',
    };
    return statusClasses[order.status] || 'bg-gray-50 text-gray-700';
};

const selectedOrder = ref<IOrder | null>(null);
const viewOrderModal = ref(false);
const viewPaymentModal = ref(false);
const paymentOrder = ref<IOrder | null>(null);

const onRowClick = (event: any) => {
    selectedOrder.value = event.data;
    viewOrderModal.value = true;
}

const handleViewPayment = (order: IOrder) => {
    paymentOrder.value = order;
    viewPaymentModal.value = true;
}

onMounted(() => {
    load();
});
</script>
