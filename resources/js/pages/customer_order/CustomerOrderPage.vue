<template>

    <div class="min-h-screen bg-gray-50 flex flex-col">
        <div class="flex-grow" :class="getResponsiveClasses({
            mobile: 'p-3 pb-24',
            tablet: 'p-4 pb-24',
            desktop: 'p-5 pb-24'
        })">
        <!-- Navigation Tabs -->
        <div :class="getResponsiveClasses({
            mobile: 'card mb-4 bg-white p-3 rounded-lg shadow-sm',
            tablet: 'card mb-4 bg-white p-4 rounded-lg shadow-sm', 
            desktop: 'card mb-4 bg-white p-4 rounded-lg shadow-sm'
        })">
            <div :class="getResponsiveClasses({
                mobile: 'flex flex-col gap-3',
                tablet: 'flex gap-2 flex-wrap items-center justify-between',
                desktop: 'flex gap-2 flex-wrap items-center justify-between'
            })">
                <!-- Navigation Buttons Container -->
                <div :class="getResponsiveClasses({
                    mobile: 'flex gap-2 overflow-x-auto pb-2 -mb-2',
                    tablet: 'flex gap-2 flex-wrap',
                    desktop: 'flex gap-2 flex-wrap'
                })" :style="getResponsiveClasses({
                    mobile: 'scrollbar-width: thin; scrollbar-color: #d1d5db #f9fafb;',
                    tablet: '',
                    desktop: ''
                })">
                    <Button
                        v-for="(value, index) in navs"
                        :key="index"
                        type="button"
                        :label="value.label"
                        :icon="value.icon"
                        :class="[
                            {
                                'primary-bg': value.active
                            },
                            getResponsiveClasses({
                                mobile: 'flex-shrink-0 text-sm',
                                tablet: 'flex-shrink-0 text-sm',
                                desktop: 'flex-shrink-0'
                            })
                        ]"
                        :variant="value.active ? '' : 'outlined'"
                        @click="setNav(index)"
                        class="transition-all"
                    />
                </div>
                
                <!-- Refresh Button -->
                <Button
                    type="button"
                    icon="pi pi-refresh"
                    label="Refresh"
                    size="small"
                    @click="load"
                    :loading="loadService.request.loading"
                    :class="getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'ml-auto',
                        desktop: 'ml-auto'
                    })"
                />
            </div>
        </div>

        <!-- Orders List -->
        <DataView 
            :key="filtered.map(o => o.order_id).join('-')"
            :value="filtered"
            :paginator="filtered.length > getResponsiveClasses({
                mobile: 3,
                tablet: 5,
                desktop: 5
            })"
            :rows="getResponsiveClasses({
                mobile: 3,
                tablet: 5,
                desktop: 5
            })"
            :rowsPerPageOptions="getResponsiveClasses({
                mobile: [3, 5, 10],
                tablet: [5, 10, 20],
                desktop: [5, 10, 20, 50]
            })"
            :pt="{
                paginator: {
                    root: getResponsiveClasses({
                        mobile: 'text-sm',
                        tablet: '',
                        desktop: ''
                    })
                }
            }"
        >
            <template #list="slotProps">
                <div class="grid grid-cols-1 gap-4 pb-4">
                    <OrderCard
                        v-for="item in slotProps.items"
                        :key="item.order_id"
                        :order="item"
                        @pay="openPaymentModal"
                        @cancel="cancelOrder"
                        @rate-product="openRate"
                        @view-rating="viewRating"
                        @refresh="load"
                    />
                </div>
            </template>

            <template #empty>
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <div v-if="!loadService.request.loading">
                        <i class="pi pi-shopping-cart text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No orders found</p>
                    </div>
                    <div v-else>
                        <PageLoader />
                    </div>
                </div>
            </template>
        </DataView>

        <!-- Cancel Order Dialog -->
        <Dialog
            v-model:visible="cancelModal.visible"
            header="Cancel Order"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <CancelOrderForm
                v-if="cancelModal.order"
                :order="cancelModal.order"
                @cb="cancelCb"
                @close-popup="closeCancelModal"
            />
        </Dialog>

        <!-- Payment Dialog -->
        <Dialog
            v-model:visible="paymentModal.visible"
            header="Complete Payment"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <PaymentForm
                v-if="paymentModal.order"
                :order="paymentModal.order"
                @cb="paymentModalCb"
                @close-popup="closePaymentModal"
            />
        </Dialog>

        <!-- Rate Product Dialog -->
        <Dialog
            v-model:visible="rateModal.visible"
            header="Rate Product"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <RateForm
                v-if="rateModal.data"
                :data="rateModal.data"
                @cb="rateCb"
            />
        </Dialog>

        <!-- View Rating Dialog -->
        <Dialog
            v-model:visible="viewRateModal.visible"
            header="Your Review"
            :style="{ width: '35rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <div v-if="viewRateModal.data" class="flex flex-col gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-3">Rating</p>
                    <div class="flex justify-center gap-1">
                        <i 
                            v-for="star in 5" 
                            :key="star"
                            :class="star <= viewRateModal.data.rate ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-gray-300'"
                            class="text-2xl"
                        ></i>
                    </div>
                </div>
                <div v-if="viewRateModal.data.comment">
                    <p class="text-sm font-medium text-gray-700 mb-2">Comment</p>
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ viewRateModal.data.comment }}</p>
                    </div>
                </div>
                <div v-if="viewRateModal.data.images && viewRateModal.data.images.length > 0">
                    <p class="text-sm font-medium text-gray-700 mb-2">Media</p>
                    <div class="flex flex-wrap gap-2">
                        <div
                            v-for="(image, index) in viewRateModal.data.images"
                            :key="index"
                            @click="openRateMediaPreview(image.image_path)"
                            class="relative w-24 h-24 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity border-2 border-gray-300 hover:border-blue-600"
                        >
                            <img
                                v-if="isImageFile(image.image_path)"
                                :src="getRateImageUrl(image.image_path)"
                                class="w-full h-full object-cover"
                                alt="Review media"
                            />
                            <video
                                v-else
                                :src="getRateImageUrl(image.image_path)"
                                class="w-full h-full object-cover pointer-events-none"
                            ></video>
                            <div v-if="!isImageFile(image.image_path)" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 pointer-events-none">
                                <i class="pi pi-play text-white text-3xl drop-shadow-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Dialog>

        <!-- Rate Media Preview Dialog -->
        <Dialog
            v-model:visible="rateMediaPreviewModal.visible"
            header="Review Media"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <img
                v-if="isImageFile(rateMediaPreviewModal.mediaPath)"
                :src="getRateImageUrl(rateMediaPreviewModal.mediaPath)"
                class="w-full h-auto rounded-lg"
                alt="Review media preview"
            />
            <video
                v-else
                :src="getRateImageUrl(rateMediaPreviewModal.mediaPath)"
                controls
                autoplay
                class="w-full h-auto rounded-lg"
            >
                Your browser does not support the video tag.
            </video>
        </Dialog>

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
        <!-- Footer -->
        <Footer class="mt-8" />

    </div>
</template>

<script setup lang="ts">
import CancelOrderForm from '@/components/forms/CancelOrderForm.vue';
import ConfirmCashDeliveryPrice from '@/components/forms/ConfirmCashDeliveryPrice.vue';
import MarkAsDelivered from '@/components/forms/MarkAsDelivered.vue';
import PaymentForm from '@/components/forms/PaymentForm.vue';
import RateForm from '@/components/forms/RateForm.vue';
import OrderCard from '@/components/list/OrderCard.vue';
import Footer from '@/components/Footer.vue';
import { IOrder } from '@/interfaces/IOrder';
import { IOrderNotification } from '@/interfaces/IOrderNotification';
import IProductOrder from '@/interfaces/IProductOrder';
import { IRate } from '@/interfaces/IRate';
import Page from '@/stores/Page';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';
import { useResponsive } from '@/composables/useResponsive';
import { useEcho } from '@laravel/echo-vue';
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';
import { useRouter } from 'vue-router';

const toast = useToast();
const router = useRouter();
const loadService = useAxiosUtil<null, IOrder[]>();
const data = ref<IOrder[]>([]);
const filtered = ref<IOrder[]>([]);

// Use responsive composable
const { 
    isMobile, 
    isTablet, 
    isDesktop, 
    getResponsiveClasses, 
    getResponsivePadding, 
    getResponsiveMargin, 
    getResponsiveTextSize,
    getResponsiveGap,
    shouldUseCardLayout 
} = useResponsive();

const navs = ref([
    { label: 'All', icon: 'pi pi-list', active: true },
    { label: 'Processing', icon: 'pi pi-clock', active: false },
    { label: 'Confirmed', icon: 'pi pi-check-circle', active: false },
    { label: 'Shipped', icon: 'pi pi-truck', active: false },
    { label: 'Completed', icon: 'pi pi-box', active: false },
]);

const cancelModal = ref({ visible: false, order: null as IOrder | null });
const paymentModal = ref({ visible: false, order: null as IOrder | null });
const rateModal = ref({ visible: false, data: null as IProductOrder | null });
const viewRateModal = ref({ visible: false, data: null as IRate | null });

// Rate media preview modal
const rateMediaPreviewModal = ref({ visible: false, mediaPath: '' });

const openRateMediaPreview = (mediaPath: string) => {
    rateMediaPreviewModal.value.mediaPath = mediaPath;
    rateMediaPreviewModal.value.visible = true;
};

const getRateImageUrl = (filename: string) => {
    return UrlUtil.getBaseAppUrl(`storage/images/rate_images/${filename}`);
};

const isImageFile = (filename: string) => {
    const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp'];
    return imageExtensions.some(ext => filename.toLowerCase().endsWith(ext));
};

// Image preview modal
const imagePreviewModal = ref({ visible: false, imageUrl: '' });

const openImagePreview = (imageUrl: string) => {
    imagePreviewModal.value.imageUrl = imageUrl;
    imagePreviewModal.value.visible = true;
};

// Display status for customer - now matches database status exactly
const getCustomerStatusDisplay = (item: IOrder) => {
    if (item.status === 'Processing') {
        if (!item.admin_accepted) {
            return 'Processing - Awaiting Review';
        } else if (!item.proof_of_payment) {
            return 'Processing - Payment Required';
        } else {
            return 'Processing - Payment Under Review';
        }
    }
    if (item.status === 'Confirmed') return 'Confirmed';
    if (item.status === 'Shipped') return 'Shipped';
    if (item.status === 'Completed') return 'Completed';
    if (item.status === 'Cancelled') return 'Cancelled';
    return item.status;
};

const calculateTotal = (item: IOrder) => {
    const productTotal = item.product_orders.reduce((total, product) => 
        total + (product.price * product.quantity), 0
    );
    const shippingFee = item.shipping_fee || 0;
    return productTotal + shippingFee;
};

const openRate = (item: IProductOrder) => {
    router.push({ 
        name: 'customer.product-info.index', 
        params: { id: item.product_id },
        query: { 
            scrollToReview: 'true',
            productOrderId: item.product_order_id 
        }
    });
};

const rateCb = () => {
    load();
    rateModal.value.visible = false;
};

const viewRating = (value: IRate) => {
    viewRateModal.value.data = value;
    viewRateModal.value.visible = true;
};

const load = async () => {
    await loadService.get("customer/orders").then(() => {
        if (loadService.request.status == 200 && loadService.request.data) {
            data.value = loadService.request.data;
            // Re-apply current filter
            const activeNav = navs.value.find(n => n.active);
            if (activeNav && activeNav.label !== 'All') {
                filtered.value = loadService.request.data.filter(item => item.status === activeNav.label);
            } else {
                filtered.value = loadService.request.data;
            }
        } else {
            toast.error(loadService.request.message ?? "Failed to load orders");
        }
    });
};

const setNav = async (index: number) => {
    navs.value.forEach((item, i) => {
        item.active = i === index;
    });
    
    const activeNav = navs.value[index];
    
    // Force reload when switching to Completed tab to ensure rates are loaded
    if (activeNav.label === 'Completed') {
        await load();
    }
    
    if (activeNav.label === 'All') {
        filtered.value = data.value;
    } else {
        filtered.value = data.value.filter(item => item.status === activeNav.label);
    }
};

const cancelOrder = (item: IOrder) => {
    cancelModal.value.order = item;
    cancelModal.value.visible = true;
};

const cancelCb = () => {
    cancelModal.value.visible = false;
    load();
};

const closeCancelModal = () => {
    cancelModal.value.order = null;
    cancelModal.value.visible = false;
};

const showCompleteAddress = (value: IOrder) => {
    return `${value.other_details}, ${value.barangay.barangay_name}, ${value.barangay.municity.municity_name}, ${value.barangay.municity.province.province_name}`;
};

const openPaymentModal = (item: IOrder) => {
    if (!item) {
        console.error('No item provided to openPaymentModal');
        return;
    }
    nextTick(() => {
        paymentModal.value.order = item;
        paymentModal.value.visible = true;
    });
};

const buyAgainService = useAxiosUtil();
const buyAgain = async (order: IOrder) => {
    await buyAgainService.post(`customer/orders/buy-again/${order.order_id}`, {});
    if (buyAgainService.request.status === 200) {
        toast.success('Items added to cart!');
    } else {
        toast.error(buyAgainService.request.message || 'Failed to add items to cart.');
    }
};

const closePaymentModal = () => {
    paymentModal.value.order = null;
    paymentModal.value.visible = false;
};

const paymentModalCb = (updatedOrder?: IOrder) => {
    paymentModal.value.visible = false;

    if (updatedOrder) {
        // Update the order in the main list
        const index = data.value.findIndex(o => o.order_id === updatedOrder.order_id);
        if (index !== -1) {
            data.value[index] = updatedOrder;
        }

        // Update in filtered list too if necessary
        const filteredIndex = filtered.value.findIndex(o => o.order_id === updatedOrder.order_id);
        if (filteredIndex !== -1) {
            filtered.value[filteredIndex] = updatedOrder;
        }
    } else {
        load();
    }
};

// Check if any orders are awaiting admin review or pending action
const hasPendingOrders = computed(() => {
    return data.value.some(order => 
        order.status === 'Processing' && !order.admin_accepted
    );
});

// Auto-polling: refresh data every 15s when orders are awaiting admin review
let pollingInterval: ReturnType<typeof setInterval> | null = null;

const startPolling = () => {
    stopPolling();
    pollingInterval = setInterval(() => {
        if (hasPendingOrders.value) {
            load();
        } else {
            stopPolling();
        }
    }, 15000);
};

const stopPolling = () => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
};

// Start/stop polling based on whether there are pending orders
watch(hasPendingOrders, (pending) => {
    if (pending) {
        startPolling();
    } else {
        stopPolling();
    }
});

const { leave } = useEcho(
    `detect-order.${Page.user.user_id}`,
    [".customer-order.event"],
    (value: IOrderNotification) => {
        load();
    },
);

onMounted(() => {
    load();
    
    const handleVisibilityChange = () => {
        if (!document.hidden) {
            load();
        }
    };
    
    document.addEventListener('visibilitychange', handleVisibilityChange);
    
    onUnmounted(() => {
        document.removeEventListener('visibilitychange', handleVisibilityChange);
    });
});

onUnmounted(() => {
    leave();
    stopPolling();
});

// Also reload when route changes back to this page
watch(() => router.currentRoute.value.fullPath, (newPath, oldPath) => {
    if (newPath.includes('/orders') && oldPath && !oldPath.includes('/orders')) {
        load();
    }
});
</script>

<style scoped>
/* Remove border from tab buttons container */
.card.mb-4.bg-white.p-4.rounded-lg.shadow-sm {
    border: none !important;
    box-shadow: none !important;
}

/* Custom scrollbar for mobile navigation */
@media (max-width: 767px) {
    .overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f9fafb;
        border-radius: 2px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 2px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
}

/* Mobile/Tablet navigation button font sizes */
@media (max-width: 1023px) {
    .card.mb-4 :deep(.p-button .p-button-label) {
        font-size: 0.875rem !important;
    }
    
    .card.mb-4 :deep(.p-button .p-button-icon) {
        font-size: 0.875rem !important;
    }
}

/* Mobile pagination responsiveness */
@media (max-width: 767px) {
    :deep(.p-paginator .p-paginator-pages .p-paginator-page) {
        min-width: 2rem !important;
        height: 2rem !important;
        font-size: 0.875rem !important;
        padding: 0.25rem !important;
    }
    
    :deep(.p-paginator) {
        padding: 0.5rem !important;
        gap: 0.25rem !important;
    }
    
    :deep(.p-paginator .p-dropdown) {
        font-size: 0.75rem !important;
    }
    
    :deep(.p-paginator .p-paginator-first),
    :deep(.p-paginator .p-paginator-prev),
    :deep(.p-paginator .p-paginator-next),
    :deep(.p-paginator .p-paginator-last) {
        min-width: 2rem !important;
        height: 2rem !important;
        padding: 0.25rem !important;
    }
}

@media (max-width: 1023px) and (min-width: 768px) {
    :deep(.p-paginator .p-paginator-pages .p-paginator-page) {
        min-width: 2.25rem !important;
        height: 2.25rem !important;
    }
}

/* Unselected tab buttons - gray color (ONLY in navigation tabs container) */
.card.mb-4 :deep(.p-button[variant="outlined"]),
.card.mb-4 :deep(.p-button:not(.primary-bg)) {
    background-color: transparent !important;
    color: #6b7280 !important;
    border: none !important;
}

.card.mb-4 :deep(.p-button[variant="outlined"]:hover),
.card.mb-4 :deep(.p-button:not(.primary-bg):hover) {
    background-color: #f3f4f6 !important;
    color: #4b5563 !important;
}

/* Selected tab button - white text with bright blue background */
:deep(.p-button.primary-bg),
:deep(button.primary-bg),
:deep(.primary-bg) {
    background-color: #2563eb !important;
    background: #2563eb !important;
    background-image: none !important;
    color: #ffffff !important;
    border: none !important;
}

:deep(.p-button.primary-bg:hover),
:deep(button.primary-bg:hover),
:deep(.primary-bg:hover) {
    background-color: #1d4ed8 !important;
    background: #1d4ed8 !important;
    background-image: none !important;
    color: #ffffff !important;
}

/* Icon colors (ONLY in navigation tabs container) */
.card.mb-4 :deep(.p-button:not(.primary-bg) .p-button-icon),
.card.mb-4 :deep(.p-button:not(.primary-bg) i) {
    color: #6b7280 !important;
}

.card.mb-4 :deep(.p-button.primary-bg .p-button-icon),
.card.mb-4 :deep(.p-button.primary-bg i) {
    color: #ffffff !important;
}
</style>