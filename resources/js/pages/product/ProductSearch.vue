<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Search Header -->
        <div class="bg-white shadow-sm">
            <div class="container mx-auto px-4 py-6">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-800">Search Products</h1>
                    <p class="text-gray-600 mt-1">Find exactly what you're looking for</p>
                </div>

                <!-- Search Bar -->
                <div class="relative max-w-3xl">
                    <input
                        v-model="form.search"
                        type="text"
                        placeholder="Search for products..."
                        class="w-full rounded-full border-2 border-gray-200 px-6 py-4 pr-14 text-lg focus:border-sky-500 focus:outline-none"
                        @keyup.enter="handleSearch"
                        @input="onSearchInput"
                    />
                    <button
                        @click="handleSearch"
                        :disabled="!form.search.trim()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-sky-600 p-3 text-white transition-all hover:bg-sky-700 disabled:bg-gray-300 disabled:cursor-not-allowed"
                    >
                        <i class="pi pi-search text-lg" />
                    </button>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <!-- Initial State - Before Search -->
            <div v-if="!hasSearched" class="py-24 text-center">
                <i class="pi pi-search mb-4 text-7xl text-gray-300" />
                <h3 class="mb-2 text-2xl font-semibold text-gray-600">
                    Start Searching
                </h3>
                <p class="text-gray-400">
                    Enter a product name or keyword to begin your search
                </p>
            </div>

            <!-- After Search -->
            <div v-else>
                <!-- Products Grid -->
                <main>
                    <!-- Results Header -->
                    <div class="mb-6">
                        <p class="text-gray-600">
                            <span class="font-semibold">{{ data.length }}</span>
                            results for "<span class="text-gray-800">{{ form.search }}</span>"
                        </p>
                    </div>

                    <!-- Loading State -->
                    <div v-if="loadService.request.loading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <div v-for="i in 8" :key="i" class="animate-pulse">
                            <div class="overflow-hidden rounded-2xl bg-white shadow-md">
                                <div class="h-56 bg-gray-200" />
                                <div class="p-4 space-y-3">
                                    <div class="h-4 bg-gray-200 rounded" />
                                    <div class="h-4 w-2/3 bg-gray-200 rounded" />
                                    <div class="h-8 bg-gray-200 rounded" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div v-else-if="data.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <div
                            v-for="product in data"
                            :key="product.product_id"
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
                                    :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                    :alt="product.product_name"
                                    class="h-56 w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                />
                                <div
                                    v-else
                                    class="flex h-56 w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200"
                                >
                                    <i class="pi pi-camera text-5xl text-gray-400" />
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
                                        <button class="rounded-full bg-white px-6 py-2 text-sm font-semibold text-gray-800 transition-transform hover:scale-105">
                                            Quick View
                                        </button>
                                    </RouterLink>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="p-4">
                                <!-- Category Badge -->
                                <div class="mb-2">
                                    <span class="inline-block rounded-full bg-sky-100 px-3 py-1 text-xs font-medium text-sky-700">
                                        {{ product.category?.category_name || 'Uncategorized' }}
                                    </span>
                                </div>

                                <!-- Product Name -->
                                <h3 class="mb-2 line-clamp-2 min-h-[3rem] text-base font-semibold text-gray-800 transition-colors group-hover:text-sky-600">
                                    {{ product.product_name }}
                                </h3>
                                
                                <!-- Rating -->
                                <div class="mb-3 flex items-center gap-1">
                                    <i v-for="i in 5" :key="i" class="pi pi-star-fill text-xs text-yellow-400" />
                                    <span class="ml-1 text-xs text-gray-500">(5.0)</span>
                                </div>

                                <!-- Price -->
                                <div class="mb-3">
                                    <span class="text-xl font-bold text-gray-900">
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
                    <div v-else class="py-16 text-center">
                        <i class="pi pi-search mb-4 text-6xl text-gray-300" />
                        <h3 class="mb-2 text-xl font-semibold text-gray-600">
                            No products found
                        </h3>
                        <p class="text-gray-400 mb-4">
                            Try different keywords
                        </p>
                        <button
                            @click="form.search = ''; hasSearched = false; data = [];"
                            class="rounded-full bg-sky-600 px-6 py-2 text-white transition-all hover:bg-sky-700"
                        >
                            New Search
                        </button>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import { ProductInterface } from '@/interfaces/ProductInterface';

interface IForm {
    search: string;
}

const form: IForm = reactive({
    search: ''
});

const loadService = useAxiosUtil<IForm, ProductInterface[]>();
const data = ref<ProductInterface[]>([]);
const hasSearched = ref(false);

const load = async () => {
    if (!form.search.trim()) return;
    
    await loadService.get('products/search', form).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
            hasSearched.value = true;
        }
    });
};

const handleSearch = () => {
    if (form.search.trim()) {
        load();
    }
};

const onSearchInput = () => {
    // Optional: debounce search or clear results if empty
    if (!form.search.trim()) {
        hasSearched.value = false;
        data.value = [];
    }
};
</script>