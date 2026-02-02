<template>
    <div class="customer-dashboard">
        <!-- Welcome Hero Section -->
        <section class="welcome-hero">
            <div class="welcome-overlay"></div>
            <div class="welcome-watermark-container">
                <div class="moving-watermark">
                    <div class="watermark-row" v-for="n in 12" :key="n">
                        <span v-for="m in 15" :key="m" class="watermark-item">
                            <img :src="Logo" alt="logo" class="watermark-logo" />
                            DAVAO BRANCH &nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                    </div>
                </div>
            </div>
            <div class="container welcome-container">
                <div class="welcome-content">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold border border-blue-200 mb-6 backdrop-blur-sm">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                        </span>
                        New Arrivals Available
                    </div>
                    <h1 class="welcome-headline">Welcome Back, <span class="text-accent">{{ Page.user?.full_name?.split(' ')[0] }}!</span></h1>
                    <p class="welcome-subheadline">Ready for your next upgrade? Browse our latest collection of premium tech.</p>
                    
                    <div class="welcome-actions">
                        <button @click="goToProducts" class="btn-primary btn-lg shine-effect">
                            Browse Products <i class="pi pi-arrow-right"></i>
                        </button>
                        <button @click="goToOrders" class="btn-secondary btn-lg">
                            Track Order <i class="pi pi-truck"></i>
                        </button>
                    </div>

                    <!-- Minimal Features / Quick Stats chips -->
                    <div class="welcome-stats mt-8 flex justify-center gap-4 flex-wrap">
                         <div v-if="stats.pending_orders > 0" class="stat-chip bg-amber-100 text-amber-700 border border-amber-200">
                            <i class="pi pi-clock"></i> {{ stats.pending_orders }} Pending Orders
                        </div>
                         <div v-else class="stat-chip bg-emerald-100 text-emerald-700 border border-emerald-200">
                            <i class="pi pi-check-circle"></i> All Orders Completed
                        </div>
                        <div class="stat-chip bg-purple-100 text-purple-700 border border-purple-200">
                            <i class="pi pi-star"></i> Loyalty Member
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section v-if="featuredProducts.length > 0" class="featured-products">
            <div class="container">
                <div class="section-header">
                    <p class="section-label">Top Picks</p>
                    <h2 class="section-title">Featured Products</h2>
                </div>
                <div class="featured-carousel-wrapper">
                    <div class="hero-carousel">
                        <div class="hero-grid">
                            <div class="hero-text">
                                <span class="hero-badge">RECOMMENDED</span>
                                <h2 class="hero-product-name">{{ featuredProducts[currentSlide]?.product_name }}</h2>
                                <p class="hero-category">{{ featuredProducts[currentSlide]?.category?.category_name || 'Premium Electronics' }}</p>
                                <div class="hero-price">{{ CurrencyUtil.formatCurrency(featuredProducts[currentSlide]?.product_price) }}</div>
                                <div class="hero-buttons">
                                    <button @click="viewProduct(featuredProducts[currentSlide]?.product_id)" class="btn-primary">
                                        <i class="pi pi-eye"></i>
                                        View Details
                                    </button>
                                    <button @click="goToProducts" class="btn-secondary">
                                        Browse All
                                    </button>
                                </div>
                            </div>
                            <div class="hero-image-container">
                                <div class="hero-image-wrapper">
                                    <img v-if="featuredProducts[currentSlide]?.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${featuredProducts[currentSlide]?.product_image}`)" :alt="featuredProducts[currentSlide]?.product_name" class="hero-image" @error="handleImageError" />
                                    <div v-else class="hero-image-placeholder"><i class="pi pi-image"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="hero-dots">
                            <button v-for="(_, index) in featuredProducts.slice(0, 4)" :key="index" @click="currentSlide = index" :class="['dot', { active: currentSlide === index }]"></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trust Bar -->
        <section class="trust-bar">
            <div class="container">
                <div class="trust-grid">
                    <div class="trust-item">
                        <div class="trust-icon trust-icon-green"><i class="pi pi-verified"></i></div>
                        <div><p class="trust-title">100% Genuine</p><p class="trust-sub">Authentic Products</p></div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon trust-icon-blue"><i class="pi pi-truck"></i></div>
                        <div><p class="trust-title">Fast Delivery</p><p class="trust-sub">Nationwide Shipping</p></div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon trust-icon-purple"><i class="pi pi-shield"></i></div>
                        <div><p class="trust-title">Warranty</p><p class="trust-sub">Official Coverage</p></div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon trust-icon-amber"><i class="pi pi-headphones"></i></div>
                        <div><p class="trust-title">24/7 Support</p><p class="trust-sub">We're Here to Help</p></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions: Management Suite -->
        <section class="quick-actions-section">
            <div class="container">
                <div class="section-header">
                    <p class="section-label">Quick Control</p>
                    <h2 class="section-title">Your Management Suite</h2>
                </div>

                <div class="action-grid">
                    <div @click="goToOrders" class="action-card">
                        <div class="action-icon action-icon-blue">
                            <i class="pi pi-truck"></i>
                        </div>
                        <h3>My Orders</h3>
                        <p>Track your deliveries and view history.</p>
                        <i class="pi pi-arrow-right action-arrow"></i>
                    </div>

                    <div @click="goToProfile" class="action-card">
                        <div class="action-icon action-icon-purple">
                            <i class="pi pi-user"></i>
                        </div>
                        <h3>Identity</h3>
                        <p>Manage your profile and security.</p>
                        <i class="pi pi-arrow-right action-arrow"></i>
                    </div>

                    <div @click="goToInvoices" class="action-card">
                        <div class="action-icon action-icon-green">
                            <i class="pi pi-file-pdf"></i>
                        </div>
                        <h3>Invoices</h3>
                        <p>Download billing and official logs.</p>
                        <i class="pi pi-arrow-right action-arrow"></i>
                    </div>

                    <div @click="goToAddress" class="action-card">
                        <div class="action-icon action-icon-amber">
                            <i class="pi pi-map-marker"></i>
                        </div>
                        <h3>Addresses</h3>
                        <p>Update your shipping destinations.</p>
                        <i class="pi pi-arrow-right action-arrow"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories & Best Sellers (Stacked) -->
        <main class="main-content">
            <div class="container">
                
                <!-- Categories -->
                <div class="mb-16">
                    <div class="section-header mb-8">
                        <p class="section-label">Explore</p>
                        <h2 class="section-title">Shop by Category</h2>
                    </div>
                    <div v-if="categories?.length" class="category-grid">
                        <button v-for="category in categories" :key="category.category_id" class="category-card" @click="goToCategory(category.category_id)">
                            <div class="category-image-box">
                                <img v-if="category.category_image" :src="UrlUtil.getBaseAppUrl(`storage/images/category/${category.category_image}`)" :alt="category.category_name" />
                                <i v-else class="pi pi-image"></i>
                            </div>
                            <span class="category-name">{{ category.category_name }}</span>
                        </button>
                    </div>
                    <div v-else class="empty-state">
                        <div class="empty-icon"><i class="pi pi-th-large"></i></div>
                        <h3>No Categories Available</h3>
                        <p>Check back soon for new product categories.</p>
                    </div>
                </div>

                <!-- Best Sellers -->
                <div class="mb-16">
                    <div class="section-header mb-8">
                        <p class="section-label">Trending Now</p>
                        <h2 class="section-title">Best Selling Products</h2>
                    </div>
                    <div v-if="bestSellers?.length" class="product-grid">
                        <div v-for="product in bestSellers" :key="product.product_id" class="product-card" @click="viewProduct(product.product_id)">
                            <div class="product-image-box">
                                <img v-if="product.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" :alt="product.product_name" />
                                <i v-else class="pi pi-image"></i>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">{{ product.product_name }}</h3>
                                <p class="product-price">{{ CurrencyUtil.formatCurrency(product.product_price) }}</p>
                                <span class="btn-view">View Details</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state">
                        <div class="empty-icon"><i class="pi pi-shopping-bag"></i></div>
                        <h3>No Products Available</h3>
                        <p>Stay tuned for our latest offerings!</p>
                    </div>
                </div>

            </div>
        </main>

        <!-- Recommendations & Order Timeline Section -->
        <section class="recommendations-section">
            <div class="container">
                <div class="recommendations-grid">
                    <!-- Recommendations -->
                    <div class="recommendations-column">
                        <div class="section-header-left">
                            <p class="section-label">Recommendations</p>
                            <h2 class="section-title">Picked for You</h2>
                        </div>
                        
                        <div v-if="featuredProducts.length > 0" class="recommendation-cards">
                            <div 
                                v-for="product in featuredProducts.slice(0, 4)" 
                                :key="product.product_id"
                                class="recommendation-card"
                                @click="viewProduct(product.product_id)"
                            >
                                <div class="recommendation-img">
                                    <img v-if="product.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" :alt="product.product_name">
                                    <i v-else class="pi pi-image"></i>
                                </div>
                                <div class="recommendation-info">
                                    <h4>{{ product.product_name }}</h4>
                                    <div class="recommendation-meta">
                                        <span class="recommendation-price">{{ CurrencyUtil.formatCurrency(product.product_price) }}</span>
                                        <span class="recommendation-category">
                                            <i class="pi pi-tag"></i>
                                            {{ product.category?.category_name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="empty-state">
                            <div class="empty-icon"><i class="pi pi-star"></i></div>
                            <h3>No Recommendations Yet</h3>
                            <p>Browse our products to get personalized picks!</p>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="timeline-column">
                        <div class="section-header-left">
                            <p class="section-label">Activity</p>
                            <!-- Stats displayed here -->
                            <h2 class="section-title">Order Timeline <span v-if="stats.total_orders" class="text-sm font-normal text-gray-500 ml-2">({{ stats.total_orders }} orders)</span></h2>
                        </div>

                        <div class="timeline-container">
                            <div v-if="recentOrders.length > 0" class="timeline-list">
                                <div v-for="(order, index) in recentOrders" :key="order.order_id" class="timeline-item">
                                    <div class="timeline-dot-wrapper">
                                        <div class="timeline-dot" :class="getStatusTimelineClass(order.status)">
                                            <i :class="getStatusIcon(order.status)"></i>
                                        </div>
                                        <div v-if="index !== recentOrders.length - 1" class="timeline-line"></div>
                                    </div>
                                    <div class="timeline-content">
                                        <span class="timeline-date">{{ new Date(order.created_at).toLocaleDateString() }}</span>
                                        <h4 class="timeline-order-id">Order #{{ order.order_public_id }}</h4>
                                        <p class="timeline-product">{{ order.product_orders?.[0]?.product?.product_name || 'Electronics' }}</p>
                                        <div class="timeline-status" :class="getStatusTextColor(order.status)">
                                            <span>{{ order.status }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="empty-state">
                                <div class="empty-icon"><i class="pi pi-history"></i></div>
                                <h3>No Recent Orders</h3>
                                <p>Your order history will appear here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2>Ready to upgrade your tech?</h2>
                <p>Explore our wide selection of laptops, phones, and computers at competitive prices.</p>
                <div class="cta-buttons">
                    <button @click="goToProducts" class="btn-primary btn-lg shine-effect">Browse Products <i class="pi pi-arrow-right"></i></button>
                    <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="btn-outline"><i class="pi pi-facebook"></i> Message Us</a>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import Page from '@/stores/Page'
import useAxiosUtil from '@/utils/AxiosUtil'
import CurrencyUtil from '@/utils/CurrencyUtil'
import UrlUtil from '@/utils/UrlUtil'
import Logo from "@/../img/logo.png";

const router = useRouter()
const dashboardService = useAxiosUtil()

// Data state
const stats = ref<any>({})
const recentOrders = ref<any[]>([])
const featuredProducts = ref<any[]>([])
const bestSellers = ref<any[]>([])
const categories = ref<any[]>([])
const currentSlide = ref(0)
let carouselInterval: number | null = null

// Fetch Data
const fetchDashboardData = async () => {
    await dashboardService.get('customer/dashboard').then(() => {
        if (dashboardService.request.status === 200 && dashboardService.request.data) {
            const data = dashboardService.request.data
            stats.value = data.stats
            recentOrders.value = data.recent_orders
            featuredProducts.value = data.featured_products
            bestSellers.value = data.best_sellers || []
            categories.value = data.categories
            startCarousel()
        }
    })
}

// Carousel
const startCarousel = () => {
    const count = Math.min(featuredProducts.value.length, 4)
    if (count > 1) carouselInterval = window.setInterval(() => { currentSlide.value = (currentSlide.value + 1) % count }, 5000)
}

const stopCarousel = () => { if (carouselInterval) { clearInterval(carouselInterval); carouselInterval = null } }

// Helpers
const getStatusTimelineClass = (status: string) => {
    switch (status) {
        case 'Completed':
        case 'Delivered': return 'status-completed'
        case 'Processing': return 'status-processing'
        case 'Shipped': return 'status-shipped'
        case 'Cancelled': return 'status-cancelled'
        default: return 'status-default'
    }
}

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'Completed':
        case 'Delivered': return 'pi pi-check'
        case 'Processing': return 'pi pi-spin pi-cog'
        case 'Shipped': return 'pi pi-truck'
        case 'Cancelled': return 'pi pi-times'
        default: return 'pi pi-circle'
    }
}

const getStatusTextColor = (status: string) => {
    switch (status) {
        case 'Completed':
        case 'Delivered': return 'text-completed'
        case 'Processing': return 'text-processing'
        case 'Shipped': return 'text-shipped'
        case 'Cancelled': return 'text-cancelled'
        default: return 'text-default'
    }
}

const handleImageError = (event: Event) => { (event.target as HTMLImageElement).style.display = 'none' }

// Navigation
const goToOrders = () => router.push({ name: 'customer.order.index' })
const goToInvoices = () => router.push({ name: 'customer-invoice' })
const goToProfile = () => router.push({ name: 'customer.profile' })
const goToAddress = () => { window.dispatchEvent(new CustomEvent('open-address-form')) }
const goToProducts = () => router.push({ name: 'customer.browse-products' })
const viewProduct = (id: number) => router.push({ name: 'customer.product-info.index', params: { id } })
const goToCategory = (id: number) => router.push({ name: 'customer.product-category', params: { id } })

onMounted(() => { fetchDashboardData() })
onUnmounted(() => stopCarousel())
</script>

<style scoped>
/* Base */
.customer-dashboard { min-height: 100vh; background: #f8fafc; font-family: 'Inter', 'Poppins', sans-serif; }
.container { max-width: 1280px; margin: 0 auto; padding: 0 1rem; }
.mb-16 { margin-bottom: 4rem; }
.mb-8 { margin-bottom: 2rem; }

/* Welcome Hero */
.welcome-hero { position: relative; background: #f8fafc; min-height: 400px; display: flex; align-items: center; overflow: hidden; border-bottom: 1px solid #e2e8f0; }
.welcome-overlay { position: absolute; inset: 0; background: none; }
.welcome-container { position: relative; z-index: 10; padding: 6rem 1rem 2rem 1rem; }
.welcome-content { max-width: 800px; margin: 0 auto; text-align: center; }
.welcome-headline { font-size: clamp(2rem, 6vw, 3.5rem); font-weight: 800; color: #0f172a; line-height: 1.1; margin-bottom: 1rem; letter-spacing: -0.02em; }
.welcome-subheadline { font-size: 1.125rem; color: #475569; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6; font-weight: 500; }
.welcome-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 2rem; }
.stat-chip { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; backdrop-filter: blur(4px); }
.text-accent { color: #2563eb; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

/* Watermark Animation */
.welcome-watermark-container { position: absolute; inset: 0; overflow: hidden; opacity: 0.03; pointer-events: none; z-index: 5; }
.moving-watermark { transform: rotate(-25deg) scale(1.5); width: 250%; height: 250%; position: absolute; top: -50%; left: -75%; display: flex; flex-direction: column; gap: 4rem; animation: moveWatermark 80s linear infinite; }
.watermark-row { white-space: nowrap; font-size: 1.5rem; font-weight: 900; color: #3b82f6; letter-spacing: 0.2rem; display: flex; align-items: center; }
.watermark-item { display: inline-flex; align-items: center; gap: 1rem; }
.watermark-logo { width: 32px; height: 32px; border-radius: 50%; filter: grayscale(1) invert(0.1); }
.watermark-row:nth-child(even) { margin-left: -8rem; }

@keyframes moveWatermark {
    0% { transform: rotate(-25deg) translate(0, 0) scale(1.5); }
    100% { transform: rotate(-25deg) translate(-10%, -10%) scale(1.5); }
}

/* Buttons */
.btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; background: #2563eb; color: #fff; font-weight: 600; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); }
.btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); }
.btn-secondary { display: inline-flex; align-items: center; gap: 0.5rem; background: #fff; color: #334155; font-weight: 600; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; cursor: pointer; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; transform: translateY(-1px); }
.btn-lg { padding: 0.75rem 1.5rem; font-size: 1rem; }
.btn-outline { display: inline-flex; align-items: center; gap: 0.5rem; background: transparent; color: #fff; font-weight: 500; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.3); text-decoration: none; font-size: 0.875rem; transition: all 0.2s; }
.btn-outline:hover { background: rgba(255,255,255,0.1); }
.btn-view { display: block; background: #1e293b; color: #fff; text-align: center; padding: 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; text-decoration: none; transition: all 0.2s; }
.btn-view:hover { background: #334155; }

/* Shine Effect */
.shine-effect { position: relative; overflow: hidden; }
.shine-effect::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: linear-gradient(45deg, transparent 45%, rgba(255,255,255,0.1) 50%, transparent 55%); transform: rotate(45deg); animation: shine 3s infinite; }
@keyframes shine { 0% { left: -100%; } 100% { left: 100%; } }

/* Featured Products Section */
.featured-products { padding: 4rem 0; background: #fff; }
.featured-carousel-wrapper { background: #1e293b; border-radius: 2rem; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); }
.hero-carousel { padding: 3rem; position: relative; }
.hero-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; align-items: center; }
.hero-text { text-align: center; position: relative; z-index: 5; }
.hero-badge { display: inline-block; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #94a3b8; font-size: 0.75rem; font-weight: 500; padding: 0.375rem 0.75rem; border-radius: 9999px; margin-bottom: 1rem; }
.hero-product-name { font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.2; margin-bottom: 0.5rem; }
.hero-category { color: #94a3b8; font-size: 0.875rem; margin-bottom: 1rem; }
.hero-price { font-size: 1.5rem; font-weight: 700; color: #60a5fa; margin-bottom: 1.5rem; }
.hero-buttons { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
.hero-image-container { display: flex; align-items: center; justify-content: center; }
.hero-image-wrapper { max-width: 300px; }
.hero-image { max-width: 100%; max-height: 250px; object-fit: contain; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3)); }
.hero-image-placeholder { width: 200px; height: 200px; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.2); font-size: 4rem; }
.hero-dots { display: flex; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; }
.dot { width: 8px; height: 8px; border-radius: 9999px; background: rgba(255,255,255,0.3); border: none; cursor: pointer; transition: all 0.3s; }
.dot.active { width: 24px; background: #fff; }

/* Trust Bar */
.trust-bar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1rem 0; }
.trust-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
.trust-item { display: flex; align-items: center; gap: 0.75rem; }
.trust-icon { width: 36px; height: 36px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.trust-icon i { font-size: 0.875rem; }
.trust-icon-green { background: #ecfdf5; color: #059669; }
.trust-icon-blue { background: #eff6ff; color: #2563eb; }
.trust-icon-purple { background: #f5f3ff; color: #7c3aed; }
.trust-icon-amber { background: #fffbeb; color: #d97706; }
.trust-title { font-size: 0.75rem; font-weight: 600; color: #1e293b; }
.trust-sub { font-size: 0.625rem; color: #64748b; }

/* Quick Actions Section */
.quick-actions-section { padding: 4rem 0; background: #fff; }
.action-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.5rem; }
.action-card { background: #fff; padding: 2rem; border-radius: 1.5rem; border: 1px solid #e2e8f0; cursor: pointer; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
.action-card:hover { transform: translateY(-8px); border-color: #2563eb; box-shadow: 0 20px 40px rgba(37, 99, 235, 0.08); }
.action-icon { width: 3rem; height: 3rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-bottom: 1.25rem; transition: transform 0.4s; }
.action-card:hover .action-icon { transform: scale(1.1) rotate(5deg); }
.action-icon-blue { background: #eff6ff; color: #2563eb; }
.action-icon-purple { background: #f5f3ff; color: #7c3aed; }
.action-icon-green { background: #ecfdf5; color: #059669; }
.action-icon-amber { background: #fffbeb; color: #d97706; }
.action-card h3 { font-size: 1rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; }
.action-card p { font-size: 0.875rem; color: #6b7280; font-weight: 500; line-height: 1.5; padding-right: 1.5rem; }
.action-arrow { position: absolute; bottom: 2rem; right: 2rem; color: #2563eb; opacity: 0; transform: translateX(-10px); transition: all 0.3s; font-size: 0.75rem; }
.action-card:hover .action-arrow { opacity: 1; transform: translateX(0); }

/* Section Headers */
.section-header { text-align: center; margin-bottom: 2rem; }
.section-header-left { text-align: left; margin-bottom: 1.5rem; }
.section-label { font-size: 0.75rem; font-weight: 500; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; }
.section-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; }

/* Main Content */
.main-content { padding: 3rem 0; background: #f8fafc; }

/* Categories (Styled like HomeView) */
.category-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
.category-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1.5rem; padding: 3rem 2rem; text-align: center; cursor: pointer; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 300px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.category-card:hover { border-color: #2563eb; box-shadow: 0 20px 40px rgba(37, 99, 235, 0.08); transform: translateY(-8px); }
.category-image-box { width: 180px; height: 180px; margin-bottom: 2rem; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 1.5rem; transition: all 0.4s; }
.category-card:hover .category-image-box { transform: scale(1.05) rotate(2deg); background: #eff6ff; }
.category-image-box img { max-width: 85%; max-height: 85%; object-fit: contain; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.05)); }
.category-image-box i { font-size: 4rem; color: #cbd5e1; }
.category-name { font-size: 1.5rem; font-weight: 700; color: #1e293b; display: block; }

/* Products (Styled like HomeView) */
.product-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
.product-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; overflow: hidden; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.product-card:hover { border-color: #2563eb; box-shadow: 0 15px 30px rgba(0,0,0,0.08); transform: translateY(-4px); }
.product-image-box { position: relative; height: 200px; background: #f8fafc; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.product-image-box img { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s; }
.product-card:hover .product-image-box img { transform: scale(1.05); }
.product-image-box i { font-size: 3rem; color: #cbd5e1; }
.product-info { padding: 1.25rem; }
.product-name { font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5rem; }
.product-price { font-size: 1rem; font-weight: 700; color: #2563eb; margin-bottom: 0.75rem; }

/* Empty State */
.empty-state { text-align: center; padding: 3rem 1rem; }
.empty-icon { width: 48px; height: 48px; background: #f1f5f9; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
.empty-icon i { font-size: 1.25rem; color: #94a3b8; }
.empty-state h3 { font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem; }
.empty-state p { font-size: 0.75rem; color: #64748b; }

/* Recommendations Section */
.recommendations-section { padding: 4rem 0; background: #fff; }
.recommendations-grid { display: grid; grid-template-columns: 1fr; gap: 3rem; }

/* Recommendation Cards */
.recommendation-cards { display: grid; grid-template-columns: repeat(1, 1fr); gap: 1rem; }
.recommendation-card { background: #f8fafc; border-radius: 1rem; overflow: hidden; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 1rem; padding: 0.75rem; border: 1px solid #e2e8f0; }
.recommendation-card:hover { background: #fff; border-color: #2563eb; box-shadow: 0 10px 25px rgba(0,0,0,0.05); transform: translateX(5px); }
.recommendation-img { width: 80px; height: 80px; background: #fff; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.recommendation-img img { max-width: 90%; max-height: 90%; object-fit: contain; }
.recommendation-img i { font-size: 2rem; color: #cbd5e1; }
.recommendation-info { flex: 1; min-width: 0; }
.recommendation-info h4 { font-size: 0.875rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.recommendation-meta { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; }
.recommendation-price { font-size: 0.875rem; font-weight: 700; color: #2563eb; }
.recommendation-category { font-size: 0.625rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; display: flex; align-items: center; gap: 0.25rem; }

/* Timeline Container */
.timeline-container { background: #f8fafc; border-radius: 1.5rem; padding: 1.5rem; border: 1px solid #e2e8f0; min-height: 300px; }
.timeline-list { display: flex; flex-direction: column; gap: 0; }
.timeline-item { display: flex; gap: 1rem; }
.timeline-dot-wrapper { display: flex; flex-direction: column; align-items: center; }
.timeline-dot { width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.75rem; }
.timeline-dot.status-completed { background: #10b981; color: #fff; }
.timeline-dot.status-processing { background: #3b82f6; color: #fff; animation: pulse 2s infinite; }
.timeline-dot.status-shipped { background: #8b5cf6; color: #fff; }
.timeline-dot.status-cancelled { background: #ef4444; color: #fff; }
.timeline-dot.status-default { background: #e2e8f0; color: #64748b; }
.timeline-line { width: 2px; flex: 1; min-height: 2rem; background: #e2e8f0; }
.timeline-content { flex: 1; padding-bottom: 1.5rem; }
.timeline-date { font-size: 0.625rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
.timeline-order-id { font-size: 0.875rem; font-weight: 700; color: #1e293b; margin: 0.25rem 0; }
.timeline-product { font-size: 0.75rem; color: #64748b; font-style: italic; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.timeline-status { margin-top: 0.5rem; }
.timeline-status span { font-size: 0.625rem; font-weight: 700; text-transform: uppercase; background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 0.25rem; }
.text-completed { color: #10b981; }
.text-processing { color: #3b82f6; }
.text-shipped { color: #8b5cf6; }
.text-cancelled { color: #ef4444; }
.text-default { color: #64748b; }

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* CTA Section */
.cta-section { background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); padding: 3rem 1rem; text-align: center; }
.cta-section h2 { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem; }
.cta-section > .container > p { color: #94a3b8; margin-bottom: 1.5rem; font-size: 0.875rem; }
.cta-buttons { display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap; }

/* Tablet */
@media (min-width: 640px) {
    .action-grid { grid-template-columns: repeat(2, 1fr); }
    .recommendation-cards { grid-template-columns: repeat(2, 1fr); }
}

/* Tablet Large */
@media (min-width: 768px) {
    .hero-grid { grid-template-columns: 1fr 1fr; }
    .hero-text { text-align: left; }
    .hero-buttons { justify-content: flex-start; }
    .hero-product-name { font-size: 2.5rem; }
    .hero-price { font-size: 2rem; }
    .hero-image { max-height: 350px; }
    .trust-grid { grid-template-columns: repeat(4, 1fr); }
    .product-grid { grid-template-columns: repeat(3, 1fr); }
    .recommendations-grid { grid-template-columns: 7fr 5fr; }
}

/* Desktop */
@media (min-width: 1024px) {
    .action-grid { grid-template-columns: repeat(4, 1fr); }
    .product-grid { grid-template-columns: repeat(3, 1fr); }
    .cta-section { padding: 4rem 2rem; }
    .cta-section h2 { font-size: 2rem; }
}
</style>
