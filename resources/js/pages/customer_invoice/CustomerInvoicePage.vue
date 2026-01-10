<template>
    <div :class="getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-4 px-3',
        tablet: 'min-h-screen bg-gray-50 py-6 px-4',
        desktop: 'min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8'
    })">
        <div :class="getResponsiveClasses({
            mobile: 'max-w-full mx-auto',
            tablet: 'max-w-6xl mx-auto',
            desktop: 'max-w-7xl mx-auto'
        })">
            <!-- Header -->
            <div :class="getResponsiveClasses({
                mobile: 'mb-4',
                tablet: 'mb-6',
                desktop: 'mb-6'
            })">
                <h1 :class="getResponsiveClasses({
                    mobile: 'text-xl font-bold text-gray-900',
                    tablet: 'text-2xl font-bold text-gray-900',
                    desktop: 'text-2xl font-bold text-gray-900'
                })">My Invoices</h1>
                <p :class="getResponsiveClasses({
                    mobile: 'mt-1 text-sm text-gray-600',
                    tablet: 'mt-1 text-sm text-gray-600',
                    desktop: 'mt-1 text-sm text-gray-600'
                })">View and download your order invoices</p>
            </div>

            <!-- Loading State -->
            <div v-if="loadService.request.loading" class="flex justify-center items-center py-12">
                <PageLoader />
            </div>

            <!-- Error State -->
            <div v-else-if="loadService.request.error" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="pi pi-exclamation-triangle text-red-600 mr-3"></i>
                    <p class="text-red-800">{{ loadService.request.message || 'Failed to load invoices' }}</p>
                </div>
            </div>

            <!-- Invoices List -->
            <div v-else-if="orders && orders.length > 0" :class="getResponsiveClasses({
                mobile: 'space-y-3',
                tablet: 'space-y-4',
                desktop: 'space-y-4'
            })">
                <div
                    v-for="order in orders"
                    :key="order.order_id"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                >
                    <div :class="getResponsiveClasses({
                        mobile: 'p-4',
                        tablet: 'p-5',
                        desktop: 'p-6'
                    })">
                        <div :class="getResponsiveClasses({
                            mobile: 'flex flex-col gap-4',
                            tablet: 'flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4',
                            desktop: 'flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4'
                        })">
                            <!-- Order Info -->
                            <div class="flex-1">
                                <div :class="getResponsiveClasses({
                                    mobile: 'flex flex-col gap-2 mb-3',
                                    tablet: 'flex items-center gap-3 mb-2',
                                    desktop: 'flex items-center gap-3 mb-2'
                                })">
                                    <h3 :class="getResponsiveClasses({
                                        mobile: 'text-base font-semibold text-gray-900',
                                        tablet: 'text-lg font-semibold text-gray-900',
                                        desktop: 'text-lg font-semibold text-gray-900'
                                    })">
                                        Order #{{ order.order_public_id }}
                                    </h3>
                                    <span
                                        :class="[
                                            'px-3 py-1 text-xs font-medium rounded-full',
                                            getStatusClass(order.status),
                                            getResponsiveClasses({
                                                mobile: 'self-start',
                                                tablet: '',
                                                desktop: ''
                                            })
                                        ]"
                                    >
                                        {{ order.status }}
                                    </span>
                                </div>
                                <div :class="getResponsiveClasses({
                                    mobile: 'space-y-1 text-sm text-gray-600',
                                    tablet: 'space-y-1 text-sm text-gray-600',
                                    desktop: 'space-y-1 text-sm text-gray-600'
                                })">
                                    <p>
                                        <i class="pi pi-calendar mr-2"></i>
                                        Order Date: {{ DateUtil.formatToMonthDayYear(order.created_at) }}
                                    </p>
                                    <p v-if="order.date_paid_confirmed">
                                        <i class="pi pi-check-circle mr-2 text-green-600"></i>
                                        Paid: {{ DateUtil.formatToMonthDayYear(order.date_paid_confirmed) }}
                                    </p>
                                    <p v-if="order.shipping_fee">
                                        <i class="pi pi-truck mr-2 text-blue-600"></i>
                                        Shipping Fee: <span class="font-semibold text-gray-900">{{ CurrencyUtil.formatCurrency(order.shipping_fee) }}</span>
                                    </p>
                                    <p>
                                        <i class="pi pi-money-bill mr-2"></i>
                                        Total: <span :class="getResponsiveClasses({
                                            mobile: 'font-semibold text-gray-900 text-base',
                                            tablet: 'font-semibold text-gray-900',
                                            desktop: 'font-semibold text-gray-900'
                                        })">{{ CurrencyUtil.formatCurrency(order.total_amount) }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Invoice Generated -->
                            <div v-if="order.invoice_generated_at" :class="getResponsiveClasses({
                                mobile: 'w-full',
                                tablet: 'text-right',
                                desktop: 'text-right'
                            })">
                                <div :class="getResponsiveClasses({
                                    mobile: 'flex items-center gap-2 px-2 py-1.5 bg-blue-50 rounded-md border border-blue-200',
                                    tablet: 'inline-flex items-center px-3 py-2 bg-blue-50 rounded-lg border border-blue-200',
                                    desktop: 'inline-flex items-center px-3 py-2 bg-blue-50 rounded-lg border border-blue-200'
                                })">
                                    <i class="pi pi-file-pdf text-blue-600"></i>
                                    <div :class="getResponsiveClasses({
                                        mobile: 'text-xs text-gray-600',
                                        tablet: 'text-xs text-gray-600',
                                        desktop: 'text-xs text-gray-600'
                                    })">
                                        <div :class="getResponsiveClasses({
                                            mobile: 'font-semibold text-blue-700',
                                            tablet: 'flex items-center gap-2 mb-1',
                                            desktop: 'flex items-center gap-2 mb-1'
                                        })">
                                            <span :class="getResponsiveClasses({
                                                mobile: 'block',
                                                tablet: 'font-semibold text-blue-700',
                                                desktop: 'font-semibold text-blue-700'
                                            })">Invoice Generated</span>
                                        </div>
                                        <div :class="getResponsiveClasses({
                                            mobile: 'text-gray-700 text-xs',
                                            tablet: 'text-gray-700',
                                            desktop: 'text-gray-700'
                                        })">
                                            {{ DateUtil.formatToMonthDayYearTime(order.invoice_generated_at) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Summary -->
                        <div :class="getResponsiveClasses({
                            mobile: 'mt-3 pt-3 border-t border-gray-200',
                            tablet: 'mt-4 pt-4 border-t border-gray-200',
                            desktop: 'mt-4 pt-4 border-t border-gray-200'
                        })">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-sm font-medium text-gray-700 mb-2',
                                tablet: 'text-sm font-medium text-gray-700 mb-2',
                                desktop: 'text-sm font-medium text-gray-700 mb-2'
                            })">Items:</p>
                            <div :class="getResponsiveClasses({
                                mobile: 'flex flex-wrap gap-1',
                                tablet: 'flex flex-wrap gap-2',
                                desktop: 'flex flex-wrap gap-2'
                            })">
                                <span
                                    v-for="(item, index) in order.product_orders"
                                    :key="index"
                                    :class="getResponsiveClasses({
                                        mobile: 'inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700',
                                        tablet: 'inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700',
                                        desktop: 'inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700'
                                    })"
                                >
                                    {{ item.product?.product_name || 'N/A' }} (x{{ item.quantity }})
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else :class="getResponsiveClasses({
                mobile: 'bg-white rounded-lg shadow-sm border border-gray-200 p-8',
                tablet: 'bg-white rounded-lg shadow-sm border border-gray-200 p-12',
                desktop: 'bg-white rounded-lg shadow-sm border border-gray-200 p-12'
            })">
                <div class="text-center">
                    <div :class="getResponsiveClasses({
                        mobile: 'mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-3',
                        tablet: 'mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4',
                        desktop: 'mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4'
                    })">
                        <i :class="getResponsiveClasses({
                            mobile: 'pi pi-file-pdf text-3xl text-gray-400',
                            tablet: 'pi pi-file-pdf text-4xl text-gray-400',
                            desktop: 'pi pi-file-pdf text-4xl text-gray-400'
                        })"></i>
                    </div>
                    <h3 :class="getResponsiveClasses({
                        mobile: 'text-base font-semibold text-gray-900 mb-2',
                        tablet: 'text-lg font-semibold text-gray-900 mb-2',
                        desktop: 'text-lg font-semibold text-gray-900 mb-2'
                    })">No Invoices Yet</h3>
                    <p :class="getResponsiveClasses({
                        mobile: 'text-gray-600 mb-4 text-sm',
                        tablet: 'text-gray-600 mb-6',
                        desktop: 'text-gray-600 mb-6'
                    })">You don't have any paid orders yet.</p>
                    <Button
                        label="Browse Products"
                        icon="pi pi-shopping-bag"
                        :class="getResponsiveClasses({
                            mobile: '!bg-blue-600 hover:!bg-blue-700 w-full',
                            tablet: '!bg-blue-600 hover:!bg-blue-700',
                            desktop: '!bg-blue-600 hover:!bg-blue-700'
                        })"
                        @click="goToBrowseProducts"
                    />
                </div>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import useAxiosUtil from '@/utils/AxiosUtil';
import DateUtil from '@/utils/DateUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import { useResponsive } from '@/composables/useResponsive';
import { IOrder } from '@/interfaces/IOrder';

const router = useRouter();
const toast = useToast();
const loadService = useAxiosUtil<null, IOrder[]>();
const orders = ref<IOrder[]>([]);

// Use responsive composable
const { 
    isMobile, 
    isTablet, 
    isDesktop, 
    getResponsiveClasses, 
    getResponsivePadding, 
    getResponsiveMargin, 
    getResponsiveTextSize,
    getResponsiveGap 
} = useResponsive();

const load = async () => {
    await loadService.get('customer/orders/paid').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            orders.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? 'Failed to load invoices');
        }
    });
};

const getStatusClass = (status: string) => {
    const statusClasses: Record<string, string> = {
        'Pending': 'bg-yellow-100 text-yellow-800',
        'Confirmed': 'bg-blue-100 text-blue-800',
        'Processing': 'bg-purple-100 text-purple-800',
        'Shipped': 'bg-indigo-100 text-indigo-800',
        'Delivered': 'bg-green-100 text-green-800',
        'Completed': 'bg-green-100 text-green-800',
        'Cancelled': 'bg-red-100 text-red-800',
        'Released': 'bg-teal-100 text-teal-800',
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
};

const goToBrowseProducts = () => {
    router.push({ name: 'customer.browse-products' });
};

onMounted(() => {
    load();
});
</script>
