<template>
    <div>
        <div>
            <div class="mb-2 w-full">
                <img :src="HeroBg" class="h-full w-full" />
            </div>
            <div class="p-2">
                <h1 class="mb-4 text-2xl font-semibold bg-white p-5 rounded border-b-4 border-blue-900 text-center">
                    Categories
                </h1>
                <div class="flex flex-wrap p-2">
                    <div
                        v-for="(category, index) in CategoryStore.categories"
                        :key="index"
                        class="p-2"
                    >
                        <button
                            class="primary-bg flex cursor-pointer items-center gap-2 rounded-full p-2 min-w-36 border-4 border-white"
                        >
                            <div
                                v-if="category.category_image"
                                class="flex items-center justify-center rounded-full bg-white p-2"
                            >
                                <img
                                    :src="
                                        UrlUtil.getBaseAppUrl(
                                            `storage/images/category/${category.category_image}`,
                                        )
                                    "
                                    :alt="category.category_image"
                                    class="h-8 w-8"
                                />
                            </div>
                            <div
                                v-else
                                class="flex items-center justify-center rounded-full bg-white p-2"
                            >
                                <Avatar
                                    :label="category.category_name[0]"
                                    shape="circle"
                                />
                            </div>
                            
                            <div class="grow">
                                <span>{{ category.category_name }}</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <h1 class="mb-4 text-2xl font-semibold bg-white p-5 rounded border-b-4 border-blue-900 text-center">
                    Best Selling Products
                </h1>
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6"
                >
                    <div
                        v-for="(product, index) in products"
                        :key="index"
                        class="p-2"
                    >
                        <div
                            class="max-w-xs overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-lg"
                        >
                            <div class="flex justify-center">
                                <img
                                    v-if="product.product_image"
                                    class="h-48 w-48 object-cover"
                                    :src="
                                        UrlUtil.getBaseAppUrl(
                                            `storage/images/product/${product.product_image}`,
                                        )
                                    "
                                    :alt="product.product_name"
                                />
                                <div
                                    v-else
                                    class="flex h-48 w-48 items-center justify-center p-4"
                                >
                                    <i class="pi pi-camera text-2xl" />
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    {{ product.product_name }}
                                </h3>
                                <p class="mt-1 text-gray-600">
                                    {{
                                        CurrencyUtil.formatCurrency(
                                            product.product_price,
                                        )
                                    }}
                                </p>
                            </div>
                            <div class="mb-5 flex justify-center">
                                <RouterLink
                                    :to="{
                                        name: 'customer.product-info.index',
                                        params: { id: product.product_id },
                                    }"
                                >
                                    <Button
                                        label="View Details"
                                        class="primary-bg"
                                    />
                                </RouterLink>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
// import {
//     SearchErrorInterface,
//     SearchProductInterface,
// } from "@/interfaces/SearchProductInterface";
import { onMounted, ref } from "vue";
import HeroBg from "@/../img/hero-bg.jpg";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";

// const appName = import.meta.env.VITE_APP_NAME;
// const loginFormVisible = ref<boolean>(false);
// const registerFormVisible = ref<boolean>(false);
const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const products = ref<ProductInterface[]>([]);
// const form: SearchProductInterface = reactive({
//     search: null,
// });
// const errors: SearchErrorInterface = reactive({
//     search: [],
// });

// const openLoginForm = () => {
//     registerFormVisible.value = false;
//     loginFormVisible.value = true;
// };

// const openRegisterForm = () => {
//     loginFormVisible.value = false;
//     registerFormVisible.value = true;
// };

const loadBestSellingProducts = async () => {
    await loadBestSellingService.get("best-selling").then(() => {
        if (
            loadBestSellingService.request.status === 200 &&
            loadBestSellingService.request.data
        ) {
            products.value = loadBestSellingService.request.data;
        }
    });
};

onMounted(() => {
    CategoryStore.fetchCategories();
    loadBestSellingProducts();
});
</script>
