<template>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Product Specifications</h2>
            <p class="text-sm text-gray-600">Manage technical specifications for your product (optional)</p>
        </div>

        <div class="flex justify-end mb-4">
            <Button
                type="button"
                icon="pi pi-plus"
                label="Add Specification"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                @click="showAddSpecificationForm()"
            />
        </div>

        <!-- Specifications List -->
        <div v-if="props.data.specifications && props.data.specifications.length > 0" class="space-y-2 mb-4">
            <EditProductSpecificationForm
                v-for="(specification, index) in props.data.specifications"
                :key="specification.specification_id"
                :data="specification"
                :isSelected="selectedProductSpecification?.specification_id === specification.specification_id"
                @cb="handleInlineEdit"
            >
                <template #select>
                    <Button
                        type="button"
                        icon="pi pi-pencil"
                        class="!bg-blue-500 hover:!bg-blue-600 !text-white h-10 w-10 flex items-center justify-center"
                        @click="selectSpecification(specification)"
                        v-tooltip.top="'Edit'"
                    />
                </template>
            </EditProductSpecificationForm>
        </div>

        <div v-else class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-gray-200">
            <i class="pi pi-list text-4xl mb-2"></i>
            <p class="mb-4">No specifications added yet</p>
            <Button
                type="button"
                icon="pi pi-plus"
                label="Add First Specification"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                @click="showAddSpecificationForm()"
            />
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end mt-6">
            <Button
                type="button"
                label="Next"
                icon="pi pi-arrow-right"
                iconPos="right"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                @click="goToNext()"
            />
        </div>

        <!-- Add Specification Dialog -->
        <Dialog
            v-model:visible="specificationForm"
            header="Add Specification"
            modal
            :dismissableMask="true"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :pt="{
                header: { class: '!bg-blue-600 !text-white' },
                closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <ProductSpecificationForm :data="props.data" @cb="submitCb" />
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { ProductSpecificationInterface } from "@/interfaces/ProductSpecificationInterface";
import EditProductSpecificationForm from "@/components/forms/EditProductSpecificationForm.vue";
import ProductSpecificationForm from "@/components/forms/ProductSpecificationForm.vue";
import { provide, ref } from "vue";
import { ProductInterface } from "@/interfaces/ProductInterface";

interface Props {
    data: ProductInterface;
}

const props = defineProps<Props>();
const emit = defineEmits(["cb"]);
const selectedProductSpecification = ref<ProductSpecificationInterface | null>(
    null,
);
const specificationForm = ref(false);
provide("selectedProductSpecification", selectedProductSpecification);

const showAddSpecificationForm = () => {
    selectedProductSpecification.value = null;
    specificationForm.value = true;
};

const selectSpecification = (specification: ProductSpecificationInterface) => {
    if (selectedProductSpecification.value?.specification_id === specification.specification_id) {
        selectedProductSpecification.value = null;
    } else {
        selectedProductSpecification.value = specification;
    }
};

const submitCb = (data: ProductInterface) => {
    // Don't close the modal - keep it open for adding more specifications
    selectedProductSpecification.value = null;
    emit("cb", data, false); // Pass false to prevent modal closure
};

const handleInlineEdit = (data: ProductInterface) => {
    // Handle inline editing without closing any modals
    selectedProductSpecification.value = null;
    emit("cb", data, false); // Pass false to prevent modal closure
};

const closeModal = () => {
    specificationForm.value = false;
    selectedProductSpecification.value = null;
};

const goToNext = () => {
    // Emit with false to move to next tab without closing modal
    emit("cb", props.data, false);
};
</script>
