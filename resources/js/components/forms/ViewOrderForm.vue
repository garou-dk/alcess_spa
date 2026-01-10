<template>
    <div>
        <!-- Order Information Card -->
        <div class="mb-4 border border-gray-200 rounded-lg overflow-hidden bg-white">
            <div class="flex flex-wrap p-4 gap-4">
                <div class="flex-1 min-w-[200px]">
                    <p class="text-xs font-medium text-gray-700 mb-1">Order ID</p>
                    <p class="text-sm font-semibold text-gray-900">{{ props.data.order_public_id }}</p>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <p class="text-xs font-medium text-gray-700 mb-1">Order Type</p>
                    <p class="text-sm text-gray-900">
                        <i 
                            :class="props.data.order_type == 'Pickup' ? 'pi pi-shopping-bag' : 'pi pi-truck'"
                            class="mr-1"
                        />
                        {{ props.data.order_type }}
                    </p>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <p class="text-xs font-medium text-gray-700 mb-1">Order Date</p>
                    <p class="text-sm text-gray-900">{{ DateUtil.formatToMonthDayYear(props.data.created_at) }}</p>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <p class="text-xs font-medium text-gray-700 mb-1">Order Status</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="getStatusBadgeClass(getAdminStatusDisplay(props.data))">
                        {{ getAdminStatusDisplay(props.data) }}
                    </span>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <p class="text-xs font-medium text-gray-700 mb-1">Payment Status</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                        'bg-green-50 text-green-700': props.data.date_paid_confirmed,
                        'bg-red-50 text-red-700': !props.data.date_paid_confirmed
                    }">
                        <i :class="props.data.date_paid_confirmed ? 'pi pi-check-circle' : 'pi pi-times-circle'" class="mr-1"></i>
                        {{ props.data.date_paid_confirmed ? "Paid" : "Unpaid" }}
                    </span>
                </div>
                <div v-if="props.data.order_type === 'Delivery'" class="w-full">
                    <p class="text-xs font-medium text-gray-700 mb-1">Delivery Address</p>
                    <p class="text-sm text-gray-900">{{ showCompleteAddress(props.data) }}</p>
                </div>
                <div v-if="props.data.delivery_courier" class="flex-1 min-w-[200px]">
                    <p class="text-xs font-medium text-gray-700 mb-1">Delivery Courier</p>
                    <p class="text-sm text-gray-900">{{ props.data.delivery_courier }}</p>
                </div>
                <div v-if="props.data.tracking_number" class="flex-1 min-w-[200px]">
                    <p class="text-xs font-medium text-gray-700 mb-1">Tracking Number</p>
                    <p class="text-sm font-mono text-gray-900">{{ props.data.tracking_number }}</p>
                </div>
                <div v-if="props.data.estimated_delivery_date_start && props.data.estimated_delivery_date_end" class="flex-1 min-w-[200px]">
                    <p class="text-xs font-medium text-gray-700 mb-1">Estimated Delivery</p>
                    <p class="text-sm text-gray-900">{{ DateUtil.formatToMonthDayYear(props.data.estimated_delivery_date_start) }} - {{ DateUtil.formatToMonthDayYear(props.data.estimated_delivery_date_end) }}</p>
                </div>
            </div>
        </div>

        <!-- Delivery Proof Section -->
        <div v-if="(props.data.status === 'Shipped' || props.data.status === 'Completed') && (props.data.delivery_proof_images || props.data.delivery_proof_video)" class="mb-4 border border-gray-200 rounded-lg overflow-hidden bg-white">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Preparation & Delivery Proof</h3>
            </div>
            <div class="p-4">
                <!-- Images -->
                <div v-if="props.data.delivery_proof_images" class="mb-4">
                    <p class="text-xs font-medium text-gray-700 mb-2">Images:</p>
                    <div class="flex flex-wrap gap-2">
                        <img
                            v-for="(image, index) in parseDeliveryImages(props.data.delivery_proof_images)"
                            :key="index"
                            :src="getDeliveryProofUrl(image)"
                            @click="openImagePreview(getDeliveryProofUrl(image))"
                            class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity border border-gray-200"
                            alt="Delivery proof"
                        />
                    </div>
                </div>

                <!-- Video -->
                <div v-if="props.data.delivery_proof_video">
                    <p class="text-xs font-medium text-gray-700 mb-2">Video:</p>
                    <video
                        :src="getDeliveryProofUrl(props.data.delivery_proof_video)"
                        controls
                        class="w-full max-w-md rounded-lg border border-gray-200"
                    >
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>

        <!-- Order Items Section -->
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div :class="responsive.getResponsiveClasses({
                mobile: 'px-3 py-3',
                tablet: 'px-4 py-3',
                desktop: 'px-4 py-3'
            }) + ' bg-gray-50 border-b border-gray-200'">
                <h3 :class="responsive.getResponsiveTextSize('base') + ' font-semibold text-gray-900'">Order Items</h3>
            </div>
            
            <!-- Desktop Table Layout -->
            <div class="hidden lg:block">
                <DataTable
                    :value="props.data.product_orders"
                    stripedRows
                >
                    <Column field="product.product_name" header="Product">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3 py-1">
                                <Avatar
                                    v-if="data.product.product_image"
                                    shape="circle"
                                    :image="
                                        UrlUtil.getBaseAppUrl(
                                            `storage/images/product/${data.product.product_image}`,
                                        )
                                    "
                                    class="w-10 h-10"
                                    size="large"
                                />
                                <Avatar
                                    v-else
                                    shape="circle"
                                    icon="pi pi-camera"
                                    class="bg-gray-100 text-gray-400"
                                    size="large"
                                />
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">
                                        {{ data.product.product_name }}
                                    </div>
                                    <div v-if="data.product.description" class="text-xs text-gray-500 mt-1 line-clamp-2">
                                        {{ data.product.description }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column header="Price" field="price">
                        <template #body="{ data }">
                            <span class="text-sm text-gray-900">{{ CurrencyUtil.formatCurrency(data.price) }}</span>
                        </template>
                    </Column>
                    <Column header="Quantity" field="quantity">
                        <template #body="{ data }">
                            <span class="text-sm font-medium text-gray-900">{{ data.quantity }}</span>
                        </template>
                    </Column>
                    <Column header="Subtotal">
                        <template #body="{ data }">
                            <span class="text-sm font-semibold text-gray-900">{{ CurrencyUtil.formatCurrency(data.price * data.quantity) }}</span>
                        </template>
                    </Column>
                    <template #footer>
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <div class="space-y-2 min-w-[250px]">
                                <div v-if="props.data.order_type === 'Delivery'" class="flex justify-between text-sm">
                                    <span class="text-gray-600">Delivery Fee:</span>
                                    <span class="font-medium text-gray-900">
                                        {{ props.data.shipping_fee !== null ? CurrencyUtil.formatCurrency(props.data.shipping_fee) : 'TBD' }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-base pt-2 border-t border-gray-200">
                                    <span class="font-semibold text-gray-900">Total:</span>
                                    <span class="font-bold text-blue-600 text-lg">
                                        {{ CurrencyUtil.formatCurrency(props.data.product_orders.reduce((total, item) => total + item.price * item.quantity, 0) + (props.data.shipping_fee ?? 0)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </DataTable>
            </div>

            <!-- Mobile/Tablet Card Layout -->
            <div class="block lg:hidden" :class="responsive.getResponsiveClasses({ mobile: 'p-3', tablet: 'p-4' })">
                <div class="space-y-3">
                    <div 
                        v-for="item in props.data.product_orders" 
                        :key="item.product_order_id"
                        class="bg-gray-50 rounded-lg p-3 border border-gray-200"
                    >
                        <div class="flex items-center gap-3 mb-3">
                            <Avatar
                                v-if="item.product.product_image"
                                shape="circle"
                                :image="UrlUtil.getBaseAppUrl(`storage/images/product/${item.product.product_image}`)"
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'w-12 h-12',
                                    tablet: 'w-14 h-14'
                                })"
                                size="large"
                            />
                            <Avatar
                                v-else
                                shape="circle"
                                icon="pi pi-camera"
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'w-12 h-12',
                                    tablet: 'w-14 h-14'
                                }) + ' bg-gray-100 text-gray-400'"
                                size="large"
                            />
                            <div class="flex-1 min-w-0">
                                <div :class="responsive.getResponsiveTextSize('base') + ' font-medium text-gray-900 truncate'">
                                    {{ item.product.product_name }}
                                </div>
                                <div v-if="item.product.description" class="text-xs text-gray-500 mt-1 line-clamp-2">
                                    {{ item.product.description }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3 text-sm">
                            <div>
                                <span class="text-gray-600">Price:</span>
                                <div class="font-medium text-gray-900">{{ CurrencyUtil.formatCurrency(item.price) }}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Qty:</span>
                                <div class="font-medium text-gray-900">{{ item.quantity }}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Subtotal:</span>
                                <div class="font-semibold text-gray-900">{{ CurrencyUtil.formatCurrency(item.price * item.quantity) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile/Tablet Total Section -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="space-y-2">
                        <div v-if="props.data.order_type === 'Delivery'" class="flex justify-between text-sm">
                            <span class="text-gray-600">Delivery Fee:</span>
                            <span class="font-medium text-gray-900">
                                {{ props.data.shipping_fee !== null ? CurrencyUtil.formatCurrency(props.data.shipping_fee) : 'TBD' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-base pt-2 border-t border-gray-200">
                            <span class="font-semibold text-gray-900">Total:</span>
                            <span :class="responsive.getResponsiveTextSize('lg') + ' font-bold text-blue-600'">
                                {{ CurrencyUtil.formatCurrency(props.data.product_orders.reduce((total, item) => total + item.price * item.quantity, 0) + (props.data.shipping_fee ?? 0)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons Section - Hidden for Staff (View Only) -->
        <div v-if="Page.user && Page.user.role.role_name === RoleEnum.ADMIN" :class="responsive.getResponsiveClasses({
            mobile: 'mt-4 flex flex-col gap-2 border-t border-gray-200 pt-4',
            tablet: 'mt-4 flex flex-wrap gap-3 justify-end border-t border-gray-200 pt-4',
            desktop: 'mt-4 flex flex-wrap gap-3 justify-end border-t border-gray-200 pt-4'
        })">
            <!-- Processing Status - Not Accepted: Accept/Decline Buttons -->
            <Button
                v-if="props.data.status === 'Processing' && !props.data.admin_accepted"
                label="Accept Order"
                icon="pi pi-check"
                class="!bg-green-600 hover:!bg-green-700"
                @click="emit('approve', props.data)"
            />
            
            <Button
                v-if="props.data.status === 'Processing' && !props.data.admin_accepted"
                label="Decline Order"
                icon="pi pi-times"
                severity="danger"
                @click="emit('decline', props.data)"
            />
            
            <!-- Processing Status - Accepted with Payment: View Payment Button -->
            <Button
                v-if="props.data.status === 'Processing' && props.data.admin_accepted && props.data.proof_of_payment"
                label="View Payment"
                icon="pi pi-eye"
                class="!bg-blue-600 hover:!bg-blue-700"
                @click="emit('viewPayment', props.data)"
            />
            
            <!-- Confirmed Status: Generate Invoice & Set Delivery -->
            <Button
                v-if="props.data.status === 'Confirmed' && !props.data.sale_id"
                label="Generate Sales Invoice"
                icon="pi pi-file-pdf"
                class="!bg-purple-600 hover:!bg-purple-700"
                @click="emit('generateInvoice', props.data)"
            />
            
            <!-- View Invoice Button - If invoice already generated -->
            <Button
                v-if="props.data.sale_id"
                label="View Invoice"
                icon="pi pi-eye"
                class="!bg-indigo-600 hover:!bg-indigo-700"
                @click="emit('viewInvoice', props.data)"
            />
            
            <!-- Set Delivery Button - If invoice generated but not yet shipped -->
            <Button
                v-if="props.data.sale_id && props.data.status === 'Confirmed'"
                label="Set Delivery"
                icon="pi pi-truck"
                class="!bg-orange-600 hover:!bg-orange-700"
                @click="emit('setDelivery', props.data)"
            />
        </div>

        <!-- Image Preview Dialog -->
        <Dialog
            v-model:visible="imagePreviewModal.visible"
            header="Delivery Proof Image"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <img
                :src="imagePreviewModal.imageUrl"
                class="w-full h-auto rounded-lg"
                alt="Delivery proof preview"
            />
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';
import Page from '@/stores/Page';
import { RoleEnum } from '@/enums/RoleEnum';
import { ref } from 'vue';

import { useResponsive } from '@/composables/useResponsive';

interface Props {
    data: IOrder;
}
const props = defineProps<Props>();
const responsive = useResponsive();

const emit = defineEmits<{
    approve: [order: IOrder];
    decline: [order: IOrder];
    viewPayment: [order: IOrder];
    markPaid: [order: IOrder];
    generateInvoice: [order: IOrder];
    viewInvoice: [order: IOrder];
    setDelivery: [order: IOrder];
}>();

// Display actual database status - no more mapping confusion
const getAdminStatusDisplay = (order: IOrder) => {
    return order.status;
};

const getStatusBadgeClass = (status: string) => {
    const statusClasses: Record<string, string> = {
        'Processing': 'bg-yellow-50 text-yellow-700',
        'Confirmed': 'bg-blue-50 text-blue-700',
        'Shipped': 'bg-purple-50 text-purple-700',
        'Completed': 'bg-green-50 text-green-700',
        'Cancelled': 'bg-red-50 text-red-700',
    };
    return statusClasses[status] || 'bg-gray-50 text-gray-700';
}

const showCompleteAddress = (value: IOrder) => {
    return `${value.other_details}, ${value.barangay.barangay_name}, ${value.barangay.municity.municity_name}, ${value.barangay.municity.province.province_name}, ${value.barangay.municity.province.region.region_name}, ${value.barangay.municity.province.region.island_group.island_group_name}, ${value.postal_code}`
}

// Parse delivery proof images - handle both array and JSON string
const parseDeliveryImages = (images: string | string[]) => {
    if (Array.isArray(images)) {
        return images;
    }
    try {
        return JSON.parse(images);
    } catch {
        return [];
    }
};

// Get full URL for delivery proof files
const getDeliveryProofUrl = (filename: string) => {
    return UrlUtil.getBaseAppUrl(`storage/images/delivery-proof/${filename}`);
};

// Image preview modal state
const imagePreviewModal = ref({ visible: false, imageUrl: '' });

const openImagePreview = (imageUrl: string) => {
    imagePreviewModal.value.imageUrl = imageUrl;
    imagePreviewModal.value.visible = true;
};
</script>