<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Search Header -->
        <div class="bg-white shadow-sm sticky top-0 z-10">
            <div class="container mx-auto px-4 py-6">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <!-- Search Bar -->
                    <div class="flex-1 w-full">
                        <div class="relative">
                            <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search products by name, description, or SKU..."
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                @input="handleSearch"
                            />
                            <button
                                v-if="searchQuery"
                                @click="clearSearch"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <i class="pi pi-times" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Filters -->
                <div v-if="searchQuery || selectedCategory" class="mt-4 flex flex-wrap gap-2 items-center">
                    <span class="text-sm text-gray-600">Active filters:</span>
                    <div v-if="searchQuery" class="flex items-center gap-2 bg-sky-100 text-sky-700 px-3 py-1 rounded-full text-sm">
                        <span>Search: "{{ searchQuery }}"</span>
                        <button @click="clearSearch" class="hover:text-sky-900">
                            <i class="pi pi-times text-xs" />
                        </button>
                    </div>
                    <div v-if="selectedCategory" class="flex items-center gap-2 bg-sky-100 text-sky-700 px-3 py-1 rounded-full text-sm">
                        <span>Category: {{ getCategoryName(selectedCategory) }}</span>
                        <button @click="clearCategory" class="hover:text-sky-900">
                            <i class="pi pi-times text-xs" />
                        </button>
                    </div>
                    <button
                        @click="clearAllFilters"
                        class="text-sm text-red-600 hover:text-red-700 underline"
                    >
                        Clear all
                    </button>
                </div>

                <!-- Results Count -->
                <div class="mt-4 text-sm text-gray-600">
                    Showing <strong>{{ filteredProducts.length }}</strong> product{{ filteredProducts.length !== 1 ? 's' : '' }}
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <!-- Loading State -->
            <div v-if="loading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                <div v-for="i in 10" :key="i" class="animate-pulse">
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

            <!-- Empty State -->
            <div v-else-if="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-20 bg-white rounded-xl shadow-sm">
                <i class="pi pi-search text-6xl text-gray-300 mb-4" />
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No products found</h3>
                <p class="text-gray-500 mb-4">
                    {{ searchQuery ? `No results for "${searchQuery}"` : 'Try adjusting your filters' }}
                </p>
                <button
                    @click="clearAllFilters"
                    class="px-6 py-2 bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition-colors"
                >
                    Clear Filters
                </button>
            </div>

            <!-- Products Grid -->
            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                <div
                    v-for="product in filteredProducts"
                    :key="product.product_id"
                    class="group relative overflow-hidden rounded-2xl bg-white shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                >
                    <!-- Stock Badge -->
                    <div class="absolute left-3 top-3 z-10">
                        <span
                            v-if="product.product_quantity <= product.low_stock_threshold && product.product_quantity > 0"
                            class="inline-block rounded-full bg-orange-500 px-2 py-1 text-xs font-semibold text-white"
                        >
                            Low Stock
                        </span>
                        <span
                            v-else-if="product.product_quantity === 0"
                            class="inline-block rounded-full bg-red-500 px-2 py-1 text-xs font-semibold text-white"
                        >
                            Out of Stock
                        </span>
                        <span
                            v-else-if="!product.is_active"
                            class="inline-block rounded-full bg-gray-500 px-2 py-1 text-xs font-semibold text-white"
                        >
                            Inactive
                        </span>
                    </div>

                    <!-- Product Image -->
                    <div class="relative overflow-hidden bg-gray-50">
                        <img
                            v-if="product.product_image"
                            class="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-110"
                            :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
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
                                    View Details
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

                        <!-- SKU -->
                        <div v-if="product.sku" class="mb-1">
                            <span class="text-xs text-gray-500">SKU: {{ product.sku }}</span>
                        </div>

                        <!-- Product Name -->
                        <h3 class="mb-2 line-clamp-2 min-h-[2.5rem] text-sm font-semibold text-gray-800 transition-colors group-hover:text-sky-600">
                            {{ product.product_name }}
                        </h3>
                        
                        <!-- Description -->
                        <p class="mb-3 text-xs text-gray-500 line-clamp-2 min-h-[2rem]">
                            {{ product.description }}
                        </p>

                        <!-- Stock Info -->
                        <div class="mb-3 flex items-center justify-between text-xs">
                            <span class="text-gray-500">
                                <i class="pi pi-box mr-1" />
                                Stock: {{ product.product_quantity }} {{ product.unit?.unit_name || '' }}
                            </span>
                        </div>

                        <!-- Price -->
                        <div class="mb-3">
                            <span class="text-lg font-bold text-gray-900">
                                {{ CurrencyUtil.formatCurrency(product.product_price) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                / {{ product.unit?.unit_name || 'unit' }}
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
                            <button 
                                class="w-full rounded-xl py-2.5 font-semibold text-white transition-all"
                                :class="product.product_quantity === 0 || !product.is_active || !product.available_online
                                    ? 'bg-gray-400 cursor-not-allowed'
                                    : 'bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 hover:shadow-lg'"
                                :disabled="product.product_quantity === 0 || !product.is_active || !product.available_online"
                            >
                                {{ product.product_quantity === 0 ? 'Out of Stock' : !product.available_online ? 'Not Available Online' : 'View Details' }}
                            </button>
                        </RouterLink>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import UrlUtil from '@/utils/UrlUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import useAxiosUtil from '@/utils/AxiosUtil';
import { ProductInterface } from '@/interfaces/ProductInterface';
import { useRoute } from 'vue-router';

interface Category {
    category_id: number;
    category_name: string;
}

const loadProductsService = useAxiosUtil<null, ProductInterface[]>();
const loadCategoriesService = useAxiosUtil<null, Category[]>();

const products = ref<ProductInterface[]>([]);
const categories = ref<Category[]>([]);
const searchQuery = ref('');
const selectedCategory = ref<number | null>(null);
const sortBy = ref('name_asc');
const loading = ref(false);

const filteredProducts = computed(() => {
    let filtered = [...products.value];

    // Filter by search query
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase().trim();
        filtered = filtered.filter(product => 
            product.product_name.toLowerCase().includes(query) ||
            product.description.toLowerCase().includes(query) ||
            (product.sku && product.sku.toLowerCase().includes(query))
        );
    }

    // Filter by category
    if (selectedCategory.value !== null) {
        filtered = filtered.filter(product => product.category_id === selectedCategory.value);
    }

    // Filter only available online products
    filtered = filtered.filter(product => product.available_online);

    // Sort products
    switch (sortBy.value) {
        case 'name_asc':
            filtered.sort((a, b) => a.product_name.localeCompare(b.product_name));
            break;
        case 'name_desc':
            filtered.sort((a, b) => b.product_name.localeCompare(a.product_name));
            break;
        case 'price_asc':
            filtered.sort((a, b) => a.product_price - b.product_price);
            break;
        case 'price_desc':
            filtered.sort((a, b) => b.product_price - a.product_price);
            break;
        case 'newest':
            filtered.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
            break;
    }

    return filtered;
});

const handleSearch = () => {
    // Debounce could be added here if needed
};

const handleCategoryChange = () => {
    // Category filter is reactive
};

const handleSort = () => {
    // Sort is reactive
};

const clearSearch = () => {
    searchQuery.value = '';
};

const clearCategory = () => {
    selectedCategory.value = null;
};

const clearAllFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = null;
    sortBy.value = 'name_asc';
};

const route = useRoute();

const getCategoryName = (categoryId: number) => {
    const category = categories.value.find(c => c.category_id === categoryId);
    return category ? category.category_name : '';
};

const loadProducts = async () => {
    loading.value = true;
    await loadProductsService.get(`products/category/${route.params['id']}`).then(() => {
        if (loadProductsService.request.status === 200 && loadProductsService.request.data) {
            products.value = loadProductsService.request.data;
        }
        loading.value = false;
    });
};

const loadCategories = async () => {
    await loadCategoriesService.get('categories').then(() => {
        if (loadCategoriesService.request.status === 200 && loadCategoriesService.request.data) {
            categories.value = loadCategoriesService.request.data;
        }
    });
};

onMounted(() => {
    loadProducts();
    loadCategories();
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}
</style>