<template>
    <div>
        <BoxShadow class="mb-4">
            <div class="flex justify-center flex-wrap w-full">
                <div class="p-2">
                    <button 
                        type="button"
                        class="p-2 cursor-pointer rounded hover:bg-gray-200"
                        :class="{'bg-gray-200': page === 'information'}"
                        @click="page = 'information'"
                    >
                        <i class="pi pi-file"></i>
                        Product Information
                    </button>
                </div>
                <div class="p-2">
                    <button 
                        type="button"
                        class="p-2 cursor-pointer rounded"
                        :class="{
                            'bg-gray-200 hover:bg-gray-200': page === 'image',
                            'opacity-70': selectedItem === null
                        }"
                        :disabled="selectedItem === null"
                        @click="page = 'image'"
                    >
                        <i class="pi pi-image"></i>
                        Product Image
                    </button>
                </div>
                <div class="p-2">
                    <button 
                        type="button"
                        class="p-2 cursor-pointer rounded"
                        :class="{
                            'bg-gray-200 hover:bg-gray-200': page === 'specification',
                            'opacity-70': selectedItem === null
                        }"
                        :disabled="selectedItem === null"
                        @click="page = 'specification'"
                    >
                        <i class="pi pi-list"></i>
                        Product Specification
                    </button>
                </div>
                <div class="p-2">
                    <button 
                        type="button"
                        class="p-2 cursor-pointer rounded"
                        :class="{
                            'bg-gray-200 hover:bg-gray-200': page === 'featured-image',
                            'opacity-70': selectedItem === null
                        }"
                        :disabled="selectedItem === null"
                        @click="page = 'featured-image'"
                    >
                        <i class="pi pi-image"></i>
                        Product Featured Images
                    </button>
                </div>
            </div>
        </BoxShadow>

        <BoxShadow class="mb-4">
            <div v-if="page === 'information'" class="w-full">
                <AddProductForm
                    v-if="selectedItem === null"
                    @cb="productInfoCb"
                />
                <EditProductForm
                    v-else-if="selectedItem !== null"
                    :data="selectedItem"
                    @cb="productInfoCb"
                />
            </div>
            <div v-else-if="page === 'image'" class="w-full">
                <UpdateProductImageForm
                    v-if="selectedItem !== null"
                    :data="selectedItem"
                    @cb="productInfoCb"
                />
            </div>
            <div v-else-if="page === 'specification'" class="w-full">
                <ProductSpecificationList
                    v-if="selectedItem !== null"
                    :data="selectedItem"
                    @cb="productInfoCb"
                />
            </div>
            <div v-else-if="page === 'featured-image'" class="w-full">
                
            </div>
        </BoxShadow>
    </div>
</template>
<script setup lang="ts">
import { ProductInterface } from '@/interfaces/ProductInterface';
import { onMounted, ref } from 'vue';
import AddProductForm from '@/components/forms/AddProductForm.vue';
import EditProductForm from '@/components/forms/EditProductForm.vue';
import UpdateProductImageForm from '@/components/forms/UpdateProductImageForm.vue';
import ProductSpecificationList from '@/components/forms/ProductSpecificationList.vue';

interface Props {
    data: ProductInterface | null;
}

const props = defineProps<Props>();
const selectedItem = ref<ProductInterface | null>(props.data);
const emit = defineEmits(['cb']);
const page = ref<'information' | 'image' | 'specification' | 'featured-image'>('information');

const productInfoCb = (data: ProductInterface) => {
    selectedItem.value = data;
    emit('cb');
};

onMounted(() => {
    if (props.data) {
        selectedItem.value = props.data;
    }
});
</script>