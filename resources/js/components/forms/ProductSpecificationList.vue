<template>
    <div class="p-4">
        <div class="flex justify-end">
            <Button type="button" icon="pi pi-plus" label="Add Specification" @click="showAddSpecificationForm()" />
        </div>
        <div v-for="(specification, index) in props.data.specifications" :key="index" class="p-2">
            <EditProductSpecificationForm
                :data="specification"
                :is-selected="selectedProductSpecification !== null && selectedProductSpecification.specification_id === specification.specification_id"
                @cb="submitCb"
            >
                <template #select>
                    <Button type="button" icon="pi pi-pencil" @click="selectSpecification(specification)" />
                </template>
            </EditProductSpecificationForm>
        </div>
        <Dialog v-model:visible="specificationForm" header="Add Specification" modal :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <ProductSpecificationForm
                :data="props.data"
                @cb="submitCb"
            />
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { ProductSpecificationInterface } from '@/interfaces/ProductSpecificationInterface';
import EditProductSpecificationForm from '@/components/forms/EditProductSpecificationForm.vue';
import { provide, ref } from 'vue';
import { ProductInterface } from '@/interfaces/ProductInterface';
import ProductSpecificationForm from '@/components/forms/ProductSpecificationForm.vue';

interface Props {
    data: ProductInterface;
}

const props = defineProps<Props>();
const emit = defineEmits(['cb']);
const selectedProductSpecification = ref<ProductSpecificationInterface | null>(null);
const specificationForm = ref(false);
provide('selectedProductSpecification', selectedProductSpecification);

const showAddSpecificationForm = () => {
    selectedProductSpecification.value = null;
    specificationForm.value = true;
};

const selectSpecification = (specification: ProductSpecificationInterface) => {
    selectedProductSpecification.value = specification;
};

const submitCb = (data: ProductInterface) => {
    specificationForm.value = false;
    selectedProductSpecification.value = null;
    emit('cb', data);
};
</script>