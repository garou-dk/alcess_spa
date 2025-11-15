<template>
    <div>
        <div>
            <div class="relative mb-8">
                <div class="relative h-[300px] md:h-[400px] lg:h-[500px] overflow-hidden">
                    <img :src="HeroBg" class="h-full w-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent" />
                    <div class="absolute inset-0 flex items-center">
                        <div class="container mx-auto px-6">
                            <div class="max-w-2xl text-white">
                                <h1 class="mb-4 text-4xl font-bold md:text-5xl lg:text-6xl animate-fade-in">
                                    Welcome to {{ appName }}
                                </h1>
                                <p class="mb-6 text-lg md:text-xl text-gray-200">
                                    Discover amazing products at unbeatable prices
                                </p>
                                <button class="rounded-full bg-white px-8 py-3 font-semibold text-sky-800 transition-all hover:bg-sky-50 hover:shadow-xl">
                                    Shop Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4">
                <!-- Enhanced Categories Section -->
                <section class="mb-12">
                    <div class="mb-6">
                        <h2 class="mb-2 text-3xl font-bold text-gray-800">
                            Shop by Category
                        </h2>
                        <p class="text-gray-600">
                            Browse our wide range of categories
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                        <button
                            v-for="(category, index) in CategoryStore.categories"
                            :key="index"
                            class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
                            @click="goRoute('customer.product-category', { id: category.category_slug })"
                        >
                            <div class="absolute inset-0 bg-gradient-to-br from-sky-500/10 to-transparent opacity-0 transition-opacity group-hover:opacity-100" />
                            <div class="relative flex flex-col items-center gap-3">
                                <div
                                    v-if="category.category_image"
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-sky-100 p-3 transition-all group-hover:scale-110 group-hover:bg-sky-200"
                                >
                                    <img
                                        :src="
                                            UrlUtil.getBaseAppUrl(
                                                `storage/images/category/${category.category_image}`,
                                            )
                                        "
                                        :alt="category.category_name"
                                        class="h-full w-full object-contain"
                                    />
                                </div>
                                <div
                                    v-else
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-sky-700 text-2xl font-bold text-white transition-all group-hover:scale-110"
                                >
                                    {{ category.category_name[0] }}
                                </div>
                                <span class="text-center text-sm font-semibold text-gray-800 group-hover:text-sky-600">
                                    {{ category.category_name }}
                                </span>
                            </div>
                        </button>
                    </div>
                </section>

                <!-- Enhanced Best Selling Products -->
                <section class="mb-12">
                    <div class="mb-6">
                        <h2 class="mb-2 text-3xl font-bold text-gray-800">
                            🔥 Best Sellers
                        </h2>
                        <p class="text-gray-600">
                            Most popular products this month
                        </p>
                    </div>

                    <!-- Loading Skeletons -->
                    <div v-if="loadBestSellingService.request.loading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                        <div v-for="i in 6" :key="i" class="animate-pulse">
                            <div class="overflow-hidden rounded-2xl bg-white shadow-md">
                                <div class="h-48 bg-gray-200" />
                                <div class="p-4 space-y-3">
                                    <div class="h-4 bg-gray-200 rounded" />
                                    <div class="h-4 w-2/3 bg-gray-200 rounded" />
                                    <div class="h-8 bg-gray-200 rounded" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                        <div
                            v-for="(product, index) in products"
                            :key="index"
                            class="group relative overflow-hidden rounded-2xl bg-white shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                        >
                            <!-- Wishlist Button -->
                            <button class="absolute right-3 top-3 z-10 rounded-full bg-white/90 p-2 opacity-0 shadow-lg transition-all group-hover:opacity-100 hover:bg-white hover:scale-110">
                                <i class="pi pi-heart text-gray-600 hover:text-red-500" />
                            </button>

                            <!-- Product Image -->
                            <div class="relative overflow-hidden bg-gray-50">
                                <img
                                    v-if="product.product_image"
                                    class="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    :src="
                                        UrlUtil.getBaseAppUrl(
                                            `storage/images/product/${product.product_image}`,
                                        )
                                    "
                                    :alt="product.product_name"
                                />
                                <div
                                    v-else
                                    class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200"
                                >
                                    <i class="pi pi-camera text-4xl text-gray-400" />
                                </div>
                                
                                <!-- Quick View Overlay -->
                                <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                                    <RouterLink
                                        :to="{
                                            name: 'customer.product-info.index',
                                            params: { id: product.product_id },
                                        }"
                                        class="block"
                                    >
                                        <button type="button" class="cursor-pointer rounded-full bg-white px-6 py-2 text-sm font-semibold text-gray-800 transition-transform hover:scale-105">
                                            View
                                        </button>
                                    </RouterLink>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="p-4">
                                <!-- Category Badge -->
                                <div class="mb-2">
                                    <span class="inline-block rounded-full bg-sky-100 px-2 py-1 text-xs font-medium text-sky-700">
                                        {{ product.category?.category_name || 'Uncategorized' }}
                                    </span>
                                </div>

                                <!-- Product Name -->
                                <h3 class="mb-2 line-clamp-2 min-h-[2.5rem] text-sm font-semibold text-gray-800 transition-colors group-hover:text-sky-600">
                                    {{ product.product_name }}
                                </h3>

                                <!-- Price -->
                                <div class="mb-3">
                                    <span class="text-lg font-bold text-gray-900">
                                        {{ CurrencyUtil.formatCurrency(product.product_price) }}
                                    </span>
                                </div>

                                <!-- Action Button -->
                                <RouterLink
                                    :to="{
                                        name: 'customer.product-info.index',
                                        params: { id: product.product_id },
                                    }"
                                    class="block"
                                >
                                    <button class="w-full rounded-xl bg-gradient-to-r from-sky-600 to-sky-700 py-2.5 font-semibold text-white transition-all hover:from-sky-700 hover:to-sky-800 hover:shadow-lg">
                                        View Details
                                    </button>
                                </RouterLink>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!loadBestSellingService.request.loading && products.length === 0" class="py-16 text-center">
                        <i class="pi pi-shopping-bag mb-4 text-6xl text-gray-300" />
                        <h3 class="mb-2 text-xl font-semibold text-gray-600">
                            No products available
                        </h3>
                        <p class="text-gray-400">
                            Check back soon for new arrivals!
                        </p>
                    </div>
                </section>

                <!-- Features Section -->
                <section class="mb-12">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                <i class="pi pi-check-circle text-2xl text-green-600" />
                            </div>
                            <h3 class="mb-2 font-semibold text-gray-800">
                                Quality Guarantee
                            </h3>
                            <p class="text-sm text-gray-600">
                                All products are carefully selected and verified
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                                <i class="pi pi-truck text-2xl text-blue-600" />
                            </div>
                            <h3 class="mb-2 font-semibold text-gray-800">
                                Fast Delivery
                            </h3>
                            <p class="text-sm text-gray-600">
                                Get your orders delivered quickly and safely
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100">
                                <i class="pi pi-shield text-2xl text-purple-600" />
                            </div>
                            <h3 class="mb-2 font-semibold text-gray-800">
                                Secure Payment
                            </h3>
                            <p class="text-sm text-gray-600">
                                Your payment information is always protected
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-orange-100">
                                <i class="pi pi-sync text-2xl text-orange-600" />
                            </div>
                            <h3 class="mb-2 font-semibold text-gray-800">
                                Easy Returns
                            </h3>
                            <p class="text-sm text-gray-600">
                                30-day return policy for your peace of mind
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <footer class="mt-16 bg-gradient-to-r from-gray-800 to-gray-900 py-12 text-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <h3 class="mb-4 text-lg font-bold">{{ appName }}</h3>
                        <p class="text-sm text-gray-400">
                            Your trusted online marketplace for quality products at great prices.
                        </p>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold">Quick Links</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">FAQs</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Shipping Info</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold">Customer Service</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><a href="#" class="hover:text-white transition-colors">Track Order</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Returns</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Terms & Conditions</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold">Connect With Us</h4>
                        <div class="flex gap-3">
                            <button class="rounded-full bg-white/10 p-3 transition-colors hover:bg-white/20">
                                <i class="pi pi-facebook" />
                            </button>
                            <button class="rounded-full bg-white/10 p-3 transition-colors hover:bg-white/20">
                                <i class="pi pi-twitter" />
                            </button>
                            <button class="rounded-full bg-white/10 p-3 transition-colors hover:bg-white/20">
                                <i class="pi pi-instagram" />
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-700 pt-8 text-center text-sm text-gray-400">
                    <p>&copy; 2025 {{ appName }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from "vue";
import HeroBg from "@/../img/hero-bg.jpg";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";
import { useRouter } from "vue-router";

const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const products = ref<ProductInterface[]>([]);

const appName = import.meta.env.VITE_APP_NAME;

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

const router = useRouter();

const goRoute = (route: string, params: Record<string, string>) => {
    router.push({ name: route, params: params });
};


onMounted(() => {
    CategoryStore.fetchCategories();
    loadBestSellingProducts();
});
</script>