<template>
    <div class="max-w-7xl mx-auto">
        <!-- Enhanced Tab Navigation -->
        <BoxShadow class="mb-6">
            <div class="flex w-full flex-wrap justify-center gap-2 p-4">
                <button
                    type="button"
                    class="cursor-pointer group relative flex items-center gap-2 px-4 py-3 rounded-lg font-medium transition-all duration-200 hover:shadow-md"
                    :class="
                        page === 'information'
                            ? 'bg-blue-600 text-white shadow-lg scale-105'
                            : 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-200'
                    "
                    @click="page = 'information'"
                >
                    <i
                        class="pi pi-file text-lg transition-transform duration-200"
                        :class="
                            page === 'information'
                                ? 'scale-110'
                                : 'group-hover:scale-110'
                        "
                    ></i>
                    <span class="whitespace-nowrap">Product Information</span>
                    <div
                        v-if="page === 'information'"
                        class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-blue-400 rounded-full"
                    ></div>
                </button>

                <button
                    type="button"
                    class="cursor-pointer group relative flex items-center gap-2 px-4 py-3 rounded-lg font-medium transition-all duration-200"
                    :class="[
                        page === 'image'
                            ? 'bg-green-600 text-white shadow-lg scale-105'
                            : selectedItem === null
                              ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                              : 'bg-white text-gray-700 hover:bg-green-50 hover:shadow-md border border-gray-200',
                    ]"
                    :disabled="selectedItem === null"
                    @click="page = 'image'"
                >
                    <i
                        class="pi pi-image text-lg transition-transform duration-200"
                        :class="
                            page === 'image' && selectedItem !== null
                                ? 'scale-110'
                                : selectedItem !== null
                                  ? 'group-hover:scale-110'
                                  : ''
                        "
                    ></i>
                    <span class="whitespace-nowrap">Product Image</span>
                    <div
                        v-if="page === 'image'"
                        class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-green-400 rounded-full"
                    ></div>
                    <i
                        v-if="selectedItem === null"
                        class="pi pi-lock text-xs ml-1"
                    ></i>
                </button>

                <button
                    type="button"
                    class="cursor-pointer group relative flex items-center gap-2 px-4 py-3 rounded-lg font-medium transition-all duration-200"
                    :class="[
                        page === 'specification'
                            ? 'bg-purple-600 text-white shadow-lg scale-105'
                            : selectedItem === null
                              ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                              : 'bg-white text-gray-700 hover:bg-purple-50 hover:shadow-md border border-gray-200',
                    ]"
                    :disabled="selectedItem === null"
                    @click="page = 'specification'"
                >
                    <i
                        class="pi pi-list text-lg transition-transform duration-200"
                        :class="
                            page === 'specification' && selectedItem !== null
                                ? 'scale-110'
                                : selectedItem !== null
                                  ? 'group-hover:scale-110'
                                  : ''
                        "
                    ></i>
                    <span class="whitespace-nowrap">Specifications</span>
                    <div
                        v-if="page === 'specification'"
                        class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-purple-400 rounded-full"
                    ></div>
                    <i
                        v-if="selectedItem === null"
                        class="pi pi-lock text-xs ml-1"
                    ></i>
                </button>

                <button
                    type="button"
                    class="cursor-pointer group relative flex items-center gap-2 px-4 py-3 rounded-lg font-medium transition-all duration-200"
                    :class="[
                        page === 'featured-image'
                            ? 'bg-amber-600 text-white shadow-lg scale-105'
                            : selectedItem === null
                              ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                              : 'bg-white text-gray-700 hover:bg-amber-50 hover:shadow-md border border-gray-200',
                    ]"
                    :disabled="selectedItem === null"
                    @click="page = 'featured-image'"
                >
                    <i
                        class="pi pi-images text-lg transition-transform duration-200"
                        :class="
                            page === 'featured-image' && selectedItem !== null
                                ? 'scale-110'
                                : selectedItem !== null
                                  ? 'group-hover:scale-110'
                                  : ''
                        "
                    ></i>
                    <span class="whitespace-nowrap">Featured Images</span>
                    <div
                        v-if="page === 'featured-image'"
                        class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-amber-400 rounded-full"
                    ></div>
                    <i
                        v-if="selectedItem === null"
                        class="pi pi-lock text-xs ml-1"
                    ></i>
                </button>
            </div>

            <!-- Info Banner for Locked Tabs -->
            <div
                v-if="selectedItem === null && page === 'information'"
                class="mx-4 mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3"
            >
                <i class="pi pi-info-circle text-blue-600 mt-0.5"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Getting Started</p>
                    <p class="text-blue-700">
                        Create a product first to unlock additional tabs for
                        images, specifications, and featured images.
                    </p>
                </div>
            </div>
        </BoxShadow>

        <!-- Tab Content -->
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
                <FeaturedImageList
                    v-if="selectedItem !== null"
                    :data="selectedItem"
                    @cb="productInfoCb"
                />
            </div>
        </BoxShadow>
    </div>
</template>

<script setup lang="ts">
import { ProductInterface } from "@/interfaces/ProductInterface";
import { onMounted, ref } from "vue";
import AddProductForm from "@/components/forms/AddProductForm.vue";
import EditProductForm from "@/components/forms/EditProductForm.vue";
import UpdateProductImageForm from "@/components/forms/UpdateProductImageForm.vue";
import ProductSpecificationList from "@/components/forms/ProductSpecificationList.vue";
import FeaturedImageList from "@/components/forms/FeaturedImageList.vue";

interface Props {
    data: ProductInterface | null;
}

const props = defineProps<Props>();
const selectedItem = ref<ProductInterface | null>(props.data);
const emit = defineEmits(["cb"]);
const page = ref<"information" | "image" | "specification" | "featured-image">(
    "information",
);

const productInfoCb = (data: ProductInterface) => {
    selectedItem.value = data;
    emit("cb");
};

onMounted(() => {
    if (props.data) {
        selectedItem.value = props.data;
    }
});
</script>