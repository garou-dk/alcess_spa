<template>
    <div>
        <BoxShadow class="p-5">
            <div class="w-full">
                <div v-if="!loadService.request.loading">
                    <div v-if="data">
                        <DataTable
                            :value="data.sale_items"
                            show-gridlines
                        >
                            <template #header>
                                <div class="flex flex-wrap justify-around">
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Purchase Code</p>
                                        <p>{{ data.sale_code }}</p>
                                    </div>
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Date Created</p>
                                        <p>{{ DateUtil.formatToMonthDayYear(data.created_at) }}</p>
                                    </div>
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Payment Method</p>
                                        <p>{{ data.payment_method }}</p>
                                    </div>
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Recorded By</p>
                                        <p>{{ data.user.full_name }}</p>
                                    </div>
                                    <div class="flex items-center justify-center">
                                        <Button
                                            type="button"
                                            label="Print"
                                            icon="pi pi-print"
                                            class="primary-bg"
                                        />
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
                            <Column field="price" header="Price">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.price) }}
                                </template>
                            </Column>
                            <Column field="quantity" header="Quantity" />
                            <Column field="total" header="Total">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.price * data.quantity) }}
                                </template>
                            </Column>
                            <template #footer>
                                <p class="text-end">
                                    <span class="font-semibold">Total:</span> {{ CurrencyUtil.formatCurrency(data.total_amount) }}
                                </p>
                            </template>
                        </DataTable>
                    </div>
                    <div v-else class="flex justify-center p-5">
                        <p>No sales found</p>
                    </div>
                </div>
                <div v-else class="flex justify-center p-5">
                    <PageLoader />
                </div>
            </div>
        </BoxShadow>
    </div>
</template>
<script setup lang="ts">
import { ISale } from '@/interfaces/ISale';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

const route = useRoute();
const router = useRouter();
const loadService = useAxiosUtil<null, ISale>();
const toast = useToast();
const data = ref<ISale | null>(null);

const load = async () => {
    await loadService.get(`admin/sales/${route.params["id"]}`).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load sales");
            router.push({ name: "admin.pos.index" });
        }
    });
};

onMounted(() => {
    load();
});
</script>