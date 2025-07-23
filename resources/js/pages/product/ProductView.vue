<template>
    <div>
        <div class="flex flex-wrap justify-center mb-2">
            <div class="p-2 grow">
                <BoxShadow>
                    <div class="p-4 text-2xl font-bold">
                        <h1 class="mb-4 text-gray-500">Total Product</h1>
                        <p class="ps-2 text-green-500">0</p>
                    </div>
                </BoxShadow>
            </div>
            <div class="p-2 grow">
                <BoxShadow>
                    <div class="p-4 text-2xl font-bold">
                        <h1 class="mb-4 text-gray-500">Nearly Out of Stock</h1>
                        <p class="ps-2 text-yellow-500">0</p>
                    </div>
                </BoxShadow>
            </div>
            <div class="p-2 grow">
                <BoxShadow>
                    <div class="p-4 text-2xl font-bold">
                        <h1 class="mb-4 text-gray-500">Active Product</h1>
                        <p class="ps-2 text-pink-500">0</p>
                    </div>
                </BoxShadow>
            </div>
            <div class="p-2 grow">
                <BoxShadow>
                    <div class="p-4 text-2xl font-bold">
                        <h1 class="mb-4 text-gray-500">Out of Stock</h1>
                        <p class="ps-2 text-red-500">0</p>
                    </div>
                </BoxShadow>
            </div>
        </div>
        <BoxShadow class="mb-4">
            <form @submit.prevent="load()" class="flex flex-wrap w-full">
                <div class="p-2 grow">
                    <InputForm :errors="errors.search" tag="label" label-name="Search" id="search">
                        <InputText v-model="form.search" id="search" type="text" placeholder="Search by name" fluid
                            :invalid="errors.search.length > 0" />
                    </InputForm>
                </div>
                <div class="p-2">
                    <InputForm :errors="errors.category_id" tag="span" label-name="Category" id="category_id">
                        <Select v-model="form.category_id" label-id="category_id" :options="categoryState.categories"
                            placeholder="Select Category" fluid :invalid="errors.category_id.length > 0"
                            option-label="category_name" option-value="category_id"
                            :loading="categoryState.loadCategoryService.request.loading" filter show-clear />
                    </InputForm>
                </div>
                <div class="p-2">
                    <InputForm :errors="errors.status" tag="span" label-name="Status" id="status">
                        <Select v-model="form.status" label-id="status" :options="[
                            { label: 'Active', value: 1 },
                            { label: 'Inactive', value: 0 }
                        ]" placeholder="Select Status" fluid :invalid="errors.status.length > 0" show-clear
                            option-label="label" option-value="value" />
                    </InputForm>
                </div>
                <div class="p-2 flex items-end">
                    <Button type="button" icon="pi pi-filter" label="Filter" />
                </div>
                <div class="p-2 flex items-end">
                    <Button type="button" icon="pi pi-plus" label="Add Product" @click="addProduct()" />
                </div>
            </form>
        </BoxShadow>
        <BoxShadow>
            <DataTable class="w-full" :value="paginate.data" :loading="loadService.request.loading" columnResizeMode="expand">
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
                <Column field="product_quantity" header="Stock" />
                <Column field="product_price" header="Price" />
                <Column header="Actions">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" rounded @click="editProduct(data)" />
                    </template>
                </Column>
                <template #empty>
                    <p>No result found</p>
                </template>

                <template #footer>
                    <div
                        class="border-primary flex w-full items-center justify-between gap-4 rounded-full border bg-transparent px-2 py-1">
                        <Button icon="pi pi-chevron-left" rounded text @click="decrementPage()" :disabled="paginate.current_page === 1 ||
                            loadService.request.loading
                            " />
                        <div class="text-color font-medium">
                            <span v-if="paginate.from && paginate.to">Showing {{ paginate.from }} to
                                {{ paginate.to }} of
                                {{ paginate.total }}</span>
                            <span v-else>No page results</span>
                        </div>
                        <Button icon="pi pi-chevron-right" rounded text @click="incrementPage()" :disabled="paginate.last_page ===
                            paginate.current_page ||
                            loadService.request.loading
                            " />
                    </div>
                </template>
            </DataTable>
        </BoxShadow>
        <Dialog
            v-model:visible="showProductDialog.visible"
            modal
            header="Product Details"
            :style="{ width: '100vw', height: '100svh' }"
            class="p-dialog-maximized"
        >
            <ProductForm
                :data="showProductDialog.selectedItem"
                @cb="load"
            />
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import ProductForm from '@/components/forms/ProductForm.vue';
import DataTableInterface from '@/interfaces/DataTableInterface';
import { ProductInterface, ProductSearchErrorInterface, ProductSearchInterface } from '@/interfaces/ProductInterface';
import { useCategoryStore } from '@/stores/CategoryState';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import { onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';

const categoryState = useCategoryStore();
const loadService = useAxiosUtil<ProductSearchInterface, DataTableInterface<ProductInterface>>();
const toast = useToast();
const showProductDialog : { 
    visible: boolean;
    selectedItem: ProductInterface | null;
} = reactive({
    visible: false,
    selectedItem: null,
});

const form: ProductSearchInterface = reactive({
    search: null,
    limit: 5,
    page: 1,
    category_id: null,
    status: null,
    available_online: null,
    low_stock: null,
});

const errors: ProductSearchErrorInterface = reactive({
    search: [],
    limit: [],
    page: [],
    category_id: [],
    status: [],
    available_online: [],
    low_stock: [],
});

const paginate: DataTableInterface<ProductInterface> = reactive({
    data: [],
    current_page: 0,
    from: 0,
    to: 0,
    total: 0,
    last_page: 0,
});

const addProduct = () => {
    showProductDialog.selectedItem = null;
    showProductDialog.visible = true;
};

const editProduct = (data: ProductInterface) => {
    showProductDialog.selectedItem = data;
    showProductDialog.visible = true;
};

const clearError = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearError();
    if (!form.limit) {
        errors.limit.push("Limit is required");
    }
    if (!form.page) {
        errors.page.push("Page is required");
    }

    const hasErrors = [errors.limit.length > 0, errors.page.length > 0];
    return hasErrors.includes(true) ? false : form;
};

const load = async (resetPage: boolean = false) => {
    if (resetPage) {
        form.page = 1;
    }
    if (!form.limit) {
        form.limit = 5;
    }

    const data = validate();

    if (data) {
        await loadService.get("admin/products", data).then(() => {
            if (
                loadService.request.status === 200 &&
                loadService.request.data
            ) {
                Object.keys(paginate).forEach((key) => {
                    paginate[key] = loadService.request.data[key];
                });
            } else {
                toast.error(loadService.request.message ?? "Please try again");
                if (loadService.request.errors) {
                    Object.keys(loadService.request.errors).forEach((key) => {
                        errors[key] = loadService.request.errors[key];
                    });
                }
            }
        });
    } else {
        toast.error("Please fill in the required fields.");
    }
};

const incrementPage = () => {
    if (paginate.current_page < paginate.last_page) {
        form.page = paginate.current_page + 1;
        load();
    }
};

const decrementPage = () => {
    if (paginate.current_page > 1) {
        form.page = paginate.current_page - 1;
        load();
    }
};

onMounted(() => {
    load();
});
</script>