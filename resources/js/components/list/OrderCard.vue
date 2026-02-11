<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
        <!-- Header -->
        <div class="p-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-3 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <span class="font-bold text-gray-900">Order #{{ order.order_public_id }}</span>
                <span class="text-xs text-gray-500 hidden sm:inline">•</span>
                <span class="text-sm text-gray-500">{{ DateUtil.formatToMonthDayYear(order.created_at) }}</span>
            </div>
            
            <!-- Status Badge -->
            <div 
                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border flex items-center gap-1.5"
                :class="statusClasses"
            >
                <!-- Optional Status Icon -->
                <i v-if="statusIcon" :class="statusIcon" class="text-[10px]"></i>
                {{ getCustomerStatusDisplay(order) }}
            </div>
        </div>

        <!-- Body -->
        <div class="p-4 flex-1">
            <!-- Product List -->
            <div class="space-y-4">
                <div v-for="item in order.product_orders" :key="item.product_order_id" class="flex gap-4 items-start">
                    <!-- Product Image -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 rounded-md border border-gray-200 overflow-hidden bg-gray-50 relative">
                        <img 
                            v-if="item.product.product_image"
                            :src="UrlUtil.getBaseAppUrl(`storage/images/product/${item.product.product_image}`)" 
                            class="w-full h-full object-cover"
                            alt="Product Image"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                            <i class="pi pi-image text-2xl"></i>
                        </div>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="flex-1 min-w-0 flex flex-col justify-between h-full">
                        <div>
                            <h4 class="font-medium text-gray-900 truncate" :title="item.product.product_name">{{ item.product.product_name }}</h4>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-sm text-gray-500">Qty: {{ item.quantity }}</p>
                                <p class="font-semibold text-gray-900">{{ CurrencyUtil.formatCurrency(item.price * item.quantity) }}</p>
                            </div>
                        </div>

                        <!-- Per-Item Actions (Rating) -->
                        <div v-if="order.status === 'Completed'" class="mt-2 flex justify-end">
                             <Button
                                v-if="!item.rate"
                                icon="pi pi-star"
                                label="Rate"
                                size="small"
                                outlined
                                severity="warning"
                                class="!py-1 !px-2 !text-xs h-7"
                                @click="$emit('rate-product', item)"
                            />
                            <Button
                                v-else
                                icon="pi pi-star-fill"
                                label="Rated"
                                size="small"
                                severity="warning"
                                class="!py-1 !px-2 !text-xs h-7 opacity-90"
                                @click="$emit('view-rating', item.rate)"
                            />
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- Shipping Fee Line (if applicable) -->
        <div v-if="order.order_type === 'Delivery' && order.shipping_fee" class="px-4 pb-2 text-right text-sm text-gray-600">
            Delivery Fee: {{ CurrencyUtil.formatCurrency(order.shipping_fee) }}
        </div>

        <!-- Payment Action Area (Prominent Banner) -->
        <div 
            v-if="isPaymentRequired" 
            class="bg-blue-50 border-t border-blue-100 p-4"
        >
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                        <i class="pi pi-wallet text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-blue-900">Payment Required</p>
                        <p class="text-sm text-blue-700 leading-tight">Admin accepted order. Please pay <span class="font-bold whitespace-nowrap">{{ CurrencyUtil.formatCurrency(calculateTotal(order)) }}</span></p>
                    </div>
                </div>
                <!-- Independent Button Container -->
                <div class="w-full sm:w-auto">
                    <Button 
                        label="Pay Now" 
                        icon="pi pi-arrow-right" 
                        iconPos="right"
                        class="w-full sm:w-auto font-bold shadow-sm"
                        severity="info"
                        @click="$emit('pay', order)"
                    />
                </div>
            </div>
        </div>

        <!-- Payment Status Feedback (Processing) -->
         <div v-if="isPaymentSubmitted" class="px-4 py-3 bg-blue-50/50 border-t border-blue-100 flex items-center justify-center sm:justify-start gap-2 text-sm text-blue-700 font-medium">
            <i class="pi pi-clock"></i>
            Payment submitted - Awaiting confirmation
        </div>

        <!-- Footer Actions -->
        <div class="p-4 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
            <!-- Total -->
            <div class="font-bold text-gray-900 text-lg">
                Total: {{ CurrencyUtil.formatCurrency(calculateTotal(order)) }}
            </div>

            <!-- Secondary Actions -->
            <div class="flex gap-2 flex-wrap justify-end">
                <!-- Cancel Button -->
                <Button
                    v-if="showCancelButton"
                    type="button"
                    label="Cancel Order"
                    severity="danger"
                    size="small"
                    outlined
                    icon="pi pi-times"
                    @click.stop="$emit('cancel', order)"
                    class="relative z-10 cursor-pointer"
                />

                <!-- Mark As Delivered Component -->
                <MarkAsDelivered
                    v-if="order.status === 'Shipped'"
                    :data="order"
                    @cb="$emit('refresh')"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { IOrder } from '@/interfaces/IOrder';
import DateUtil from '@/utils/DateUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import UrlUtil from '@/utils/UrlUtil';
import IProductOrder from '@/interfaces/IProductOrder';
import MarkAsDelivered from '@/components/forms/MarkAsDelivered.vue';

const props = defineProps<{
    order: IOrder
}>();

defineEmits(['pay', 'cancel', 'rate-product', 'view-rating', 'refresh']);

const isPaymentRequired = computed(() => {
    return props.order.status === 'Processing' && 
           props.order.admin_accepted && 
           !props.order.proof_of_payment && 
           props.order.payment_method === 'Online Payment';
});

const isPaymentSubmitted = computed(() => {
    return props.order.status === 'Processing' && 
           props.order.admin_accepted && 
           props.order.proof_of_payment;
});

const showCancelButton = computed(() => {
    // Show cancel if processing and (not accepted OR accepted but not paid)
    // Once paid (proof submitted), usually can't cancel until admin rejects or confirms
    return props.order.status === 'Processing' && 
           (!props.order.admin_accepted || !props.order.proof_of_payment);
});

const statusClasses = computed(() => {
    const status = props.order.status;
    if (status === 'Processing') return 'bg-amber-50 text-amber-700 border-amber-200';
    if (status === 'Confirmed') return 'bg-blue-50 text-blue-700 border-blue-200';
    if (status === 'Shipped') return 'bg-purple-50 text-purple-700 border-purple-200';
    if (status === 'Completed') return 'bg-green-50 text-green-700 border-green-200';
    if (status === 'Cancelled') return 'bg-red-50 text-red-700 border-red-200';
    return 'bg-gray-50 text-gray-700 border-gray-200';
});

const statusIcon = computed(() => {
    const status = props.order.status;
    if (status === 'Processing') return 'pi pi-clock';
    if (status === 'Confirmed') return 'pi pi-check';
    if (status === 'Shipped') return 'pi pi-truck';
    if (status === 'Completed') return 'pi pi-check-circle';
    if (status === 'Cancelled') return 'pi pi-times-circle';
    return '';
});

const getCustomerStatusDisplay = (item: IOrder) => {
    if (item.status === 'Processing') {
        if (!item.admin_accepted) return 'Awaiting Review';
        if (!item.proof_of_payment) return 'Payment Required';
        return 'Payment Under Review';
    }
    return item.status;
};

const calculateTotal = (item: IOrder) => {
    // Recalculate based on product lines + shipping
    // Use reduce safely
    const productTotal = (item.product_orders || []).reduce((total, product) => 
        total + (product.price * product.quantity), 0
    );
    const shippingFee = Number(item.shipping_fee) || 0;
    return productTotal + shippingFee;
};
</script>
