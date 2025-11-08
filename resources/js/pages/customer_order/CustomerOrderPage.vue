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
                        </div>
                        <DataTable
                            :value="item.product_orders"
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
                                    <p class="font-semibold">
                                        Total: {{ CurrencyUtil.formatCurrency(item.product_orders.reduce((total, item) => total + item.price * item.quantity, 0)) }}
                                    </p>
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
    </div>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
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
]);

const orderStatus = (value: string) => {
    return {
        Pending: 'bg-yellow-200',
        Confirmed: 'bg-green-200',
        Processing: 'bg-blue-200',
        Shipped: 'bg-purple-200',
        "For delivery": 'bg-orange-200',
        Delivered: 'bg-green-600',
        Released: 'bg-blue-600',
        Cancelled: 'bg-red-200',
        Refunded: 'bg-red-600',
    }[value];
}

const load = async () => {
    await loadService.get("customer/orders").then(() => {
        if (loadService.request.status == 200 && loadService.request.data) {
            data.value = loadService.request.data;
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

onMounted(() => {
    load();
});
</script>