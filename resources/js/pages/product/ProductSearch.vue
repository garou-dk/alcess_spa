<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Top Bar with Recent Searches -->
        <div class="bg-white border-b">
            <div :class="getResponsiveClasses({ mobile: 'px-3 py-3', tablet: 'px-4 py-3', desktop: 'px-6 py-4' })">
                <div :class="getResponsiveClasses({ mobile: 'flex-col gap-3', tablet: 'flex-col gap-3', desktop: 'flex items-center justify-between' })">
                    <!-- Recent Searches -->
                    <div :class="getResponsiveClasses({ mobile: 'flex flex-col gap-2', tablet: 'flex flex-col gap-2', desktop: 'flex items-center gap-3 flex-1' })">
                        <span :class="getResponsiveTextSize('sm') + ' text-gray-600 font-medium whitespace-nowrap'">Recent Searches:</span>
                        <div class="flex gap-2 flex-wrap">
                            <button
                                v-for="(search, index) in recentSearches"
                                :key="index"
                                @click="applyRecentSearch(search)"
                                :class="getResponsiveClasses({ mobile: 'px-2 py-1', tablet: 'px-2.5 py-1.5', desktop: 'px-3 py-1.5' }) + ' group flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-full text-sm transition-colors'"
                            >
                                <span class="text-gray-700">{{ search }}</span>
                                <i 
                                    @click.stop="removeRecentSearch(index)"
                                    class="pi pi-times text-xs text-gray-500 hover:text-red-600 cursor-pointer"
                                />
                            </button>
                        </div>
                    </div>
                    
                    <!-- Clear Searches -->
                    <div class="flex-shrink-0">
                        <button
                            v-if="recentSearches.length > 0"
                            @click="clearRecentSearches"
                            :class="getResponsiveTextSize('sm') + ' text-gray-500 hover:text-red-600 flex items-center gap-1 transition-colors whitespace-nowrap'"
                        >
                            <span>Clear Searches</span>
                            <i class="pi pi-trash text-xs" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div :class="getResponsiveClasses({ mobile: 'px-3 py-4', tablet: 'px-4 py-5', desktop: 'px-6 py-6' })">
            <!-- Mobile Filter Toggle Button -->
            <div v-if="!isDesktop" class="mb-4">
                <button
                    @click="showMobileFilters = !showMobileFilters"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    <i class="pi pi-filter text-sm" />
                    <span>Filters</span>
                    <i :class="showMobileFilters ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-xs ml-auto" />
                </button>
            </div>

            <div :class="getResponsiveClasses({ mobile: 'flex flex-col gap-4', tablet: 'flex flex-col gap-5', desktop: 'flex gap-6' })">
                <!-- Left Sidebar - Filters -->
                <aside :class="getResponsiveClasses({ 
                    mobile: showMobileFilters ? 'block mb-4' : 'hidden',
                    tablet: showMobileFilters ? 'block mb-5' : 'hidden', 
                    desktop: 'w-64 flex-shrink-0 block'
                })">
                    <div :class="getResponsiveClasses({ mobile: 'bg-white rounded-lg shadow-sm border border-gray-200 p-4', tablet: 'bg-white rounded-xl shadow-sm border border-gray-200 p-5', desktop: 'bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6' })">
                        <h2 :class="getResponsiveTextSize('lg') + ' font-bold text-gray-800 mb-4'">Filter</h2>
                        
                        <!-- Categories -->
                        <div :class="getResponsiveMargin()">
                            <button
                                @click="toggleSection('categories')"
                                class="flex items-center justify-between w-full mb-3"
                            >
                                <h3 :class="getResponsiveTextSize('base') + ' font-semibold text-gray-800 flex-1 text-left'">Categories</h3>
                                <i :class="expandedSections.categories ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-xs text-gray-600 flex-shrink-0" />
                            </button>
                            <div v-show="expandedSections.categories" class="space-y-1">
                                <label
                                    v-for="category in categories"
                                    :key="category.category_id"
                                    class="flex items-start gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors w-full"
                                >
                                    <input
                                        type="checkbox"
                                        :value="category.category_id"
                                        v-model="filters.selectedCategories"
                                        class="w-4 h-4 mt-0.5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 flex-shrink-0 cursor-pointer"
                                    />
                                    <span :class="getResponsiveTextSize('sm') + ' text-gray-700 leading-tight flex-1'">{{ category.category_name }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div :class="getResponsiveMargin()">
                            <button
                                @click="toggleSection('price')"
                                class="flex items-center justify-between w-full mb-3"
                            >
                                <h3 :class="getResponsiveTextSize('base') + ' font-semibold text-gray-800 flex-1 text-left'">Price</h3>
                                <i :class="expandedSections.price ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-xs text-gray-600 flex-shrink-0" />
                            </button>
                            <div v-show="expandedSections.price" class="space-y-3">
                                <div class="w-full">
                                    <label :class="getResponsiveTextSize('sm') + ' block text-gray-600 mb-1'">Minimum:</label>
                                    <input
                                        type="number"
                                        v-model.number="filters.minPrice"
                                        placeholder="₱0"
                                        :class="getResponsiveTextSize('sm') + ' w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500'"
                                    />
                                </div>
                                <div class="w-full">
                                    <label :class="getResponsiveTextSize('sm') + ' block text-gray-600 mb-1'">Maximum:</label>
                                    <input
                                        type="number"
                                        v-model.number="filters.maxPrice"
                                        placeholder="₱999,999"
                                        :class="getResponsiveTextSize('sm') + ' w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500'"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="flex-1">
                    <!-- Active Filters & Results -->
                    <div :class="getResponsivePadding() + ' mb-4'">
                        <!-- Results Count -->
                        <div :class="getResponsiveMargin()">
                            <h2 class="text-gray-800">
                                <span :class="getResponsiveTextSize('lg') + ' font-bold'">Showing</span>
                                <span :class="getResponsiveTextSize('sm') + ' font-normal ml-2'">
                                    <span v-if="filteredData.length > 0">
                                        {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredData.length) }} out of {{ filteredData.length }}
                                    </span>
                                    <span v-else>0</span>
                                    results
                                </span>
                                <span v-if="form.search" :class="getResponsiveTextSize('sm') + ' ml-1'">for <span class="text-blue-600 font-semibold">"{{ form.search }}"</span></span>
                            </h2>
                        </div>

                        <!-- Active Filters Tags -->
                        <div v-if="hasActiveFilters" :class="getResponsiveClasses({ mobile: 'flex flex-col gap-3', tablet: 'flex flex-col gap-3', desktop: 'flex items-center justify-between' })">
                            <!-- Filter Tags -->
                            <div class="flex flex-wrap gap-2 flex-1">
                                <!-- Search Query -->
                                <div v-if="form.search" :class="getResponsiveClasses({ mobile: 'px-2 py-1', tablet: 'px-2.5 py-1.5', desktop: 'px-3 py-1.5' }) + ' flex items-center gap-2 bg-gray-100 rounded-full text-sm'">
                                    <span class="text-gray-700">{{ form.search }}</span>
                                    <button @click="removeFilter('search')" class="text-gray-500 hover:text-red-600">
                                        <i class="pi pi-times text-xs" />
                                    </button>
                                </div>

                                <!-- Category Filters -->
                                <div
                                    v-for="categoryId in filters.selectedCategories"
                                    :key="'cat-' + categoryId"
                                    :class="getResponsiveClasses({ mobile: 'px-2 py-1', tablet: 'px-2.5 py-1.5', desktop: 'px-3 py-1.5' }) + ' flex items-center gap-2 bg-gray-100 rounded-full text-sm'"
                                >
                                    <span class="text-gray-700">{{ getCategoryName(categoryId) }}</span>
                                    <button @click="removeFilter('category', categoryId)" class="text-gray-500 hover:text-red-600">
                                        <i class="pi pi-times text-xs" />
                                    </button>
                                </div>

                                <!-- Price Filters -->
                                <div v-if="filters.minPrice !== null" :class="getResponsiveClasses({ mobile: 'px-2 py-1', tablet: 'px-2.5 py-1.5', desktop: 'px-3 py-1.5' }) + ' flex items-center gap-2 bg-gray-100 rounded-full text-sm'">
                                    <span class="text-gray-700">Minimum Price: {{ CurrencyUtil.formatCurrency(filters.minPrice) }}</span>
                                    <button @click="removeFilter('minPrice')" class="text-gray-500 hover:text-red-600">
                                        <i class="pi pi-times text-xs" />
                                    </button>
                                </div>
                                <div v-if="filters.maxPrice !== null" :class="getResponsiveClasses({ mobile: 'px-2 py-1', tablet: 'px-2.5 py-1.5', desktop: 'px-3 py-1.5' }) + ' flex items-center gap-2 bg-gray-100 rounded-full text-sm'">
                                    <span class="text-gray-700">Maximum Price: {{ CurrencyUtil.formatCurrency(filters.maxPrice) }}</span>
                                    <button @click="removeFilter('maxPrice')" class="text-gray-500 hover:text-red-600">
                                        <i class="pi pi-times text-xs" />
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Clear All -->
                            <div :class="getResponsiveClasses({ mobile: 'flex-shrink-0', tablet: 'flex-shrink-0', desktop: 'flex-shrink-0 ml-4' })">
                                <button
                                    @click="clearAllFilters"
                                    :class="getResponsiveTextSize('sm') + ' text-blue-600 hover:text-blue-700 font-medium underline whitespace-nowrap'"
                                >
                                    Clear All Filters
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Initial State - Before Search -->
                    <div v-if="!hasSearched" :class="getResponsiveClasses({ mobile: 'bg-white rounded-lg shadow-sm border py-16 text-center', tablet: 'bg-white rounded-xl shadow-sm border py-20 text-center', desktop: 'bg-white rounded-xl shadow-sm border py-24 text-center' })">
                        <i :class="getResponsiveClasses({ mobile: 'pi pi-search mb-3 text-5xl text-gray-300', tablet: 'pi pi-search mb-4 text-6xl text-gray-300', desktop: 'pi pi-search mb-4 text-7xl text-gray-300' })" />
                        <h3 :class="getResponsiveTextSize('xl') + ' mb-2 font-semibold text-gray-600'">
                            Start Searching
                        </h3>
                        <p :class="getResponsiveTextSize('base') + ' text-gray-400'">
                            Enter a product name or keyword to begin your search
                        </p>
                    </div>

                    <!-- Loading State -->
                    <div v-else-if="loading" :class="getResponsiveClasses({ mobile: 'grid grid-cols-2 gap-3', tablet: 'grid grid-cols-3 gap-4', desktop: 'grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4' })">
                        <div v-for="i in 15" :key="i" class="animate-pulse">
                            <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-gray-200" style="aspect-ratio: 1/1.4;">
                                <div class="h-full flex flex-col">
                                    <!-- Image skeleton (45% height) -->
                                    <div class="bg-gray-200" style="height: 45%;" />
                                    <!-- Content skeleton (55% height) -->
                                    <div :class="getResponsiveClasses({ mobile: 'p-2 pb-3 space-y-1', tablet: 'p-3 pb-4 space-y-2', desktop: 'p-4 pb-6 space-y-2' })" style="height: 55%;">
                                        <div class="h-4 bg-gray-200 rounded w-1/2" />
                                        <div class="h-3 bg-gray-200 rounded w-full" />
                                        <div class="h-3 bg-gray-200 rounded w-3/4" />
                                        <div class="h-3 bg-gray-200 rounded w-1/2 mt-2" />
                                        <div class="flex gap-1 mt-auto pt-4">
                                            <div class="flex-1 h-6 bg-gray-200 rounded" />
                                            <div class="flex-1 h-6 bg-gray-200 rounded" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div v-else-if="filteredData.length > 0">
                        <div :class="getResponsiveClasses({ mobile: 'grid grid-cols-2 gap-3 mb-6', tablet: 'grid grid-cols-3 gap-4 mb-7', desktop: 'grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 mb-8' })">
                            <div
                                v-for="product in paginatedData"
                                :key="product.product_id"
                                class="group bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg flex flex-col"
                            >
                                <!-- Upper Part - Product Image -->
                                <div :class="getResponsiveClasses({ mobile: 'relative bg-gray-50 flex items-center justify-center overflow-hidden h-40', tablet: 'relative bg-gray-50 flex items-center justify-center overflow-hidden h-48', desktop: 'relative bg-gray-50 flex items-center justify-center overflow-hidden h-56' })">
                                    <!-- Stock Badge -->
                                    <div v-if="product.product_quantity <= product.low_stock_threshold || !product.is_active" class="absolute top-2 left-2 z-10">
                                        <span
                                            v-if="product.product_quantity <= product.low_stock_threshold && product.product_quantity > 0"
                                            :class="getResponsiveClasses({ mobile: 'inline-block rounded-full bg-orange-500 px-1.5 py-0.5 text-xs font-semibold text-white', tablet: 'inline-block rounded-full bg-orange-500 px-2 py-0.5 text-xs font-semibold text-white', desktop: 'inline-block rounded-full bg-orange-500 px-2 py-0.5 text-xs font-semibold text-white' })"
                                        >
                                            Low Stock
                                        </span>
                                        <span
                                            v-else-if="product.product_quantity === 0"
                                            :class="getResponsiveClasses({ mobile: 'inline-block rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-semibold text-white', tablet: 'inline-block rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white', desktop: 'inline-block rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white' })"
                                        >
                                            Out of Stock
                                        </span>
                                        <span
                                            v-else-if="!product.is_active"
                                            :class="getResponsiveClasses({ mobile: 'inline-block rounded-full bg-gray-500 px-1.5 py-0.5 text-xs font-semibold text-white', tablet: 'inline-block rounded-full bg-gray-500 px-2 py-0.5 text-xs font-semibold text-white', desktop: 'inline-block rounded-full bg-gray-500 px-2 py-0.5 text-xs font-semibold text-white' })"
                                        >
                                            Inactive
                                        </span>
                                    </div>

                                    <RouterLink
                                        :to="{
                                            name: 'customer.product-info.index',
                                            params: { id: product.product_id },
                                        }"
                                        :class="getResponsiveClasses({ mobile: 'w-full h-full flex items-center justify-center p-2', tablet: 'w-full h-full flex items-center justify-center p-3', desktop: 'w-full h-full flex items-center justify-center p-4' })"
                                    >
                                        <img
                                            v-if="product.product_image"
                                            class="max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-110"
                                            :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                            :alt="product.product_name"
                                        />
                                        <div
                                            v-else
                                            class="flex items-center justify-center w-full h-full"
                                        >
                                            <i :class="getResponsiveClasses({ mobile: 'pi pi-image text-3xl text-gray-300', tablet: 'pi pi-image text-4xl text-gray-300', desktop: 'pi pi-image text-5xl text-gray-300' })" />
                                        </div>
                                    </RouterLink>
                                </div>

                                <!-- Lower Part - Product Details -->
                                <div :class="getResponsiveClasses({ mobile: 'p-2 flex flex-col flex-grow', tablet: 'p-3 flex flex-col flex-grow', desktop: 'p-4 flex flex-col flex-grow' })">
                                    <!-- Product Name -->
                                    <RouterLink
                                        :to="{
                                            name: 'customer.product-info.index',
                                            params: { id: product.product_id },
                                        }"
                                        class="flex-grow"
                                    >
                                        <h3 :class="getResponsiveTextSize('sm') + ' font-medium text-gray-800 line-clamp-3 hover:text-blue-600 transition-colors mb-2 min-h-[2.5rem]'">
                                            {{ product.product_name }}
                                        </h3>
                                    </RouterLink>

                                    <!-- Price -->
                                    <div :class="getResponsiveMargin()">
                                        <span :class="getResponsiveTextSize('base') + ' font-bold text-gray-900'">
                                            {{ CurrencyUtil.formatCurrency(product.product_price) }}
                                        </span>
                                    </div>

                                    <!-- Quantity, Rating & Units Sold -->
                                    <div :class="getResponsiveClasses({ mobile: 'flex items-center justify-between text-xs text-gray-500 mb-2 w-full', tablet: 'flex items-center justify-between text-xs text-gray-500 mb-3 w-full', desktop: 'flex items-center justify-between text-xs text-gray-500 mb-4 w-full' })">
                                        <div class="flex flex-col gap-1 flex-1">
                                            <span>Stock: {{ product.product_quantity }}</span>
                                            <span class="text-green-600 font-medium">{{ formatSoldCount(product.total_sales || 0) }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <i :class="product.rates_avg_rate ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-gray-400'" class="text-xs" />
                                            <span>{{ product.rates_avg_rate ? Number(product.rates_avg_rate).toFixed(1) : '0.0' }}/5</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div :class="getResponsiveClasses({ mobile: 'flex gap-1 mt-auto', tablet: 'flex gap-1.5 mt-auto', desktop: 'flex gap-2 mt-auto' })">
                                        <button 
                                            @click="openCartModal(product, $event)"
                                            :class="getResponsiveClasses({ mobile: 'flex-1 py-2 text-xs font-medium rounded transition-colors', tablet: 'flex-1 py-2 text-sm font-medium rounded transition-colors', desktop: 'flex-1 py-2.5 text-sm font-medium rounded transition-colors' }) + ' ' + (product.product_quantity === 0 || !product.is_active || !product.available_online
                                                ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                                                : 'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer')"
                                            :disabled="product.product_quantity === 0 || !product.is_active || !product.available_online"
                                        >
                                            <i class="pi pi-shopping-cart text-sm" />
                                        </button>
                                        <RouterLink
                                            :to="{
                                                name: 'customer.product-info.index',
                                                params: { id: product.product_id },
                                            }"
                                            class="flex-1"
                                        >
                                            <button 
                                                :class="getResponsiveClasses({ mobile: 'w-full py-2 text-xs font-medium rounded transition-colors text-gray-900 hover:text-blue-600 cursor-pointer', tablet: 'w-full py-2 text-sm font-medium rounded transition-colors text-gray-900 hover:text-blue-600 cursor-pointer', desktop: 'w-full py-2.5 text-sm font-medium rounded transition-colors text-gray-900 hover:text-blue-600 cursor-pointer' })"
                                            >
                                                Details
                                            </button>
                                        </RouterLink>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div v-if="totalPages > 1" class="flex justify-end items-center gap-1.5 mt-6">
                            <!-- First Page -->
                            <button
                                @click="goToPage(1)"
                                :disabled="currentPage === 1"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                                :class="currentPage === 1 
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
                                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300 cursor-pointer'"
                            >
                                <i class="pi pi-angle-double-left" style="font-size: 0.625rem;" />
                            </button>
                            
                            <!-- Previous Page -->
                            <button
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                                :class="currentPage === 1 
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
                                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300 cursor-pointer'"
                            >
                                <i class="pi pi-chevron-left" style="font-size: 0.625rem;" />
                            </button>
                            
                            <!-- Page Numbers -->
                            <div class="flex gap-1.5">
                                <button
                                    v-for="page in paginationRange"
                                    :key="page"
                                    @click="goToPage(page)"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs transition-colors cursor-pointer"
                                    :class="currentPage === page 
                                        ? 'bg-blue-600 text-white font-medium' 
                                        : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'"
                                >
                                    {{ page }}
                                </button>
                            </div>
                            
                            <!-- Next Page -->
                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                                :class="currentPage === totalPages 
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
                                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300 cursor-pointer'"
                            >
                                <i class="pi pi-chevron-right" style="font-size: 0.625rem;" />
                            </button>
                            
                            <!-- Last Page -->
                            <button
                                @click="goToPage(totalPages)"
                                :disabled="currentPage === totalPages"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                                :class="currentPage === totalPages 
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
                                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300 cursor-pointer'"
                            >
                                <i class="pi pi-angle-double-right" style="font-size: 0.625rem;" />
                            </button>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="bg-white rounded-xl shadow-sm border py-16 text-center">
                        <i class="pi pi-search mb-4 text-6xl text-gray-300" />
                        <h3 class="mb-2 text-xl font-semibold text-gray-600">
                            No products found
                        </h3>
                        <p class="text-gray-400">
                            Try adjusting your filters or search terms
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
                root: { class: getResponsiveClasses({ 
                    mobile: 'rounded-xl mx-2', 
                    tablet: 'rounded-2xl', 
                    desktop: 'rounded-2xl' 
                }) },
                header: { class: getResponsiveClasses({ 
                    mobile: 'bg-blue-600 text-white rounded-t-xl px-3 py-2', 
                    tablet: 'bg-blue-600 text-white rounded-t-2xl px-4 py-2.5', 
                    desktop: 'bg-blue-600 text-white rounded-t-2xl px-5 py-2.5' 
                }) },
                content: { class: getResponsiveClasses({ 
                    mobile: 'px-3 pt-3 pb-2', 
                    tablet: 'px-4 pt-4 pb-3', 
                    desktop: 'px-5 pt-5 pb-4' 
                }) },
                footer: { class: getResponsiveClasses({ 
                    mobile: 'px-3 pb-3 pt-0', 
                    tablet: 'px-4 pb-4 pt-0', 
                    desktop: 'px-5 pb-4 pt-0' 
                }) }
            }"
        >
            <template #header>
                <div class="flex items-center justify-between w-full">
                    <h2 :class="getResponsiveTextSize('lg') + ' font-bold'">Add to Cart</h2>
                </div>
            </template>

            <div v-if="selectedProduct">
                <!-- Top Section: Image and Product Info -->
                <div class="flex gap-6 items-center mb-4">
                    <!-- Left Side - Product Image -->
                    <div class="flex-shrink-0">
                        <div class="w-32 h-32 bg-gray-50 rounded-lg p-3 flex items-center justify-center">
                            <img
                                v-if="selectedProduct.product_image"
                                :src="UrlUtil.getBaseAppUrl(`storage/images/product/${selectedProduct.product_image}`)"
                                :alt="selectedProduct.product_name"
                                class="max-w-full max-h-full object-contain"
                            />
                            <div v-else class="flex items-center justify-center">
                                <i class="pi pi-image text-5xl text-gray-400" />
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Product Information -->
                    <div class="flex-1">
                    <!-- Product Name -->
                    <h3 class="text-base font-semibold text-gray-800 mb-4 pt-1 line-clamp-2">
                        {{ selectedProduct.product_name }}
                    </h3>

                    <!-- Price -->
                    <p class="text-2xl font-bold text-gray-900 mb-4">
                        {{ CurrencyUtil.formatCurrency(selectedProduct.product_price) }}
                    </p>

                    <!-- Quantity Control -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                        <div class="flex items-center gap-1.5">
                            <button
                                @click="decreaseQuantity"
                                :disabled="cartQuantity <= 1"
                                class="w-7 h-7 flex items-center justify-center rounded transition-colors"
                                :class="cartQuantity <= 1 
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
                                    : 'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer'"
                            >
                                <i class="pi pi-minus" style="font-size: 0.65rem;" />
                            </button>
                            
                            <input
                                v-model.number="cartQuantity"
                                type="number"
                                :min="1"
                                :max="selectedProduct.product_quantity"
                                class="w-12 h-7 text-center text-sm font-semibold border border-gray-300 rounded focus:outline-none focus:border-blue-600 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            />
                            
                            <button
                                @click="increaseQuantity"
                                :disabled="cartQuantity >= selectedProduct.product_quantity"
                                class="w-7 h-7 flex items-center justify-center rounded transition-colors"
                                :class="cartQuantity >= selectedProduct.product_quantity
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                    : 'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer'"
                            >
                                <i class="pi pi-plus" style="font-size: 0.65rem;" />
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Available: <span class="font-semibold">{{ selectedProduct.product_quantity }}</span> units
                        </p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-3"></div>

            <!-- Total Price -->
            <div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-800">Total:</span>
                    <span class="text-lg font-bold text-gray-900">
                        {{ CurrencyUtil.formatCurrency(selectedProduct.product_price * cartQuantity) }}
                    </span>
                </div>
            </div>
        </div>

            <template #footer>
                <div class="flex gap-2">
                    <button
                        @click="showCartModal = false"
                        class="flex-1 px-4 py-2.5 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-semibold transition-colors cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmAddToCart"
                        :disabled="addToCartService.request.loading"
                        class="flex-1 px-4 py-2.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap"
                    >
                        <i v-if="addToCartService.request.loading" class="pi pi-spin pi-spinner text-sm" />
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
        return { width: '95vw', maxWidth: '350px' };
    }
    return { width: '400px' };
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
    hasSearched.value = true; // Set this before loading to show skeleton instead of empty state
    await loadCategoryProductsService.get(`products/category/${categoryId}`).then(() => {
        if (loadCategoryProductsService.request.status === 200 && loadCategoryProductsService.request.data) {
            data.value = loadCategoryProductsService.request.data;
            
            // Auto-select the category in filters only if specified
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
    hasSearched.value = true; // Set this before loading to show skeleton instead of empty state
    await loadCategoryProductsService.get('products', { limit: 1000 }).then(() => {
        if (loadCategoryProductsService.request.status === 200 && loadCategoryProductsService.request.data) {
            // Handle paginated response
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
        // If search is empty, load all products
        loadAllProducts();
    }
};

const toggleSection = (section: keyof typeof expandedSections) => {
    expandedSections[section] = !expandedSections[section];
};

const applyFilters = () => {
    // Filters are reactive, so they're already applied
    currentPage.value = 1; // Reset to first page when filters change
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
            // Clear URL query parameter
            router.replace({ query: {} });
            // If in search mode and search is cleared, load all products
            if (!route.params.id && route.name !== 'customer.browse-products') {
                loadAllProducts();
            }
            break;
        case 'category':
            filters.selectedCategories = filters.selectedCategories.filter(id => id !== value);
            // If we're in category mode and removed the last category filter, reload all category products
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
    
    // Clear URL query parameter
    router.replace({ query: {} });
    
    // If we're in category mode, reload the category products without filters
    if (route.params.id) {
        loadCategoryProducts(route.params.id as string, false);
    } 
    // Otherwise, load all products
    else {
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
    
    // Remove if already exists
    recentSearches.value = recentSearches.value.filter(s => s !== trimmed);
    
    // Add to beginning
    recentSearches.value.unshift(trimmed);
    
    // Keep only last 5
    if (recentSearches.value.length > 5) {
        recentSearches.value = recentSearches.value.slice(0, 5);
    }
    
    // Save to localStorage
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
        // Redirect to login page
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
        // Clear previous filters and load category products
        clearAllFilters();
        loadCategoryProducts(newId as string);
    }
}, { immediate: true });

// Variables for search watchers
let searchTimeout: number | null = null;
let isUpdatingFromRoute = false;

// Watch for query parameter changes to handle search from navbar
watch(() => route.query.q, (newQuery, oldQuery) => {
    console.log('🌐 route.query.q watcher triggered:', {
        newQuery,
        oldQuery,
        currentRoute: route.name,
        currentFormSearch: form.search
    });
    
    if (newQuery && typeof newQuery === 'string') {
        console.log('✅ Setting form.search to:', newQuery);
        isUpdatingFromRoute = true;
        form.search = newQuery;
        handleSearch();
    } else {
        console.log('🧹 Clearing form.search');
        isUpdatingFromRoute = true;
        // Clear form.search when query is removed or undefined
        form.search = '';
        if (route.name === 'customer.browse-products') {
            loadAllProducts();
        }
    }
}, { immediate: true });

// Watch for form.search changes with debounce for real-time search

watch(() => form.search, (newValue, oldValue) => {
    console.log('🔍 form.search watcher triggered:', {
        newValue,
        oldValue,
        isUpdatingFromRoute,
        currentRoute: route.name,
        currentQuery: route.query.q
    });
    
    // Skip if we're updating from route watcher to avoid circular updates
    if (isUpdatingFromRoute) {
        console.log('⏭️ Skipping - updating from route');
        isUpdatingFromRoute = false;
        return;
    }
    
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    searchTimeout = window.setTimeout(() => {
        const trimmedValue = newValue ? newValue.trim() : '';
        const currentQuery = route.query.q as string | undefined;
        
        console.log('⏰ Debounce timeout executed:', {
            trimmedValue,
            currentQuery,
            willNavigate: trimmedValue !== currentQuery
        });
        
        // Only update if the value actually changed from the current query
        if (trimmedValue && trimmedValue !== currentQuery) {
            console.log('➡️ Navigating to search with query:', trimmedValue);
            // Navigate to search page with query
            if (route.name !== 'customer.search-product') {
                router.push({ name: 'customer.search-product', query: { q: trimmedValue } });
            } else {
                router.replace({ query: { q: trimmedValue } });
                handleSearch();
            }
        } else if (!trimmedValue && currentQuery) {
            console.log('⬅️ Navigating back to browse - search cleared');
            // Navigate back to browse products when search is cleared
            router.push({ name: 'customer.browse-products' });
        } else {
            console.log('❌ No navigation - conditions not met');
        }
    }, 500); // 500ms debounce
});

// Watch for route name changes to reset filters when navigating to browse products
watch(() => route.name, (newName, oldName) => {
    if (newName === 'customer.browse-products' && oldName && oldName !== 'customer.browse-products') {
        // Reset all filters and load all products
        form.search = '';
        filters.selectedCategories = [];
        filters.minPrice = null;
        filters.maxPrice = null;
        currentPage.value = 1;
        router.replace({ query: {} });
        loadAllProducts();
    }
});

// Watch for category filter changes - reload all products if all categories are unchecked
watch(() => filters.selectedCategories, (newCategories, oldCategories) => {
    // If we were in category mode (had categories selected) and now have none
    if (oldCategories && oldCategories.length > 0 && newCategories.length === 0) {
        // If we came from a category route, load all products
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
    
    // Load recent searches from localStorage
    const saved = localStorage.getItem('recentSearches');
    if (saved) {
        try {
            recentSearches.value = JSON.parse(saved);
        } catch (e) {
            recentSearches.value = [];
        }
    }
    
    // Check if we're in browse mode
    if (route.name === 'customer.browse-products') {
        // Reset filters but load all products
        form.search = '';
        filters.selectedCategories = [];
        filters.minPrice = null;
        filters.maxPrice = null;
        currentPage.value = 1;
        loadAllProducts();
    }
    // Check for query parameter and perform search (only if not a category route)
    else if (!route.params.id) {
        // Reset filters
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
            // If no search query, reset everything
            form.search = '';
            hasSearched.value = false;
            data.value = [];
        }
    }
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
</style>
