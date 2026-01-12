<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Unified Responsive Header - Uses CSS for responsiveness to prevent layout jumps -->
        <header class="bg-blue-600 sticky top-0 z-[100] shadow-md">
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


        <!-- Stripe-Inspired Hero Section -->
        <div class="relative overflow-hidden">
            <!-- Clean Gradient Background -->
            <div :class="[
                'relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900',
                isMobile ? 'min-h-[480px]' : isTablet ? 'min-h-[520px]' : 'min-h-[600px]'
            ]">
                <!-- Subtle Mesh Gradient Overlay -->
                <div class="absolute inset-0 opacity-40">
                    <div class="absolute top-0 -left-40 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
                    <div class="absolute top-0 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
                    <div class="absolute -bottom-32 left-1/2 w-80 h-80 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
                </div>
                
                <!-- Grid Pattern Overlay -->
                <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:64px_64px]"></div>
                
                <!-- Main Hero Content -->
                <div v-if="showCarousel" class="relative h-full z-10">
                    <TransitionGroup name="hero-slide">
                        <div
                            v-for="(product, index) in [products.slice(0, 3)[currentSlide]]"
                            :key="currentSlide"
                            class="absolute inset-0"
                        >
                            <div :class="['container mx-auto h-full', isMobile ? 'px-5' : 'px-8']">
                                <div :class="[
                                    'grid h-full items-center',
                                    isMobile ? 'grid-cols-1 gap-6 py-10' : 'grid-cols-2 gap-12 py-16'
                                ]">
                                    <!-- Left Side - Product Info -->
                                    <div :class="[
                                        'flex flex-col justify-center',
                                        isMobile ? 'text-center order-2' : 'text-left'
                                    ]">
                                        <!-- Subtle Badge -->
                                        <div :class="[
                                            'inline-flex items-center gap-2 rounded-full bg-white/5 border border-white/10',
                                            isMobile ? 'px-3 py-1.5 mb-4 mx-auto' : 'px-4 py-2 mb-6 self-start'
                                        ]">
                                            <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                                            <span :class="[
                                                'font-medium text-slate-300',
                                                isMobile ? 'text-xs' : 'text-sm'
                                            ]">Featured Product</span>
                                        </div>
                                        
                                        <!-- Product Name -->
                                        <h1 :class="[
                                            'font-semibold text-white leading-[1.1] tracking-tight',
                                            isMobile ? 'text-2xl mb-3' : isTablet ? 'text-4xl mb-4' : 'text-5xl lg:text-6xl mb-6'
                                        ]">
                                            {{ product.product_name }}
                                        </h1>
                                        
                                        <!-- Category -->
                                        <p :class="[
                                            'text-slate-400 font-medium',
                                            isMobile ? 'text-sm mb-4' : 'text-lg mb-6'
                                        ]">
                                            {{ product.category?.category_name || 'Premium Electronics' }}
                                        </p>
                                        
                                        <!-- Price Display -->
                                        <div :class="[
                                            'flex items-baseline gap-3',
                                            isMobile ? 'justify-center mb-6' : 'mb-8'
                                        ]">
                                            <span :class="[
                                                'font-bold text-white',
                                                isMobile ? 'text-3xl' : 'text-4xl lg:text-5xl'
                                            ]">
                                                {{ CurrencyUtil.formatCurrency(product.product_price) }}
                                            </span>
                                            <div class="flex items-center gap-1">
                                                <i v-for="i in 5" :key="i" :class="[
                                                    i <= 4 ? 'pi pi-star-fill text-amber-400' : 'pi pi-star text-slate-600',
                                                    'text-sm'
                                                ]"></i>
                                            </div>
                                        </div>
                                        
                                        <!-- CTA Buttons -->
                                        <div :class="[
                                            'flex items-center',
                                            isMobile ? 'gap-3 justify-center' : 'gap-4'
                                        ]">
                                            <button 
                                                @click="addToCart(product.product_id)"
                                                :disabled="addToCartService.request.loading"
                                                :class="[
                                                    'inline-flex items-center gap-2 bg-white text-slate-900 font-semibold rounded-lg transition-all duration-200 hover:bg-slate-100 disabled:opacity-50',
                                                    isMobile ? 'px-5 py-2.5 text-sm' : 'px-6 py-3 text-base'
                                                ]"
                                            >
                                                <i v-if="addToCartService.request.loading" class="pi pi-spin pi-spinner"></i>
                                                <i v-else class="pi pi-shopping-cart"></i>
                                                Add to Cart
                                            </button>
                                            <button 
                                                @click="goToProductDetails(product.product_id)"
                                                :class="[
                                                    'inline-flex items-center gap-2 text-white font-semibold rounded-lg border border-white/20 transition-all duration-200 hover:bg-white/5',
                                                    isMobile ? 'px-5 py-2.5 text-sm' : 'px-6 py-3 text-base'
                                                ]"
                                            >
                                                Learn More
                                                <i class="pi pi-arrow-right text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Side - Product Image -->
                                    <div :class="[
                                        'flex items-center justify-center relative',
                                        isMobile ? 'order-1' : ''
                                    ]">
                                        <div class="relative">
                                            <!-- Subtle glow behind image -->
                                            <div class="absolute inset-0 bg-gradient-to-tr from-blue-500/20 to-purple-500/20 rounded-3xl blur-2xl scale-110"></div>
                                            <img
                                                v-if="product.product_image"
                                                :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                                :alt="product.product_name"
                                                :class="[
                                                    'relative w-auto object-contain drop-shadow-2xl',
                                                    isMobile ? 'max-h-[200px]' : isTablet ? 'max-h-[300px]' : 'max-h-[420px]'
                                                ]"
                                                @error="handleImageError"
                                            />
                                            <div
                                                v-else
                                                :class="[
                                                    'relative flex items-center justify-center rounded-3xl bg-white/5 border border-white/10',
                                                    isMobile ? 'h-[200px] w-[200px]' : isTablet ? 'h-[300px] w-[300px]' : 'h-[420px] w-[420px]'
                                                ]"
                                            >
                                                <i :class="[
                                                    'pi pi-image text-slate-600',
                                                    isMobile ? 'text-4xl' : 'text-7xl'
                                                ]"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>
                    
                    <!-- Clean Carousel Indicators -->
                    <div :class="[
                        'absolute left-1/2 -translate-x-1/2 flex gap-2',
                        isMobile ? 'bottom-6' : 'bottom-10'
                    ]">
                        <button
                            v-for="(product, index) in products.slice(0, 3)"
                            :key="index"
                            @click="currentSlide = index"
                            :class="[
                                'transition-all duration-300 rounded-full h-2',
                                currentSlide === index 
                                    ? 'w-8 bg-white' 
                                    : 'w-2 bg-white/30 hover:bg-white/50'
                            ]"
                        ></button>
                    </div>
                </div>

                <!-- Fallback Static Hero -->
                <div v-else class="relative h-full min-h-[inherit] z-10">
                    <div class="absolute inset-0 flex items-center">
                        <div :class="['container mx-auto', isMobile ? 'px-5' : 'px-8']">
                            <div :class="[
                                isMobile ? 'text-center max-w-full' : 'max-w-2xl'
                            ]">
                                <div :class="[
                                    'inline-flex items-center gap-2 rounded-full bg-white/5 border border-white/10 mb-6',
                                    isMobile ? 'px-3 py-1.5' : 'px-4 py-2'
                                ]">
                                    <i class="pi pi-sparkles text-amber-400 text-sm"></i>
                                    <span :class="['font-medium text-slate-300', isMobile ? 'text-xs' : 'text-sm']">Premium Tech Store</span>
                                </div>
                                <h1 :class="[
                                    'font-semibold text-white leading-[1.1] tracking-tight mb-6',
                                    isMobile ? 'text-3xl' : isTablet ? 'text-4xl' : 'text-5xl lg:text-6xl'
                                ]">
                                    Welcome to <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">{{ appName }}</span>
                                </h1>
                                <p :class="[
                                    'text-slate-400 mb-8',
                                    isMobile ? 'text-base' : 'text-lg'
                                ]">
                                    Discover premium laptops, phones & computers at unbeatable prices with genuine warranty.
                                </p>
                                <button 
                                    @click="goToBrowseProducts"
                                    :class="[
                                        'inline-flex items-center gap-2 bg-white text-slate-900 font-semibold rounded-lg transition-all duration-200 hover:bg-slate-100',
                                        isMobile ? 'px-6 py-3 text-base' : 'px-8 py-4 text-lg'
                                    ]"
                                >
                                    <span>Shop Now</span>
                                    <i class="pi pi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trust Indicators Section - Clean Stripe Style -->
        <div :class="['border-b border-slate-200', isMobile ? 'py-6' : 'py-8']">
            <div :class="['container mx-auto', isMobile ? 'px-5' : 'px-8']">
                <div :class="[
                    'grid',
                    isMobile ? 'grid-cols-2 gap-4' : 'grid-cols-4 gap-8'
                ]">
                    <div class="flex items-center gap-3">
                        <div :class="[
                            'flex-shrink-0 flex items-center justify-center rounded-lg bg-emerald-50',
                            isMobile ? 'w-9 h-9' : 'w-10 h-10'
                        ]">
                            <i :class="['pi pi-verified text-emerald-600', isMobile ? 'text-sm' : 'text-base']"></i>
                        </div>
                        <div>
                            <p :class="['font-medium text-slate-900', isMobile ? 'text-xs' : 'text-sm']">100% Genuine</p>
                            <p class="text-slate-500 text-xs">Authentic Products</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div :class="[
                            'flex-shrink-0 flex items-center justify-center rounded-lg bg-blue-50',
                            isMobile ? 'w-9 h-9' : 'w-10 h-10'
                        ]">
                            <i :class="['pi pi-truck text-blue-600', isMobile ? 'text-sm' : 'text-base']"></i>
                        </div>
                        <div>
                            <p :class="['font-medium text-slate-900', isMobile ? 'text-xs' : 'text-sm']">Fast Delivery</p>
                            <p class="text-slate-500 text-xs">Nationwide Shipping</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div :class="[
                            'flex-shrink-0 flex items-center justify-center rounded-lg bg-violet-50',
                            isMobile ? 'w-9 h-9' : 'w-10 h-10'
                        ]">
                            <i :class="['pi pi-shield text-violet-600', isMobile ? 'text-sm' : 'text-base']"></i>
                        </div>
                        <div>
                            <p :class="['font-medium text-slate-900', isMobile ? 'text-xs' : 'text-sm']">Warranty</p>
                            <p class="text-slate-500 text-xs">Official Coverage</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div :class="[
                            'flex-shrink-0 flex items-center justify-center rounded-lg bg-amber-50',
                            isMobile ? 'w-9 h-9' : 'w-10 h-10'
                        ]">
                            <i :class="['pi pi-headphones text-amber-600', isMobile ? 'text-sm' : 'text-base']"></i>
                        </div>
                        <div>
                            <p :class="['font-medium text-slate-900', isMobile ? 'text-xs' : 'text-sm']">24/7 Support</p>
                            <p class="text-slate-500 text-xs">We're Here to Help</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Container -->
        <div :class="['container mx-auto', isMobile ? 'px-5 py-12' : 'px-8 py-20']">
            
            <!-- Categories/Products Section -->
            <section :class="isMobile ? 'mb-16' : 'mb-24'">
                <!-- Clean Section Header with Tabs -->
                <div :class="['flex items-center justify-center gap-1 mb-12', isMobile ? 'mb-8' : 'mb-12']">
                    <button
                        @click="activeView = 'categories'"
                        :class="[
                            'font-medium transition-all duration-200 rounded-full',
                            activeView === 'categories' 
                                ? 'bg-slate-900 text-white' 
                                : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100',
                            isMobile ? 'px-4 py-2 text-sm' : 'px-6 py-2.5 text-base'
                        ]"
                    >
                        {{ categoriesHeading }}
                    </button>
                    <button
                        @click="activeView = 'products'"
                        :class="[
                            'font-medium transition-all duration-200 rounded-full',
                            activeView === 'products' 
                                ? 'bg-slate-900 text-white' 
                                : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100',
                            isMobile ? 'px-4 py-2 text-sm' : 'px-6 py-2.5 text-base'
                        ]"
                    >
                        {{ productsHeading }}
                    </button>
                </div>
                
                <!-- Categories View with Transition -->
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 translate-y-4"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-4"
                    mode="out-in"
                >
                    <div v-if="activeView === 'categories'" key="categories" class="relative">
                        <!-- Categories Grid - Clean Design -->
                        <div v-if="CategoryStore.categories && CategoryStore.categories.length > 0" :class="[
                            'grid gap-4',
                            isMobile ? 'grid-cols-2' : isTablet ? 'grid-cols-3' : 'grid-cols-4 lg:grid-cols-6'
                        ]">
                            <button
                                v-for="(category, index) in CategoryStore.categories"
                                :key="index"
                                :class="[
                                    'group relative bg-white rounded-xl p-5 border border-slate-200 transition-all duration-200 hover:border-slate-300 hover:shadow-lg'
                                ]"
                                @click="goRoute('customer.product-category', { id: category.category_id })"
                            >
                                <div class="flex flex-col items-center gap-3">
                                    <!-- Category Image Container -->
                                    <div :class="[
                                        'flex items-center justify-center rounded-lg bg-slate-50',
                                        isMobile ? 'h-16 w-16' : 'h-20 w-20'
                                    ]">
                                        <img
                                            v-if="category.category_image"
                                            :src="UrlUtil.getBaseAppUrl(`storage/images/category/${category.category_image}`)"
                                            :alt="category.category_name"
                                            class="h-full w-full object-contain"
                                        />
                                        <i v-else :class="['pi pi-image text-slate-300', isMobile ? 'text-2xl' : 'text-3xl']"></i>
                                    </div>
                                    
                                    <!-- Category Name -->
                                    <span :class="[
                                        'font-semibold text-slate-700 text-center line-clamp-2 group-hover:text-slate-900',
                                        isMobile ? 'text-xs' : 'text-sm'
                                    ]">
                                        {{ category.category_name }}
                                    </span>
                                </div>
                            </button>
                        </div>
                        
                        <!-- Empty State for Categories -->
                        <div v-else class="flex flex-col items-center justify-center py-16">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                <i class="pi pi-th-large text-slate-400 text-2xl"></i>
                            </div>
                            <h3 class="font-semibold text-slate-700 mb-2">No Categories Available</h3>
                            <p class="text-slate-500 text-sm text-center max-w-sm">
                                Check back soon for new product categories.
                            </p>
                        </div>
                    </div>

                    <!-- Products View with Transition -->
                    <div v-else-if="activeView === 'products'" key="products" class="relative">
                        <!-- Products List - Uniform Design -->
                        <div v-if="products && products.length > 0" :class="[
                            'grid gap-4',
                            isMobile ? 'grid-cols-2' : isTablet ? 'grid-cols-3' : 'grid-cols-4 lg:grid-cols-5'
                        ]">
                            <div
                                v-for="(product, index) in products"
                                :key="index"
                                class="group bg-white rounded-xl p-4 border border-slate-200 transition-all duration-200 hover:border-slate-300 hover:shadow-lg"
                            >
                                <div class="flex flex-col gap-3">
                                    <!-- Product Image Container -->
                                    <div :class="[
                                        'flex items-center justify-center rounded-lg bg-slate-50',
                                        isMobile ? 'h-24' : 'h-32'
                                    ]">
                                        <img
                                            v-if="product.product_image"
                                            :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                            :alt="product.product_name"
                                            class="h-full w-full object-contain"
                                        />
                                        <i v-else :class="['pi pi-image text-slate-300', isMobile ? 'text-2xl' : 'text-3xl']"></i>
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="text-center">
                                        <h3 :class="[
                                            'font-semibold text-slate-700 line-clamp-2 min-h-[2.5rem] mb-3',
                                            isMobile ? 'text-xs' : 'text-sm'
                                        ]">
                                            {{ product.product_name }}
                                        </h3>
                                        
                                        <!-- View Details Button -->
                                        <RouterLink
                                            :to="{
                                                name: 'customer.product-info.index',
                                                params: { id: product.product_id },
                                            }"
                                            :class="[
                                                'inline-flex items-center justify-center w-full py-2 rounded-lg bg-slate-900 text-white font-medium transition-all duration-200 hover:bg-slate-800',
                                                isMobile ? 'text-xs' : 'text-sm'
                                            ]"
                                        >
                                            View Details
                                        </RouterLink>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Empty State for Products -->
                        <div v-else class="flex flex-col items-center justify-center py-16">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                <i class="pi pi-shopping-bag text-slate-400 text-2xl"></i>
                            </div>
                            <h3 class="font-semibold text-slate-700 mb-2">No Products Available</h3>
                            <p class="text-slate-500 text-sm text-center max-w-sm">
                                Stay tuned for our latest offerings!
                            </p>
                        </div>
                    </div>
                </Transition>
            </section>

            <!-- Features Section - Clean Stripe Style -->
            <section :class="isMobile ? 'mb-16' : 'mb-24'">
                <!-- Section Header -->
                <div :class="['text-center', isMobile ? 'mb-10' : 'mb-16']">
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-3">Why Choose Us</p>
                    <h2 :class="[
                        'font-semibold text-slate-900',
                        isMobile ? 'text-2xl' : 'text-3xl'
                    ]">
                        Built for trust
                    </h2>
                </div>

                <div :class="[
                    'grid gap-8',
                    isMobile ? 'grid-cols-1' : isTablet ? 'grid-cols-2' : 'grid-cols-4'
                ]">
                    <!-- Feature 1 -->
                    <div class="text-center">
                        <div :class="[
                            'mx-auto mb-4 flex items-center justify-center rounded-xl bg-emerald-50',
                            isMobile ? 'w-12 h-12' : 'w-14 h-14'
                        ]">
                            <i :class="['pi pi-check-circle text-emerald-600', isMobile ? 'text-xl' : 'text-2xl']"></i>
                        </div>
                        <h3 :class="['font-semibold text-slate-900 mb-2', isMobile ? 'text-sm' : 'text-base']">
                            Quality Guarantee
                        </h3>
                        <p class="text-slate-500 text-sm">
                            100% genuine and verified products
                        </p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="text-center">
                        <div :class="[
                            'mx-auto mb-4 flex items-center justify-center rounded-xl bg-blue-50',
                            isMobile ? 'w-12 h-12' : 'w-14 h-14'
                        ]">
                            <i :class="['pi pi-truck text-blue-600', isMobile ? 'text-xl' : 'text-2xl']"></i>
                        </div>
                        <h3 :class="['font-semibold text-slate-900 mb-2', isMobile ? 'text-sm' : 'text-base']">
                            Fast Delivery
                        </h3>
                        <p class="text-slate-500 text-sm">
                            Quick nationwide shipping
                        </p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="text-center">
                        <div :class="[
                            'mx-auto mb-4 flex items-center justify-center rounded-xl bg-violet-50',
                            isMobile ? 'w-12 h-12' : 'w-14 h-14'
                        ]">
                            <i :class="['pi pi-shield text-violet-600', isMobile ? 'text-xl' : 'text-2xl']"></i>
                        </div>
                        <h3 :class="['font-semibold text-slate-900 mb-2', isMobile ? 'text-sm' : 'text-base']">
                            Secure Payment
                        </h3>
                        <p class="text-slate-500 text-sm">
                            Protected payment information
                        </p>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="text-center">
                        <div :class="[
                            'mx-auto mb-4 flex items-center justify-center rounded-xl bg-amber-50',
                            isMobile ? 'w-12 h-12' : 'w-14 h-14'
                        ]">
                            <i :class="['pi pi-sync text-amber-600', isMobile ? 'text-xl' : 'text-2xl']"></i>
                        </div>
                        <h3 :class="['font-semibold text-slate-900 mb-2', isMobile ? 'text-sm' : 'text-base']">
                            Easy Returns
                        </h3>
                        <p class="text-slate-500 text-sm">
                            7-day replacements for defects
                        </p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Call to Action Section - Stripe Style -->
        <section class="relative overflow-hidden">
            <div :class="[
                'bg-gradient-to-br from-slate-900 to-slate-800',
                isMobile ? 'py-16 px-5' : 'py-24 px-8'
            ]">
                <!-- Subtle Gradient Overlay -->
                <div class="absolute inset-0 opacity-30">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
                </div>

                <div class="container mx-auto relative z-10">
                    <div :class="[
                        'flex flex-col items-center text-center',
                        isMobile ? 'gap-6' : 'gap-8'
                    ]">
                        <!-- Heading -->
                        <div class="max-w-2xl">
                            <h2 :class="[
                                'font-semibold text-white mb-4',
                                isMobile ? 'text-2xl' : 'text-3xl lg:text-4xl'
                            ]">
                                Ready to upgrade your tech?
                            </h2>
                            <p :class="[
                                'text-slate-400',
                                isMobile ? 'text-base' : 'text-lg'
                            ]">
                                Explore our wide selection of laptops, phones, and computers at competitive prices.
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
                                    'inline-flex items-center gap-2 bg-white text-slate-900 font-semibold rounded-lg transition-all duration-200 hover:bg-slate-100',
                                    isMobile ? 'px-5 py-2.5 text-sm' : 'px-6 py-3 text-base'
                                ]"
                            >
                                Browse Products
                                <i class="pi pi-arrow-right text-sm"></i>
                            </button>
                            <a 
                                href="https://www.facebook.com/alcesslaptopstore"
                                target="_blank"
                                :class="[
                                    'inline-flex items-center gap-2 text-white font-medium rounded-lg border border-white/20 transition-all duration-200 hover:bg-white/5',
                                    isMobile ? 'px-5 py-2.5 text-sm' : 'px-6 py-3 text-base'
                                ]"
                            >
                                <i class="pi pi-facebook"></i>
                                Message Us
                            </a>
                        </div>

                        <!-- Stats -->
                        <div :class="[
                            'grid border-t border-white/10 mt-4 pt-8',
                            isMobile ? 'grid-cols-3 gap-6' : 'grid-cols-3 gap-12'
                        ]">
                            <div class="text-center">
                                <p :class="['font-semibold text-white', isMobile ? 'text-2xl' : 'text-3xl']">500+</p>
                                <p :class="['text-slate-500', isMobile ? 'text-xs' : 'text-sm']">Products</p>
                            </div>
                            <div class="text-center">
                                <p :class="['font-semibold text-white', isMobile ? 'text-2xl' : 'text-3xl']">1K+</p>
                                <p :class="['text-slate-500', isMobile ? 'text-xs' : 'text-sm']">Happy Customers</p>
                            </div>
                            <div class="text-center">
                                <p :class="['font-semibold text-white', isMobile ? 'text-2xl' : 'text-3xl']">4.8</p>
                                <p :class="['text-slate-500', isMobile ? 'text-xs' : 'text-sm']">Star Rating</p>
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