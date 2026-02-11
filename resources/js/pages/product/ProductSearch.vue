<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Page Header -->
        <div class="bg-white border-b border-gray-200">
            <div :class="getResponsiveClasses({ mobile: 'px-4 py-4', tablet: 'px-5 py-5', desktop: 'px-6 py-6 max-w-[1400px] mx-auto' })">
                <div :class="getResponsiveClasses({ mobile: 'flex flex-col gap-3', tablet: 'flex flex-col gap-4', desktop: 'flex items-end justify-between gap-6' })">
                    <div>
                        <h1 :class="getResponsiveClasses({ mobile: 'text-xl font-bold text-gray-900', tablet: 'text-2xl font-bold text-gray-900', desktop: 'text-2xl font-bold text-gray-900' })">
                            Browse Products
                        </h1>
                        <p :class="getResponsiveTextSize('sm') + ' text-gray-500 mt-1'">
                            Discover our full catalog of laptops and accessories
                        </p>
                    </div>

                    <!-- Search Bar -->
                    <div :class="getResponsiveClasses({ mobile: 'w-full', tablet: 'w-full', desktop: 'w-[420px]' })">
                        <div class="relative">
                            <i class="pi pi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input
                                v-model="form.search"
                                type="text"
                                placeholder="Search products..."
                                :class="getResponsiveTextSize('sm') + ' w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all'"
                            />
                            <button
                                v-if="form.search"
                                @click="form.search = ''; handleSearch();"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
                            >
                                <i class="pi pi-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Searches -->
                <div v-if="recentSearches.length > 0" class="mt-3 flex items-center gap-2 flex-wrap">
                    <span :class="getResponsiveTextSize('sm') + ' text-gray-400 font-medium'">Recent:</span>
                    <button
                        v-for="(search, index) in recentSearches"
                        :key="index"
                        @click="applyRecentSearch(search)"
                        class="group flex items-center gap-1.5 px-3 py-1 bg-gray-100 hover:bg-blue-50 hover:text-blue-600 rounded-full text-xs text-gray-600 transition-colors cursor-pointer"
                    >
                        <span>{{ search }}</span>
                        <i
                            @click.stop="removeRecentSearch(index)"
                            class="pi pi-times text-[10px] text-gray-400 group-hover:text-red-400 cursor-pointer"
                        />
                    </button>
                    <button
                        @click="clearRecentSearches"
                        class="text-xs text-gray-400 hover:text-red-500 transition-colors ml-1 cursor-pointer"
                    >
                        Clear all
                    </button>
                </div>
            </div>
        </div>

        <div :class="getResponsiveClasses({ mobile: 'px-4 py-4', tablet: 'px-5 py-5', desktop: 'px-6 py-6 max-w-[1400px] mx-auto' })">
            <!-- Mobile Filter Toggle -->
            <div v-if="!isDesktop" class="mb-4">
                <button
                    @click="showMobileFilters = !showMobileFilters"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full justify-between shadow-sm cursor-pointer"
                >
                    <div class="flex items-center gap-2">
                        <i class="pi pi-sliders-h text-sm text-blue-600" />
                        <span>Filters</span>
                        <span v-if="hasActiveFilters" class="w-5 h-5 flex items-center justify-center bg-blue-600 text-white text-[10px] font-bold rounded-full">
                            {{ activeFilterCount }}
                        </span>
                    </div>
                    <i :class="showMobileFilters ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-xs text-gray-400" />
                </button>
            </div>

            <div :class="getResponsiveClasses({ mobile: 'flex flex-col gap-4', tablet: 'flex flex-col gap-5', desktop: 'flex gap-6' })">
                <!-- Left Sidebar - Filters -->
                <aside :class="getResponsiveClasses({
                    mobile: showMobileFilters ? 'block mb-2' : 'hidden',
                    tablet: showMobileFilters ? 'block mb-3' : 'hidden',
                    desktop: 'w-60 flex-shrink-0 block'
                })">
                    <div :class="getResponsiveClasses({ mobile: 'bg-white rounded-xl border border-gray-200 shadow-sm', tablet: 'bg-white rounded-xl border border-gray-200 shadow-sm', desktop: 'bg-white rounded-xl border border-gray-200 shadow-sm sticky top-4' })">
                        <!-- Filter Header -->
                        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                            <h2 :class="getResponsiveTextSize('base') + ' font-bold text-gray-800 flex items-center gap-2'">
                                <i class="pi pi-filter text-blue-600 text-sm" />
                                Filters
                            </h2>
                            <button
                                v-if="hasActiveFilters"
                                @click="clearAllFilters"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium cursor-pointer"
                            >
                                Reset
                            </button>
                        </div>

                        <div class="p-5 space-y-5">
                            <!-- Categories -->
                            <div>
                                <button
                                    @click="toggleSection('categories')"
                                    class="flex items-center justify-between w-full mb-3 cursor-pointer"
                                >
                                    <h3 :class="getResponsiveTextSize('sm') + ' font-semibold text-gray-800'">Categories</h3>
                                    <i :class="expandedSections.categories ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-[10px] text-gray-400" />
                                </button>
                                <div v-show="expandedSections.categories" class="space-y-1">
                                    <label
                                        v-for="category in categories"
                                        :key="category.category_id"
                                        class="flex items-center gap-2.5 cursor-pointer hover:bg-gray-50 px-2 py-1.5 rounded-lg transition-colors"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="category.category_id"
                                            v-model="filters.selectedCategories"
                                            class="w-3.5 h-3.5 text-blue-600 rounded border-gray-300 focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                        />
                                        <span class="text-sm text-gray-600">{{ category.category_name }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="border-t border-gray-100 pt-5">
                                <button
                                    @click="toggleSection('price')"
                                    class="flex items-center justify-between w-full mb-3 cursor-pointer"
                                >
                                    <h3 :class="getResponsiveTextSize('sm') + ' font-semibold text-gray-800'">Price Range</h3>
                                    <i :class="expandedSections.price ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-[10px] text-gray-400" />
                                </button>
                                <div v-show="expandedSections.price" class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1 font-medium">Min Price</label>
                                        <input
                                            type="number"
                                            v-model.number="filters.minPrice"
                                            placeholder="₱0"
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 focus:bg-white transition-all"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1 font-medium">Max Price</label>
                                        <input
                                            type="number"
                                            v-model.number="filters.maxPrice"
                                            placeholder="₱999,999"
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 focus:bg-white transition-all"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="flex-1 min-w-0">
                    <!-- Active Filters & Results Bar -->
                    <div class="mb-4">
                        <div :class="getResponsiveClasses({ mobile: 'flex flex-col gap-3', tablet: 'flex flex-col gap-3', desktop: 'flex items-center justify-between' })">
                            <!-- Results Count -->
                            <div :class="getResponsiveTextSize('sm') + ' text-gray-500'">
                                <span v-if="filteredData.length > 0">
                                    Showing <span class="font-semibold text-gray-800">{{ (currentPage - 1) * itemsPerPage + 1 }}–{{ Math.min(currentPage * itemsPerPage, filteredData.length) }}</span>
                                    of <span class="font-semibold text-gray-800">{{ filteredData.length }}</span> results
                                </span>
                                <span v-else>0 results</span>
                                <span v-if="form.search" class="ml-1">
                                    for "<span class="text-blue-600 font-semibold">{{ form.search }}</span>"
                                </span>
                            </div>

                            <!-- Active Filter Tags -->
                            <div v-if="hasActiveFilters" class="flex items-center gap-2 flex-wrap">
                                <div v-if="form.search" class="flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-100 rounded-full text-xs text-blue-700">
                                    <span class="font-medium">{{ form.search }}</span>
                                    <button @click="removeFilter('search')" class="text-blue-400 hover:text-blue-600 cursor-pointer">
                                        <i class="pi pi-times text-[10px]" />
                                    </button>
                                </div>
                                <div
                                    v-for="categoryId in filters.selectedCategories"
                                    :key="'cat-' + categoryId"
                                    class="flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-100 rounded-full text-xs text-blue-700"
                                >
                                    <span class="font-medium">{{ getCategoryName(categoryId) }}</span>
                                    <button @click="removeFilter('category', categoryId)" class="text-blue-400 hover:text-blue-600 cursor-pointer">
                                        <i class="pi pi-times text-[10px]" />
                                    </button>
                                </div>
                                <div v-if="filters.minPrice !== null" class="flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-100 rounded-full text-xs text-blue-700">
                                    <span class="font-medium">Min: {{ CurrencyUtil.formatCurrency(filters.minPrice) }}</span>
                                    <button @click="removeFilter('minPrice')" class="text-blue-400 hover:text-blue-600 cursor-pointer">
                                        <i class="pi pi-times text-[10px]" />
                                    </button>
                                </div>
                                <div v-if="filters.maxPrice !== null" class="flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-100 rounded-full text-xs text-blue-700">
                                    <span class="font-medium">Max: {{ CurrencyUtil.formatCurrency(filters.maxPrice) }}</span>
                                    <button @click="removeFilter('maxPrice')" class="text-blue-400 hover:text-blue-600 cursor-pointer">
                                        <i class="pi pi-times text-[10px]" />
                                    </button>
                                </div>
                                <button
                                    @click="clearAllFilters"
                                    class="text-xs text-gray-400 hover:text-red-500 font-medium transition-colors cursor-pointer"
                                >
                                    Clear all
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Initial State -->
                    <div v-if="!hasSearched" class="bg-white rounded-2xl border border-gray-200 shadow-sm py-20 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-5">
                            <i class="pi pi-search text-3xl text-gray-400" />
                        </div>
                        <h3 :class="getResponsiveTextSize('lg') + ' font-bold text-gray-800 mb-2'">Start Searching</h3>
                        <p :class="getResponsiveTextSize('sm') + ' text-gray-500 max-w-sm mx-auto'">
                            Enter a product name or keyword to begin your search
                        </p>
                    </div>

                    <!-- Loading State -->
                    <div v-else-if="loading" :class="getResponsiveClasses({ mobile: 'grid grid-cols-2 gap-3', tablet: 'grid grid-cols-3 gap-4', desktop: 'grid grid-cols-2 gap-5 lg:grid-cols-3 xl:grid-cols-4' })">
                        <div v-for="i in 12" :key="i" class="animate-pulse">
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                                <div :class="getResponsiveClasses({ mobile: 'h-36', tablet: 'h-44', desktop: 'h-52' })" class="bg-gray-100" />
                                <div :class="getResponsiveClasses({ mobile: 'p-3 space-y-2', tablet: 'p-4 space-y-2.5', desktop: 'p-4 space-y-2.5' })">
                                    <div class="h-3 bg-gray-100 rounded-full w-2/3" />
                                    <div class="h-3 bg-gray-100 rounded-full w-full" />
                                    <div class="h-4 bg-gray-100 rounded-full w-1/3 mt-2" />
                                    <div class="flex gap-2 mt-3">
                                        <div class="flex-1 h-9 bg-gray-100 rounded-lg" />
                                        <div class="flex-1 h-9 bg-gray-100 rounded-lg" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div v-else-if="filteredData.length > 0">
                        <div :class="getResponsiveClasses({ mobile: 'grid grid-cols-2 gap-3 mb-6', tablet: 'grid grid-cols-3 gap-4 mb-7', desktop: 'grid grid-cols-2 gap-5 lg:grid-cols-3 xl:grid-cols-4 mb-8' })">
                            <div
                                v-for="product in paginatedData"
                                :key="product.product_id"
                                class="product-card group bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col transition-shadow duration-200 hover:shadow-md hover:border-gray-300"
                            >
                                <!-- Product Image -->
                                <RouterLink
                                    :to="{ name: 'customer.product-info.index', params: { id: product.product_id } }"
                                    :class="getResponsiveClasses({ mobile: 'relative bg-gray-50 flex items-center justify-center overflow-hidden h-36', tablet: 'relative bg-gray-50 flex items-center justify-center overflow-hidden h-44', desktop: 'relative bg-gray-50 flex items-center justify-center overflow-hidden h-52' })"
                                >
                                    <!-- Status Badges -->
                                    <div v-if="product.product_quantity <= product.low_stock_threshold || !product.is_active" class="absolute top-2.5 left-2.5 z-10">
                                        <span
                                            v-if="product.product_quantity <= product.low_stock_threshold && product.product_quantity > 0"
                                            class="inline-flex items-center gap-1 rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-semibold text-white"
                                        >
                                            <i class="pi pi-exclamation-triangle text-[8px]" />
                                            Low Stock
                                        </span>
                                        <span
                                            v-else-if="product.product_quantity === 0"
                                            class="inline-flex items-center gap-1 rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-semibold text-white"
                                        >
                                            Out of Stock
                                        </span>
                                        <span
                                            v-else-if="!product.is_active"
                                            class="inline-flex items-center gap-1 rounded-full bg-gray-500 px-2 py-0.5 text-[10px] font-semibold text-white"
                                        >
                                            Inactive
                                        </span>
                                    </div>

                                    <img
                                        v-if="product.product_image"
                                        class="max-h-full max-w-full object-contain p-3 transition-transform duration-300 group-hover:scale-105"
                                        :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                        :alt="product.product_name"
                                    />
                                    <div v-else class="flex items-center justify-center w-full h-full">
                                        <i :class="getResponsiveClasses({ mobile: 'pi pi-image text-3xl text-gray-300', tablet: 'pi pi-image text-4xl text-gray-300', desktop: 'pi pi-image text-4xl text-gray-300' })" />
                                    </div>
                                </RouterLink>

                                <!-- Product Details -->
                                <div :class="getResponsiveClasses({ mobile: 'p-3 flex flex-col flex-grow', tablet: 'p-4 flex flex-col flex-grow', desktop: 'p-4 flex flex-col flex-grow' })">
                                    <!-- Product Name -->
                                    <RouterLink
                                        :to="{ name: 'customer.product-info.index', params: { id: product.product_id } }"
                                        class="flex-grow"
                                    >
                                        <h3 :class="getResponsiveTextSize('sm') + ' font-semibold text-gray-800 line-clamp-2 hover:text-blue-600 transition-colors leading-snug mb-2'">
                                            {{ product.product_name }}
                                        </h3>
                                    </RouterLink>

                                    <!-- Rating & Sales -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="flex items-center gap-0.5">
                                            <i :class="product.rates_avg_rate ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-gray-300'" class="text-[10px]" />
                                            <span class="text-xs text-gray-500 font-medium">{{ product.rates_avg_rate ? Number(product.rates_avg_rate).toFixed(1) : '0.0' }}</span>
                                        </div>
                                        <span class="text-gray-200">|</span>
                                        <span class="text-xs text-gray-400">{{ formatSoldCount(product.total_sales || 0) }}</span>
                                    </div>

                                    <!-- Price -->
                                    <div class="mb-3">
                                        <span :class="getResponsiveClasses({ mobile: 'text-base font-bold text-gray-900', tablet: 'text-lg font-bold text-gray-900', desktop: 'text-lg font-bold text-gray-900' })">
                                            {{ CurrencyUtil.formatCurrency(product.product_price) }}
                                        </span>
                                    </div>

                                    <!-- Stock Info -->
                                    <div class="mb-3">
                                        <span class="text-xs font-medium" :class="product.product_quantity > product.low_stock_threshold ? 'text-green-600' : product.product_quantity > 0 ? 'text-amber-600' : 'text-red-500'">
                                            <i class="pi text-[10px] mr-0.5" :class="product.product_quantity > 0 ? 'pi-check-circle' : 'pi-times-circle'" />
                                            {{ product.product_quantity > 0 ? product.product_quantity + ' in stock' : 'Out of stock' }}
                                        </span>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div :class="getResponsiveClasses({ mobile: 'flex gap-2 mt-auto', tablet: 'flex gap-2 mt-auto', desktop: 'flex gap-2 mt-auto' })">
                                        <button
                                            @click="openCartModal(product, $event)"
                                            :disabled="product.product_quantity === 0 || !product.is_active || !product.available_online"
                                            :class="[
                                                'flex-1 flex items-center justify-center gap-1.5 py-2 rounded-lg text-xs font-semibold transition-colors',
                                                product.product_quantity === 0 || !product.is_active || !product.available_online
                                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                    : 'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer'
                                            ]"
                                        >
                                            <i class="pi pi-shopping-cart text-[11px]" />
                                            <span :class="getResponsiveClasses({ mobile: 'hidden', tablet: 'inline', desktop: 'inline' })">Add</span>
                                        </button>
                                        <RouterLink
                                            :to="{ name: 'customer.product-info.index', params: { id: product.product_id } }"
                                            class="flex-1"
                                        >
                                            <button class="w-full py-2 rounded-lg text-xs font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors cursor-pointer">
                                                Details
                                            </button>
                                        </RouterLink>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="totalPages > 1" class="flex justify-center items-center gap-1.5 mt-6">
                            <button
                                @click="goToPage(1)"
                                :disabled="currentPage === 1"
                                class="pagination-btn"
                                :class="currentPage === 1 ? 'pagination-btn-disabled' : 'pagination-btn-active'"
                            >
                                <i class="pi pi-angle-double-left" style="font-size: 0.625rem;" />
                            </button>
                            <button
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                class="pagination-btn"
                                :class="currentPage === 1 ? 'pagination-btn-disabled' : 'pagination-btn-active'"
                            >
                                <i class="pi pi-chevron-left" style="font-size: 0.625rem;" />
                            </button>

                            <div class="flex gap-1">
                                <button
                                    v-for="page in paginationRange"
                                    :key="page"
                                    @click="goToPage(page)"
                                    class="pagination-btn cursor-pointer"
                                    :class="currentPage === page ? 'pagination-btn-current' : 'pagination-btn-active'"
                                >
                                    {{ page }}
                                </button>
                            </div>

                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="pagination-btn"
                                :class="currentPage === totalPages ? 'pagination-btn-disabled' : 'pagination-btn-active'"
                            >
                                <i class="pi pi-chevron-right" style="font-size: 0.625rem;" />
                            </button>
                            <button
                                @click="goToPage(totalPages)"
                                :disabled="currentPage === totalPages"
                                class="pagination-btn"
                                :class="currentPage === totalPages ? 'pagination-btn-disabled' : 'pagination-btn-active'"
                            >
                                <i class="pi pi-angle-double-right" style="font-size: 0.625rem;" />
                            </button>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="bg-white rounded-2xl border border-gray-200 shadow-sm py-20 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-5">
                            <i class="pi pi-inbox text-3xl text-gray-400" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">No products found</h3>
                        <p class="text-sm text-gray-500 max-w-sm mx-auto">
                            Try adjusting your filters or search terms to find what you're looking for
                        </p>
                    </div>
                </main>
            </div>
        </div>

        <!-- Add to Cart Modal -->
        <Dialog
            v-model:visible="showCartModal"
            modal
            :style="cartModalStyle"
            :pt="{
                root: { class: 'rounded-2xl overflow-hidden' },
                header: { class: 'bg-white border-b border-gray-100 px-6 py-4' },
                content: { class: 'px-6 pt-5 pb-4' },
                footer: { class: 'px-6 pb-5 pt-0' }
            }"
        >
            <template #header>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                        <i class="pi pi-shopping-cart text-blue-600 text-sm" />
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Add to Cart</h2>
                </div>
            </template>

            <div v-if="selectedProduct">
                <!-- Product Info Row -->
                <div class="flex gap-4 items-start mb-5">
                    <!-- Product Image -->
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 bg-gray-50 rounded-xl overflow-hidden border border-gray-100 flex items-center justify-center p-2">
                            <img
                                v-if="selectedProduct.product_image"
                                :src="UrlUtil.getBaseAppUrl(`storage/images/product/${selectedProduct.product_image}`)"
                                :alt="selectedProduct.product_name"
                                class="max-w-full max-h-full object-contain"
                            />
                            <i v-else class="pi pi-image text-3xl text-gray-300" />
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 leading-snug mb-2">
                            {{ selectedProduct.product_name }}
                        </h3>
                        <p class="text-xl font-bold text-gray-900 mb-2">
                            {{ CurrencyUtil.formatCurrency(selectedProduct.product_price) }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <span class="text-green-600 font-medium">
                                <i class="pi pi-check-circle text-[10px] mr-0.5" />
                                {{ selectedProduct.product_quantity }} available
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                            <button
                                @click="decreaseQuantity"
                                :disabled="cartQuantity <= 1"
                                class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                            >
                                <i class="pi pi-minus text-xs" />
                            </button>
                            <input
                                v-model.number="cartQuantity"
                                type="number"
                                :min="1"
                                :max="selectedProduct.product_quantity"
                                class="w-12 h-9 text-center text-sm font-semibold border-x border-gray-200 focus:outline-none bg-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            />
                            <button
                                @click="increaseQuantity"
                                :disabled="cartQuantity >= selectedProduct.product_quantity"
                                class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                            >
                                <i class="pi pi-plus text-xs" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Divider & Total -->
                <div class="border-t border-gray-100 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-600">Total</span>
                        <span class="text-xl font-bold text-gray-900">
                            {{ CurrencyUtil.formatCurrency(selectedProduct.product_price * cartQuantity) }}
                        </span>
                    </div>
                </div>
            </div>

            <template #footer>
                <div class="flex gap-3 mt-2">
                    <button
                        @click="showCartModal = false"
                        class="flex-1 px-4 py-2.5 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition-colors cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmAddToCart"
                        :disabled="addToCartService.request.loading"
                        class="flex-1 px-4 py-2.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-xl font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <i v-if="addToCartService.request.loading" class="pi pi-spin pi-spinner text-sm" />
                        <i v-else class="pi pi-shopping-cart text-sm" />
                        <span>Add to Cart</span>
                    </button>
                </div>
            </template>
        </Dialog>
    </div>
</template>


<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch, inject } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import { ProductInterface } from '@/interfaces/ProductInterface';
import { CartFormInterface } from '@/interfaces/CartInterface';
import { useToast } from 'vue-toastification';
import Page from '@/stores/Page';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputNumber from 'primevue/inputnumber';
import { useResponsive } from '@/composables/useResponsive';

const toast = useToast();

// Use responsive composable
const { isMobile, isTablet, isDesktop, getResponsiveClasses, getResponsivePadding, getResponsiveMargin, getResponsiveTextSize, getResponsiveGap } = useResponsive();

// Mobile filter sidebar state
const showMobileFilters = ref(false);



interface IForm {
    search: string;
}

interface Category {
    category_id: number;
    category_name: string;
}

const route = useRoute();
const router = useRouter();

const form: IForm = reactive({
    search: ''
});

const filters = reactive({
    selectedCategories: [] as number[],
    minPrice: null as number | null,
    maxPrice: null as number | null
});

const expandedSections = reactive({
    categories: true,
    price: true
});

const loadService = useAxiosUtil<IForm, ProductInterface[]>();
const loadCategoryProductsService = useAxiosUtil<null, ProductInterface[]>();
const loadCategoriesService = useAxiosUtil<null, Category[]>();
const addToCartService = useAxiosUtil<CartFormInterface, null>();
const data = ref<ProductInterface[]>([]);
const categories = ref<Category[]>([]);
const hasSearched = ref(false);
const recentSearches = ref<string[]>([]);
const loading = ref(false);
const showCartModal = ref(false);
const selectedProduct = ref<ProductInterface | null>(null);
const cartQuantity = ref(1);
const currentPage = ref(1);
const itemsPerPage = 15;

const cartModalStyle = computed(() => {
    if (isMobile.value) {
        return { width: '95vw', maxWidth: '380px' };
    }
    return { width: '420px' };
});

const filteredData = computed(() => {
    // Ensure data.value is an array
    if (!Array.isArray(data.value)) {
        return [];
    }
    
    let filtered = [...data.value];

    // Filter by categories
    if (filters.selectedCategories.length > 0) {
        filtered = filtered.filter(product => 
            filters.selectedCategories.includes(product.category_id)
        );
    }

    // Filter by price range
    if (filters.minPrice !== null) {
        filtered = filtered.filter(product => product.product_price >= filters.minPrice!);
    }
    if (filters.maxPrice !== null) {
        filtered = filtered.filter(product => product.product_price <= filters.maxPrice!);
    }

    // Filter only available online products
    filtered = filtered.filter(product => product.available_online);

    return filtered;
});

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredData.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / itemsPerPage);
});

const paginationRange = computed(() => {
    const range = [];
    const delta = 2;
    const left = currentPage.value - delta;
    const right = currentPage.value + delta + 1;
    
    for (let i = 1; i <= totalPages.value; i++) {
        if (i === 1 || i === totalPages.value || (i >= left && i < right)) {
            range.push(i);
        }
    }
    
    return range;
});

const hasActiveFilters = computed(() => {
    return form.search || 
           filters.selectedCategories.length > 0 || 
           filters.minPrice !== null ||
           filters.maxPrice !== null;
});

const activeFilterCount = computed(() => {
    let count = 0;
    if (form.search) count++;
    count += filters.selectedCategories.length;
    if (filters.minPrice !== null) count++;
    if (filters.maxPrice !== null) count++;
    return count;
});

const load = async () => {
    if (!form.search.trim()) return;
    
    loading.value = true;
    await loadService.get('products/search', form).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
            hasSearched.value = true;
            addToRecentSearches(form.search);
        }
        loading.value = false;
    });
};

const loadCategoryProducts = async (categoryId: string, autoSelectCategory: boolean = true) => {
    loading.value = true;
    hasSearched.value = true;
    await loadCategoryProductsService.get(`products/category/${categoryId}`).then(() => {
        if (loadCategoryProductsService.request.status === 200 && loadCategoryProductsService.request.data) {
            data.value = loadCategoryProductsService.request.data;
            
            if (autoSelectCategory) {
                const catId = parseInt(categoryId);
                if (!filters.selectedCategories.includes(catId)) {
                    filters.selectedCategories.push(catId);
                }
            }
        }
        loading.value = false;
    });
};

const loadAllProducts = async () => {
    loading.value = true;
    hasSearched.value = true;
    await loadCategoryProductsService.get('products', { limit: 1000 }).then(() => {
        if (loadCategoryProductsService.request.status === 200 && loadCategoryProductsService.request.data) {
            const responseData = loadCategoryProductsService.request.data;
            data.value = responseData.data || responseData;
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

const handleSearch = () => {
    if (form.search.trim()) {
        load();
    } else {
        loadAllProducts();
    }
};

const toggleSection = (section: keyof typeof expandedSections) => {
    expandedSections[section] = !expandedSections[section];
};

const applyFilters = () => {
    currentPage.value = 1;
};

const applyPriceFilter = (type: 'min' | 'max') => {
    // Price filters are reactive
};

const goToPage = (page: number) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const removeFilter = (type: string, value?: any) => {
    switch (type) {
        case 'search':
            form.search = '';
            router.replace({ query: {} });
            if (!route.params.id && route.name !== 'customer.browse-products') {
                loadAllProducts();
            }
            break;
        case 'category':
            filters.selectedCategories = filters.selectedCategories.filter(id => id !== value);
            if (route.params.id && filters.selectedCategories.length === 0) {
                loadCategoryProducts(route.params.id as string, false);
            }
            break;
        case 'minPrice':
            filters.minPrice = null;
            break;
        case 'maxPrice':
            filters.maxPrice = null;
            break;
    }
};

const clearAllFilters = () => {
    form.search = '';
    filters.selectedCategories = [];
    filters.minPrice = null;
    filters.maxPrice = null;
    
    router.replace({ query: {} });
    
    if (route.params.id) {
        loadCategoryProducts(route.params.id as string, false);
    } else {
        loadAllProducts();
    }
};

const getCategoryName = (categoryId: number) => {
    const category = categories.value.find(c => c.category_id === categoryId);
    return category ? category.category_name : '';
};

const addToRecentSearches = (search: string) => {
    const trimmed = search.trim();
    if (!trimmed) return;
    
    recentSearches.value = recentSearches.value.filter(s => s !== trimmed);
    recentSearches.value.unshift(trimmed);
    
    if (recentSearches.value.length > 5) {
        recentSearches.value = recentSearches.value.slice(0, 5);
    }
    
    localStorage.setItem('recentSearches', JSON.stringify(recentSearches.value));
};

const applyRecentSearch = (search: string) => {
    form.search = search;
    handleSearch();
};

const removeRecentSearch = (index: number) => {
    recentSearches.value.splice(index, 1);
    localStorage.setItem('recentSearches', JSON.stringify(recentSearches.value));
};

const clearRecentSearches = () => {
    recentSearches.value = [];
    localStorage.removeItem('recentSearches');
};

const formatSoldCount = (count: number): string => {
    if (count <= 1000) {
        return `${count} sold`;
    } else {
        const thousands = Math.floor(count / 1000);
        return `${thousands}K+ sold`;
    }
};

const openCartModal = (product: ProductInterface, event: Event) => {
    event.preventDefault();
    event.stopPropagation();
    
    if (!Page.user) {
        router.push({ name: 'auth.login' });
        return;
    }

    selectedProduct.value = product;
    cartQuantity.value = 1;
    showCartModal.value = true;
};

const increaseQuantity = () => {
    if (selectedProduct.value && cartQuantity.value < selectedProduct.value.product_quantity) {
        cartQuantity.value++;
    }
};

const decreaseQuantity = () => {
    if (cartQuantity.value > 1) {
        cartQuantity.value--;
    }
};

const confirmAddToCart = async () => {
    if (!selectedProduct.value) return;

    const cartData: CartFormInterface = {
        product_id: selectedProduct.value.product_id,
        quantity: cartQuantity.value
    };

    await addToCartService.post('customer/carts', cartData).then(() => {
        if (addToCartService.request.status === 200) {
            toast.success(`Added ${cartQuantity.value} ${selectedProduct.value?.product_name} to cart`);
            showCartModal.value = false;
            selectedProduct.value = null;
            cartQuantity.value = 1;
        }
    }).catch(() => {
        toast.error(addToCartService.request.message || 'Failed to add product to cart');
    });
};

// Watch for quantity changes to ensure it doesn't exceed stock
watch(cartQuantity, (newQuantity) => {
    if (selectedProduct.value) {
        if (newQuantity > selectedProduct.value.product_quantity) {
            cartQuantity.value = selectedProduct.value.product_quantity;
            toast.warning(`Maximum available quantity is ${selectedProduct.value.product_quantity}`);
        } else if (newQuantity < 1) {
            cartQuantity.value = 1;
        }
    }
});

// Watch for route changes to handle category navigation
watch(() => route.params.id, (newId) => {
    if (newId) {
        clearAllFilters();
        loadCategoryProducts(newId as string);
    }
}, { immediate: true });

// Variables for search watchers
let searchTimeout: number | null = null;
let isUpdatingFromRoute = false;

// Watch for query parameter changes to handle search from navbar
watch(() => route.query.q, (newQuery, oldQuery) => {
    if (newQuery && typeof newQuery === 'string') {
        isUpdatingFromRoute = true;
        form.search = newQuery;
        handleSearch();
    } else {
        isUpdatingFromRoute = true;
        form.search = '';
        if (route.name === 'customer.browse-products') {
            loadAllProducts();
        }
    }
}, { immediate: true });

// Watch for form.search changes with debounce for real-time search
watch(() => form.search, (newValue, oldValue) => {
    if (isUpdatingFromRoute) {
        isUpdatingFromRoute = false;
        return;
    }
    
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    searchTimeout = window.setTimeout(() => {
        const trimmedValue = newValue ? newValue.trim() : '';
        const currentQuery = route.query.q as string | undefined;
        
        if (trimmedValue && trimmedValue !== currentQuery) {
            if (route.name !== 'customer.search-product') {
                router.push({ name: 'customer.search-product', query: { q: trimmedValue } });
            } else {
                router.replace({ query: { q: trimmedValue } });
                handleSearch();
            }
        } else if (!trimmedValue && currentQuery) {
            router.push({ name: 'customer.browse-products' });
        }
    }, 500);
});

// Watch for route name changes
watch(() => route.name, (newName, oldName) => {
    if (newName === 'customer.browse-products' && oldName && oldName !== 'customer.browse-products') {
        form.search = '';
        filters.selectedCategories = [];
        filters.minPrice = null;
        filters.maxPrice = null;
        currentPage.value = 1;
        router.replace({ query: {} });
        loadAllProducts();
    }
});

// Watch for category filter changes
watch(() => filters.selectedCategories, (newCategories, oldCategories) => {
    if (oldCategories && oldCategories.length > 0 && newCategories.length === 0) {
        if (route.params.id) {
            loadAllProducts();
        }
    }
    currentPage.value = 1;
}, { deep: true });

// Reset to page 1 when price filters change
watch([() => filters.minPrice, () => filters.maxPrice], () => {
    currentPage.value = 1;
}, { deep: true });

onMounted(() => {
    loadCategories();
    
    const saved = localStorage.getItem('recentSearches');
    if (saved) {
        try {
            recentSearches.value = JSON.parse(saved);
        } catch (e) {
            recentSearches.value = [];
        }
    }
    
    if (route.name === 'customer.browse-products') {
        form.search = '';
        filters.selectedCategories = [];
        filters.minPrice = null;
        filters.maxPrice = null;
        currentPage.value = 1;
        loadAllProducts();
    } else if (!route.params.id) {
        filters.selectedCategories = [];
        filters.minPrice = null;
        filters.maxPrice = null;
        currentPage.value = 1;
        
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('q');
        if (searchQuery) {
            form.search = searchQuery;
            handleSearch();
        } else {
            form.search = '';
            hasSearched.value = false;
            data.value = [];
        }
    }
});
</script>

<style scoped>
@reference "tailwindcss";
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Pagination buttons */
.pagination-btn {
    @apply w-8 h-8 rounded-full flex items-center justify-center text-xs transition-all;
}
.pagination-btn-active {
    @apply bg-white text-gray-600 hover:bg-gray-100 border border-gray-200 cursor-pointer;
}
.pagination-btn-disabled {
    @apply bg-gray-100 text-gray-300 cursor-not-allowed border border-gray-100;
}
.pagination-btn-current {
    @apply bg-blue-600 text-white font-semibold border border-blue-600;
}

/* Product card subtle hover */
.product-card {
    will-change: box-shadow;
}
</style>
