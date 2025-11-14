<template>
    <BoxShadow>
        <div class="w-full">
            <div class="flex items-center justify-between p-3">
                <p class="font-semibold text-gray-700">
                    Low Stock Alert
                </p>
                <span
                    class="rounded-full bg-red-100 px-2 py-1 text-xs text-red-600"
                >
                    {{ lowStockCount }} items
                </span>
            </div>
            <DataTable
                class="w-full"
                :value="paginate.data"
                :loading="loadProductService.request.loading"
                columnResizeMode="expand"
                editMode="cell" 
                @cell-edit-complete="onCellEditComplete"
                size="large"
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
                <Column
                    field="category.category_name"
                    header="Category"
                />
                <Column header="Quantity" field="product_quantity">
                    <template #body="{ data }">
                        <div class="flex items-center gap-2">
                            <span
                                :class="[
                                    'font-semibold',
                                    data.low_stock_threshold >= data.product_quantity
                                        ? 'text-red-600'
                                        : 'text-orange-600',
                                ]"
                            >
                                {{ data.product_quantity }}
                            </span>
                            <i
                                class="pi pi-exclamation-triangle text-orange-600"
                            />
                        </div>
                    </template>
                </Column>
                <Column
                    header="Refill"
                    field="quantity"
                >
                    <template #editor="{ data, field }">
                        <InputNumber size="small" style="width: 5rem;" v-model="data[field]" :min="0" autofocus fluid @focus="e => (e.target as HTMLInputElement).select()" />
                    </template>
                </Column>
                <Column header="Action">
                    <template #body="{ data }">
                        <Button type="button" icon="pi pi-trash" severity="danger" size="small" @click="removeProduct(data)" />
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-end">
                        <Button
                            type="button"
                            label="Create Batch"
                            icon="pi pi-plus"
                            size="small"
                            :loading="submitService.request.loading"
                            @click="handleSubmit()"
                        />
                    </div>
                </template>
                <template #empty>
                    <p>No low stock items</p>
                </template>
            </DataTable>
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
import { IBatch } from '@/interfaces/IBatch';
import { ProductInterface } from '@/interfaces/ProductInterface';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import { DataTableCellEditCompleteEvent } from 'primevue';
import { computed, onMounted, reactive } from 'vue';
import { useToast } from 'vue-toastification';

interface IForm {
    batch_products: {
        product_id: number;
        quantity: number;
    }[]
}

interface BatchProduct extends ProductInterface {
    quantity: number;
}

const paginate : {
    batch_id: number;
    data: BatchProduct[],
} = reactive({
    batch_id: 0,
    data: [],
});

const form : IForm = reactive({
    batch_products: []
});

const loadProductService = useAxiosUtil<null, IBatch>();
const lowStockCount = computed(() => paginate.data.length);

const loadBatch = async () => {
    await loadProductService.get("admin/batches").then(() => {
        if (
            loadProductService.request.status === 200 &&
            loadProductService.request.data
        ) {
            paginate.batch_id = loadProductService.request.data.batch_id;
            paginate.data = loadProductService.request.data.batch_products.map((product: ProductInterface) => {
                return {
                    ...product,
                    quantity: 0,
                };
            });
        }
    });
};

const removeProduct = async (product: BatchProduct) => {
    const index = paginate.data.findIndex((item) => item.product_id === product.product_id);
    if (index !== -1) {
        paginate.data.splice(index, 1);
    }
};

const onCellEditComplete = (event: DataTableCellEditCompleteEvent) => {
    let { data, newValue, field } = event;

    if (field === 'quantity') {
        const product = paginate.data.find((product) => product.product_id === data.product_id);
        product.quantity = newValue ? newValue : 0;
    }
};

const submitService = useAxiosUtil<IForm, null>();
const toast = useToast();

const validate = () => {
    form.batch_products = [];
    paginate.data.forEach((product: BatchProduct) => {
        if (product.quantity > 0) {
            form.batch_products.push({
                product_id: product.product_id,
                quantity: product.quantity
            }) 
        }
    });
    if (form.batch_products.length === 0) {
        toast.error("Please select at least one product");
        return false;
    }
    return form;
}

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService.post(`admin/batches`, form).then(() => {
            if (submitService.request.status === 200) {
                loadBatch();
                toast.success(submitService.request.message ?? "Batch Created Successfully.");
            }
            else {
                toast.error(submitService.request.message ?? "Please try again.");
            }
        });
    }
};

onMounted(() => {
    loadBatch();
});
</script>