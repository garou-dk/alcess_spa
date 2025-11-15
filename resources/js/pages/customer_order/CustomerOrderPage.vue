<template>
    <div class="p-5 bg-gray-50 min-h-screen">
        <!-- Navigation Tabs -->
        <div class="card mb-4 bg-white p-4 rounded-lg shadow-sm">
            <div class="flex gap-2 flex-wrap">
                <Button
                    v-for="(value, index) in navs"
                    :key="index"
                    type="button"
                    :label="value.label"
                    :icon="value.icon"
                    :class="{
                        'primary-bg': value.active
                    }"
                    :variant="value.active ? '' : 'outlined'"
                    @click="setNav(index)"
                    class="transition-all"
                />
            </div>
        </div>

        <!-- Orders List -->
        <DataView :value="filtered">
            <template #list="slotProps">
                <div class="flex flex-col gap-4">
                    <div 
                        v-for="(item, index) in slotProps.items" 
                        :key="index" 
                        class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 hover:shadow-md transition-shadow"
                    >
                        <!-- Order Header -->
                        <div 
                            class="p-4"
                            :class="orderStatus(item.status)"
                        >
                            <div class="flex flex-wrap gap-4 items-center justify-between">
                                <div class="flex flex-wrap gap-4 flex-1">
                                    <div>
                                        <p class="text-xs text-gray-600 mb-1">Order ID</p>
                                        <p class="font-semibold text-gray-900">{{ item.order_public_id }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 mb-1">Order Type</p>
                                        <p class="font-medium">
                                            <i :class="item.order_type == 'Pickup' ? 'pi pi-map-marker' : 'pi pi-truck'" class="mr-1" />
                                            {{ item.order_type }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 mb-1">Order Date</p>
                                        <p class="font-medium">{{ DateUtil.formatToMonthDayYear(item.created_at) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 mb-1">Status</p>
                                        <p class="font-semibold">{{ item.status }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Order Info -->
                            <div v-if="hasAdditionalInfo(item)" class="flex flex-wrap gap-4 mt-4 pt-4 border-t border-gray-300">
                                <div v-if="item.order_type === 'Delivery'" class="flex-1 min-w-[200px]">
                                    <p class="text-xs text-gray-600 mb-1">Delivery Address</p>
                                    <p class="text-sm">{{ showCompleteAddress(item) }}</p>
                                </div>
                                <div v-if="item.payment_method">
                                    <p class="text-xs text-gray-600 mb-1">Payment Method</p>
                                    <p class="font-medium">{{ item.payment_method }}</p>
                                </div>
                                <div v-if="item.delivery_courier">
                                    <p class="text-xs text-gray-600 mb-1">Courier</p>
                                    <p class="font-medium">{{ item.delivery_courier }}</p>
                                </div>
                                <div v-if="item.tracking_number">
                                    <p class="text-xs text-gray-600 mb-1">Tracking Number</p>
                                    <p class="font-mono font-medium">{{ item.tracking_number }}</p>
                                </div>
                                <div v-if="item.estimated_delivery_date_start && item.estimated_delivery_date_end">
                                    <p class="text-xs text-gray-600 mb-1">Estimated Delivery</p>
                                    <p class="font-medium">
                                        {{ DateUtil.formatToMonthDayYear(item.estimated_delivery_date_start) }} - 
                                        {{ DateUtil.formatToMonthDayYear(item.estimated_delivery_date_end) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <DataTable
                            :value="item.product_orders"
                            class="border-t"
                        >
                            <Column field="product.product_name" header="Product" class="min-w-[250px]">
                                <template #body="{ data }">
                                    <div class="flex items-center gap-3 py-2">
                                        <Avatar
                                            v-if="data.product.product_image"
                                            shape="circle"
                                            :image="UrlUtil.getBaseAppUrl(`storage/images/product/${data.product.product_image}`)"
                                            class="w-12 h-12"
                                            size="large"
                                        />
                                        <Avatar
                                            v-else
                                            shape="circle"
                                            icon="pi pi-camera"
                                            class="w-12 h-12"
                                            size="large"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ data.product.product_name }}</p>
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column header="Price" field="price" class="text-right">
                                <template #body="{ data }">
                                    <span class="font-medium">{{ CurrencyUtil.formatCurrency(data.price) }}</span>
                                </template>
                            </Column>
                            <Column header="Qty" field="quantity" class="text-center">
                                <template #body="{ data }">
                                    <span class="font-medium">{{ data.quantity }}</span>
                                </template>
                            </Column>
                            <Column header="Subtotal" class="text-right">
                                <template #body="{ data }">
                                    <span class="font-semibold text-gray-900">{{ CurrencyUtil.formatCurrency(data.price * data.quantity) }}</span>
                                </template>
                            </Column>
                            <Column v-if="item.status === 'Delivered'" header="Rating" class="text-center">
                                <template #body="{ data }">
                                    <Button
                                        v-if="!data.rate"
                                        icon="pi pi-star"
                                        label="Rate"
                                        size="small"
                                        @click="openRate(data)"
                                    />
                                    <Button
                                        v-else
                                        icon="pi pi-star-fill"
                                        label="View"
                                        size="small"
                                        severity="secondary"
                                        @click="viewRating(data.rate)"
                                    />
                                </template>
                            </Column>
                        </DataTable>

                        <!-- Order Footer -->
                        <div class="p-4 bg-gray-50 border-t">
                            <div class="flex flex-wrap gap-3 justify-between items-center">
                                <!-- Order Total -->
                                <div class="text-right">
                                    <p v-if="item.order_type === 'Delivery' && item.shipping_fee !== null" class="text-sm text-gray-600 mb-1">
                                        Delivery Fee: {{ CurrencyUtil.formatCurrency(item.shipping_fee) }}
                                    </p>
                                    <p class="text-lg font-bold text-gray-900">
                                        Total: {{ CurrencyUtil.formatCurrency(calculateTotal(item)) }}
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 flex-wrap">
                                    <!-- Pending Status -->
                                    <Button
                                        v-if="item.status === 'Pending'"
                                        label="Cancel Order"
                                        severity="danger"
                                        size="small"
                                        icon="pi pi-times"
                                        @click="cancelOrder(item)"
                                        type="button"
                                    />

                                    <!-- Confirmed Status -->
                                    <template v-if="item.status === 'Confirmed'">
                                        <Button
                                            label="Cancel Order"
                                            severity="danger"
                                            size="small"
                                            icon="pi pi-times"
                                            @click="cancelOrder(item)"
                                            type="button"
                                        />
                                        <Button
                                            v-if="item.payment_method === 'Online Payment'"
                                            label="Pay Now"
                                            severity="success"
                                            size="small"
                                            icon="pi pi-money-bill"
                                            @click="openPaymentModal(item)"
                                            type="button"
                                        />
                                        <ConfirmCashDeliveryPrice 
                                            v-if="item.order_type === 'Delivery' && item.payment_method === 'Cash on Delivery'"
                                            :order="item" 
                                            @cb="load" 
                                        />
                                    </template>

                                    <!-- Shipped Status -->
                                    <MarkAsDelivered
                                        v-if="item.status === 'Shipped'"
                                        :data="item"
                                        @cb="load"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
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
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <div v-if="viewRateModal.data" class="flex flex-col gap-4">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Rating</p>
                    <div class="flex justify-center">
                        <Rating
                            :model-value="viewRateModal.data.rate"
                            :readonly="true"
                        />
                    </div>
                </div>
                <div v-if="viewRateModal.data.comment">
                    <p class="text-sm text-gray-600 mb-2">Comment</p>
                    <Textarea
                        :model-value="viewRateModal.data.comment"
                        placeholder="No comment provided"
                        fluid
                        readonly
                        rows="4"
                    />
                </div>
            </div>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import CancelOrderForm from '@/components/forms/CancelOrderForm.vue';
import ConfirmCashDeliveryPrice from '@/components/forms/ConfirmCashDeliveryPrice.vue';
import MarkAsDelivered from '@/components/forms/MarkAsDelivered.vue';
import PaymentForm from '@/components/forms/PaymentForm.vue';
import RateForm from '@/components/forms/RateForm.vue';
import { IOrder } from '@/interfaces/IOrder';
import IProductOrder from '@/interfaces/IProductOrder';
import { IRate } from '@/interfaces/IRate';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';
import { onMounted, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const loadService = useAxiosUtil<null, IOrder[]>();
const data = ref<IOrder[]>([]);
const filtered = ref<IOrder[]>([]);

const navs = ref([
    { label: 'All', icon: 'pi pi-list', active: true },
    { label: 'Pending', icon: 'pi pi-clock', active: false },
    { label: 'Confirmed', icon: 'pi pi-check-circle', active: false },
    { label: 'Shipped', icon: 'pi pi-truck', active: false },
    { label: 'Delivered', icon: 'pi pi-box', active: false },
    { label: 'Cancelled', icon: 'pi pi-times-circle', active: false },
    { label: 'Rejected', icon: 'pi pi-ban', active: false },
]);

const orderStatus = (value: string) => {
    const statusMap = {
        'Pending': 'bg-yellow-50 border-l-4 border-yellow-400',
        'Confirmed': 'bg-blue-50 border-l-4 border-blue-400',
        'Processing': 'bg-indigo-50 border-l-4 border-indigo-400',
        'Shipped': 'bg-purple-50 border-l-4 border-purple-400',
        'For delivery': 'bg-orange-50 border-l-4 border-orange-400',
        'Delivered': 'bg-green-50 border-l-4 border-green-400',
        'Released': 'bg-teal-50 border-l-4 border-teal-400',
        'Cancelled': 'bg-red-50 border-l-4 border-red-400',
        'Refunded': 'bg-pink-50 border-l-4 border-pink-400',
        'Rejected': 'bg-gray-50 border-l-4 border-gray-400',
    };
    return statusMap[value] || 'bg-gray-50';
};

const cancelModal = ref({ visible: false, order: null as IOrder | null });
const paymentModal = ref({ visible: false, order: null as IOrder | null });
const rateModal = ref({ visible: false, data: null as IProductOrder | null });
const viewRateModal = ref({ visible: false, data: null as IRate | null });

const hasAdditionalInfo = (item: IOrder) => {
    return item.order_type === 'Delivery' || 
           item.payment_method || 
           item.delivery_courier || 
           item.tracking_number || 
           (item.estimated_delivery_date_start && item.estimated_delivery_date_end);
};

const calculateTotal = (item: IOrder) => {
    const productTotal = item.product_orders.reduce((total, product) => 
        total + (product.price * product.quantity), 0
    );
    const shippingFee = item.shipping_fee || 0;
    return productTotal + shippingFee;
};

const openRate = (item: IProductOrder) => {
    rateModal.value.data = item;
    rateModal.value.visible = true;
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
            filtered.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load orders");
        }
    });
};

const setNav = (index: number) => {
    navs.value.forEach((item, i) => {
        item.active = i === index;
    });
    
    const activeNav = navs.value[index];
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
    paymentModal.value.order = item;
    paymentModal.value.visible = true;
};

const closePaymentModal = () => {
    paymentModal.value.order = null;
    paymentModal.value.visible = false;
};

const paymentModalCb = () => {
    paymentModal.value.visible = false;
    load();
};

onMounted(() => {
    load();
});
</script>