<template>
    <div :class="responsive.getResponsiveClasses({
        mobile: 'w-full',
        tablet: 'max-w-4xl mx-auto',
        desktop: 'max-w-7xl mx-auto'
    })">
        <!-- Enhanced Tab Navigation -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'mb-3 rounded-lg bg-white border border-gray-200 shadow-sm',
            tablet: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm',
            desktop: 'mb-6 rounded-lg bg-white border border-gray-200 shadow-sm'
        })">
            <div :class="responsive.getResponsiveClasses({
                mobile: 'flex w-full overflow-x-auto gap-2 p-3 scrollbar-hide',
                tablet: 'flex w-full flex-wrap justify-center gap-2 p-4',
                desktop: 'flex w-full flex-wrap justify-center gap-2 p-4'
            })">
                <button
                    type="button"
                    :class="[
                        'cursor-pointer group relative flex items-center gap-2 rounded-lg font-medium transition-all duration-200 hover:shadow-md',
                        responsive.getResponsiveClasses({
                            mobile: 'flex-shrink-0 px-3 py-2 text-xs',
                            tablet: 'px-4 py-3',
                            desktop: 'px-4 py-3'
                        }),
                        page === 'information'
                            ? 'bg-blue-600 text-white shadow-lg scale-105'
                            : 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-200'
                    ]"
                    @click="page = 'information'"
                >
                    <i
                        :class="[
                            'pi pi-file transition-transform duration-200',
                            responsive.getResponsiveClasses({
                                mobile: 'text-base',
                                tablet: 'text-lg',
                                desktop: 'text-lg'
                            }),
                            page === 'information'
                                ? 'scale-110'
                                : 'group-hover:scale-110'
                        ]"
                    ></i>
                    <span :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs',
                        tablet: 'text-sm whitespace-nowrap',
                        desktop: 'text-base whitespace-nowrap'
                    })">Product Information</span>
                    <div
                        v-if="page === 'information'"
                        :class="responsive.getResponsiveClasses({
                            mobile: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-8 h-1 bg-blue-400 rounded-full',
                            tablet: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-blue-400 rounded-full',
                            desktop: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-blue-400 rounded-full'
                        })"
                    ></div>
                </button>

                <button
                    type="button"
                    :class="[
                        'cursor-pointer group relative flex items-center gap-2 rounded-lg font-medium transition-all duration-200',
                        responsive.getResponsiveClasses({
                            mobile: 'flex-shrink-0 px-3 py-2 text-xs',
                            tablet: 'px-4 py-3',
                            desktop: 'px-4 py-3'
                        }),
                        page === 'image'
                            ? 'bg-green-600 text-white shadow-lg scale-105'
                            : (selectedItem === null && !completedTabs.information)
                              ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                              : 'bg-white text-gray-700 hover:bg-green-50 hover:shadow-md border border-gray-200',
                    ]"
                    :disabled="selectedItem === null && !completedTabs.information"
                    @click="page = 'image'"
                >
                    <i
                        :class="[
                            'pi pi-image transition-transform duration-200',
                            responsive.getResponsiveClasses({
                                mobile: 'text-base',
                                tablet: 'text-lg',
                                desktop: 'text-lg'
                            }),
                            page === 'image' && (selectedItem !== null || completedTabs.information)
                                ? 'scale-110'
                                : (selectedItem !== null || completedTabs.information)
                                  ? 'group-hover:scale-110'
                                  : ''
                        ]"
                    ></i>
                    <span :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs',
                        tablet: 'text-sm whitespace-nowrap',
                        desktop: 'text-base whitespace-nowrap'
                    })">Product Image</span>
                    <div
                        v-if="page === 'image'"
                        :class="responsive.getResponsiveClasses({
                            mobile: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-8 h-1 bg-green-400 rounded-full',
                            tablet: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-green-400 rounded-full',
                            desktop: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-green-400 rounded-full'
                        })"
                    ></div>
                    <i
                        v-if="selectedItem === null && !completedTabs.information"
                        :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-lock text-xs ml-1',
                            tablet: 'pi pi-lock text-xs ml-1',
                            desktop: 'pi pi-lock text-xs ml-1'
                        })"
                    ></i>
                </button>

                <button
                    type="button"
                    :class="[
                        'cursor-pointer group relative flex items-center gap-2 rounded-lg font-medium transition-all duration-200',
                        responsive.getResponsiveClasses({
                            mobile: 'flex-shrink-0 px-3 py-2 text-xs',
                            tablet: 'px-4 py-3',
                            desktop: 'px-4 py-3'
                        }),
                        page === 'specification'
                            ? 'bg-purple-600 text-white shadow-lg scale-105'
                            : (selectedItem === null && !completedTabs.image)
                              ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                              : 'bg-white text-gray-700 hover:bg-purple-50 hover:shadow-md border border-gray-200',
                    ]"
                    :disabled="selectedItem === null && !completedTabs.image"
                    @click="page = 'specification'"
                >
                    <i
                        :class="[
                            'pi pi-list transition-transform duration-200',
                            responsive.getResponsiveClasses({
                                mobile: 'text-base',
                                tablet: 'text-lg',
                                desktop: 'text-lg'
                            }),
                            page === 'specification' && (selectedItem !== null || completedTabs.image)
                                ? 'scale-110'
                                : (selectedItem !== null || completedTabs.image)
                                  ? 'group-hover:scale-110'
                                  : ''
                        ]"
                    ></i>
                    <span :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs',
                        tablet: 'text-sm whitespace-nowrap',
                        desktop: 'text-base whitespace-nowrap'
                    })">Specifications</span>
                    <div
                        v-if="page === 'specification'"
                        :class="responsive.getResponsiveClasses({
                            mobile: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-8 h-1 bg-purple-400 rounded-full',
                            tablet: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-purple-400 rounded-full',
                            desktop: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-purple-400 rounded-full'
                        })"
                    ></div>
                    <i
                        v-if="selectedItem === null && !completedTabs.image"
                        :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-lock text-xs ml-1',
                            tablet: 'pi pi-lock text-xs ml-1',
                            desktop: 'pi pi-lock text-xs ml-1'
                        })"
                    ></i>
                </button>

                <button
                    type="button"
                    :class="[
                        'cursor-pointer group relative flex items-center gap-2 rounded-lg font-medium transition-all duration-200',
                        responsive.getResponsiveClasses({
                            mobile: 'flex-1 px-2 py-2 text-xs justify-center',
                            tablet: 'px-4 py-3',
                            desktop: 'px-4 py-3'
                        }),
                        page === 'featured-image'
                            ? 'bg-amber-600 text-white shadow-lg scale-105'
                            : (selectedItem === null && !completedTabs.specification)
                              ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                              : 'bg-white text-gray-700 hover:bg-amber-50 hover:shadow-md border border-gray-200',
                    ]"
                    :disabled="selectedItem === null && !completedTabs.specification"
                    @click="page = 'featured-image'"
                >
                    <i
                        :class="[
                            'pi pi-images transition-transform duration-200',
                            responsive.getResponsiveClasses({
                                mobile: 'text-base',
                                tablet: 'text-lg',
                                desktop: 'text-lg'
                            }),
                            page === 'featured-image' && (selectedItem !== null || completedTabs.specification)
                                ? 'scale-110'
                                : (selectedItem !== null || completedTabs.specification)
                                  ? 'group-hover:scale-110'
                                  : ''
                        ]"
                    ></i>
                    <span :class="responsive.getResponsiveClasses({
                        mobile: 'text-xs',
                        tablet: 'text-sm whitespace-nowrap',
                        desktop: 'text-base whitespace-nowrap'
                    })">Featured Images</span>
                    <div
                        v-if="page === 'featured-image'"
                        :class="responsive.getResponsiveClasses({
                            mobile: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-8 h-1 bg-amber-400 rounded-full',
                            tablet: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-amber-400 rounded-full',
                            desktop: 'absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-amber-400 rounded-full'
                        })"
                    ></div>
                    <i
                        v-if="selectedItem === null && !completedTabs.specification"
                        :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-lock text-xs ml-1',
                            tablet: 'pi pi-lock text-xs ml-1',
                            desktop: 'pi pi-lock text-xs ml-1'
                        })"
                    ></i>
                </button>
            </div>

            <!-- Info Banner for Locked Tabs -->
            <div
                v-if="selectedItem === null && page === 'information'"
                :class="responsive.getResponsiveClasses({
                    mobile: 'mx-3 mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3',
                    tablet: 'mx-4 mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3',
                    desktop: 'mx-4 mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3'
                })"
            >
                <i class="pi pi-info-circle text-blue-600 mt-0.5"></i>
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'text-sm text-blue-800',
                    tablet: 'text-sm text-blue-800',
                    desktop: 'text-sm text-blue-800'
                })">
                    <p class="font-medium mb-1">Getting Started</p>
                    <p class="text-blue-700">
                        Create a product first to unlock additional tabs for
                        images, specifications, and featured images.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'mb-3 rounded-lg bg-white border border-gray-200 shadow-sm',
            tablet: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm',
            desktop: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm'
        })">
            <div v-if="page === 'information'" class="w-full">
                <AddProductForm
                    v-if="isNewProduct"
                    @next="handleNextFromInfo"
                />
                <EditProductForm
                    v-else
                    :data="selectedItem"
                    @cb="productInfoCb"
                />
            </div>
            <div v-else-if="page === 'image'" class="w-full">
                <AddProductImageForm
                    v-if="isNewProduct"
                    @next="handleNextFromImage"
                    @back="page = 'information'"
                />
                <UpdateProductImageForm
                    v-else
                    :data="selectedItem"
                    @cb="productInfoCb"
                />
            </div>
            <div v-else-if="page === 'specification'" class="w-full">
                <AddProductSpecificationForm
                    v-if="isNewProduct"
                    @next="handleNextFromSpec"
                    @back="page = 'image'"
                />
                <ProductSpecificationList
                    v-else
                    :data="selectedItem"
                    @cb="productInfoCb"
                />
            </div>
            <div v-else-if="page === 'featured-image'" class="w-full">
                <AddFeaturedImagesForm
                    v-if="isNewProduct"
                    @save="handleSaveFeaturedImages"
                    @back="page = 'specification'"
                />
                <FeaturedImageList
                    v-else
                    :data="selectedItem"
                    @cb="productInfoCb"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ProductInterface } from "@/interfaces/ProductInterface";
import { onMounted, provide, ref } from "vue";
import { useResponsive } from "@/composables/useResponsive";
import AddProductForm from "@/components/forms/AddProductForm.vue";
import EditProductForm from "@/components/forms/EditProductForm.vue";
import UpdateProductImageForm from "@/components/forms/UpdateProductImageForm.vue";
import ProductSpecificationList from "@/components/forms/ProductSpecificationList.vue";
import FeaturedImageList from "@/components/forms/FeaturedImageList.vue";
import AddProductImageForm from "@/components/forms/AddProductImageForm.vue";
import AddProductSpecificationForm from "@/components/forms/AddProductSpecificationForm.vue";
import AddFeaturedImagesForm from "@/components/forms/AddFeaturedImagesForm.vue";

interface Props {
    data: ProductInterface | null;
}

const props = defineProps<Props>();
const responsive = useResponsive();
const selectedItem = ref<ProductInterface | null>(props.data);
const emit = defineEmits(["cb"]);
const page = ref<"information" | "image" | "specification" | "featured-image">(
    "information",
);

// Store form data temporarily for new products (only when adding, not editing)
const isNewProduct = ref<boolean>(!props.data);
const tempProductData = ref<any>(null);
const tempProductImage = ref<File | null>(null);
const tempSpecifications = ref<any[]>([]);
const tempFeaturedImages = ref<File[]>([]);

// Track which tabs have been completed (unlocked)
const completedTabs = ref({
    information: false,
    image: false,
    specification: false,
});

// Provide the temporary data to child components
provide('isNewProduct', isNewProduct);
provide('tempProductData', tempProductData);
provide('tempProductImage', tempProductImage);
provide('tempSpecifications', tempSpecifications);
provide('tempFeaturedImages', tempFeaturedImages);

const handleNextFromInfo = (data: any) => {
    // Store product info temporarily
    tempProductData.value = data;
    completedTabs.value.information = true;
    page.value = 'image';
};

const handleNextFromImage = (imageFile: File | null) => {
    // Store image temporarily  
    tempProductImage.value = imageFile;
    completedTabs.value.image = true;
    page.value = 'specification';
};

const handleNextFromSpec = () => {
    // Move to featured images
    completedTabs.value.specification = true;
    page.value = 'featured-image';
};

const handleSaveFeaturedImages = (product: ProductInterface) => {
    // Product has been created successfully
    selectedItem.value = product;
    isNewProduct.value = false;
    emit("cb");
};

const productInfoCb = (data: ProductInterface, shouldClose: boolean = true) => {
    selectedItem.value = data;
    
    // Navigate to next tab based on current page (only for add mode, not edit mode)
    if (!shouldClose && isNewProduct.value) {
        if (page.value === 'information') {
            page.value = 'image';
        } else if (page.value === 'image') {
            page.value = 'specification';
        } else if (page.value === 'specification') {
            page.value = 'featured-image';
        }
        return;
    }
    
    // Handle explicit next navigation (for edit mode Next button)
    if (shouldClose === "next") {
        if (page.value === 'information') {
            page.value = 'image';
        } else if (page.value === 'image') {
            page.value = 'specification';
        } else if (page.value === 'specification') {
            page.value = 'featured-image';
        }
        return;
    }
    
    if (shouldClose) {
        emit("cb");
    }
};

onMounted(() => {
    if (props.data) {
        selectedItem.value = props.data;
        isNewProduct.value = false;
    }
});
</script>