<template>
    <BoxShadow>
        <div class="w-full">
            <div class="flex items-center justify-between p-3">
                <p class="font-semibold text-gray-700">
                    Low Stock Alert
                </p>
                <span class="rounded-full bg-red-100 px-2 py-1 text-xs text-red-600">
                    {{ lowStockCount }} items
                </span>
            </div>
            <div class="flex items-center">
                <p class="p-3 grow">Batch Number: {{ paginate.batch_id }}</p>
                <div>
                    <Button
                        type="button"
                        label="Add Product"
                        icon="pi pi-plus"
                        size="small"
                        @click="openProductModal()"
                    />
                </div>
            </div>
            <DataTable class="w-full" :value="paginate.data" :loading="loadProductService.request.loading"
                columnResizeMode="expand" editMode="cell" @cell-edit-complete="onCellEditComplete" size="large">          
                <Column field="product_name" header="Product Name">
                    <template #body="{ data }">
                        <div class="flex items-center">
                            <Avatar v-if="data.product_image" shape="circle" :image="UrlUtil.getBaseAppUrl(
                                `storage/images/product/${data.product_image}`,
                            )
                                " class="aspect-square!" />
                            <Avatar v-else shape="circle" icon="pi pi-camera" />
                            <div class="ml-2 shrink">
                                {{ data.product_name }}
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="category.category_name" header="Category" />
                <Column header="Quantity" field="product_quantity">
                    <template #body="{ data }">
                        <div class="flex items-center gap-2">
                            <span :class="[
                                'font-semibold',
                                data.low_stock_threshold >= data.product_quantity
                                    ? 'text-red-600'
                                    : 'text-orange-600',
                            ]">
                                {{ data.product_quantity }}
                            </span>
                            <i class="pi pi-exclamation-triangle text-orange-600" />
                        </div>
                    </template>
                </Column>
                <Column header="Refill" field="quantity">
                    <template #editor="{ data, field }">
                        <InputNumber size="small" style="width: 5rem;" v-model="data[field]" :min="0" autofocus fluid
                            @focus="e => (e.target as HTMLInputElement).select()" />
                    </template>
                </Column>
                <Column header="Action">
                    <template #body="{ data }">
                        <Button type="button" icon="pi pi-trash" severity="danger" size="small"
                            @click="removeProduct(data)" />
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-end">
                        <Button type="button" :label="`Generate ${toOrdinal(paginate.batch_id)} Batch`" icon="pi pi-plus"
                            size="small" :loading="submitService.request.loading" @click="handleSubmit()" />
                    </div>
                </template>
                <template #empty>
                    <p>No low stock items</p>
                </template>
            </DataTable>
        </div>
        <Dialog
            v-model:visible="findProductModal"
            modal
            header="Find Product"
            :style="{ width: '100vw', height: '100svh' }"
            class="p-dialog-maximized"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <DataTable
                class="w-full"
                :value="paginateProduct.data"
                :loading="loadService.request.loading"
                columnResizeMode="expand"
            >
                <template #header>
                    <form @submit.prevent="load()" class="flex w-full flex-wrap">
                        <div class="grow p-2">
                            <InputForm
                                :errors="errorsProduct.search"
                                tag="label"
                                label-name="Search"
                                id="search"
                            >
                                <InputText
                                    v-model="formProduct.search"
                                    id="search"
                                    type="text"
                                    placeholder="Search by name"
                                    fluid
                                    :invalid="errorsProduct.search.length > 0"
                                />
                            </InputForm>
                        </div>
                        <div class="p-2">
                            <InputForm
                                :errors="errorsProduct.category_id"
                                tag="span"
                                label-name="Category"
                                id="category_id"
                            >
                                <Select
                                    v-model="formProduct.category_id"
                                    label-id="category_id"
                                    :options="categoryState.categories"
                                    placeholder="Select Category"
                                    fluid
                                    :invalid="errorsProduct.category_id.length > 0"
                                    option-label="category_name"
                                    option-value="category_id"
                                    :loading="
                                        categoryState.loadCategoryService.request
                                            .loading
                                    "
                                    filter
                                    show-clear
                                />
                            </InputForm>
                        </div>
                        <div class="p-2">
                            <InputForm
                                :errors="errorsProduct.status"
                                tag="span"
                                label-name="Status"
                                id="status"
                            >
                                <Select
                                    v-model="formProduct.status"
                                    label-id="status"
                                    :options="[
                                        { label: 'Active', value: 1 },
                                        { label: 'Inactive', value: 0 },
                                    ]"
                                    placeholder="Select Status"
                                    fluid
                                    :invalid="errorsProduct.status.length > 0"
                                    show-clear
                                    option-label="label"
                                    option-value="value"
                                />
                            </InputForm>
                        </div>
                        <div class="flex items-end p-2">
                            <Button type="button" icon="pi pi-filter" label="Filter" />
                        </div>
                    </form>
                </template>
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
                            <Avatar v-else shape="circle" icon="pi pi-camera" />
                            <div class="ml-2 shrink">
                                {{ data.product_name }}
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="category.category_name" header="Category" />
                <Column field="product_quantity" header="Stock" />
                <Column header="Actions">
                    <template #body="{ data }">
                        <div class="flex gap-2">
                            <Button
                                type="button"
                                icon="pi pi-check"
                                rounded
                                @click="selectProduct(data)"
                            />
                        </div>
                    </template>
                </Column>
                <template #empty>
                    <p>No result found</p>
                </template>

                <template #footer>
                    <div
                        class="border-primary flex w-full items-center justify-between gap-4 rounded-full border bg-transparent px-2 py-1"
                    >
                        <Button
                            icon="pi pi-chevron-left"
                            rounded
                            text
                            @click="decrementPage()"
                            :disabled="
                                paginateProduct.current_page === 1 ||
                                loadService.request.loading
                            "
                        />
                        <div class="text-color font-medium">
                            <span v-if="paginateProduct.from && paginateProduct.to"
                                >Showing {{ paginateProduct.from }} to
                                {{ paginateProduct.to }} of {{ paginateProduct.total }}</span
                            >
                            <span v-else>No page results</span>
                        </div>
                        <Button
                            icon="pi pi-chevron-right"
                            rounded
                            text
                            @click="incrementPage()"
                            :disabled="
                                paginateProduct.last_page === paginateProduct.current_page ||
                                loadService.request.loading
                            "
                        />
                    </div>
                </template>
            </DataTable>
        </Dialog>
    </BoxShadow>
</template>
<script setup lang="ts">
import DataTableInterface from '@/interfaces/DataTableInterface';
import { IBatch } from '@/interfaces/IBatch';
import { ProductInterface, ProductSearchErrorInterface, ProductSearchInterface } from '@/interfaces/ProductInterface';
import { useCategoryStore } from '@/stores/CategoryState';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import { DataTableCellEditCompleteEvent } from 'primevue';
import { computed, onMounted, reactive, ref } from 'vue';
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

const findProductModal = ref(false);
const categoryState = useCategoryStore();


const paginate: {
    batch_id: number;
    data: BatchProduct[],
} = reactive({
    batch_id: 0,
    data: [],
});

const form: IForm = reactive({
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

const toOrdinal = (n: number) => {
    const rem10 = n % 10;
    const rem100 = n % 100;

    if (rem100 >= 11 && rem100 <= 13) return n + "th";
    if (rem10 === 1) return n + "st";
    if (rem10 === 2) return n + "nd";
    if (rem10 === 3) return n + "rd";

    return n + "th";
};

const paginateProduct: DataTableInterface<ProductInterface> = reactive({
    data: [],
    current_page: 0,
    from: 0,
    to: 0,
    total: 0,
    last_page: 0,
});

const formProduct: ProductSearchInterface = reactive({
    search: null,
    limit: 5,
    page: 1,
    category_id: null,
    status: null,
    available_online: null,
    low_stock: null,
});

const errorsProduct: ProductSearchErrorInterface = reactive({
    search: [],
    limit: [],
    page: [],
    category_id: [],
    status: [],
    available_online: [],
    low_stock: [],
});

const loadService = useAxiosUtil<
    ProductSearchInterface,
    DataTableInterface<ProductInterface>
>();

const clearErrorProduct = () => {
    Object.keys(errorsProduct).forEach((key) => {
        errorsProduct[key] = [];
    });
};

const validateProduct = () => {
    clearErrorProduct();
    if (!formProduct.limit) {
        errorsProduct.limit.push("Limit is required");
    }
    if (!formProduct.page) {
        errorsProduct.page.push("Page is required");
    }

    const hasErrors = [errorsProduct.limit.length > 0, errorsProduct.page.length > 0];
    return hasErrors.includes(true) ? false : formProduct;
};

const load = async (resetPage: boolean = false) => {
    if (resetPage) {
        formProduct.page = 1;
    }
    if (!formProduct.limit) {
        formProduct.limit = 5;
    }

    const data = validateProduct();

    if (data) {
        await loadService.get("admin/products", data).then(() => {
            if (
                loadService.request.status === 200 &&
                loadService.request.data
            ) {
                Object.keys(paginateProduct).forEach((key) => {
                    paginateProduct[key] = loadService.request.data[key];
                });
            } else {
                toast.error(loadService.request.message ?? "Please try again");
                if (loadService.request.errors) {
                    Object.keys(loadService.request.errors).forEach((key) => {
                        errorsProduct[key] = loadService.request.errors[key];
                    });
                }
            }
        });
    } else {
        toast.error("Please fill in the required fields.");
    }
};

const decrementPage = () => {
    if (paginateProduct.current_page > 1) {
        formProduct.page = paginateProduct.current_page - 1;
        load();
    }
};

const incrementPage = () => {
    if (paginateProduct.current_page < paginateProduct.last_page) {
        formProduct.page = paginateProduct.current_page + 1;
        load();
    }
};

const openProductModal = () => {
    load();
    findProductModal.value = true;
};

const selectProduct = (product: ProductInterface) => {
    paginate.data.push({
        ...product,
        quantity: 0,
    });
    findProductModal.value = false;
};

onMounted(() => {
    loadBatch();
});
</script>