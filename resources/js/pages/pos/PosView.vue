<template>
    <div>
        <div class="grid grid-cols-2 gap-4 px-7 py-5 md:grid-cols-3">
            <div class="rounded bg-white p-4 shadow">
                <p class="text-lg text-gray-500">Daily Sales</p>
                <p class="text-3xl font-bold text-blue-600">0</p>
            </div>
            <div class="rounded bg-white p-4 shadow">
                <p class="text-lg text-gray-500">Transaction Today</p>
                <p class="text-3xl font-bold text-blue-600">0</p>
            </div>
            <div class="rounded bg-white p-4 shadow">
                <p class="text-lg text-gray-500">Items in cart</p>
                <p class="text-3xl font-bold text-orange-500">{{ selectedProducts.products.length }}</p>
            </div>
        </div>
        <div class="flex flex-wrap px-7 py-5">
            <div class="w-full p-2 lg:w-3/5">
                <div class="rounded-lg bg-white p-4 shadow-md">
                    <form id="search-form" class="flex flex-wrap" @submit.prevent="searchProductSku()">
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
                                        placeholder="Enter SKU"
                                        fluid
                                        v-model="skuInput"
                                        autocomplete="off"
                                    />
                                </InputGroup>
                            </InputForm>
                        </div>
                    </form>

                    <hr class="mb-4" />

                    <div
                        id="results"
                    >
                        <DataTable
                            :value="selectedProducts.products"
                            show-gridlines
                            :loading="fetchProductSkuService.request.loading"
                            editMode="cell" 
                            @cell-edit-complete="onCellEditComplete"
                            size="large"
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
                            <Column field="product.sku" header="SKU" />
                            <Column field="product.price" header="Price">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.product.product_price) }}
                                </template>
                            </Column>
                            <Column field="quantity" header="Quantity" style="width: 7rem; max-width: 7rem;">
                                <template #editor="{ data, field }">
                                    <InputNumber size="small" style="width: 5rem;" v-model="data[field]" :min="1" :max="data.product.product_quantity" autofocus fluid @focus="e => (e.target as HTMLInputElement).select()" />
                                </template>
                            </Column>
                            <Column header="Total">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.product.product_price * data.quantity) }}
                                </template>
                            </Column>
                            <template #empty>
                                <p class="text-center">No products selected</p>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
            <div class="w-full p-2 lg:w-2/5">
                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div id="cart" class="flex h-full flex-col">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Cart</h3>
                            <span class="text-sm text-gray-500"
                                >Items:
                                <span id="cart-item-count">0</span></span
                            >
                        </div>

                        <div class="mb-2">
                            <InputForm
                                :errors="errors.payment_method"
                                id="payment-method"
                                label-name="Payment Method"
                                tag="label"
                                class="mb-2"
                            >
                                <Select
                                    :options="[
                                        { label: 'Cash', value: 'Cash' },
                                        { label: 'E-Wallet', value: 'E-wallet' },
                                        { label: 'Debit Card', value: 'Debit' },
                                        { label: 'Credit Card', value: 'Credit' },
                                    ]"
                                    placeholder="Select Payment Method"
                                    id="payment-method"
                                    option-label="label"
                                    option-value="value"
                                    v-model="form.payment_method"
                                    :invalid="errors.payment_method.length > 0"
                                />
                            </InputForm>
                            <p class="text-lg font-semibold">
                                Total: <span id="cart-total">
                                    {{ CurrencyUtil.formatCurrency(selectedProducts.products.reduce((total, product) => total + product.product.product_price * product.quantity, 0)) }}
                                </span>
                            </p>
                        </div>
                        <div class="mt-3 flex justify-center">
                            <Button
                                type="button"
                                label="Record Sale"
                                raised
                                icon="pi pi-save"
                                @click="handleSubmit()"
                                :loading="submitService.request.loading"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ISale } from '@/interfaces/ISale';
import { ProductInterface } from '@/interfaces/ProductInterface';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import UrlUtil from '@/utils/UrlUtil';
import { DataTableCellEditCompleteEvent } from 'primevue';
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

interface IForm {
    products: {
        product_id: number;
        quantity: number;
    }[];
    payment_method: string | null;
}

interface IFormError {
    payment_method: string[];
}

const form : IForm = reactive({
    products: [],
    payment_method: null,
});

const errors : IFormError = reactive({
    payment_method: [],
});

const selectedProducts : {
    products: {
        product: ProductInterface;
        quantity: number;
    }[]
} = reactive({
    products: [],
});

const toast = useToast();
const fetchProductSkuService = useAxiosUtil<null, ProductInterface>();
const skuInput = ref<string | null>(null);

const searchProductSku = async () => {
    const sku = skuInput.value;
    skuInput.value = null;
    if (sku) {
        await fetchProductSkuService.get("admin/products/sku-search/" + sku).then(() => {
            if (fetchProductSkuService.request.status === 200 && fetchProductSkuService.request.data) {
                const product = selectedProducts.products.find((product) => product.product.product_id === fetchProductSkuService.request.data.product_id);
                if (product) {
                    product.quantity += 1;
                    return;
                }
                selectedProducts.products.push({
                    product: fetchProductSkuService.request.data,
                    quantity: 1,
                });
            }
            else {
                toast.error(
                    fetchProductSkuService.request.message ??
                        "Failed to load product",
                );
            }
        });
    }
    else {
        toast.error("Please enter a valid SKU");
    }
};

const onCellEditComplete = (event: DataTableCellEditCompleteEvent) => {
    let { data, newValue, field } = event;

    if (field === 'quantity') {
        const product = selectedProducts.products.find((product) => product.product.product_id === data.product.product_id);
        if (product) {
            product.quantity = newValue ? newValue : product.quantity;
        }
    }
};

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearErrors();
    let noError = true;

    form.products = [];

    if (selectedProducts.products.length === 0) {
        noError = false;
        toast.error("Please select at least one product");
        return noError;
    }

    if (!form.payment_method) {
        noError = false;
        errors.payment_method.push("Payment method is required");
        toast.error("Please select a payment method");
        return noError;
    }
    
    selectedProducts.products.forEach((product) => {
        form.products.push({
            product_id: product.product.product_id,
            quantity: product.quantity
        });
    });

    return form;
};

const submitService = useAxiosUtil<IForm, ISale>();
const router = useRouter();

const handleSubmit = async () => {
    const validated = validate();
    if (validated) {
        await submitService.post("admin/sales", validated).then(() => {
            if (submitService.request.status === 200 && submitService.request.data) {
                clearErrors();
                toast.success(submitService.request.message);
                router.push({ name: "admin.pos.find", params: { id: submitService.request.data.sale_id } });
            }
            else {
                toast.error(submitService.request.message ?? "Please try again");
                if (submitService.request.errors) {
                    Object.keys(submitService.request.errors).forEach((key) => {
                        errors[key] = submitService.request.errors[key];
                    });
                }
            }
        });
    }
}

</script>
