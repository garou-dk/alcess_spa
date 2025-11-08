<template>
    <div>
        <BoxShadow class="mb-3">
            <div class="flex items-center p-2">
                <span class="mr-2 text-gray-700">Payment Status:</span>
                <div class="flex items-center">
                    <img :src="GCashIcon" alt="Gcash" class="mr-1 h-6 w-6" />
                    <span class="text-gray-700">Online Payment</span>
                </div>
                <div class="ml-4 flex items-center">
                    <img :src="CashIcon" alt="Cash" class="mr-1 h-6 w-6" />
                    <span class="text-gray-700">Cash</span>
                </div>
            </div>
        </BoxShadow>

        <BoxShadow class="mb-3">
            <div class="flex w-full flex-wrap">
                <div class="grow p-2">
                    <InputForm
                        :errors="[]"
                        id="search"
                        label-name="Search"
                        tag="label"
                    >
                        <InputGroup>
                            <InputGroupAddon>
                                <i class="pi pi-search" />
                            </InputGroupAddon>
                            <InputText
                                id="search"
                                placeholder="Search by Order ID or Customer Name"
                                fluid
                            />
                        </InputGroup>
                    </InputForm>
                </div>
                <div class="p-2">
                    <InputForm
                        :errors="[]"
                        id="status"
                        label-name="Status"
                        tag="label"
                    >
                        <Select v-model="filters.status.value" :options="orderStatuses" option-label="label" option-value="value" placeholder="Select Status" />
                    </InputForm>
                </div>
                <div class="p-2">
                    <InputForm
                        :errors="[]"
                        id="status"
                        label-name="Status"
                        tag="label"
                    >
                        <Select v-model="filters.status.value" :options="orderStatuses" option-label="label" option-value="value" placeholder="Select Status" />
                    </InputForm>
                </div>
                <div class="p-2">
                    <InputForm
                        :errors="[]"
                        id="order_type"
                        label-name="Order Type"
                        tag="label"
                    >
                        <Select v-model="filters.order_type.value" :options="orderTypes" option-label="label" option-value="value" placeholder="Select Order Type" />
                    </InputForm>
                </div>
                <!-- <div class="p-2">
                    <InputForm
                        :errors="[]"
                        id="limit"
                        label-name="Limit"
                        tag="label"
                    >
                        <Select placeholder="Select Status" />
                    </InputForm>
                </div> -->
                <div class="flex items-end p-2">
                    <Button
                        label="Filter"
                        icon="pi pi-filter"
                        class="primary-bg"
                    />
                </div>
            </div>
        </BoxShadow>

        <BoxShadow>
            <div class="w-full">
                <DataTable :value="data" :filters="filters" :loading="loadService.request.loading">
                    <Column field="order_public_id" header="Order ID" />
                    <Column field="user.full_name" header="Customer Name" />
                    <Column field="created_at" header="Order Date">
                        <template #body="{ data }">
                            {{ DateUtil.formatToMonthDayYear(data.created_at) }}
                        </template>
                    </Column>
                    <Column field="order_type" header="Order Type" />
                    <Column field="status" header="Order Status" />
                    <Column field="date_paid_confirmed" header="Payment Status">
                        <template #body="{ data }">
                            {{ 
                                data.date_paid_confirmed
                                    ? "Paid"
                                    : "Unpaid"
                            }}
                        </template>
                    </Column>
                    <Column field="amount" header="Amount">
                        <template #body="{ data }">
                            {{ 
                                data.total_amount ?
                                    CurrencyUtil.formatCurrency(data.total_amount)
                                    : CurrencyUtil.formatCurrency(data.product_orders.reduce((total, item) => total + item.price * item.quantity, 0))
                            }}
                        </template>
                    </Column>
                    <Column field="payment_method" header="Payment Method">
                        <template #body="{ data }">
                            <div class="flex items-center">
                                <img
                                    v-if="data.payment_method === 'Online Payment'"
                                    :src="GCashIcon"
                                    alt="Gcash"
                                    class="mr-1 h-6 w-6"
                                />
                                <img
                                    v-else-if="data.payment_method === 'Cash on Delivery' || data.payment_method === 'Pickup Payment'"
                                    :src="CashIcon"
                                    alt="Cash"
                                    class="mr-1 h-6 w-6"
                                />
                                <span class="text-gray-700">{{
                                    data.payment_method
                                }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="{ data }">
                            <Button
                                icon="pi pi-ellipsis-v"
                                class="primary-bg"
                                @click="selectOrder($event, data)"
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </BoxShadow>
        <Popover ref="editElement">
            <div v-if="selectedOrder" class="min-w-36">
                <div>
                    <button
                        type="button"
                        class="hover:bg-gray-200 hover:cursor-pointer p-2 w-full text-start flex items-center gap-2"
                    >
                        <i class="pi pi-eye" /> View Products
                    </button>
                </div>
                <ApprovalForm :data="selectedOrder" @cb="setNewOrder" @close-popup="" />
            </div>
        </Popover>
    </div>
</template>
<script setup lang="ts">
import GCashIcon from "@/../img/gcash.png";
import CashIcon from "@/../img/cash.png";
import { Button, Popover } from "primevue";
import { onMounted, ref } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { IOrder } from "@/interfaces/IOrder";
import { useToast } from "vue-toastification";
import DateUtil from "@/utils/DateUtil";
import CurrencyUtil from "@/utils/CurrencyUtil";
import { FilterMatchMode } from '@primevue/core/api';
import ApprovalForm from "@/components/forms/ApprovalForm.vue";

const editElement = ref<null | InstanceType<typeof Popover>>();

const selectedOrder = ref<IOrder | null>(null);

const toast = useToast();

const filters = ref({
    status: { value: 'Pending', matchMode: FilterMatchMode.EQUALS },
    order_type: { value: '', matchMode: FilterMatchMode.EQUALS },
});

const data = ref<IOrder[]>([]);

const loadService = useAxiosUtil<null, IOrder[]>();

const orderStatuses = ref<{
    label: string;
    value: string;
}[]>([
    { label: 'All', value: '' },
    { label: 'Pending', value: 'Pending' },
    { label: 'Confirmed', value: 'Confirmed' },
    { label: 'Processing', value: 'Processing' },
    { label: 'Shipped', value: 'Shipped' },
    { label: 'For delivery', value: 'For delivery' },
    { label: 'Delivered', value: 'Delivered' },
    { label: 'Released', value: 'Released' },
    { label: 'Cancelled', value: 'Cancelled' },
    { label: 'Refunded', value: 'Refunded' },
]);

const orderTypes = ref<{
    label: string;
    value: string;
}[]>([
    { label: 'All', value: '' },
    { label: 'Pickup', value: 'Pickup' },
    { label: 'Delivery', value: 'Delivery' },
]);

const load = async () => {
    await loadService.get("admin/orders").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load orders");
        }
    });
}

const selectOrder = (event: Event, order: IOrder) => {
    selectedOrder.value = order;
    editElement.value.toggle(event);
}

const setNewOrder = (value: IOrder) => {
    selectedOrder.value = value;
    load();
}

const closePopup = (event: Event) => {
    editElement.value.toggle(event);
}

onMounted(() => {
    load();
});
</script>
