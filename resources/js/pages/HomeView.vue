<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Unified Responsive Header - Uses CSS for responsiveness to prevent layout jumps -->
        <header class="bg-blue-600 sticky top-0 z-50 shadow-md">
            <nav class="header-nav">
                <div class="header-container">
                    <!-- Left: Logo Section -->
                    <RouterLink :to="{ name: 'home' }" class="header-logo-link">
                        <div class="header-logo-wrapper">
                            <img :src="Icon" class="header-logo-img" alt="Store Icon" />
                        </div>
                        <h1 class="header-title">{{ appName }}</h1>
                    </RouterLink>
                    
                    <!-- Center: Search Bar (desktop/tablet only when on product page) -->
                    <div v-if="isProductPage" class="header-search-container">
                        <form @submit.prevent="handleSearch" class="w-full">
                            <div class="relative">
                                <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Search for products..."
                                    class="header-search-input"
                                />
                            </div>
                        </form>
                    </div>
                    
                    <!-- Right: Action buttons (consistent sizing) -->
                    <div class="header-actions">
                        <a
                            href="https://www.facebook.com/alcesslaptopstore"
                            target="_blank"
                            class="header-action-btn header-action-btn-facebook"
                            title="Visit our Facebook page"
                        >
                            <i class="pi pi-facebook"></i>
                        </a>
                        <button
                            type="button"
                            class="header-action-btn header-action-btn-secondary"
                            @click="goToBrowseProducts"
                            title="Browse All Products"
                        >
                            <i class="pi pi-th-large"></i>
                        </button>
                        <button
                            v-if="!Page.user"
                            type="button"
                            class="header-action-btn header-action-btn-secondary"
                            @click="openLoginForm()"
                            title="Login"
                        >
                            <i class="pi pi-user"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Search Bar (shown below main header on mobile when on product page) -->
                <div v-if="isProductPage" class="header-mobile-search">
                    <form @submit.prevent="handleSearch">
                        <div class="relative">
                            <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="form.search"
                                type="text"
                                placeholder="Search products..."
                                class="header-search-input"
                            />
                        </div>
                    </form>
                </div>
            </nav>
        </header>


        <!-- Enhanced Hero Section with Modern Design -->
        <div class="relative mb-16 overflow-hidden">
            <!-- Animated Background Gradient -->
            <div :class="[
                'relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800',
                isMobile ? 'min-h-[400px]' : isTablet ? 'min-h-[450px]' : 'min-h-[550px]'
            ]">
                <!-- Decorative Floating Elements -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-10 left-10 w-72 h-72 bg-white/5 rounded-full blur-3xl animate-pulse-slow"></div>
                    <div class="absolute bottom-10 right-10 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
                    <div class="absolute top-1/2 left-1/3 w-48 h-48 bg-indigo-400/10 rounded-full blur-2xl animate-float"></div>
                </div>
                
                <!-- Main Hero Content -->
                <div v-if="showCarousel" class="relative h-full z-10">
                    <TransitionGroup name="hero-slide">
                        <div
                            v-for="(product, index) in [products.slice(0, 3)[currentSlide]]"
                            :key="currentSlide"
                            class="absolute inset-0"
                        >
                            <div class="container mx-auto px-4 h-full">
                                <div :class="[
                                    'grid h-full items-center',
                                    isMobile ? 'grid-cols-1 gap-4 py-8' : 'grid-cols-2 gap-8'
                                ]">
                                    <!-- Left Side - Product Info -->
                                    <div :class="[
                                        'flex flex-col justify-center',
                                        isMobile ? 'text-center order-2' : 'text-left'
                                    ]">
                                        <!-- Badge -->
                                        <div :class="[
                                            'inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20',
                                            isMobile ? 'px-3 py-1.5 mb-3 mx-auto' : isTablet ? 'px-4 py-2 mb-4' : 'px-5 py-2.5 mb-6 self-start'
                                        ]">
                                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                            <span :class="[
                                                'font-medium text-white/90',
                                                isMobile ? 'text-xs' : 'text-sm'
                                            ]">🔥 Best Seller</span>
                                        </div>
                                        
                                        <!-- Product Name -->
                                        <h1 :class="[
                                            'font-bold text-white leading-tight',
                                            isMobile ? 'text-2xl mb-2' : isTablet ? 'text-3xl mb-3' : 'text-4xl lg:text-5xl mb-4'
                                        ]">
                                            {{ product.product_name }}
                                        </h1>
                                        
                                        <!-- Category -->
                                        <p :class="[
                                            'text-blue-200',
                                            isMobile ? 'text-sm mb-3' : isTablet ? 'text-base mb-4' : 'text-lg mb-6'
                                        ]">
                                            {{ product.category?.category_name || 'Premium Electronics' }}
                                        </p>
                                        
                                        <!-- Price Card -->
                                        <div :class="[
                                            'inline-flex items-center bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20',
                                            isMobile ? 'px-4 py-3 mb-4 mx-auto' : isTablet ? 'px-5 py-4 mb-5' : 'px-6 py-4 mb-8 self-start'
                                        ]">
                                            <div>
                                                <p :class="['text-blue-200', isMobile ? 'text-xs' : 'text-sm']">Starting at</p>
                                                <span :class="[
                                                    'font-bold text-white',
                                                    isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-4xl'
                                                ]">
                                                    {{ CurrencyUtil.formatCurrency(product.product_price) }}
                                                </span>
                                            </div>
                                            <div class="ml-4 pl-4 border-l border-white/20">
                                                <div class="flex items-center gap-1 mb-1">
                                                    <i v-for="i in 5" :key="i" :class="[
                                                        i <= 4 ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-white/30',
                                                        isMobile ? 'text-xs' : 'text-sm'
                                                    ]"></i>
                                                </div>
                                                <p :class="['text-white/70', isMobile ? 'text-xs' : 'text-sm']">Top Rated</p>
                                            </div>
                                        </div>
                                        
                                        <!-- CTA Buttons -->
                                        <div :class="[
                                            'flex',
                                            isMobile ? 'gap-3 justify-center' : 'gap-4'
                                        ]">
                                            <button 
                                                @click="addToCart(product.product_id)"
                                                :disabled="addToCartService.request.loading"
                                                :class="[
                                                    'group relative overflow-hidden rounded-full bg-white font-semibold text-blue-700 transition-all duration-300 hover:shadow-2xl hover:shadow-white/25 disabled:opacity-50',
                                                    isMobile ? 'px-5 py-2.5 text-sm' : isTablet ? 'px-6 py-3 text-base' : 'px-8 py-4 text-lg'
                                                ]"
                                            >
                                                <span class="relative z-10 flex items-center gap-2">
                                                    <i v-if="addToCartService.request.loading" class="pi pi-spin pi-spinner"></i>
                                                    <i v-else class="pi pi-shopping-cart"></i>
                                                    Add to Cart
                                                </span>
                                            </button>
                                            <button 
                                                @click="goToProductDetails(product.product_id)"
                                                :class="[
                                                    'rounded-full border-2 border-white/30 text-white font-semibold transition-all duration-300 hover:bg-white/10 hover:border-white/50',
                                                    isMobile ? 'px-5 py-2.5 text-sm' : isTablet ? 'px-6 py-3 text-base' : 'px-8 py-4 text-lg'
                                                ]"
                                            >
                                                View Details
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Side - Product Image -->
                                    <div :class="[
                                        'flex items-center justify-center relative',
                                        isMobile ? 'order-1' : ''
                                    ]">
                                        <!-- Glow Effect -->
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent rounded-full blur-3xl"></div>
                                        
                                        <div class="relative">
                                            <img
                                                v-if="product.product_image"
                                                :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                                :alt="product.product_name"
                                                :class="[
                                                    'w-auto object-contain drop-shadow-2xl transform transition-transform duration-500 hover:scale-105',
                                                    isMobile ? 'max-h-[180px]' : isTablet ? 'max-h-[280px]' : 'max-h-[400px]'
                                                ]"
                                                @error="handleImageError"
                                            />
                                            <div
                                                :class="[
                                                    'fallback-image flex items-center justify-center rounded-3xl bg-white/10 backdrop-blur-sm',
                                                    isMobile ? 'h-[180px] w-[180px]' : isTablet ? 'h-[280px] w-[280px]' : 'h-[400px] w-[400px]'
                                                ]"
                                                :style="{ display: product.product_image ? 'none' : 'flex' }"
                                            >
                                                <i :class="[
                                                    'pi pi-image text-white/50',
                                                    isMobile ? 'text-4xl' : isTablet ? 'text-6xl' : 'text-8xl'
                                                ]"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>
                    
                    <!-- Modern Carousel Indicators -->
                    <div :class="[
                        'absolute left-1/2 -translate-x-1/2 flex gap-2',
                        isMobile ? 'bottom-4' : 'bottom-8'
                    ]">
                        <button
                            v-for="(product, index) in products.slice(0, 3)"
                            :key="index"
                            @click="currentSlide = index"
                            :class="[
                                'transition-all duration-300 rounded-full',
                                currentSlide === index 
                                    ? 'w-10 h-2 bg-white' 
                                    : 'w-2 h-2 bg-white/40 hover:bg-white/60'
                            ]"
                        ></button>
                    </div>
                </div>

                <!-- Fallback Static Hero -->
                <div v-else class="relative h-full min-h-[inherit] z-10">
                    <div class="absolute inset-0 flex items-center">
                        <div class="container mx-auto px-6">
                            <div :class="[
                                'text-white',
                                isMobile ? 'text-center max-w-full' : 'max-w-2xl'
                            ]">
                                <div :class="[
                                    'inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20 mb-6',
                                    isMobile ? 'px-3 py-1.5' : 'px-4 py-2'
                                ]">
                                    <i class="pi pi-sparkles text-yellow-400"></i>
                                    <span :class="['font-medium', isMobile ? 'text-xs' : 'text-sm']">Premium Tech Store</span>
                                </div>
                                <h1 :class="[
                                    'font-bold leading-tight mb-4 animate-fade-in',
                                    isMobile ? 'text-3xl' : isTablet ? 'text-4xl' : 'text-5xl lg:text-6xl'
                                ]">
                                    Welcome to <span class="text-blue-300">{{ appName }}</span>
                                </h1>
                                <p :class="[
                                    'text-blue-100 mb-8',
                                    isMobile ? 'text-base' : 'text-lg md:text-xl'
                                ]">
                                    Discover premium laptops, phones & computers at unbeatable prices with genuine warranty.
                                </p>
                                <button 
                                    @click="goToBrowseProducts"
                                    :class="[
                                        'group inline-flex items-center gap-3 bg-white text-blue-700 font-semibold rounded-full transition-all hover:shadow-2xl hover:shadow-white/20',
                                        isMobile ? 'px-6 py-3 text-base' : 'px-8 py-4 text-lg'
                                    ]"
                                >
                                    <span>Shop Now</span>
                                    <i class="pi pi-arrow-right transition-transform group-hover:translate-x-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Wave Divider -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                    <path d="M0 120L60 110C120 100 240 80 360 75C480 70 600 80 720 85C840 90 960 90 1080 85C1200 80 1320 70 1380 65L1440 60V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#f9fafb"/>
                </svg>
            </div>
        </div>

        <!-- Trust Indicators Section -->
        <div class="container mx-auto px-4 -mt-8 mb-12 relative z-10">
            <div :class="[
                'grid bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100',
                isMobile ? 'grid-cols-2 gap-4 p-4' : isTablet ? 'grid-cols-4 gap-4 p-5' : 'grid-cols-4 gap-6 p-6'
            ]">
                <div class="flex items-center gap-3 group">
                    <div :class="[
                        'flex-shrink-0 flex items-center justify-center rounded-xl bg-gradient-to-br from-green-50 to-green-100 transition-transform group-hover:scale-110',
                        isMobile ? 'w-10 h-10' : 'w-12 h-12'
                    ]">
                        <i :class="['pi pi-verified text-green-600', isMobile ? 'text-lg' : 'text-xl']"></i>
                    </div>
                    <div>
                        <p :class="['font-semibold text-gray-800', isMobile ? 'text-xs' : 'text-sm']">100% Genuine</p>
                        <p :class="['text-gray-500', isMobile ? 'text-xs' : 'text-xs']">Authentic Products</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 group">
                    <div :class="[
                        'flex-shrink-0 flex items-center justify-center rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 transition-transform group-hover:scale-110',
                        isMobile ? 'w-10 h-10' : 'w-12 h-12'
                    ]">
                        <i :class="['pi pi-truck text-blue-600', isMobile ? 'text-lg' : 'text-xl']"></i>
                    </div>
                    <div>
                        <p :class="['font-semibold text-gray-800', isMobile ? 'text-xs' : 'text-sm']">Fast Delivery</p>
                        <p :class="['text-gray-500', isMobile ? 'text-xs' : 'text-xs']">Nationwide Shipping</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 group">
                    <div :class="[
                        'flex-shrink-0 flex items-center justify-center rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 transition-transform group-hover:scale-110',
                        isMobile ? 'w-10 h-10' : 'w-12 h-12'
                    ]">
                        <i :class="['pi pi-shield text-purple-600', isMobile ? 'text-lg' : 'text-xl']"></i>
                    </div>
                    <div>
                        <p :class="['font-semibold text-gray-800', isMobile ? 'text-xs' : 'text-sm']">Warranty</p>
                        <p :class="['text-gray-500', isMobile ? 'text-xs' : 'text-xs']">Official Coverage</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 group">
                    <div :class="[
                        'flex-shrink-0 flex items-center justify-center rounded-xl bg-gradient-to-br from-orange-50 to-orange-100 transition-transform group-hover:scale-110',
                        isMobile ? 'w-10 h-10' : 'w-12 h-12'
                    ]">
                        <i :class="['pi pi-headphones text-orange-600', isMobile ? 'text-lg' : 'text-xl']"></i>
                    </div>
                    <div>
                        <p :class="['font-semibold text-gray-800', isMobile ? 'text-xs' : 'text-sm']">24/7 Support</p>
                        <p :class="['text-gray-500', isMobile ? 'text-xs' : 'text-xs']">We're Here to Help</p>
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

            <!-- Enhanced Features Section -->
            <section :class="['relative', isMobile ? 'mb-12' : isTablet ? 'mb-16' : 'mb-20']">
                <!-- Section Header -->
                <div :class="['text-center', isMobile ? 'mb-8' : 'mb-12']">
                    <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4">
                        Why Choose Us
                    </span>
                    <h2 :class="[
                        'font-bold text-gray-800',
                        isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-4xl'
                    ]">
                        Shop with <span class="text-blue-600">Confidence</span>
                    </h2>
                </div>

                <div :class="[
                    'grid gap-6',
                    isMobile ? 'grid-cols-1' : isTablet ? 'grid-cols-2' : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4'
                ]">
                    <!-- Feature Card 1 -->
                    <div :class="[
                        'group relative rounded-2xl bg-white border border-gray-100 shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl overflow-hidden',
                        isMobile ? 'p-5' : isTablet ? 'p-6' : 'p-8'
                    ]">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-50 to-transparent rounded-bl-full opacity-50"></div>
                        <div :class="[
                            'relative mb-4 flex items-center justify-center rounded-2xl bg-gradient-to-br from-green-100 to-green-50',
                            isMobile ? 'h-14 w-14' : 'h-16 w-16'
                        ]">
                            <i :class="[
                                'pi pi-check-circle text-green-600',
                                isMobile ? 'text-2xl' : 'text-3xl'
                            ]"></i>
                        </div>
                        <h3 :class="[
                            'mb-2 font-bold text-gray-800',
                            isMobile ? 'text-base' : 'text-lg'
                        ]">
                            Quality Guarantee
                        </h3>
                        <p :class="[
                            'text-gray-500 leading-relaxed',
                            isMobile ? 'text-sm' : 'text-sm'
                        ]">
                            All products are 100% genuine and carefully verified before shipping
                        </p>
                    </div>
                    
                    <!-- Feature Card 2 -->
                    <div :class="[
                        'group relative rounded-2xl bg-white border border-gray-100 shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl overflow-hidden',
                        isMobile ? 'p-5' : isTablet ? 'p-6' : 'p-8'
                    ]">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-50 to-transparent rounded-bl-full opacity-50"></div>
                        <div :class="[
                            'relative mb-4 flex items-center justify-center rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50',
                            isMobile ? 'h-14 w-14' : 'h-16 w-16'
                        ]">
                            <i :class="[
                                'pi pi-truck text-blue-600',
                                isMobile ? 'text-2xl' : 'text-3xl'
                            ]"></i>
                        </div>
                        <h3 :class="[
                            'mb-2 font-bold text-gray-800',
                            isMobile ? 'text-base' : 'text-lg'
                        ]">
                            Fast Delivery
                        </h3>
                        <p :class="[
                            'text-gray-500 leading-relaxed',
                            isMobile ? 'text-sm' : 'text-sm'
                        ]">
                            Nationwide shipping with tracking. Get your orders delivered quickly
                        </p>
                    </div>
                    
                    <!-- Feature Card 3 -->
                    <div :class="[
                        'group relative rounded-2xl bg-white border border-gray-100 shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl overflow-hidden',
                        isMobile ? 'p-5' : isTablet ? 'p-6' : 'p-8'
                    ]">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-50 to-transparent rounded-bl-full opacity-50"></div>
                        <div :class="[
                            'relative mb-4 flex items-center justify-center rounded-2xl bg-gradient-to-br from-purple-100 to-purple-50',
                            isMobile ? 'h-14 w-14' : 'h-16 w-16'
                        ]">
                            <i :class="[
                                'pi pi-shield text-purple-600',
                                isMobile ? 'text-2xl' : 'text-3xl'
                            ]"></i>
                        </div>
                        <h3 :class="[
                            'mb-2 font-bold text-gray-800',
                            isMobile ? 'text-base' : 'text-lg'
                        ]">
                            Secure Payment
                        </h3>
                        <p :class="[
                            'text-gray-500 leading-relaxed',
                            isMobile ? 'text-sm' : 'text-sm'
                        ]">
                            Multiple secure payment options. Your information is always protected
                        </p>
                    </div>
                    
                    <!-- Feature Card 4 -->
                    <div :class="[
                        'group relative rounded-2xl bg-white border border-gray-100 shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl overflow-hidden',
                        isMobile ? 'p-5' : isTablet ? 'p-6' : 'p-8'
                    ]">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-50 to-transparent rounded-bl-full opacity-50"></div>
                        <div :class="[
                            'relative mb-4 flex items-center justify-center rounded-2xl bg-gradient-to-br from-orange-100 to-orange-50',
                            isMobile ? 'h-14 w-14' : 'h-16 w-16'
                        ]">
                            <i :class="[
                                'pi pi-sync text-orange-600',
                                isMobile ? 'text-2xl' : 'text-3xl'
                            ]"></i>
                        </div>
                        <h3 :class="[
                            'mb-2 font-bold text-gray-800',
                            isMobile ? 'text-base' : 'text-lg'
                        ]">
                            Easy Returns
                        </h3>
                        <p :class="[
                            'text-gray-500 leading-relaxed',
                            isMobile ? 'text-sm' : 'text-sm'
                        ]">
                            7-day replacement policy for defective items. Shop worry-free
                        </p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Call to Action Section -->
        <section class="relative overflow-hidden mb-16">
            <div :class="[
                'bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800',
                isMobile ? 'py-12 px-4' : isTablet ? 'py-16 px-6' : 'py-20 px-8'
            ]">
                <!-- Decorative Elements -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-400/10 rounded-full blur-3xl"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] border border-white/5 rounded-full"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] border border-white/10 rounded-full"></div>
                </div>

                <div class="container mx-auto relative z-10">
                    <div :class="[
                        'flex flex-col items-center text-center',
                        isMobile ? 'gap-6' : 'gap-8'
                    ]">
                        <!-- Badge -->
                        <div :class="[
                            'inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20',
                            isMobile ? 'px-3 py-1.5' : 'px-4 py-2'
                        ]">
                            <i class="pi pi-star-fill text-yellow-400"></i>
                            <span class="text-white/90 font-medium text-sm">Special Offers Available</span>
                        </div>

                        <!-- Heading -->
                        <div>
                            <h2 :class="[
                                'font-bold text-white mb-4',
                                isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-4xl lg:text-5xl'
                            ]">
                                Ready to Upgrade Your Tech?
                            </h2>
                            <p :class="[
                                'text-blue-100 max-w-2xl mx-auto',
                                isMobile ? 'text-base' : 'text-lg'
                            ]">
                                Explore our wide selection of laptops, phones, and computers. Find the perfect device for your needs at competitive prices.
                            </p>
                        </div>

                        <!-- CTA Buttons -->
                        <div :class="[
                            'flex flex-wrap justify-center',
                            isMobile ? 'gap-3' : 'gap-4'
                        ]">
                            <button 
                                @click="goToBrowseProducts"
                                :class="[
                                    'group inline-flex items-center gap-2 bg-white text-blue-700 font-semibold rounded-full transition-all duration-300 hover:shadow-2xl hover:shadow-white/20',
                                    isMobile ? 'px-6 py-3 text-base' : 'px-8 py-4 text-lg'
                                ]"
                            >
                                <i class="pi pi-th-large"></i>
                                Browse Products
                                <i class="pi pi-arrow-right transition-transform group-hover:translate-x-1"></i>
                            </button>
                            <a 
                                href="https://www.facebook.com/alcesslaptopstore"
                                target="_blank"
                                :class="[
                                    'inline-flex items-center gap-2 border-2 border-white/30 text-white font-semibold rounded-full transition-all duration-300 hover:bg-white/10 hover:border-white/50',
                                    isMobile ? 'px-6 py-3 text-base' : 'px-8 py-4 text-lg'
                                ]"
                            >
                                <i class="pi pi-facebook"></i>
                                Message Us
                            </a>
                        </div>

                        <!-- Stats -->
                        <div :class="[
                            'grid bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20',
                            isMobile ? 'grid-cols-3 gap-4 px-4 py-5 mt-4' : 'grid-cols-3 gap-8 px-8 py-6 mt-6'
                        ]">
                            <div class="text-center">
                                <p :class="['font-bold text-white', isMobile ? 'text-2xl' : 'text-3xl']">500+</p>
                                <p :class="['text-blue-200', isMobile ? 'text-xs' : 'text-sm']">Products</p>
                            </div>
                            <div class="text-center border-l border-r border-white/20">
                                <p :class="['font-bold text-white', isMobile ? 'text-2xl' : 'text-3xl']">1K+</p>
                                <p :class="['text-blue-200', isMobile ? 'text-xs' : 'text-sm']">Happy Customers</p>
                            </div>
                            <div class="text-center">
                                <p :class="['font-bold text-white', isMobile ? 'text-2xl' : 'text-3xl']">4.8</p>
                                <p :class="['text-blue-200', isMobile ? 'text-xs' : 'text-sm']">Star Rating</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


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
                            Your trusted destination for quality laptops, phones, and computers. We offer genuine products with warranty and excellent customer service.
                        </p>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold">Quick Links</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><button @click="openFooterModal('about')" class="hover:text-white transition-colors text-left">About Us</button></li>
                            <li><button @click="openFooterModal('contact')" class="hover:text-white transition-colors text-left">Contact</button></li>
                            <li><button @click="openFooterModal('faqs')" class="hover:text-white transition-colors text-left">FAQs</button></li>
                            <li><button @click="openFooterModal('shipping')" class="hover:text-white transition-colors text-left">Shipping Info</button></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold">Customer Service</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><button @click="handleTrackOrder" class="hover:text-white transition-colors text-left">Track Order</button></li>
                            <li><button @click="openFooterModal('returns')" class="hover:text-white transition-colors text-left">Returns & Warranty</button></li>
                            <li><button @click="openFooterModal('privacy')" class="hover:text-white transition-colors text-left">Privacy Policy</button></li>
                            <li><button @click="openFooterModal('terms')" class="hover:text-white transition-colors text-left">Terms & Conditions</button></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 font-semibold">Connect With Us</h4>
                        <div class="flex gap-3">
                            <a 
                                href="https://www.facebook.com/alcesslaptopstore" 
                                target="_blank"
                                class="rounded-full bg-white/10 p-3 transition-colors hover:bg-white/20"
                                title="Visit our Facebook page"
                            >
                                <i class="pi pi-facebook" />
                            </a>
                        </div>
                        <p class="mt-4 text-sm text-gray-400">
                            <i class="pi pi-phone mr-2"></i>Contact us for inquiries
                        </p>
                        <p class="mt-2 text-sm text-gray-400">
                            <i class="pi pi-envelope mr-2"></i>support@alcess.com
                        </p>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-700 pt-8 text-center text-sm text-gray-400">
                    <p>&copy; {{ new Date().getFullYear() }} {{ appName }}. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Footer Modal Dialogs -->
        <Dialog
            v-model:visible="footerModalVisible"
            modal
            :header="footerModalTitle"
            :style="{ width: isMobile ? '95vw' : '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '95vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <!-- About Us Content -->
            <div v-if="footerModalType === 'about'" class="space-y-4">
                <div class="flex items-center gap-4 mb-6">
                    <div class="rounded-full bg-blue-100 p-4">
                        <i class="pi pi-building text-3xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ appName }}</h3>
                        <p class="text-gray-500">Your Trusted Tech Partner</p>
                    </div>
                </div>
                <p class="text-gray-700 leading-relaxed">
                    Welcome to {{ appName }}! We are a dedicated online retailer specializing in high-quality laptops, smartphones, desktop computers, and computer accessories. 
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Our mission is to provide our customers with the latest technology products at competitive prices, backed by excellent customer service and reliable after-sales support.
                </p>
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <i class="pi pi-check-circle text-2xl text-blue-600 mb-2"></i>
                        <p class="font-semibold text-gray-800">100% Genuine</p>
                        <p class="text-sm text-gray-600">Authentic products only</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <i class="pi pi-shield text-2xl text-green-600 mb-2"></i>
                        <p class="font-semibold text-gray-800">Warranty</p>
                        <p class="text-sm text-gray-600">Full manufacturer warranty</p>
                    </div>
                </div>
            </div>

            <!-- Contact Content -->
            <div v-if="footerModalType === 'contact'" class="space-y-4">
                <p class="text-gray-700 mb-6">Have questions or need assistance? We're here to help!</p>
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="rounded-full bg-blue-100 p-3">
                            <i class="pi pi-facebook text-xl text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Facebook</h4>
                            <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="text-blue-600 hover:underline">
                                facebook.com/alcesslaptopstore
                            </a>
                            <p class="text-sm text-gray-500 mt-1">Message us for quick responses</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="rounded-full bg-green-100 p-3">
                            <i class="pi pi-envelope text-xl text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Email</h4>
                            <p class="text-gray-600">support@alcess.com</p>
                            <p class="text-sm text-gray-500 mt-1">We respond within 24 hours</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQs Content -->
            <div v-if="footerModalType === 'faqs'" class="space-y-4">
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="font-semibold text-gray-800 mb-2">How do I place an order?</h4>
                        <p class="text-gray-600 text-sm">Browse our products, add items to your cart, and proceed to checkout. You'll need to create an account or log in to complete your purchase.</p>
                    </div>
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="font-semibold text-gray-800 mb-2">What payment methods do you accept?</h4>
                        <p class="text-gray-600 text-sm">We accept bank transfers, GCash, and cash on delivery for select areas. Payment instructions will be provided after your order is confirmed.</p>
                    </div>
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Are your products genuine?</h4>
                        <p class="text-gray-600 text-sm">Yes! All our products are 100% genuine and come with official manufacturer warranty. We source directly from authorized distributors.</p>
                    </div>
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="font-semibold text-gray-800 mb-2">How long does delivery take?</h4>
                        <p class="text-gray-600 text-sm">Delivery typically takes 3-7 business days depending on your location. You'll receive tracking information once your order ships.</p>
                    </div>
                    <div class="pb-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Can I cancel my order?</h4>
                        <p class="text-gray-600 text-sm">Yes, you can cancel your order before payment is confirmed. Once payment is verified, cancellation may no longer be possible.</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Info Content -->
            <div v-if="footerModalType === 'shipping'" class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <i class="pi pi-truck text-2xl text-blue-600"></i>
                        <div>
                            <h4 class="font-semibold text-gray-800">Nationwide Delivery</h4>
                            <p class="text-sm text-gray-600">We deliver to all provinces in the Philippines</p>
                        </div>
                    </div>
                </div>
                <h4 class="font-semibold text-gray-800">Delivery Timeline</h4>
                <ul class="list-disc list-inside text-gray-600 space-y-2 text-sm">
                    <li>Metro Manila: 1-3 business days</li>
                    <li>Luzon (outside Metro Manila): 3-5 business days</li>
                    <li>Visayas: 5-7 business days</li>
                    <li>Mindanao: 5-7 business days</li>
                </ul>
                <h4 class="font-semibold text-gray-800 mt-4">Shipping Fees</h4>
                <p class="text-gray-600 text-sm">Shipping fees are calculated based on your location and will be shown during checkout after your order is confirmed by our team.</p>
            </div>

            <!-- Returns & Warranty Content -->
            <div v-if="footerModalType === 'returns'" class="space-y-4">
                <div class="bg-green-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <i class="pi pi-shield text-2xl text-green-600"></i>
                        <div>
                            <h4 class="font-semibold text-gray-800">Warranty Coverage</h4>
                            <p class="text-sm text-gray-600">All products come with manufacturer warranty</p>
                        </div>
                    </div>
                </div>
                <h4 class="font-semibold text-gray-800">Return Policy</h4>
                <ul class="list-disc list-inside text-gray-600 space-y-2 text-sm">
                    <li>7-day replacement for defective products</li>
                    <li>Products must be in original packaging</li>
                    <li>Include all accessories and documentation</li>
                    <li>Contact us first before returning any item</li>
                </ul>
                <h4 class="font-semibold text-gray-800 mt-4">Warranty Claims</h4>
                <p class="text-gray-600 text-sm">For warranty claims, please contact us through Facebook or email with your order details and a description of the issue. We'll guide you through the process.</p>
            </div>

            <!-- Privacy Policy Content -->
            <div v-if="footerModalType === 'privacy'" class="space-y-4 max-h-96 overflow-y-auto">
                <p class="text-gray-600 text-sm">Last updated: January 2025</p>
                <h4 class="font-semibold text-gray-800">Information We Collect</h4>
                <p class="text-gray-600 text-sm">We collect information you provide when creating an account, placing orders, or contacting us. This includes your name, email, phone number, and shipping address.</p>
                <h4 class="font-semibold text-gray-800">How We Use Your Information</h4>
                <p class="text-gray-600 text-sm">Your information is used to process orders, provide customer support, and improve our services. We never sell your personal data to third parties.</p>
                <h4 class="font-semibold text-gray-800">Data Security</h4>
                <p class="text-gray-600 text-sm">We implement security measures to protect your personal information. Payment details are processed securely and we do not store credit card information.</p>
            </div>

            <!-- Terms & Conditions Content -->
            <div v-if="footerModalType === 'terms'" class="space-y-4 max-h-96 overflow-y-auto">
                <p class="text-gray-600 text-sm">Last updated: January 2025</p>
                <h4 class="font-semibold text-gray-800">Order Acceptance</h4>
                <p class="text-gray-600 text-sm">All orders are subject to acceptance and availability. We reserve the right to refuse or cancel any order for any reason.</p>
                <h4 class="font-semibold text-gray-800">Pricing</h4>
                <p class="text-gray-600 text-sm">Prices are subject to change without notice. The final price will be confirmed when your order is accepted.</p>
                <h4 class="font-semibold text-gray-800">Product Descriptions</h4>
                <p class="text-gray-600 text-sm">We strive to provide accurate product descriptions and images. However, actual products may vary slightly from images shown.</p>
                <h4 class="font-semibold text-gray-800">Limitation of Liability</h4>
                <p class="text-gray-600 text-sm">{{ appName }} is not liable for any indirect, incidental, or consequential damages arising from the use of our products or services.</p>
            </div>
        </Dialog>
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

// Footer modal state
const footerModalVisible = ref<boolean>(false);
const footerModalType = ref<string>('about');
const footerModalTitle = computed(() => {
    const titles: Record<string, string> = {
        'about': 'About Us',
        'contact': 'Contact Us',
        'faqs': 'Frequently Asked Questions',
        'shipping': 'Shipping Information',
        'returns': 'Returns & Warranty',
        'privacy': 'Privacy Policy',
        'terms': 'Terms & Conditions'
    };
    return titles[footerModalType.value] || 'Information';
});

const openFooterModal = (type: string) => {
    footerModalType.value = type;
    footerModalVisible.value = true;
};

const handleTrackOrder = () => {
    if (!Page.user) {
        openLoginForm();
        return;
    }
    // Navigate to customer orders page
    router.push({ name: 'customer.order.index' });
};

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