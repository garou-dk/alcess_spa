<template>
    <div class="min-h-screen bg-gray-50">
        <div>
            <!-- Dynamic Hero Carousel Section -->
            <div class="relative mb-16">
                <div class="relative h-[300px] md:h-[400px] lg:h-[500px] overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
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
                                    <div class="flex items-center justify-center p-3 md:p-4 lg:p-8 xl:p-12">
                                        <div class="max-w-xs md:max-w-sm lg:max-w-xl">
                                            <div class="font-semibold text-xs mb-2 md:text-sm md:mb-3 lg:text-lg xl:text-xl lg:mb-6">
                                                <span class="text-blue-600">Top Selling</span>
                                                <span class="text-gray-900"> Product!</span>
                                            </div>
                                            <h1 class="font-bold text-gray-900 text-base mb-1 md:text-lg md:mb-2 lg:text-2xl xl:text-3xl 2xl:text-4xl lg:mb-4">
                                                {{ product.product_name }}
                                            </h1>
                                            <p class="text-gray-600 mb-2 text-xs md:mb-3 md:text-sm lg:mb-6 lg:text-lg">
                                                {{ product.category?.category_name || 'Featured Product' }}
                                            </p>
                                            <div class="mb-2 md:mb-3 lg:mb-6">
                                                <span class="font-bold text-gray-900 text-lg md:text-xl lg:text-4xl">
                                                    {{ CurrencyUtil.formatCurrency(product.product_price) }}
                                                </span>
                                            </div>
                                            <!-- Rating -->
                                            <div class="flex items-center gap-1 mb-3 md:mb-4 lg:mb-8">
                                                <i 
                                                    v-for="i in 5" 
                                                    :key="i" 
                                                    :class="[
                                                        i <= Math.round(Number(product.rates_avg_rate) || 0) ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-gray-300',
                                                        'text-xs md:text-sm lg:text-2xl'
                                                    ]" 
                                                />
                                                <span class="ml-2 text-gray-600 whitespace-nowrap text-xs md:text-sm lg:text-lg">
                                                    {{ product.rates_avg_rate ? Number(product.rates_avg_rate).toFixed(1) : '0' }}/5 Ratings
                                                </span>
                                            </div>
                                            <!-- Action Buttons -->
                                            <div class="flex gap-2 md:gap-3 lg:gap-4">
                                                <button 
                                                    @click="openCartModal(product)"
                                                    class="cursor-pointer rounded-full bg-blue-600 font-semibold text-white transition-all hover:bg-blue-700 hover:shadow-xl px-3 py-1.5 text-xs md:px-4 md:py-2 md:text-sm lg:px-8 lg:py-3 lg:text-base"
                                                >
                                                    Add to Cart
                                                </button>
                                                <button 
                                                    @click="viewDetails(product.product_id)"
                                                    class="cursor-pointer rounded-full bg-white text-gray-700 transition-all hover:shadow-lg border border-gray-200 px-3 py-1.5 text-xs md:px-4 md:py-2 md:text-sm lg:px-8 lg:py-3 lg:text-base"
                                                >
                                                    Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Right Side - Product Image -->
                                    <div class="flex items-center justify-center p-2 md:p-3 lg:p-8">
                                        <div class="relative">
                                            <img
                                                v-if="product.product_image"
                                                :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                                :alt="product.product_name"
                                                class="w-auto object-contain drop-shadow-2xl max-h-[150px] md:max-h-[250px] lg:max-h-[400px]"
                                                @error="handleImageError"
                                            />
                                            <div
                                                class="fallback-image flex items-center justify-center rounded-2xl bg-gradient-to-br from-gray-200 to-gray-300 h-[150px] w-[150px] md:h-[250px] md:w-[250px] lg:h-[400px] lg:w-[400px]"
                                                :style="{ display: product.product_image ? 'none' : 'flex' }"
                                            >
                                                <i class="pi pi-image text-gray-400 text-3xl md:text-5xl lg:text-8xl" />
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
                                        'text-center text-gray-800 transition-all duration-300 group-hover:underline group-hover:decoration-blue-600 group-hover:decoration-2 group-hover:underline-offset-4',
                                        isMobile ? 'text-xs font-medium' : isTablet ? 'text-sm font-medium' : 'text-base font-medium'
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
                                    isMobile ? 'text-sm' : isTablet ? 'text-sm' : 'text-sm'
                                ]">
                                    We're currently setting up our product categories. Check back soon for exciting new collections!
                                </p>
                            </div>
                            
                            <!-- Fade overlays -->
                            <div v-if="CategoryStore.categories && CategoryStore.categories.length > 0" :class="[
                                'pointer-events-none absolute left-0 top-0 bg-gradient-to-r from-gray-50 to-transparent',
                                isMobile ? 'bottom-6 w-16' : isTablet ? 'bottom-7 w-18' : 'bottom-8 w-20'
                            ]"></div>
                            <div v-if="CategoryStore.categories && CategoryStore.categories.length > 0" :class="[
                                'pointer-events-none absolute right-0 top-0 bg-gradient-to-l from-gray-50 to-transparent',
                                isMobile ? 'bottom-6 w-16' : isTablet ? 'bottom-7 w-18' : 'bottom-8 w-20'
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
                                        'group flex flex-col items-center gap-3 transition-transform duration-300 hover:-translate-y-2 flex-shrink-0',
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
                                        'text-center text-gray-800 line-clamp-2',
                                        isMobile ? 'text-xs font-medium' : isTablet ? 'text-sm font-medium' : 'text-base font-medium'
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
                                    isMobile ? 'text-sm' : isTablet ? 'text-sm' : 'text-sm'
                                ]">
                                    Our store is being stocked with amazing products. Stay tuned for our latest offerings!
                                </p>
                            </div>
                            
                            <!-- Fade overlays -->
                            <div v-if="products && products.length > 0" :class="[
                                'pointer-events-none absolute left-0 top-0 bg-gradient-to-r from-gray-50 to-transparent',
                                isMobile ? 'bottom-6 w-16' : isTablet ? 'bottom-7 w-18' : 'bottom-8 w-20'
                            ]"></div>
                            <div v-if="products && products.length > 0" :class="[
                                'pointer-events-none absolute right-0 top-0 bg-gradient-to-l from-gray-50 to-transparent',
                                isMobile ? 'bottom-6 w-16' : isTablet ? 'bottom-7 w-18' : 'bottom-8 w-20'
                            ]"></div>
                        </div>
                    </Transition>
                </section>

                <!-- Features Section -->
                <section class="mb-12">
                    <div class="grid gap-6 grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl bg-white shadow-md transition-all hover:-translate-y-1 hover:shadow-xl p-4 md:p-5 lg:p-6">
                            <div class="mb-3 flex items-center justify-center rounded-full bg-green-100 h-8 w-8 md:h-10 md:w-10 lg:h-12 lg:w-12">
                                <i class="pi pi-check-circle text-green-600 text-lg md:text-xl lg:text-2xl" />
                            </div>
                            <h3 class="mb-2 font-semibold text-gray-800 text-sm md:text-base lg:text-base">
                                Quality Guarantee
                            </h3>
                            <p class="text-gray-600 text-xs md:text-sm lg:text-sm">
                                All products are carefully selected and verified
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white shadow-md transition-all hover:-translate-y-1 hover:shadow-xl p-4 md:p-5 lg:p-6">
                            <div class="mb-3 flex items-center justify-center rounded-full bg-blue-100 h-8 w-8 md:h-10 md:w-10 lg:h-12 lg:w-12">
                                <i class="pi pi-truck text-blue-600 text-lg md:text-xl lg:text-2xl" />
                            </div>
                            <h3 class="mb-2 font-semibold text-gray-800 text-sm md:text-base lg:text-base">
                                Fast Delivery
                            </h3>
                            <p class="text-gray-600 text-xs md:text-sm lg:text-sm">
                                Get your orders delivered quickly and safely
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white shadow-md transition-all hover:-translate-y-1 hover:shadow-xl p-4 md:p-5 lg:p-6">
                            <div class="mb-3 flex items-center justify-center rounded-full bg-purple-100 h-8 w-8 md:h-10 md:w-10 lg:h-12 lg:w-12">
                                <i class="pi pi-shield text-purple-600 text-lg md:text-xl lg:text-2xl" />
                            </div>
                            <h3 class="mb-2 font-semibold text-gray-800 text-sm md:text-base lg:text-base">
                                Secure Payment
                            </h3>
                            <p class="text-gray-600 text-xs md:text-sm lg:text-sm">
                                Your payment information is always protected
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white shadow-md transition-all hover:-translate-y-1 hover:shadow-xl p-4 md:p-5 lg:p-6">
                            <div class="mb-3 flex items-center justify-center rounded-full bg-orange-100 h-8 w-8 md:h-10 md:w-10 lg:h-12 lg:w-12">
                                <i class="pi pi-sync text-orange-600 text-lg md:text-xl lg:text-2xl" />
                            </div>
                            <h3 class="mb-2 font-semibold text-gray-800 text-sm md:text-base lg:text-base">
                                Easy Returns
                            </h3>
                            <p class="text-gray-600 text-xs md:text-sm lg:text-sm">
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

        <!-- Add to Cart Modal -->
        <Dialog 
            v-model:visible="showCartModal" 
            modal 
            :style="{ width: '400px' }" 
            :pt="{
                root: { class: 'rounded-2xl' },
                header: { class: 'bg-blue-600 text-white rounded-t-2xl px-5 py-2.5' },
                content: { class: 'px-5 pt-5 pb-4' },
                footer: { class: 'px-5 pb-4 pt-0' }
            }"
        >
            <template #header>
                <div class="flex items-center justify-between w-full">
                    <h2 class="text-lg font-bold">Add to Cart</h2>
                </div>
            </template>

            <div v-if="selectedProduct">
                <div class="flex gap-6 items-center mb-4">
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

                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-800 mb-4 pt-1 line-clamp-2">
                            {{ selectedProduct.product_name }}
                        </h3>
                        <p class="text-2xl font-bold text-gray-900 mb-4">
                            {{ CurrencyUtil.formatCurrency(selectedProduct.product_price) }}
                        </p>
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
                                    class="w-12 h-7 text-center text-sm font-semibold border border-gray-300 rounded focus:outline-none focus:border-blue-600"
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

                <div class="border-t border-gray-200 my-3"></div>

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
import { computed, onMounted, onUnmounted, ref } from "vue";
import { useResponsive } from "@/composables/useResponsive";
import HeroBg from "@/../img/hero-bg.jpg";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";
import Dialog from 'primevue/dialog';
import { useToast } from 'vue-toastification';
import Page from '@/stores/Page';
import { useRouter } from "vue-router";
import { CartFormInterface } from "@/interfaces/CartInterface";

const { isMobile, isTablet, isDesktop } = useResponsive();

const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const addToCartService = useAxiosUtil<CartFormInterface, null>();
const products = ref<ProductInterface[]>([]);
const activeView = ref<'categories' | 'products'>('categories');
const currentSlide = ref<number>(0);
let carouselInterval: number | null = null;
const showCartModal = ref(false);
const selectedProduct = ref<ProductInterface | null>(null);
const cartQuantity = ref(1);
const toast = useToast();
const router = useRouter();

const appName = import.meta.env.VITE_APP_NAME;

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

const goRoute = (route: string, params: Record<string, string>) => {
    router.push({ name: route, params: params });
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

const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    target.style.display = 'none';
    const fallback = target.nextElementSibling || target.parentElement?.querySelector('.fallback-image');
    if (fallback) {
        (fallback as HTMLElement).style.display = 'flex';
    }
};

const openCartModal = (product: ProductInterface) => {
    if (!Page.user) {
        toast.error('Please login to add items to cart');
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

const viewDetails = (productId: number) => {
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
<style
 scoped>
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
