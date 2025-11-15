<template>
    <BoxShadow class="mb-5">
        <div class="w-full">
            <div class="flex items-center justify-between p-3">
                <h1 class="text-xl font-semibold text-gray-700">
                    Best Selling Products
                </h1>
            </div>
            <DataTable
                class="w-full"
                :value="data"
                :loading="loadService.request.loading"
                columnResizeMode="expand"
            >
                <Column field="product_name" header="Product Name">
                    <template #body="{ data }">
                        <div class="flex items-center">
                            <Avatar
                                v-if="data.product_image"
                                shape="circle"
                                :image="
                                    UrlUtil.getBaseAppUrl(
                                        `storage/images/product/${data.product_image}`,
                                    )
                                "
                                class="aspect-square!"
                            />
                            <Avatar
                                v-else
                                shape="circle"
                                icon="pi pi-camera"
                            />
                            <div class="ml-2 shrink">
                                {{ data.product_name }}
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="category.category_name" header="Category" />
                <Column field="units_sold" header="Units Sold" />
                <template #empty>
                    <p>No result found</p>
                </template>
            </DataTable>
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import UrlUtil from '@/utils/UrlUtil';
import { onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';

export interface TopSellingProductInterface {
    product_id: number;
    product_name: string;
    product_image: string | null;
    units_sold: number;
    unit: {
        unit_id: number;
        unit_name: string;
        deleted_at: string | null;
        created_at: string;
        updated_at: string;
    };
    category: {
        category_id: number;
        category_name: string;
        category_slug: string;
        category_image: string | null;
        deleted_at: string | null;
        editable: number;
        created_at: string;
        updated_at: string;
    };
}

const loadService = useAxiosUtil<null, TopSellingProductInterface[]>();
const toast = useToast();
const data = ref<TopSellingProductInterface[]>([]);

const load = async () => {
    await loadService.get('admin/dashboard/top-sellers').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        }
        else {
            toast.error(loadService.request.message ?? 'Failed to load data');
        }
    });
}

onMounted(() => {
    load();
});
</script>