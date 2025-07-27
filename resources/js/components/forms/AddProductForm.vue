<template>
    <form @submit.prevent="handleSubmit()" class="p-4">
        <div class="flex flex-wrap">
            <div class="p-2 max-lg:w-full lg:w-1/3">
                <InputForm
                    :errors="errors.product_name"
                    tag="label"
                    label-name="Product Name*"
                    id="product_name"
                >
                    <InputText
                        v-model="form.product_name"
                        id="product_name"
                        type="text"
                        placeholder="Enter product name"
                        fluid
                        :invalid="errors.product_name.length > 0"
                    />
                </InputForm>
            </div>
            <div class="p-2 max-lg:w-full lg:w-1/3">
                <InputForm
                    :errors="errors.category_id"
                    tag="span"
                    label-name="Category*"
                    id="category_id"
                >
                    <Select
                        v-model="form.category_id"
                        label-id="category_id"
                        :options="categoryState.categories"
                        placeholder="Select Category"
                        fluid
                        :invalid="errors.category_id.length > 0"
                        option-label="category_name"
                        option-value="category_id"
                        :loading="
                            categoryState.loadCategoryService.request.loading
                        "
                        filter
                    />
                </InputForm>
            </div>
            <div class="p-2 max-lg:w-full lg:w-1/3">
                <InputForm
                    :errors="errors.unit_id"
                    tag="span"
                    label-name="Unit*"
                    id="unit_id"
                >
                    <Select
                        v-model="form.unit_id"
                        label-id="unit_id"
                        :options="unitState.units"
                        placeholder="Select Unit"
                        fluid
                        :invalid="errors.unit_id.length > 0"
                        option-label="unit_name"
                        option-value="unit_id"
                        :loading="unitState.loadUnitService.request.loading"
                        filter
                    />
                </InputForm>
            </div>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.description"
                tag="label"
                label-name="Description*"
                id="description"
            >
                <Textarea
                    v-model="form.description"
                    id="description"
                    placeholder="Enter product description"
                    fluid
                    :invalid="errors.description.length > 0"
                />
            </InputForm>
        </div>
        <div class="flex flex-wrap">
            <div class="p-2 max-lg:w-full lg:w-1/3">
                <InputForm
                    :errors="errors.product_price"
                    tag="label"
                    label-name="Product Price*"
                    id="product_price"
                >
                    <InputNumber
                        v-model="form.product_price"
                        input-id="product_price"
                        placeholder="Enter product price"
                        fluid
                        :invalid="errors.product_price.length > 0"
                        mode="currency"
                        currency="PHP"
                    />
                </InputForm>
            </div>
            <div class="p-2 max-lg:w-full lg:w-1/3">
                <InputForm
                    :errors="errors.product_quantity"
                    tag="label"
                    label-name="Product Quantity*"
                    id="product_quantity"
                >
                    <InputNumber
                        v-model="form.product_quantity"
                        input-id="product_quantity"
                        placeholder="Enter product quantity"
                        fluid
                        :invalid="errors.product_quantity.length > 0"
                    />
                </InputForm>
            </div>
            <div class="p-2 max-lg:w-full lg:w-1/3">
                <InputForm
                    :errors="errors.low_stock_threshold"
                    tag="label"
                    label-name="Low Stock Threshold*"
                    id="low_stock_threshold"
                >
                    <InputNumber
                        v-model="form.low_stock_threshold"
                        input-id="low_stock_threshold"
                        type="number"
                        placeholder="Enter low stock threshold"
                        fluid
                        :invalid="errors.low_stock_threshold.length > 0"
                    />
                </InputForm>
            </div>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.sku"
                tag="label"
                label-name="SKU"
                id="sku"
            >
                <InputText
                    v-model="form.sku"
                    id="sku"
                    type="text"
                    placeholder="Enter SKU"
                    fluid
                    :invalid="errors.sku.length > 0"
                />
            </InputForm>
        </div>
        <div class="flex flex-wrap">
            <div class="p-2 max-lg:w-full lg:w-1/2">
                <InputForm
                    :errors="errors.is_active"
                    tag="span"
                    label-name="Status*"
                    id="is_active"
                >
                    <Select
                        v-model="form.is_active"
                        label-id="is_active"
                        :options="[
                            { label: 'Active', value: 1 },
                            { label: 'Inactive', value: 0 },
                        ]"
                        placeholder="Select Status"
                        fluid
                        :invalid="errors.is_active.length > 0"
                        option-label="label"
                        option-value="value"
                    />
                </InputForm>
            </div>
            <div class="p-2 max-lg:w-full lg:w-1/2">
                <InputForm
                    :errors="errors.available_online"
                    tag="span"
                    label-name="Available Online*"
                    id="available_online"
                >
                    <Select
                        v-model="form.available_online"
                        label-id="available_online"
                        :options="[
                            { label: 'Yes', value: 1 },
                            { label: 'No', value: 0 },
                        ]"
                        placeholder="Select Availability"
                        fluid
                        :invalid="errors.available_online.length > 0"
                        option-label="label"
                        option-value="value"
                    />
                </InputForm>
            </div>
        </div>
        <div class="flex justify-center p-2">
            <Button
                type="submit"
                label="Save"
                icon="pi pi-save"
                class="primary-bg"
                :loading="submitService.request.loading"
            />
        </div>
    </form>
</template>
<script setup lang="ts">
import {
    AddProductFormErrorInterface,
    AddProductFormInterface,
    ProductInterface,
} from "@/interfaces/ProductInterface";
import { useCategoryStore } from "@/stores/CategoryState";
import { useUnitStore } from "@/stores/UnitState";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();
const emit = defineEmits(["cb"]);
const submitService = useAxiosUtil<AddProductFormInterface, ProductInterface>();
const categoryState = useCategoryStore();
const unitState = useUnitStore();

const form: AddProductFormInterface = reactive({
    product_name: null,
    description: null,
    category_id: null,
    unit_id: null,
    product_price: null,
    product_quantity: null,
    low_stock_threshold: null,
    is_active: null,
    sku: null,
    available_online: null,
});

const errors: AddProductFormErrorInterface = reactive({
    product_name: [],
    description: [],
    category_id: [],
    unit_id: [],
    product_price: [],
    product_quantity: [],
    low_stock_threshold: [],
    is_active: [],
    sku: [],
    available_online: [],
});

const clearError = () => {
    errors.product_name = [];
    errors.description = [];
    errors.category_id = [];
    errors.unit_id = [];
    errors.product_price = [];
    errors.product_quantity = [];
    errors.low_stock_threshold = [];
    errors.is_active = [];
    errors.sku = [];
    errors.available_online = [];
};

const validate = () => {
    clearError();

    if (!form.product_name) {
        errors.product_name.push("Product name is required.");
    }
    if (!form.description) {
        errors.description.push("Description is required.");
    }
    if (!form.category_id) {
        errors.category_id.push("Category is required.");
    }
    if (!form.unit_id) {
        errors.unit_id.push("Unit is required.");
    }
    if (!form.product_price) {
        errors.product_price.push("Product price is required.");
    }
    if (!form.product_quantity) {
        errors.product_quantity.push("Product quantity is required.");
    }
    if (!form.low_stock_threshold) {
        errors.low_stock_threshold.push("Low stock threshold is required.");
    }
    if (form.is_active === null) {
        errors.is_active.push("Product status is required.");
    }
    if (form.available_online === null) {
        errors.available_online.push("Availability is required.");
    }

    const hasErrors = [
        errors.product_name.length > 0,
        errors.description.length > 0,
        errors.category_id.length > 0,
        errors.unit_id.length > 0,
        errors.product_price.length > 0,
        errors.product_quantity.length > 0,
        errors.low_stock_threshold.length > 0,
        errors.is_active.length > 0,
        errors.sku.length > 0,
        errors.available_online.length > 0,
    ];

    return hasErrors.includes(true) ? false : form;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService.post("admin/products", data).then(() => {
            if (
                submitService.request.status === 200 &&
                submitService.request.data
            ) {
                toast.success(submitService.request.message);
                emit("cb", submitService.request.data);
            } else {
                toast.error(
                    submitService.request.message ?? "Please try again",
                );
                if (submitService.request.errors) {
                    Object.keys(submitService.request.errors).forEach((key) => {
                        errors[key] = submitService.request.errors[key];
                    });
                }
            }
        });
    } else {
        toast.error("Please fill in all required fields.");
    }
};
</script>
