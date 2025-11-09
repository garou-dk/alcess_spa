<template>
    <DataTable
        :value="props.item.product_orders"
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
                    Total: {{ CurrencyUtil.formatCurrency(props.item.product_orders.reduce((total, item) => total + item.price * item.quantity, 0)) }}
                </p>
            </div>
        </template>
    </DataTable>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import CurrencyUtil from '@/utils/CurrencyUtil';
import UrlUtil from '@/utils/UrlUtil';

interface Props {
    item: IOrder;
}

const props = defineProps<Props>();
</script>