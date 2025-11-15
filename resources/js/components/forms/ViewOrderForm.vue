<template>
    <div>
        <div class="flex flex-col">
            <div class="p-2 border">
                <div 
                    class="flex flex-wrap rounded"
                    :class="orderStatus(props.data.status)"
                >
                    <div class="p-2">
                        <div>
                            <p class="font-semibold">Order ID</p>
                        </div>
                        <div>
                            <p>{{ props.data.order_public_id }}</p>
                        </div>
                    </div>
                    <div class="p-2">
                        <div>
                            <p class="font-semibold">Order Type</p>
                        </div>
                        <div>
                            <p>
                                <i 
                                    :class="props.data.order_type == 'Pickup' ? 'pi pi-map-marker' : 'pi pi-truck'"
                                />
                                {{ props.data.order_type }}
                            </p>
                        </div>
                    </div>
                    <div class="p-2">
                        <div>
                            <p class="font-semibold">Order Date</p>
                        </div>
                        <div>
                            <p>{{ DateUtil.formatToMonthDayYear(props.data.created_at) }}</p>
                        </div>
                    </div>
                    <div class="p-2">
                        <div>
                            <p class="font-semibold">Order Status</p>
                        </div>
                        <div>
                            <p>{{ props.data.status }}</p>
                        </div>
                    </div>
                    <div v-if="props.data.order_type === 'Delivery'" class="p-2">
                        <div>
                            <p class="font-semibold">Address</p>
                        </div>
                        <div>
                            <p>{{ showCompleteAddress(props.data) }}</p>
                        </div>
                    </div>
                    <div v-if="props.data.delivery_courier" class="p-2">
                        <div>
                            <p class="font-semibold">Delivery Courier</p>
                        </div>
                        <div>
                            <p>{{ props.data.delivery_courier }}</p>
                        </div>
                    </div>
                    <div v-if="props.data.tracking_number" class="p-2">
                        <div>
                            <p class="font-semibold">Tracking Number</p>
                        </div>
                        <div>
                            <p>{{ props.data.tracking_number }}</p>
                        </div>
                    </div>
                        <div v-if="props.data.estimated_delivery_date_start && props.data.estimated_delivery_date_end" class="p-2">
                        <div>
                            <p class="font-semibold">Estimated Delivery Date</p>
                        </div>
                        <div>
                            <p>{{ DateUtil.formatToMonthDayYear(props.data.estimated_delivery_date_start) }} - {{ DateUtil.formatToMonthDayYear(props.data.estimated_delivery_date_end) }}</p>
                        </div>
                    </div>
                </div>
                <DataTable
                    :value="props.data.product_orders"
                    show-gridlines
                    class="mt-2"
                >
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
                    <template #footer>
                        <div class="flex justify-end">
                            <div>
                                <p v-if="props.data.order_type === 'Delivery'" class="font-semibold">
                                    Delivery Fee: {{ props.data.shipping_fee !== null ? CurrencyUtil.formatCurrency(props.data.shipping_fee) : 'TBD' }}
                                </p>
                                <p class="font-semibold">
                                    Total: {{ CurrencyUtil.formatCurrency(props.data.product_orders.reduce((total, item) => total + item.price * item.quantity, 0) + (props.data.shipping_fee ?? 0)) }}
                                </p>
                            </div>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';

interface Props {
    data: IOrder;
}
const props = defineProps<Props>();

const orderStatus = (value: string) => {
    return {
        Pending: 'bg-yellow-200',
        Confirmed: 'bg-green-200',
        Processing: 'bg-blue-200',
        Shipped: 'bg-purple-200',
        Delivered: 'bg-green-600',
    }[value];
}

const showCompleteAddress = (value: IOrder) => {
    return `${value.other_details}, ${value.barangay.barangay_name}, ${value.barangay.municity.municity_name}, ${value.barangay.municity.province.province_name}, ${value.barangay.municity.province.region.region_name}, ${value.barangay.municity.province.region.island_group.island_group_name}, ${value.postal_code}`
}
</script>