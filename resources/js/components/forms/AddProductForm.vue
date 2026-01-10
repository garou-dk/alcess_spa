<template>
    <form @submit.prevent="handleSubmit()" :class="responsive.getResponsiveClasses({
        mobile: 'p-3 w-full',
        tablet: 'p-4 max-w-4xl mx-auto',
        desktop: 'p-6 max-w-7xl mx-auto'
    })">
        <!-- Header Section -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'mb-4',
            tablet: 'mb-6',
            desktop: 'mb-8'
        })">
            <h2 :class="responsive.getResponsiveClasses({
                mobile: 'text-lg font-bold text-gray-800 mb-2',
                tablet: 'text-xl font-bold text-gray-800 mb-2',
                desktop: 'text-2xl font-bold text-gray-800 mb-2'
            })">Product Information</h2>
            <p :class="responsive.getResponsiveClasses({
                mobile: 'text-xs text-gray-600',
                tablet: 'text-sm text-gray-600',
                desktop: 'text-sm text-gray-600'
            })">Fill in the details below to add a new product</p>
        </div>

        <!-- Basic Information Card -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-4',
            tablet: 'bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6',
            desktop: 'bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6'
        })">
            <h3 :class="responsive.getResponsiveClasses({
                mobile: 'text-base font-semibold text-gray-800 mb-3 flex items-center',
                tablet: 'text-lg font-semibold text-gray-800 mb-4 flex items-center',
                desktop: 'text-lg font-semibold text-gray-800 mb-4 flex items-center'
            })">
                <i class="pi pi-info-circle mr-2 text-blue-600"></i>
                Basic Information
            </h3>
            <div :class="responsive.getResponsiveClasses({
                mobile: 'space-y-4',
                tablet: 'flex flex-wrap -mx-2',
                desktop: 'flex flex-wrap -mx-2'
            })">
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'w-full',
                    tablet: 'p-2 max-lg:w-full lg:w-1/3',
                    desktop: 'p-2 max-lg:w-full lg:w-1/3'
                })">
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
                            class="transition-all duration-200"
                        />
                    </InputForm>
                </div>
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'w-full',
                    tablet: 'p-2 max-lg:w-full lg:w-1/3',
                    desktop: 'p-2 max-lg:w-full lg:w-1/3'
                })">
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
                            :loading="categoryState.loadCategoryService.request.loading"
                            filter
                            class="transition-all duration-200"
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
                            class="transition-all duration-200"
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
                        rows="4"
                        class="transition-all duration-200"
                    />
                </InputForm>
            </div>
            <div class="p-2">
                <InputForm
                    :errors="errors.sku"
                    tag="label"
                    label-name="SKU (Stock Keeping Unit)"
                    id="sku"
                >
                    <InputText
                        v-model="form.sku"
                        id="sku"
                        type="text"
                        placeholder="Enter SKU (Numbers only)"
                        fluid
                        class="transition-all duration-200"
                        @keydown="validateSkuInput"
                        @paste="handleSkuPaste"
                    />
                </InputForm>
            </div>
        </div>

        <!-- Pricing & Inventory Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="pi pi-dollar mr-2 text-green-600"></i>
                Pricing & Inventory
            </h3>
            <div class="flex flex-wrap -mx-2">
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
                            placeholder="0.00"
                            fluid
                            :invalid="errors.product_price.length > 0"
                            mode="currency"
                            currency="PHP"
                            class="transition-all duration-200"
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
                            placeholder="0"
                            fluid
                            :invalid="errors.product_quantity.length > 0"
                            class="transition-all duration-200"
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
                            placeholder="0"
                            fluid
                            :invalid="errors.low_stock_threshold.length > 0"
                            class="transition-all duration-200"
                        />
                    </InputForm>
                </div>
            </div>
        </div>

        <!-- Settings Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="pi pi-cog mr-2 text-purple-600"></i>
                Product Settings
            </h3>
            <div class="flex flex-wrap -mx-2">
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
                            class="transition-all duration-200"
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
                            class="transition-all duration-200"
                        />
                    </InputForm>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 p-2 bg-gray-50 rounded-lg border border-gray-200">
            <Button
                type="submit"
                label="Next"
                icon="pi pi-arrow-right"
                iconPos="right"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
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
import { useResponsive } from "@/composables/useResponsive";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive, watch, ref } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();
const responsive = useResponsive();
const emit = defineEmits(["cb", "next"]);
const submitService = useAxiosUtil<AddProductFormInterface, ProductInterface>();
const categoryState = useCategoryStore();
const unitState = useUnitStore();

// Debounce timers
const productNameDebounce = ref<number | null>(null);
const skuDebounce = ref<number | null>(null);

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

const validateSkuInput = (event: KeyboardEvent) => {
    // Allow: backspace, delete, tab, escape, enter
    if ([8, 9, 27, 13, 46].indexOf(event.keyCode) !== -1 ||
        // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
        (event.keyCode === 65 && event.ctrlKey === true) ||
        (event.keyCode === 67 && event.ctrlKey === true) ||
        (event.keyCode === 86 && event.ctrlKey === true) ||
        (event.keyCode === 88 && event.ctrlKey === true)) {
        return;
    }
    
    // Ensure that it is a number and stop the keypress if not
    if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
        event.preventDefault();
        errors.sku = ["SKU must contain only numbers."];
        setTimeout(() => {
            errors.sku = [];
        }, 2000);
    }
};

const handleSkuPaste = (event: ClipboardEvent) => {
    event.preventDefault();
    const pastedText = event.clipboardData?.getData('text') || '';
    const numericOnly = pastedText.replace(/[^0-9]/g, '');
    
    if (numericOnly) {
        form.sku = numericOnly;
    }
    
    if (pastedText !== numericOnly) {
        errors.sku = ["SKU must contain only numbers."];
        setTimeout(() => {
            errors.sku = [];
        }, 2000);
    }
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
    // Validate SKU contains only numbers if provided
    if (form.sku && !/^\d+$/.test(form.sku)) {
        errors.sku.push("SKU must contain only numbers.");
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

// Real-time validation for product name
const validateProductNameRealtime = async () => {
    if (!form.product_name || form.product_name.trim() === '') {
        const categoryError = 'The product name cannot be the same as any category name.';
        const unitError = 'The product name cannot be the same as any unit name.';
        errors.product_name = errors.product_name.filter(
            err => err === categoryError || err === unitError
        );
        return;
    }

    const validateService = useAxiosUtil<{ product_name: string; category_id: number | null; unit_id: number | null }, null>();
    await validateService.post("admin/products/validate-product-name", {
        product_name: form.product_name,
        category_id: form.category_id,
        unit_id: form.unit_id
    }).then(() => {
        const categoryError = 'The product name cannot be the same as any category name.';
        const unitError = 'The product name cannot be the same as any unit name.';
        
        // Preserve frontend validation errors
        const frontendErrors = errors.product_name.filter(
            err => err === categoryError || err === unitError
        );
        
        if (validateService.request.status === 200) {
            // Only clear API-related errors, keep frontend errors
            errors.product_name = frontendErrors;
        } else {
            if (validateService.request.errors?.product_name) {
                // Combine frontend errors with API errors
                errors.product_name = [
                    ...frontendErrors,
                    ...validateService.request.errors.product_name
                ];
            }
        }
    });
};

// Real-time validation for SKU
const validateSkuRealtime = async () => {
    if (!form.sku || form.sku.trim() === '') {
        errors.sku = [];
        return;
    }

    // Check numeric format first
    if (!/^\d+$/.test(form.sku)) {
        errors.sku = ["SKU must contain only numbers."];
        return;
    }

    const validateService = useAxiosUtil<{ sku: string }, null>();
    await validateService.post("admin/products/validate-sku", {
        sku: form.sku
    }).then(() => {
        if (validateService.request.status === 200) {
            errors.sku = [];
        } else {
            if (validateService.request.errors?.sku) {
                errors.sku = validateService.request.errors.sku;
            }
        }
    });
};

// Immediate frontend validation for product name vs category and unit
const validateProductNameVsCategoryAndUnit = () => {
    if (!form.product_name) {
        return;
    }
    
    const productNameLower = form.product_name.trim().toLowerCase();
    const categoryError = 'The product name cannot be the same as any category name.';
    const unitError = 'The product name cannot be the same as any unit name.';
    
    // Remove previous category/unit errors
    errors.product_name = errors.product_name.filter(
        err => err !== categoryError && err !== unitError
    );
    
    // Check against ALL categories (not just selected one)
    const matchingCategory = categoryState.categories.find(
        cat => cat.category_name.toLowerCase() === productNameLower
    );
    
    if (matchingCategory) {
        errors.product_name.push(categoryError);
    }
    
    // Check against ALL units (not just selected one)
    const matchingUnit = unitState.units.find(
        unit => unit.unit_name.toLowerCase() === productNameLower
    );
    
    if (matchingUnit) {
        errors.product_name.push(unitError);
    }
};

// Watch product name changes
watch(
    () => form.product_name,
    (newValue) => {
        // Immediate validation for category and unit name match
        validateProductNameVsCategoryAndUnit();
        
        if (productNameDebounce.value) {
            clearTimeout(productNameDebounce.value);
        }
        
        if (newValue && newValue.trim() !== '') {
            productNameDebounce.value = window.setTimeout(() => {
                validateProductNameRealtime();
            }, 150);
        } else {
            // Clear API-related errors when field is empty, keep frontend errors
            const categoryError = 'The product name cannot be the same as any category name.';
            const unitError = 'The product name cannot be the same as any unit name.';
            errors.product_name = errors.product_name.filter(
                err => err === categoryError || err === unitError
            );
        }
    }
);

// Watch category changes (re-validate product name against new category)
watch(
    () => form.category_id,
    (newValue) => {
        // Immediate validation when category changes
        validateProductNameVsCategoryAndUnit();
        
        if (form.product_name && form.product_name.trim() !== '') {
            if (productNameDebounce.value) {
                clearTimeout(productNameDebounce.value);
            }
            productNameDebounce.value = window.setTimeout(() => {
                validateProductNameRealtime();
            }, 150);
        }
    }
);

// Watch unit changes (re-validate product name against new unit)
watch(
    () => form.unit_id,
    (newValue) => {
        // Immediate validation when unit changes
        validateProductNameVsCategoryAndUnit();
        
        if (form.product_name && form.product_name.trim() !== '') {
            if (productNameDebounce.value) {
                clearTimeout(productNameDebounce.value);
            }
            productNameDebounce.value = window.setTimeout(() => {
                validateProductNameRealtime();
            }, 150);
        }
    }
);

// Watch SKU changes
watch(
    () => form.sku,
    (newValue) => {
        if (skuDebounce.value) {
            clearTimeout(skuDebounce.value);
        }
        
        if (newValue && newValue.trim() !== '') {
            skuDebounce.value = window.setTimeout(() => {
                validateSkuRealtime();
            }, 150);
        } else {
            errors.sku = [];
        }
    }
);

const handleSubmit = async () => {
    const validateService = useAxiosUtil<AddProductFormInterface, null>();
    const data = validate();
    if (data) {
        // Validate with backend to check for duplicates
        await validateService.post("admin/products/validate-info", data).then(() => {
            if (validateService.request.status === 200) {
                toast.success("Product information validated. Continue to add images.");
                // Emit the form data without saving to database
                emit("next", data);
            } else {
                toast.error(validateService.request.message ?? "Validation failed");
                if (validateService.request.errors) {
                    Object.keys(validateService.request.errors).forEach((key) => {
                        if (key in errors) {
                            errors[key] = validateService.request.errors[key];
                        }
                    });
                }
            }
        });
    } else {
        toast.error("Please correct all errors before submitting.");
    }
};
</script>