<template>
    <Dialog
        :visible="visible"
        @update:visible="handleVisibilityChange"
        modal
        dismissableMask
        :header="`Add Stock - ${product?.product_name || ''}`"
        :style="responsive.getResponsiveClasses({
            mobile: '{ width: \'95vw\', maxWidth: \'100%\' }',
            tablet: '{ width: \'85vw\', maxWidth: \'500px\' }',
            desktop: '{ width: \'28rem\' }'
        })"
        :breakpoints="{ 
            '960px': '90vw',
            '640px': '95vw'
        }"
        class="modal-blue-theme"
        :pt="{
            root: { class: '!rounded-xl' },
            header: { 
                class: responsive.getResponsiveClasses({
                    mobile: '!bg-blue-600 !text-white !rounded-t-xl !px-3 !py-3',
                    tablet: '!bg-blue-600 !text-white !rounded-t-xl !px-4 !py-3',
                    desktop: '!bg-blue-600 !text-white !rounded-t-xl'
                })
            },
            closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
            closeButtonIcon: { class: '!text-white' },
            content: {
                class: responsive.getResponsiveClasses({
                    mobile: '!p-3',
                    tablet: '!p-4',
                    desktop: '!p-6'
                })
            }
        }"
    >
        <form @submit.prevent="handleSubmit" :class="responsive.getResponsiveClasses({
            mobile: 'space-y-3',
            tablet: 'space-y-4',
            desktop: 'space-y-4'
        })">
            <!-- Current Stock Display -->
            <div :class="responsive.getResponsiveClasses({
                mobile: 'rounded-lg border border-gray-200 bg-gray-50 p-3',
                tablet: 'rounded-lg border border-gray-200 bg-gray-50 p-4',
                desktop: 'rounded-lg border border-gray-200 bg-gray-50 p-4'
            })">
                <label :class="responsive.getResponsiveClasses({
                    mobile: 'mb-2 block text-xs font-medium text-gray-700',
                    tablet: 'mb-2 block text-sm font-medium text-gray-700',
                    desktop: 'mb-2 block text-sm font-medium text-gray-700'
                })">
                    Current Stock
                </label>
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'text-xl font-bold text-gray-900',
                    tablet: 'text-2xl font-bold text-gray-900',
                    desktop: 'text-2xl font-bold text-gray-900'
                })">
                    {{ product?.product_quantity || 0 }}
                    <span :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs font-normal text-gray-500',
                        tablet: 'text-sm font-normal text-gray-500',
                        desktop: 'text-sm font-normal text-gray-500'
                    })">
                        {{ product?.unit?.unit_name || 'units' }}
                    </span>
                </div>
            </div>

            <!-- Add Quantity Input -->
            <div>
                <InputForm
                    :errors="errors.quantity"
                    tag="label"
                    label-name="Quantity to Add*"
                    id="quantity"
                >
                    <InputNumber
                        v-model="form.quantity"
                        input-id="quantity"
                        placeholder="Enter quantity to add"
                        fluid
                        :invalid="errors.quantity.length > 0"
                        :min="1"
                        :use-grouping="false"
                        :class="responsive.getResponsiveClasses({
                            mobile: 'transition-all duration-200 !text-sm',
                            tablet: 'transition-all duration-200',
                            desktop: 'transition-all duration-200'
                        })"
                        @update:modelValue="clearError"
                    />
                </InputForm>
            </div>

            <!-- Action Buttons -->
            <div :class="responsive.getResponsiveClasses({
                mobile: 'flex justify-end gap-2 pt-3 border-t border-gray-200',
                tablet: 'flex justify-end gap-3 pt-4 border-t border-gray-200',
                desktop: 'flex justify-end gap-3 pt-4 border-t border-gray-200'
            })">
                <Button
                    type="button"
                    label="Cancel"
                    icon="pi pi-times"
                    severity="secondary"
                    outlined
                    :class="[
                        '!border-0 hover:!bg-gray-200',
                        responsive.getResponsiveClasses({
                            mobile: 'flex-1 !text-sm',
                            tablet: 'w-auto',
                            desktop: 'w-auto'
                        })
                    ]"
                    @click="handleCancel"
                    :disabled="loading"
                />
                <Button
                    type="submit"
                    label="Add Stock"
                    icon="pi pi-plus"
                    :class="[
                        '!bg-blue-600 hover:!bg-blue-700 !text-white',
                        responsive.getResponsiveClasses({
                            mobile: 'flex-1 !text-sm',
                            tablet: 'w-auto',
                            desktop: 'w-auto'
                        })
                    ]"
                    :loading="loading"
                />
            </div>
        </form>
    </Dialog>
</template>

<script setup lang="ts">
import {
    ProductInterface,
    AddStockRequestInterface,
    AddStockFormInterface,
    AddStockFormErrorInterface,
} from "@/interfaces/ProductInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive, ref, watch } from "vue";
import { useToast } from "vue-toastification";

import { useResponsive } from '@/composables/useResponsive';

interface Props {
    product: ProductInterface | null;
    visible: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits(["update:visible", "success"]);
const toast = useToast();
const responsive = useResponsive();
const submitService = useAxiosUtil<AddStockRequestInterface, ProductInterface>();
const loading = ref(false);

const form: AddStockFormInterface = reactive({
    quantity: null,
});

const errors: AddStockFormErrorInterface = reactive({
    quantity: [],
});

const clearError = () => {
    errors.quantity = [];
};

const validate = () => {
    clearError();

    if (!form.quantity) {
        errors.quantity.push("Please enter a quantity to add");
        return false;
    }

    if (form.quantity <= 0) {
        errors.quantity.push("Quantity must be greater than zero");
        return false;
    }

    if (!Number.isInteger(form.quantity)) {
        errors.quantity.push("Please enter a valid number");
        return false;
    }

    return true;
};

const handleSubmit = async () => {
    if (!validate()) {
        toast.error("Please correct the errors before submitting");
        return;
    }

    if (!props.product) {
        toast.error("No product selected");
        return;
    }

    loading.value = true;

    await submitService
        .patch(`admin/products/${props.product.product_id}/add-stock`, {
            quantity: form.quantity,
        })
        .then(() => {
            if (
                submitService.request.status === 200 &&
                submitService.request.data
            ) {
                toast.success(
                    submitService.request.message || "Stock added successfully",
                );
                emit("success", submitService.request.data);
                emit("update:visible", false);
                resetForm();
            } else {
                toast.error(
                    submitService.request.message ?? "Failed to add stock",
                );
                if (submitService.request.errors) {
                    Object.keys(submitService.request.errors).forEach((key) => {
                        if (key in errors) {
                            errors[key] = submitService.request.errors[key];
                        }
                    });
                }
            }
        })
        .catch(() => {
            toast.error("An error occurred while adding stock");
        })
        .finally(() => {
            loading.value = false;
        });
};

const handleCancel = () => {
    emit("update:visible", false);
    resetForm();
};

const handleVisibilityChange = (newValue: boolean) => {
    emit("update:visible", newValue);
    if (!newValue) {
        resetForm();
    }
};

const resetForm = () => {
    form.quantity = null;
    clearError();
};

// Reset form when modal is closed
watch(
    () => props.visible,
    (newValue) => {
        if (!newValue) {
            resetForm();
        }
    },
);
</script>
