<template>
    <div class="p-5">
        <div class="card mb-2 flex gap-2 flex-wrap bg-white p-3 rounded">
            <div v-for="(value, index) in navs" :key="index">
                <Button
                    type="button"
                    :label="value.label"
                    :icon="value.icon"
                    :class="{
                        'primary-bg': value.active
                    }"
                    :variant="value.active ? '' : 'outlined'"
                    @click="setNav(index)"
                />
            </div>
        </div>
        <DataView
            :value="filtered"
        >
            <template #list="slotProps">
                <div class="flex flex-col">
                    <div v-for="(item, index) in slotProps.items" :key="index" class="p-2 border">
                        <div 
                            class="flex flex-wrap rounded"
                            :class="orderStatus(item.status)"
                        >
                            <div class="p-2">
                                <div>
                                    <p class="font-semibold">Order ID</p>
                                </div>
                                <div>
                                    <p>{{ item.order_public_id }}</p>
                                </div>
                            </div>
                            <div class="p-2">
                                <div>
                                    <p class="font-semibold">Order Type</p>
                                </div>
                                <div>
                                    <p>
                                        <i 
                                            :class="item.order_type == 'Pickup' ? 'pi pi-map-marker' : 'pi pi-truck'"
                                        />
                                        {{ item.order_type }}
                                    </p>
                                </div>
                            </div>
                            <div class="p-2">
                                <div>
                                    <p class="font-semibold">Order Date</p>
                                </div>
                                <div>
                                    <p>{{ DateUtil.formatToMonthDayYear(item.created_at) }}</p>
                                </div>
                            </div>
                            <div class="p-2">
                                <div>
                                    <p class="font-semibold">Order Status</p>
                                </div>
                                <div>
                                    <p>{{ item.status }}</p>
                                </div>
                            </div>
                            <div v-if="item.order_type === 'Delivery'" class="p-2">
                                <div>
                                    <p class="font-semibold">Address</p>
                                </div>
                                <div>
                                    <p>{{ showCompleteAddress(item) }}</p>
                                </div>
                            </div>
                            <div v-if="item.payment_method" class="p-2">
                                <div>
                                    <p class="font-semibold">Payment Method</p>
                                </div>
                                <div>
                                    <p>{{ item.payment_method }}</p>
                                </div>
                            </div>
                            <div v-if="item.delivery_courier" class="p-2">
                                <div>
                                    <p class="font-semibold">Delivery Courier</p>
                                </div>
                                <div>
                                    <p>{{ item.delivery_courier }}</p>
                                </div>
                            </div>
                            <div v-if="item.tracking_number" class="p-2">
                                <div>
                                    <p class="font-semibold">Tracking Number</p>
                                </div>
                                <div>
                                    <p>{{ item.tracking_number }}</p>
                                </div>
                            </div>
                            <div v-if="item.estimated_delivery_date_start && item.estimated_delivery_date_end" class="p-2">
                                <div>
                                    <p class="font-semibold">Estimated Delivery Date</p>
                                </div>
                                <div>
                                    <p>{{ DateUtil.formatToMonthDayYear(item.estimated_delivery_date_start) }} - {{ DateUtil.formatToMonthDayYear(item.estimated_delivery_date_end) }}</p>
                                </div>
                            </div>
                        </div>
                        <DataTable
                            :value="item.product_orders"
                            show-gridlines
                            class="mt-2"
                        >
                            <template #header>
                                <div class="flex justify-end gap-2">
                                    <Button
                                        v-if="item.status === 'Pending' || item.status === 'Confirmed'"
                                        label="Cancel Order"
                                        severity="danger"
                                        @click="cancelOrder(item)"
                                        type="button"
                                    />
                                    <Button
                                        v-if="item.status === 'Pending'"
                                        label="Please wait for the confirmation"
                                        :disabled="true"
                                        type="button"
                                    />
                                    <Button
                                        v-if="item.status === 'Confirmed' && item.payment_method === 'Online Payment'"
                                        label="Set Payment"
                                        @click="openPaymentModal(item)"
                                        type="button"
                                    />
                                    <MarkAsDelivered
                                        v-if="item.status === 'Shipped'"
                                        :data="item"
                                        @cb="load"
                                    />
                                    <div v-if="item.order_type === 'Delivery' && item.status === 'Confirmed' && item.payment_method === 'Cash on Delivery'">
                                        <ConfirmCashDeliveryPrice :order="item" @cb="load" />
                                    </div>
                                </div>
                            </template>
                            <Column field="product.product_name" header="Product Name">
                                <template #body="{ data }">
                                    <div class="flex items-center">
                                        <Avatar
                                            v-if="data.product.product_image"
                                            shape="circle"
                                            :image="
                                                UrlUtil.getBaseAppUrl(
                                                    `storage/images/product/${data.product.product_image}`,
                                                )
                                            "
                                            class="aspect-square!"
                                        />
                                        <Avatar
                                            v-else
                                            shape="circle"
                                            icon="pi pi-camera"
                                            class="aspect-square!"
                                        />
                                        <div class="ml-2 shrink">
                                            {{ data.product.product_name }}
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column header="Price" field="price">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.price) }}
                                </template>
                            </Column>
                            <Column header="Quantity" field="quantity" />
                            <Column header="Total">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.price * data.quantity) }}
                                </template>
                            </Column>
                            <Column v-if="item.status === 'Delivered'" header="Rate">
                                <template #body="{ data }">
                                    <div class="flex justify-center">
                                        <Button
                                            v-if="!data.rate"
                                            icon="pi pi-star"
                                            label="Rate"
                                            @click="openRate(data)"
                                        />
                                        <Button
                                            v-else
                                            icon="pi pi-star-fill"
                                            label="View Rate"
                                            @click="viewRating(data.rate)"
                                        />
                                    </div>
                                </template>
                            </Column>
                            <template #footer>
                                <div class="flex justify-end">
                                    <div>
                                        <p v-if="item.order_type === 'Delivery'" class="font-semibold">
                                            Delivery Fee: {{ item.shipping_fee !== null ? CurrencyUtil.formatCurrency(item.shipping_fee) : 'TBD' }}
                                        </p>
                                        <p class="font-semibold">
                                            Total: {{ CurrencyUtil.formatCurrency(item.product_orders.reduce((total, item) => total + item.price * item.quantity, 0) + item.shipping_fee) }}
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </template>
            <template #empty>
                <div v-if="!loadService.request.loading" class="flex justify-center p-5">
                    <p>No orders found</p>
                </div>
                <div v-else class="flex justify-center p-5">
                    <PageLoader />
                </div>
            </template>
        </DataView>
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
        <Dialog
            v-model:visible="paymentModal.visible"
            header="Pay Order"
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
        <Dialog
            v-model:visible="rateModal.visible"
            header="Rate Order"
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
        <Dialog
            v-model:visible="viewRateModal.visible"
            header="View Rate"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <div class="flex flex-col gap-2">
                <div v-if="viewRateModal.data">
                    <InputForm
                        :errors="[]"
                        label-name="Rate"
                        id="rating"
                        tag="span"
                    >
                        <div class="flex justify-center">
                            <Rating
                                :model-value="viewRateModal.data.rate"
                                :readonly="true"
                            />
                        </div>
                    </InputForm>
                </div>
                <div>
                    <InputForm
                        :errors="[]"
                        id="comment"
                        labelName="Comment"
                        tag="label"
                    >
                        <Textarea
                            :model-value="viewRateModal.data.comment"
                            id="comment"
                            placeholder="Enter your comment"
                            fluid
                            readonly
                        />
                    </InputForm>
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
const navs = ref<{
    label: string;
    icon: string;
    active?: boolean;
}[]>([
    {
        label: 'All',
        icon: 'pi pi-list',
        active: false
    },
    {
        label: 'Pending',
        icon: 'pi pi-clock',
        active: false
    },
    {
        label: 'To Pay',
        icon: 'pi pi-money-bill',
        active: false
    },
    {
        label: 'To Ship',
        icon: 'pi pi-truck',
        active: false
    },
    {
        label: 'To Receive',
        icon: 'pi pi-box',
        active: false
    },
    {
        label: 'To Review',
        icon: 'pi pi-star',
        active: false
    },
    {
        label: 'Cancelled',
        icon: 'pi pi-times',
        active: false
    },
    {
        label: 'Rejected',
        icon: 'pi pi-ban',
        active: false
    },
    {
        label: 'Completed',
        icon: 'pi pi-check',
        active: false
    },
]);

const orderStatus = (value: string) => {
    return {
        Pending: 'bg-yellow-200',
        Confirmed: 'bg-green-200',
        Processing: 'bg-blue-200',
        Shipped: 'bg-purple-200',
        "For delivery": 'bg-orange-200',
        Delivered: 'bg-green-300',
        Released: 'bg-blue-300',
        Cancelled: 'bg-red-200',
        Refunded: 'bg-red-300',
        Rejected: 'bg-red-300',
    }[value];
}

const cancelModal = ref<{
    visible: boolean;
    order: IOrder | null;
}>({
    visible: false,
    order: null,
});

const paymentModal = ref<{
    visible: boolean;
    order: IOrder | null;
}>({
    visible: false,
    order: null,
});

const rateModal = ref<{
    visible: boolean;
    data: IProductOrder | null;
}>({
    visible: false,
    data: null
});

const openRate = (item: IProductOrder) => {
    rateModal.value.data = item;
    rateModal.value.visible = true;
}

const rateCb = () => {
    load();
    rateModal.value.visible = false;
}

const viewRateModal = ref<{
    visible: boolean;
    data: IRate | null;
}>({
    visible: false,
    data: null
});

const viewRating = (value: IRate) => {
    viewRateModal.value.data = value;
    console.log(value);
    
    viewRateModal.value.visible = true;
}

const load = async () => {
    await loadService.get("customer/orders").then(() => {
        if (loadService.request.status == 200 && loadService.request.data) {
            data.value = loadService.request.data;
            filtered.value = loadService.request.data;
            setNav(0);
        }
        else {
            toast.error(loadService.request.message ?? "Failed to load orders");
        }
    });
}

const setNav = (index: number) => {
    navs.value.forEach((item, i) => {
        item.active = i === index;
    });
}

watch(navs.value,
    () => {
        const active = navs.value.find((item) => item.active);
        filtered.value = [];
        if (active.label == 'All') {
            filtered.value = data.value;
        }
        else {
            filtered.value = data.value.filter((item) => item.status === active?.label);
        }
    },
);

const cancelOrder = (item: IOrder) => {
    cancelModal.value.order = item;
    cancelModal.value.visible = true;
}

const cancelCb = () => {
    cancelModal.value.visible = false;
    load();
}

const closeCancelModal = () => {
    cancelModal.value.order = null;
    cancelModal.value.visible = false;
}

const showCompleteAddress = (value: IOrder) => {
    return `${value.other_details}, ${value.barangay.barangay_name}, ${value.barangay.municity.municity_name}, ${value.barangay.municity.province.province_name}, ${value.barangay.municity.province.region.region_name}, ${value.barangay.municity.province.region.island_group.island_group_name}, ${value.postal_code}`
}

const openPaymentModal = (item: IOrder) => {
    paymentModal.value.order = item;
    paymentModal.value.visible = true;
}

const closePaymentModal = () => {
    paymentModal.value.order = null;
    paymentModal.value.visible = false;
}

const paymentModalCb = () => {
    paymentModal.value.visible = false;
    load();
}

onMounted(() => {
    load();
});
</script>