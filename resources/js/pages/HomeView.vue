<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Improved Responsive Header -->
        <header class="bg-blue-600 sticky top-0 z-50 shadow-md">
            <!-- Mobile Header (< 768px) -->
            <nav v-if="isMobile">
                <!-- Top row: Logo and Menu Toggle -->
                <div class="flex items-center justify-between p-3">
                    <RouterLink :to="{ name: 'home' }" class="flex items-center gap-2">
                        <div class="rounded-full bg-white p-1">
                            <img :src="Icon" class="h-10 w-10" alt="Store Icon" />
                        </div>
                        <h1 class="text-lg font-bold text-white">{{ appName }}</h1>
                    </RouterLink>
                    
                    <!-- Mobile action buttons -->
                    <div class="flex items-center gap-2">
                        <Button
                            icon="pi pi-facebook"
                            rounded
                            as="a"
                            href="https://www.facebook.com/alcesslaptopstore"
                            target="_blank"
                            class="!bg-white !text-blue-600"
                            size="small"
                        />
                        <Button
                            icon="pi pi-th-large"
                            rounded
                            severity="secondary"
                            size="small"
                            @click="goToBrowseProducts"
                            v-tooltip.bottom="'Browse Products'"
                        />
                        <Button
                            v-if="!Page.user"
                            icon="pi pi-user"
                            rounded
                            severity="secondary"
                            size="small"
                            @click="openLoginForm()"
                            v-tooltip.bottom="'Login'"
                        />
                    </div>
                </div>
                
                <!-- Bottom row: Search bar (only on product pages) -->
                <div v-if="isProductPage" class="px-3 pb-3">
                    <form @submit.prevent="handleSearch">
                        <div class="relative">
                            <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="form.search"
                                type="text"
                                placeholder="Search products..."
                                class="w-full pl-10 pr-4 py-2 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all text-sm"
                            />
                        </div>
                    </form>
                </div>
            </nav>

            <!-- Tablet Header (768px - 1023px) -->
            <nav v-if="isTablet">
                <div class="flex items-center justify-between p-3">
                    <!-- Left: Logo -->
                    <RouterLink :to="{ name: 'home' }" class="flex items-center gap-3">
                        <div class="rounded-full bg-white p-1">
                            <img :src="Icon" class="h-12 w-12" alt="Store Icon" />
                        </div>
                        <h1 class="text-xl font-bold text-white">{{ appName }}</h1>
                    </RouterLink>
                    
                    <!-- Center: Search (only on product pages) -->
                    <div v-if="isProductPage" class="flex-1 max-w-md mx-6">
                        <form @submit.prevent="handleSearch">
                            <div class="relative">
                                <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Search for products..."
                                    class="w-full pl-10 pr-4 py-2 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                />
                            </div>
                        </form>
                    </div>
                    
                    <!-- Right: Action buttons -->
                    <div class="flex items-center gap-3">
                        <Button
                            icon="pi pi-facebook"
                            rounded
                            as="a"
                            href="https://www.facebook.com/alcesslaptopstore"
                            target="_blank"
                            class="!bg-white !text-blue-600"
                            v-tooltip.bottom="'Facebook'"
                        />
                        <Button
                            icon="pi pi-th-large"
                            rounded
                            severity="secondary"
                            @click="goToBrowseProducts"
                            v-tooltip.bottom="'Browse Products'"
                        />
                        <Button
                            v-if="!Page.user"
                            icon="pi pi-user"
                            rounded
                            severity="secondary"
                            @click="openLoginForm()"
                            v-tooltip.bottom="'Login'"
                        />
                    </div>
                </div>
            </nav>

            <!-- Desktop Header (≥ 1024px) - Original Layout -->
            <nav v-if="isDesktop" class="p-2">
            <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-2">
                        <RouterLink :to="{ name: 'home' }">
                            <div class="flex">
                                <div class="px-2">
                                    <div class="rounded-full bg-white">
                                        <img
                                            :src="Icon"
                                            class="h-16 w-16"
                                            alt="Store Icon"
                                        />
                                    </div>
                                </div>
                                <div class="flex items-center px-2">
                                    <h1 class="text-3xl font-bold text-white">
                                        {{ appName }}
                                    </h1>
                                </div>
                            </div>
                        </RouterLink>
                    </div>
                    
                    <div class="flex items-center w-full flex-1 justify-end">
                        <div class="flex items-center w-full">
                            <!-- Search Bar - Only show on product pages and hide on small screens -->
                            <div v-if="isProductPage && !isMobile" class="flex-1 max-w-4xl mx-auto">
                                <form @submit.prevent="handleSearch" class="w-full">
                                    <div class="relative">
                                        <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                                        <input
                                            v-model="form.search"
                                            type="text"
                                            placeholder="Search for products..."
                                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                        />
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Right side icons - Responsive layout -->
                            <div class="flex items-center" :class="isProductPage && !isMobile ? 'ml-6' : 'ml-auto'">
                                <!-- Desktop layout -->
                                <div v-if="!isMobile" class="flex gap-4 items-center">
                                    <!-- Facebook Icon -->
                                    <Button
                                        icon="pi pi-facebook"
                                        rounded
                                        as="a"
                                        href="https://www.facebook.com/alcesslaptopstore"
                                        target="_blank"
                                        class="!bg-white !text-blue-600"
                                        v-tooltip.bottom="'Visit our Facebook page'"
                                    />
                                    <!-- Browse All Products -->
                                    <Button
                                        icon="pi pi-th-large"
                                        rounded
                                        severity="secondary"
                                        @click="goToBrowseProducts"
                                        v-tooltip.bottom="'Browse All Products'"
                                    />
                                    <!-- Login Button -->
                                    <div v-if="!Page.user">
                                        <Button
                                            type="button"
                                            icon="pi pi-user"
                                            severity="secondary"
                                            rounded
                                            @click="openLoginForm()"
                                            v-tooltip.bottom="'Login'"
                                        />
                                    </div>
                                </div>

                                <!-- Mobile/Tablet layout -->
                                <div v-if="isMobile" class="flex gap-2 items-center">
                                    <!-- Facebook Icon -->
                                    <Button
                                        icon="pi pi-facebook"
                                        rounded
                                        size="small"
                                        as="a"
                                        href="https://www.facebook.com/alcesslaptopstore"
                                        target="_blank"
                                        class="!bg-white !text-blue-600 !w-10 !h-10"
                                    />
                                    <!-- Browse All Products -->
                                    <Button
                                        icon="pi pi-th-large"
                                        rounded
                                        size="small"
                                        severity="secondary"
                                        @click="goToBrowseProducts"
                                        class="!w-10 !h-10"
                                    />
                                    <!-- Login Button -->
                                    <div v-if="!Page.user">
                                        <Button
                                            type="button"
                                            icon="pi pi-user"
                                            severity="secondary"
                                            rounded
                                            size="small"
                                            @click="openLoginForm()"
                                            class="!w-10 !h-10"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Search Bar - Show below header on small screens when on product pages -->
                        <div v-if="isProductPage && isMobile" class="w-full mt-3">
                            <form @submit.prevent="handleSearch">
                                <div class="relative">
                                    <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                                    <input
                                        v-model="form.search"
                                        type="text"
                                        placeholder="Search for products..."
                                        class="w-full pl-11 pr-4 py-2.5 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                    />
                                </div>
                            </form>
                        </div>
                    </div>
            </div>
            </nav>
        </header>

        <!-- Dynamic Hero Carousel Section -->
        <div class="relative mb-16">
            <div :class="[
                'relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100',
                isMobile ? 'h-[300px]' : isTablet ? 'h-[400px]' : 'h-[500px]'
            ]">
                <!-- Carousel with Best Selling Products -->
                <div v-if="showCarousel" class="relative h-full">
                    <TransitionGroup name="hero-slide">
                        <div
                            v-for="(product, index) in [products.slice(0, 3)[currentSlide]]"
                            :key="currentSlide"
                            class="absolute inset-0"
                        >
                            <div class="grid h-full grid-cols-2">
                                <!-- Left Side - Product Info -->
                                <div :class="[
                                    'flex items-center justify-center',
                                    isMobile ? 'p-3' : isTablet ? 'p-4' : 'p-8 md:p-12'
                                ]">
                                    <div :class="[
                                        isMobile ? 'max-w-xs' : isTablet ? 'max-w-sm' : 'max-w-xl'
                                    ]">
                                        <div :class="[
                                            'font-semibold',
                                            isMobile ? 'text-xs mb-2' : isTablet ? 'text-sm mb-3' : 'text-lg md:text-xl mb-6'
                                        ]">
                                            <span class="text-blue-600">Top Selling</span>
                                            <span class="text-gray-900"> Product!</span>
                                        </div>
                                        <h1 :class="[
                                            'font-bold text-gray-900',
                                            isMobile ? 'text-base mb-1' : isTablet ? 'text-lg mb-2' : 'text-2xl md:text-3xl lg:text-4xl mb-4'
                                        ]">
                                            {{ product.product_name }}
                                        </h1>
                                        <p :class="[
                                            'text-gray-600',
                                            isMobile ? 'mb-2 text-xs' : isTablet ? 'mb-3 text-sm' : 'mb-6 text-lg'
                                        ]">
                                            {{ product.category?.category_name || 'Featured Product' }}
                                        </p>
                                        <div :class="isMobile ? 'mb-2' : isTablet ? 'mb-3' : 'mb-6'">
                                            <span :class="[
                                                'font-bold text-gray-900',
                                                isMobile ? 'text-lg' : isTablet ? 'text-xl' : 'text-4xl'
                                            ]">
                                                {{ CurrencyUtil.formatCurrency(product.product_price) }}
                                            </span>
                                        </div>
                                        <!-- Rating -->
                                        <div :class="[
                                            'flex items-center gap-1',
                                            isMobile ? 'mb-3' : isTablet ? 'mb-4' : 'mb-8'
                                        ]">
                                            <i v-for="i in 4" :key="i" :class="[
                                                'pi pi-star-fill text-yellow-400',
                                                isMobile ? 'text-xs' : isTablet ? 'text-sm' : 'text-2xl'
                                            ]" />
                                            <i :class="[
                                                'pi pi-star text-gray-300',
                                                isMobile ? 'text-xs' : isTablet ? 'text-sm' : 'text-2xl'
                                            ]" />
                                        </div>
                                        <!-- Action Buttons -->
                                        <div :class="[
                                            'flex',
                                            isMobile ? 'gap-2' : isTablet ? 'gap-3' : 'gap-4'
                                        ]">
                                            <button 
                                                @click="addToCart(product.product_id)"
                                                :disabled="addToCartService.request.loading"
                                                :class="[
                                                    'cursor-pointer rounded-full bg-blue-600 font-semibold text-white transition-all hover:bg-blue-700 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50',
                                                    isMobile ? 'px-3 py-1.5 text-xs' : isTablet ? 'px-4 py-2 text-sm' : 'px-8 py-3'
                                                ]"
                                            >
                                                <i v-if="addToCartService.request.loading" class="pi pi-spin pi-spinner mr-2" />
                                                Add to Cart
                                            </button>
                                            <button 
                                                @click="goToProductDetails(product.product_id)"
                                                :class="[
                                                    'cursor-pointer rounded-full bg-white text-gray-700 transition-all hover:shadow-lg border border-gray-200',
                                                    isMobile ? 'px-3 py-1.5 text-xs' : isTablet ? 'px-4 py-2 text-sm' : 'px-8 py-3'
                                                ]"
                                            >
                                                Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right Side - Product Image -->
                                <div :class="[
                                    'flex items-center justify-center',
                                    isMobile ? 'p-2' : isTablet ? 'p-3' : 'p-8'
                                ]">
                                    <div class="relative">
                                        <img
                                            v-if="product.product_image"
                                            :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                            :alt="product.product_name"
                                            :class="[
                                                'w-auto object-contain drop-shadow-2xl',
                                                isMobile ? 'max-h-[150px]' : isTablet ? 'max-h-[250px]' : 'max-h-[400px]'
                                            ]"
                                            @error="handleImageError"
                                        />
                                        <div
                                            :class="[
                                                'fallback-image flex items-center justify-center rounded-2xl bg-gradient-to-br from-gray-200 to-gray-300',
                                                isMobile ? 'h-[150px] w-[150px]' : isTablet ? 'h-[250px] w-[250px]' : 'h-[400px] w-[400px]'
                                            ]"
                                            :style="{ display: product.product_image ? 'none' : 'flex' }"
                                        >
                                            <i :class="[
                                                'pi pi-image text-gray-400',
                                                isMobile ? 'text-3xl' : isTablet ? 'text-5xl' : 'text-8xl'
                                            ]" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>
                    
                    <!-- Carousel Indicators -->
                    <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
                        <button
                            v-for="(product, index) in products.slice(0, 3)"
                            :key="index"
                            @click="currentSlide = index"
                            :class="[
                                'h-2 rounded-full transition-all',
                                currentSlide === index ? 'w-8 bg-blue-600' : 'w-2 bg-gray-400 hover:bg-gray-500'
                            ]"
                        />
                    </div>
                </div>

                <!-- Fallback Static Hero -->
                <div v-else class="relative h-full">
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
        </div>

        <div class="container mx-auto px-4">
            <!-- Interactive Categories/Products Section -->
            <section :class="isMobile ? 'mb-12' : isTablet ? 'mb-16' : 'mb-20'">
                <!-- Toggle Headers with Smooth Transitions -->
                <div :class="[
                    'text-center relative',
                    isMobile ? 'mb-8' : isTablet ? 'mb-10' : 'mb-12'
                ]" :style="{ minHeight: isMobile ? '60px' : isTablet ? '70px' : '80px' }">
                    <button
                        @click="activeView = 'categories'"
                        :class="[
                            'font-bold transition-all duration-500 ease-in-out cursor-pointer block w-full absolute left-0 right-0',
                            activeView === 'categories' 
                                ? (isMobile ? 'text-lg text-gray-800 top-0' : isTablet ? 'text-xl text-gray-800 top-0' : 'text-3xl text-gray-800 top-0')
                                : (isMobile ? 'text-xs text-gray-400 hover:text-blue-600 top-8' : isTablet ? 'text-sm text-gray-400 hover:text-blue-600 top-10' : 'text-base text-gray-400 hover:text-blue-600 top-12')
                        ]"
                    >
                        {{ categoriesHeading }}
                    </button>
                    <button
                        @click="activeView = 'products'"
                        :class="[
                            'font-bold transition-all duration-500 ease-in-out cursor-pointer block w-full absolute left-0 right-0',
                            activeView === 'products' 
                                ? (isMobile ? 'text-lg text-gray-800 top-0' : isTablet ? 'text-xl text-gray-800 top-0' : 'text-3xl text-gray-800 top-0')
                                : (isMobile ? 'text-xs text-gray-400 hover:text-blue-600 top-8' : isTablet ? 'text-sm text-gray-400 hover:text-blue-600 top-10' : 'text-base text-gray-400 hover:text-blue-600 top-12')
                        ]"
                    >
                        {{ productsHeading }}
                    </button>
                </div>
                
                <!-- Categories View with Transition -->
                <Transition
                    enter-active-class="transition-all duration-500 ease-out"
                    enter-from-class="opacity-0 translate-y-8"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-500 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-8"
                    mode="out-in"
                >
                    <div v-if="activeView === 'categories'" key="categories" class="relative">
                        <!-- Categories List -->
                        <div v-if="CategoryStore.categories && CategoryStore.categories.length > 0" :class="[
                            'overflow-x-auto category-scroll',
                            isMobile ? 'pb-6 pt-3' : isTablet ? 'pb-7 pt-3' : 'pb-8 pt-4'
                        ]">
                            <div :class="[
                                'flex px-4',
                                isMobile ? 'gap-4 pb-6' : isTablet ? 'gap-6 pb-7' : 'gap-8 pb-8',
                                CategoryStore.categories.length <= 5 ? 'justify-center' : 'justify-start'
                            ]" style="min-width: min-content;">
                            <button
                                v-for="(category, index) in CategoryStore.categories"
                                :key="index"
                                :class="[
                                    'group flex flex-col items-center gap-3 transition-transform duration-300 hover:-translate-y-2 flex-shrink-0',
                                    isMobile ? 'gap-2' : isTablet ? 'gap-2.5' : 'gap-3'
                                ]"
                                :style="{ 
                                    minWidth: isMobile ? '120px' : isTablet ? '150px' : '180px', 
                                    width: isMobile ? '120px' : isTablet ? '150px' : '180px' 
                                }"
                                @click="goRoute('customer.product-category', { id: category.category_id })"
                            >
                                <!-- Category Image Container -->
                                <div :class="[
                                    'flex items-center justify-center',
                                    isMobile ? 'h-20 w-20' : isTablet ? 'h-24 w-24' : 'h-32 w-32'
                                ]">
                                    <img
                                        v-if="category.category_image"
                                        :src="
                                            UrlUtil.getBaseAppUrl(
                                                `storage/images/category/${category.category_image}`,
                                            )
                                        "
                                        :alt="category.category_name"
                                        class="h-full w-full object-contain"
                                    />
                                    <div
                                        v-else
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-gradient-to-br from-gray-100 to-gray-200"
                                    >
                                        <i :class="[
                                            'pi pi-image text-gray-400',
                                            isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-4xl'
                                        ]" />
                                    </div>
                                </div>
                                
                                <!-- Category Name with Underline on Hover -->
                                <span :class="[
                                    'text-center font-medium text-gray-800 transition-all duration-300 group-hover:underline group-hover:decoration-blue-600 group-hover:decoration-2 group-hover:underline-offset-4',
                                    isMobile ? 'text-sm' : isTablet ? 'text-sm' : 'text-base'
                                ]">
                                    {{ category.category_name }}
                                </span>
                            </button>
                            </div>
                        </div>
                        
                        <!-- Empty State for Categories -->
                        <div v-else :class="[
                            'flex flex-col items-center justify-center px-4',
                            isMobile ? 'py-12' : isTablet ? 'py-14' : 'py-16'
                        ]">
                            <div :class="[
                                'rounded-full bg-gradient-to-br from-blue-50 to-blue-100 mb-6',
                                isMobile ? 'p-6' : isTablet ? 'p-7' : 'p-8'
                            ]">
                                <i :class="[
                                    'pi pi-th-large text-blue-400',
                                    isMobile ? 'text-4xl' : isTablet ? 'text-5xl' : 'text-6xl'
                                ]" />
                            </div>
                            <h3 :class="[
                                'font-semibold text-gray-800 mb-2',
                                isMobile ? 'text-lg' : isTablet ? 'text-xl' : 'text-xl'
                            ]">No Categories Available</h3>
                            <p :class="[
                                'text-gray-500 text-center max-w-md',
                                isMobile ? 'text-sm' : isTablet ? 'text-sm' : 'text-base'
                            ]">
                                We're currently setting up our product categories. Check back soon for exciting new collections!
                            </p>
                        </div>
                        
                        <!-- Fade overlays -->
                        <div v-if="CategoryStore.categories && CategoryStore.categories.length > 0" :class="[
                            'pointer-events-none absolute left-0 top-0 bg-gradient-to-r from-gray-50 to-transparent',
                            isMobile ? 'bottom-6 w-12' : isTablet ? 'bottom-7 w-16' : 'bottom-8 w-20'
                        ]"></div>
                        <div v-if="CategoryStore.categories && CategoryStore.categories.length > 0" :class="[
                            'pointer-events-none absolute right-0 top-0 bg-gradient-to-l from-gray-50 to-transparent',
                            isMobile ? 'bottom-6 w-12' : isTablet ? 'bottom-7 w-16' : 'bottom-8 w-20'
                        ]"></div>
                    </div>

                    <!-- Products View with Transition -->
                    <div v-else-if="activeView === 'products'" key="products" class="relative">
                        <!-- Products List -->
                        <div v-if="products && products.length > 0" :class="[
                            'overflow-x-auto category-scroll',
                            isMobile ? 'pb-6 pt-3' : isTablet ? 'pb-7 pt-3' : 'pb-8 pt-4'
                        ]">
                            <div :class="[
                                'flex px-4',
                                isMobile ? 'gap-4 pb-6' : isTablet ? 'gap-6 pb-7' : 'gap-8 pb-8',
                                products.length <= 5 ? 'justify-center' : 'justify-start'
                            ]" style="min-width: min-content;">
                        <div
                            v-for="(product, index) in products"
                            :key="index"
                            :class="[
                                'group flex flex-col items-center transition-transform duration-300 hover:-translate-y-2 flex-shrink-0',
                                isMobile ? 'gap-2' : isTablet ? 'gap-2.5' : 'gap-3'
                            ]"
                            :style="{ 
                                minWidth: isMobile ? '120px' : isTablet ? '150px' : '180px', 
                                width: isMobile ? '120px' : isTablet ? '150px' : '180px' 
                            }"
                        >
                            <!-- Product Image Container -->
                            <div :class="[
                                'flex items-center justify-center',
                                isMobile ? 'h-20 w-20' : isTablet ? 'h-24 w-24' : 'h-32 w-32'
                            ]">
                                <img
                                    v-if="product.product_image"
                                    :src="
                                        UrlUtil.getBaseAppUrl(
                                            `storage/images/product/${product.product_image}`,
                                        )
                                    "
                                    :alt="product.product_name"
                                    class="h-full w-full object-contain"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-gradient-to-br from-gray-100 to-gray-200"
                                >
                                    <i :class="[
                                        'pi pi-image text-gray-400',
                                        isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-4xl'
                                    ]" />
                                </div>
                            </div>
                            
                            <!-- Product Name -->
                            <span :class="[
                                'text-center font-medium text-gray-800 line-clamp-2',
                                isMobile ? 'text-sm' : isTablet ? 'text-sm' : 'text-base'
                            ]">
                                {{ product.product_name }}
                            </span>

                            <!-- View Details Button -->
                            <RouterLink
                                :to="{
                                    name: 'customer.product-info.index',
                                    params: { id: product.product_id },
                                }"
                                :class="[
                                    'text-blue-600 hover:underline hover:decoration-blue-600 hover:decoration-2 hover:underline-offset-4 transition-all duration-300',
                                    isMobile ? 'text-xs' : isTablet ? 'text-sm' : 'text-sm'
                                ]"
                            >
                                View Details
                            </RouterLink>
                        </div>
                            </div>
                        </div>
                        
                        <!-- Empty State for Products -->
                        <div v-else :class="[
                            'flex flex-col items-center justify-center px-4',
                            isMobile ? 'py-12' : isTablet ? 'py-14' : 'py-16'
                        ]">
                            <div :class="[
                                'rounded-full bg-gradient-to-br from-blue-50 to-blue-100 mb-6',
                                isMobile ? 'p-6' : isTablet ? 'p-7' : 'p-8'
                            ]">
                                <i :class="[
                                    'pi pi-shopping-bag text-blue-400',
                                    isMobile ? 'text-4xl' : isTablet ? 'text-5xl' : 'text-6xl'
                                ]" />
                            </div>
                            <h3 :class="[
                                'font-semibold text-gray-800 mb-2',
                                isMobile ? 'text-lg' : isTablet ? 'text-xl' : 'text-xl'
                            ]">No Products Available</h3>
                            <p :class="[
                                'text-gray-500 text-center max-w-md',
                                isMobile ? 'text-sm' : isTablet ? 'text-sm' : 'text-base'
                            ]">
                                Our store is being stocked with amazing products. Stay tuned for our latest offerings!
                            </p>
                        </div>
                        
                        <!-- Fade overlays -->
                        <div v-if="products && products.length > 0" :class="[
                            'pointer-events-none absolute left-0 top-0 bg-gradient-to-r from-gray-50 to-transparent',
                            isMobile ? 'bottom-6 w-12' : isTablet ? 'bottom-7 w-16' : 'bottom-8 w-20'
                        ]"></div>
                        <div v-if="products && products.length > 0" :class="[
                            'pointer-events-none absolute right-0 top-0 bg-gradient-to-l from-gray-50 to-transparent',
                            isMobile ? 'bottom-6 w-12' : isTablet ? 'bottom-7 w-16' : 'bottom-8 w-20'
                        ]"></div>
                    </div>
                </Transition>
            </section>

            <!-- Features Section -->
            <section class="mb-12">
                <div :class="[
                    'grid gap-6',
                    isMobile ? 'grid-cols-2' : isTablet ? 'grid-cols-2' : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4'
                ]">
                    <div :class="[
                        'rounded-2xl bg-white shadow-md transition-all hover:-translate-y-1 hover:shadow-xl',
                        isMobile ? 'p-4' : isTablet ? 'p-5' : 'p-6'
                    ]">
                        <div :class="[
                            'mb-3 flex items-center justify-center rounded-full bg-green-100',
                            isMobile ? 'h-8 w-8' : isTablet ? 'h-10 w-10' : 'h-12 w-12'
                        ]">
                            <i :class="[
                                'pi pi-check-circle text-green-600',
                                isMobile ? 'text-lg' : isTablet ? 'text-xl' : 'text-2xl'
                            ]" />
                        </div>
                        <h3 :class="[
                            'mb-2 font-semibold text-gray-800',
                            isMobile ? 'text-sm' : isTablet ? 'text-base' : 'text-base'
                        ]">
                            Quality Guarantee
                        </h3>
                        <p :class="[
                            'text-gray-600',
                            isMobile ? 'text-xs' : isTablet ? 'text-sm' : 'text-sm'
                        ]">
                            All products are carefully selected and verified
                        </p>
                    </div>
                    <div :class="[
                        'rounded-2xl bg-white shadow-md transition-all hover:-translate-y-1 hover:shadow-xl',
                        isMobile ? 'p-4' : isTablet ? 'p-5' : 'p-6'
                    ]">
                        <div :class="[
                            'mb-3 flex items-center justify-center rounded-full bg-blue-100',
                            isMobile ? 'h-8 w-8' : isTablet ? 'h-10 w-10' : 'h-12 w-12'
                        ]">
                            <i :class="[
                                'pi pi-truck text-blue-600',
                                isMobile ? 'text-lg' : isTablet ? 'text-xl' : 'text-2xl'
                            ]" />
                        </div>
                        <h3 :class="[
                            'mb-2 font-semibold text-gray-800',
                            isMobile ? 'text-sm' : isTablet ? 'text-base' : 'text-base'
                        ]">
                            Fast Delivery
                        </h3>
                        <p :class="[
                            'text-gray-600',
                            isMobile ? 'text-xs' : isTablet ? 'text-sm' : 'text-sm'
                        ]">
                            Get your orders delivered quickly and safely
                        </p>
                    </div>
                    <div :class="[
                        'rounded-2xl bg-white shadow-md transition-all hover:-translate-y-1 hover:shadow-xl',
                        isMobile ? 'p-4' : isTablet ? 'p-5' : 'p-6'
                    ]">
                        <div :class="[
                            'mb-3 flex items-center justify-center rounded-full bg-purple-100',
                            isMobile ? 'h-8 w-8' : isTablet ? 'h-10 w-10' : 'h-12 w-12'
                        ]">
                            <i :class="[
                                'pi pi-shield text-purple-600',
                                isMobile ? 'text-lg' : isTablet ? 'text-xl' : 'text-2xl'
                            ]" />
                        </div>
                        <h3 :class="[
                            'mb-2 font-semibold text-gray-800',
                            isMobile ? 'text-sm' : isTablet ? 'text-base' : 'text-base'
                        ]">
                            Secure Payment
                        </h3>
                        <p :class="[
                            'text-gray-600',
                            isMobile ? 'text-xs' : isTablet ? 'text-sm' : 'text-sm'
                        ]">
                            Your payment information is always protected
                        </p>
                    </div>
                    <div :class="[
                        'rounded-2xl bg-white shadow-md transition-all hover:-translate-y-1 hover:shadow-xl',
                        isMobile ? 'p-4' : isTablet ? 'p-5' : 'p-6'
                    ]">
                        <div :class="[
                            'mb-3 flex items-center justify-center rounded-full bg-orange-100',
                            isMobile ? 'h-8 w-8' : isTablet ? 'h-10 w-10' : 'h-12 w-12'
                        ]">
                            <i :class="[
                                'pi pi-sync text-orange-600',
                                isMobile ? 'text-lg' : isTablet ? 'text-xl' : 'text-2xl'
                            ]" />
                        </div>
                        <h3 :class="[
                            'mb-2 font-semibold text-gray-800',
                            isMobile ? 'text-sm' : isTablet ? 'text-base' : 'text-base'
                        ]">
                            Easy Returns
                        </h3>
                        <p :class="[
                            'text-gray-600',
                            isMobile ? 'text-xs' : isTablet ? 'text-sm' : 'text-sm'
                        ]">
                            30-day return policy for your peace of mind
                        </p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Original Login Dialog - Unchanged -->
        <Dialog
            v-model:visible="loginFormVisible"
            modal
            header="Login"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <LoginForm @success="loginFormVisible = false">
                <template #footer>
                    <div class="flex flex-col">
                        <div class="flex">
                            <div class="w-1/2 p-2">
                                <Button
                                    type="button"
                                    variant="link"
                                    label="Forgot password?"
                                    class="p-0! text-black! text-sm!"
                                    pt:label:class="font-normal!"
                                    @click="openResetPasswordForm()"
                                />
                            </div>
                            <div class="flex w-1/2 flex-col p-2">
                                <span class="text-end text-sm">Don't have an account?</span>
                                <div class="flex justify-end">
                                    <Button
                                        type="button"
                                        variant="link"
                                        label="Register"
                                        class="p-0! text-sm! text-blue-600!"
                                        pt:label:class="font-normal!"
                                        @click="openRegisterForm()"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center p-2 text-sm text-gray-600">
                            <span>Are you an administrator? </span>
                            <RouterLink 
                                :to="{ name: 'admin.login' }"
                                class="ml-1 text-green-600 hover:underline cursor-pointer"
                            >
                                Login
                            </RouterLink>
                        </div>
                    </div>
                </template>
            </LoginForm>
        </Dialog>

        <!-- Original Register Dialog - Unchanged -->
        <Dialog
            v-model:visible="registerFormVisible"
            modal
            header="Register"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <RegisterForm>
                <template #footer>
                    <div class="flex justify-center p-2">
                        <span>Already have an account?</span>
                        &nbsp;
                        <Button
                            type="button"
                            variant="link"
                            label="Login"
                            class="p-0!"
                            pt:label:class="font-normal!"
                            @click="openLoginForm()"
                        />
                    </div>
                </template>
            </RegisterForm>
        </Dialog>

        <!-- Reset Password Dialog - Step 1: Send Code -->
        <Dialog
            v-model:visible="resetPasswordFormVisible"
            modal
            header="Reset Account"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <ResetPasswordForm @code-sent="handleCodeSent" />
        </Dialog>

        <!-- Reset Password Dialog - Step 2: Verify Code -->
        <Dialog
            v-model:visible="verifyCodeFormVisible"
            modal
            header="Verify Code"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <VerifyCodeForm 
                :email="resetEmail"
                :current-password="resetCurrentPassword"
                :new-password="resetNewPassword"
                :new-password-confirmation="resetNewPasswordConfirmation"
                @success="handleResetSuccess" 
            />
        </Dialog>

        <!-- Footer -->
        <footer class="mt-16 bg-gradient-to-r from-gray-800 to-gray-900 py-12 text-white">
            <div class="container mx-auto px-4">
                <div :class="[
                    'grid gap-8',
                    isMobile ? 'grid-cols-1' : isTablet ? 'grid-cols-2' : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4'
                ]">
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
import Icon from "@/../img/logo.png";
import LoginForm from "@/components/forms/LoginForm.vue";
import RegisterForm from "@/components/forms/RegisterForm.vue";
import ResetPasswordForm from "@/components/forms/ResetPasswordForm.vue";
import VerifyCodeForm from "@/components/forms/VerifyCodeForm.vue";
import {
    SearchErrorInterface,
    SearchProductInterface,
} from "@/interfaces/SearchProductInterface";
import Page from "@/stores/Page";
import { computed, onMounted, onUnmounted, reactive, ref, watch } from "vue";
import { useResponsive } from "@/composables/useResponsive";
import HeroBg from "@/../img/hero-bg.jpg";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";
import { useRouter } from "vue-router";
import { CartFormInterface } from "@/interfaces/CartInterface";

const { isMobile, isTablet, isDesktop } = useResponsive();
const appName = import.meta.env.VITE_APP_NAME;
const loginFormVisible = ref<boolean>(false);
const registerFormVisible = ref<boolean>(false);
const resetPasswordFormVisible = ref<boolean>(false);
const verifyCodeFormVisible = ref<boolean>(false);
const resetEmail = ref<string | null>(null);
const resetCurrentPassword = ref<string | null>(null);
const resetNewPassword = ref<string | null>(null);
const resetNewPasswordConfirmation = ref<string | null>(null);
const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const addToCartService = useAxiosUtil<CartFormInterface, null>();
const products = ref<ProductInterface[]>([]);
const currentSlide = ref<number>(0);
let carouselInterval: number | null = null;
const activeView = ref<'categories' | 'products'>('categories');

// Computed property to dynamically change the category heading
const categoriesHeading = computed(() => {
    if (CategoryStore.categories && CategoryStore.categories.length > 0) {
        // Check if categories have the is_popular flag (must be explicitly true)
        const hasPopularCategories = CategoryStore.categories.some((cat: any) => cat.is_popular === true || cat.is_popular === 1);
        return hasPopularCategories ? 'Browse Popular Categories' : 'Browse Categories';
    }
    return 'Browse Categories';
});

// Computed property to dynamically change the products heading
const productsHeading = computed(() => {
    if (products.value && products.value.length > 0) {
        // Check if products have the is_best_selling flag (must be explicitly true)
        const hasBestSellingProducts = products.value.some((prod: any) => prod.is_best_selling === true || prod.is_best_selling === 1);
        return hasBestSellingProducts ? 'Best Selling Products' : 'Current Products';
    }
    return 'Current Products';
});

// Computed property to check if we should show carousel (only for best selling products)
const showCarousel = computed(() => {
    if (products.value && products.value.length > 0) {
        // Only show carousel if products are truly best selling
        return products.value.some((prod: any) => prod.is_best_selling === true || prod.is_best_selling === 1);
    }
    return false;
});

const form: SearchProductInterface = reactive({
    search: null,
});
const errors: SearchErrorInterface = reactive({
    search: [],
});

// Check if current route is a product-related page or home page
const isProductPage = computed(() => {
    const productRoutes = [
        'customer.product-search',
        'customer.product-info.index',
        'customer.product-category',
        'customer.browse-products',
        'customer.home.index',
        'customer.cart',
        'home'
    ];
    return productRoutes.includes(router.currentRoute.value.name as string);
});

const goToBrowseProducts = () => {
    router.push({ name: "customer.browse-products" });
};

const openLoginForm = () => {
    registerFormVisible.value = false;
    resetPasswordFormVisible.value = false;
    loginFormVisible.value = true;
};

const openRegisterForm = () => {
    loginFormVisible.value = false;
    resetPasswordFormVisible.value = false;
    registerFormVisible.value = true;
};

const openResetPasswordForm = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    resetPasswordFormVisible.value = true;
};

const handleSearch = () => {
    if (form.search && form.search.trim()) {
        router.push({
            name: 'customer.search-product',
            query: { q: form.search }
        });
    } else if (form.search === '' || form.search === null) {
        // If search is cleared, navigate to search page without query
        if (router.currentRoute.value.name === 'customer.search-product') {
            router.push({
                name: 'customer.search-product'
            });
        }
    }
};

// Watch for search input changes with debounce
let searchTimeout: number | null = null;
watch(() => form.search, (newValue, oldValue) => {
    // Only trigger if on search page
    if (router.currentRoute.value.name === 'customer.search-product') {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        searchTimeout = window.setTimeout(() => {
            if (newValue && newValue.trim()) {
                router.push({
                    name: 'customer.search-product',
                    query: { q: newValue }
                });
            } else if (newValue === '' || newValue === null) {
                // Clear search results when input is empty
                router.push({
                    name: 'customer.search-product'
                });
            }
        }, 500); // 500ms debounce
    }
});

const handleCodeSent = (data: { email: string; current_password: string; new_password: string; new_password_confirmation: string }) => {
    resetEmail.value = data.email;
    resetCurrentPassword.value = data.current_password;
    resetNewPassword.value = data.new_password;
    resetNewPasswordConfirmation.value = data.new_password_confirmation;
    resetPasswordFormVisible.value = false;
    verifyCodeFormVisible.value = true;
};

const handleResetSuccess = () => {
    verifyCodeFormVisible.value = false;
    resetPasswordFormVisible.value = false;
    loginFormVisible.value = true;
};

const loadBestSellingProducts = async () => {
    await loadBestSellingService.get("best-selling").then(() => {
        if (
            loadBestSellingService.request.status === 200 &&
            loadBestSellingService.request.data
        ) {
            products.value = loadBestSellingService.request.data;
            startCarousel();
        }
    });
};

const startCarousel = () => {
    const carouselProducts = Math.min(products.value.length, 3);
    if (carouselProducts > 1) {
        carouselInterval = window.setInterval(() => {
            currentSlide.value = (currentSlide.value + 1) % carouselProducts;
        }, 5000);
    }
};

const stopCarousel = () => {
    if (carouselInterval) {
        clearInterval(carouselInterval);
        carouselInterval = null;
    }
};

const router = useRouter();

const goRoute = (route: string, params: Record<string, string>) => {
    router.push({ name: route, params: params });
};

const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    target.style.display = 'none';
    const fallback = target.nextElementSibling || target.parentElement?.querySelector('.fallback-image');
    if (fallback) {
        (fallback as HTMLElement).style.display = 'flex';
    }
};

const addToCart = async (productId: number) => {
    if (!Page.user) {
        openLoginForm();
        return;
    }

    const cartData: CartFormInterface = {
        product_id: productId,
        quantity: 1
    };

    await addToCartService.post('customer/carts', cartData).then(() => {
        if (addToCartService.request.status === 200) {
            // Product added successfully - cart count will update via websocket
        }
    }).catch(() => {
        // Error adding to cart
        console.error('Failed to add product to cart:', addToCartService.request.message);
    });
};

const buyNow = (productId: number) => {
    if (!Page.user) {
        openLoginForm();
        return;
    }
    
    // Navigate to product page if logged in
    router.push({
        name: 'customer.product-info.index',
        params: { id: productId }
    });
};

const goToProductDetails = (productId: number) => {
    router.push({
        name: 'customer.product-info.index',
        params: { id: productId }
    });
};

onMounted(() => {
    CategoryStore.fetchCategories();
    loadBestSellingProducts();
});

onUnmounted(() => {
    stopCarousel();
});
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Hero Carousel Transitions */
.hero-slide-enter-active {
    transition: all 0.8s ease;
}

.hero-slide-leave-active {
    transition: all 0.8s ease;
}

.hero-slide-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.hero-slide-leave-to {
    opacity: 0;
    transform: translateX(-100%);
}

/* Blue scrollbar styling */
.category-scroll::-webkit-scrollbar {
    height: 8px;
}

.category-scroll::-webkit-scrollbar-track {
    background: #e5e7eb;
    border-radius: 10px;
}

.category-scroll::-webkit-scrollbar-thumb {
    background: #2563eb;
    border-radius: 10px;
}

.category-scroll::-webkit-scrollbar-thumb:hover {
    background: #1d4ed8;
}
</style>