<template>
    <div>
        <div>
            <div class="w-full mb-2">
                <img :src="HeroBg" class="w-full h-full" />
            </div>
            <div class="p-2">
                <h1 class="p-2 text-2xl font-semibold">Categories</h1>
                <div class="flex flex-wrap p-2">
                    <div v-for="(category, index) in CategoryStore.categories" :key="index" class="p-2">
                        <button
                            class="rounded-full p-2 flex items-center justify-center gap-2 primary-bg cursor-pointer">
                            <img v-if="category.category_image"
                                :src="UrlUtil.getBaseAppUrl(`storage/images/category/${category.category_image}`)"
                                :alt="category.category_image" class="w-8 h-8 mr-2" />
                            <Avatar :label="category.category_name[0]" shape="circle" />
                            <span>{{ category.category_name }}</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <h1 class="text-2xl font-semibold mb-4">Best Selling Products</h1>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div v-for="(product, index) in products" :key="index" class="p-2">
                        <div
                            class="max-w-xs rounded-lg shadow-md overflow-hidden bg-white hover:shadow-lg transition-shadow duration-300">
                            <div class="flex justify-center">
                                <img v-if="product.product_image" class="w-48 h-48 object-cover"
                                    :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                    :alt="product.product_name" />
                                <div v-else class="flex items-center justify-center p-4 w-48 h-48">
                                    <i class="pi pi-camera text-2xl" />
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800">{{ product.product_name }}</h3>
                                <p class="text-gray-600 mt-1">{{ CurrencyUtil.formatCurrency(product.product_price) }}
                                </p>
                            </div>
                            <div class="flex justify-center mb-5">
                                <RouterLink :to="{ name: 'customer.product-info.index', params: { id: product.product_id } }">
                                    <Button label="View Details" class="primary-bg" />
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
import {
    SearchErrorInterface,
    SearchProductInterface,
} from "@/interfaces/SearchProductInterface";
import { onMounted, reactive, ref } from "vue";
import HeroBg from "@/../img/hero-bg.jpg";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";

const appName = import.meta.env.VITE_APP_NAME;
const loginFormVisible = ref<boolean>(false);
const registerFormVisible = ref<boolean>(false);
const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const products = ref<ProductInterface[]>([]);
const form: SearchProductInterface = reactive({
    search: null,
});
const errors: SearchErrorInterface = reactive({
    search: [],
});

const openLoginForm = () => {
    registerFormVisible.value = false;
    loginFormVisible.value = true;
};

const openRegisterForm = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = true;
};

const loadBestSellingProducts = async () => {
    await loadBestSellingService.get("best-selling").then(() => {
        if (loadBestSellingService.request.status === 200 && loadBestSellingService.request.data) {
            products.value = loadBestSellingService.request.data;
        }
    })
};

onMounted(() => {
    CategoryStore.fetchCategories();
    loadBestSellingProducts();
});
</script>
