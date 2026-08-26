<template>
    <div class="home-page">
        <!-- Header -->
        <NavBar 
            v-if="$route.name === 'home'" 
            :mode="navbarMode" 
            :transparent="false" 
        />

        <!-- Welcome Hero Section -->
        <section class="welcome-hero" id="home">
            <div class="hero-bg-container">
                <div class="hero-bg-image"></div>
            </div>
            <div class="welcome-container">
                <div class="welcome-content">
                    <div class="welcome-badge-wrapper">
                        <span class="welcome-badge">Premium Tech Destination</span>
                    </div>
                    <h1 class="welcome-headline">Elevate <span class="typewriter-text">{{ typewriterText }}</span><span class="cursor">|</span></h1>
                    <p class="welcome-subheadline">Your premier destination for high-performance PCs, premium laptops, and cutting-edge computer accessories. Built for creators, gamers, and professionals.</p>
                        <div class="welcome-actions">
                            <button @click="goToBrowseProducts" class="btn-primary btn-lg shine-effect">
                                Get Started <i class="pi pi-arrow-right"></i>
                            </button>
                        </div>
                        
                    <!-- Branch Info -->
                    <div class="branch-footer-info">
                        <div class="flex items-center justify-center gap-2 text-slate-500 font-bold text-sm uppercase tracking-widest">
                            <img :src="Icon" alt="logo" class="w-5 h-5 opacity-80" />
                            {{ BranchUtil.getBranchName() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sticky Icons -->
        <!-- Sticky Menu -->
        <FloatingActionMenu />

        <!-- Featured & Pinned Products Section -->
        <section v-if="products.length > 0" class="featured-showcase-section" id="products" @mouseenter="stopCarousel" @mouseleave="startCarousel">
            <div class="container">
                <!-- Section Header with Controls -->
                <div class="showcase-header">
                    <div>
                        <div class="showcase-badge">
                            <i class="pi pi-sparkles"></i>
                            <span>Spotlight Selection</span>
                        </div>
                        <h2 class="showcase-title">Featured & Pinned Tech</h2>
                        <p class="showcase-subtitle">Hand-picked gear, trending bestsellers, and top staff recommendations.</p>
                    </div>
                    <div class="showcase-controls" v-if="products.length > 1">
                        <button @click="prevSlide" class="control-btn" title="Previous Item" aria-label="Previous">
                            <i class="pi pi-chevron-left"></i>
                        </button>
                        <span class="slide-indicator">{{ String(currentSlide + 1).padStart(2, '0') }} / {{ String(products.length).padStart(2, '0') }}</span>
                        <button @click="nextSlide" class="control-btn" title="Next Item" aria-label="Next">
                            <i class="pi pi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Showcase Main Stage -->
                <div class="showcase-stage">
                    <div class="stage-mesh-glow"></div>
                    <div class="stage-content">
                        <!-- Left Info Panel -->
                        <div class="stage-info">
                            <div class="stage-tags">
                                <span v-if="products[currentSlide]?.is_pinned" class="pill-pinned">
                                    <i class="pi pi-bookmark-fill"></i> Staff Pinned
                                </span>
                                <span v-else-if="products[currentSlide]?.is_best_selling" class="pill-bestseller">
                                    <i class="pi pi-bolt"></i> Best Seller
                                </span>
                                <span class="pill-category">
                                    <i class="pi pi-tag"></i> {{ products[currentSlide]?.category?.category_name || 'Electronics' }}
                                </span>
                                <span v-if="products[currentSlide]?.product_quantity && products[currentSlide]?.product_quantity > 0" class="pill-stock in-stock">
                                    <span class="stock-dot"></span> In Stock
                                </span>
                            </div>

                            <h3 class="stage-product-title" :title="products[currentSlide]?.product_name">
                                {{ products[currentSlide]?.product_name }}
                            </h3>

                            <div class="stage-rating-row">
                                <div class="rating-stars">
                                    <i v-for="i in 5" :key="i" :class="i <= Math.round(Number(products[currentSlide]?.rates_avg_rate) || 0) ? 'pi pi-star-fill active' : 'pi pi-star'"></i>
                                </div>
                                <span class="rating-value">{{ products[currentSlide]?.rates_avg_rate ? Number(products[currentSlide]?.rates_avg_rate).toFixed(1) : '5.0' }}</span>
                                <span class="rating-count">({{ products[currentSlide]?.rates_count || 0 }} reviews)</span>
                            </div>

                            <p class="stage-description" v-if="products[currentSlide]?.product_description">
                                {{ products[currentSlide]?.product_description.slice(0, 140) }}...
                            </p>

                            <div class="stage-price-box">
                                <span class="price-label">Current Price</span>
                                <div class="price-value">{{ CurrencyUtil.formatCurrency(products[currentSlide]?.product_price) }}</div>
                            </div>

                            <div class="stage-actions">
                                <button @click="addToCart(products[currentSlide]?.product_id)" :disabled="addToCartService.request.loading" class="btn-spotlight-cart">
                                    <i :class="addToCartService.request.loading ? 'pi pi-spin pi-spinner' : 'pi pi-shopping-cart'"></i>
                                    <span>Add to Cart</span>
                                </button>
                                <button @click="goToProductDetails(products[currentSlide]?.product_id)" class="btn-spotlight-details">
                                    <span>View Specs</span>
                                    <i class="pi pi-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Right Visual Stage -->
                        <div class="stage-visual" @click="goToProductDetails(products[currentSlide]?.product_id)">
                            <div class="visual-halo"></div>
                            <div class="visual-podium"></div>
                            <div class="visual-img-container">
                                <img 
                                    v-if="products[currentSlide]?.product_image" 
                                    :src="UrlUtil.getBaseAppUrl(`storage/images/product/${products[currentSlide]?.product_image}`)" 
                                    :alt="products[currentSlide]?.product_name" 
                                    class="stage-main-img" 
                                    @error="handleImageError" 
                                />
                                <div v-else class="stage-placeholder-img">
                                    <i class="pi pi-image"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Thumbnail Track (For Multi-Pinned / Multi-Featured) -->
                    <div class="stage-thumbnails-track" v-if="products.length > 1">
                        <div class="thumbnails-header">
                            <span>Featured Showcase Items ({{ products.length }})</span>
                            <div class="track-hint">Click item to switch</div>
                        </div>
                        <div class="thumbnails-list">
                            <button 
                                v-for="(prod, idx) in products" 
                                :key="prod.product_id"
                                @click="setSlide(idx)"
                                :class="['thumb-card', { active: currentSlide === idx }]"
                            >
                                <div class="thumb-img-box">
                                    <img v-if="prod.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${prod.product_image}`)" :alt="prod.product_name" />
                                    <i v-else class="pi pi-image"></i>
                                </div>
                                <div class="thumb-details">
                                    <span v-if="prod.is_pinned" class="thumb-pinned-tag">Pinned</span>
                                    <p class="thumb-title">{{ prod.product_name }}</p>
                                    <p class="thumb-price">{{ CurrencyUtil.formatCurrency(prod.product_price) }}</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trust Bar -->
        <section class="trust-bar" id="features">
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

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <!-- Section Tabs -->
                <div class="section-tabs">
                    <button @click="activeView = 'categories'" :class="['tab-btn', { active: activeView === 'categories' }]">{{ categoriesHeading }}</button>
                    <button @click="activeView = 'products'" :class="['tab-btn', { active: activeView === 'products' }]">{{ productsHeading }}</button>
                </div>

                <!-- Categories -->
                <div v-if="activeView === 'categories'" class="grid-section">
                    <div v-if="CategoryStore.categories?.length" class="category-grid">
                        <button v-for="category in CategoryStore.categories" :key="category.category_id" class="category-card" @click="goRoute('customer.product-category', { id: category.category_id })">
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

                <!-- Products -->
                <div v-else class="grid-section">
                    <div v-if="products?.length" class="product-grid">
                        <div v-for="product in products" :key="product.product_id" class="product-card">
                            <div class="product-image-box">
                                <div v-if="product.is_pinned" class="pinned-badge">
                                    <i class="pi pi-bookmark-fill"></i> Pinned
                                </div>
                                <img v-if="product.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" :alt="product.product_name" />
                                <i v-else class="pi pi-image"></i>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">{{ product.product_name }}</h3>
                                <RouterLink :to="{ name: 'customer.product-info.index', params: { id: product.product_id } }" class="btn-view">View Details</RouterLink>
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state">
                        <div class="empty-icon"><i class="pi pi-shopping-bag"></i></div>
                        <h3>No Products Available</h3>
                        <p>Stay tuned for our latest offerings!</p>
                    </div>
                </div>

                <!-- Available Brands -->
                <section class="brands-section">
                    <div class="section-header">
                        <p class="section-label">Authorized Reseller</p>
                        <h2 class="section-title">Available Brands</h2>
                    </div>
                    <div class="brands-grid">
                        <div class="brand-item">
                            <img :src="AsusLogo" alt="ASUS" class="brand-img" />
                        </div>
                        <div class="brand-item">
                            <img :src="DellLogo" alt="DELL" class="brand-img" />
                        </div>
                        <div class="brand-item">
                            <img :src="HpLogo" alt="HP" class="brand-img" />
                        </div>
                        <div class="brand-item">
                            <img :src="LenovoLogo" alt="LENOVO" class="brand-img" />
                        </div>
                    </div>
                </section>

                <!-- Customer Testimonials -->
                <section class="testimonials-section">
                    <div class="section-header">
                        <p class="section-label">Customer Reviews</p>
                        <h2 class="section-title">What Our Customers Say</h2>
                    </div>
                    <div class="testimonials-grid">
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                            </div>
                            <p class="testimonial-text">"Excellent service! Got my laptop delivered in just 2 days. The product is 100% genuine and works perfectly. Highly recommended!"</p>
                            <div class="testimonial-author">
                                <div class="author-avatar">JD</div>
                                <div class="author-info">
                                    <span class="author-name">Juan Dela Cruz</span>
                                    <span class="author-location">Gensan City</span>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                            </div>
                            <p class="testimonial-text">"Best prices in Gensan! I compared with other stores and Alcess has the most competitive rates. Great customer support too."</p>
                            <div class="testimonial-author">
                                <div class="author-avatar">MR</div>
                                <div class="author-info">
                                    <span class="author-name">Maria Reyes</span>
                                    <span class="author-location">Tagum City</span>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star"></i>
                            </div>
                            <p class="testimonial-text">"Bought a gaming laptop for my son. The warranty claim process was smooth when we needed it. Very trustworthy store!"</p>
                            <div class="testimonial-author">
                                <div class="author-avatar">PS</div>
                                <div class="author-info">
                                    <span class="author-name">Pedro Santos</span>
                                    <span class="author-location">Digos City</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2>Ready to upgrade your tech?</h2>
                <p>Explore our wide selection of laptops, phones, and computers at competitive prices.</p>
                <div class="cta-buttons">
                    <button @click="goToBrowseProducts" class="btn-primary btn-lg shine-effect">Browse Products <i class="pi pi-arrow-right"></i></button>
                    <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="btn-outline"><i class="pi pi-facebook"></i> Message Us</a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<script setup lang="ts">
import Icon from "@/../img/logo.png";
import AsusLogo from "@/../img/brands/asus.png";
import DellLogo from "@/../img/brands/dell.png";
import HpLogo from "@/../img/brands/hp.png";
import LenovoLogo from "@/../img/brands/lenovo.png";

import { SearchErrorInterface, SearchProductInterface } from "@/interfaces/SearchProductInterface";
import { useSettingsStore } from "@/stores/SettingsStore";
import NavBar from "@/components/NavBar.vue";
import Footer from "@/components/Footer.vue";
import FloatingActionMenu from "@/components/FloatingActionMenu.vue";
import Carousel from "primevue/carousel";
import { computed, onMounted, onUnmounted, reactive, ref, watch } from "vue";
import { useResponsive } from "@/composables/useResponsive";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";
import BranchUtil from "@/utils/BranchUtil";
import { useRouter } from "vue-router";
import { CartFormInterface } from "@/interfaces/CartInterface";

import Page from "@/stores/Page";
const { isMobile } = useResponsive();
const navbarMode = computed(() => {
    if (!Page.user) return 'guest';
    const customerRoles = ['Customer', 'Client']; // Adjust based on your role names
    return customerRoles.includes(Page.user.role?.role_name) ? 'customer' : 'guest';
});

const appName = import.meta.env.VITE_APP_NAME;

const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const addToCartService = useAxiosUtil<CartFormInterface, null>();
const products = ref<ProductInterface[]>([]);
const currentSlide = ref(0);
let carouselInterval: number | null = null;
const activeView = ref<'categories' | 'products'>('categories');
const router = useRouter();

// Typewriter Logic
const typewriterWords = ["Your Setup", "Your Game", "Your Tech", "Your Life"];
const typewriterText = ref("");
const typewriterIndex = ref(0);
const charIndex = ref(0);
const isDeleting = ref(false);
const typeSpeed = ref(150);

const type = () => {
    const currentWord = typewriterWords[typewriterIndex.value];
    
    if (isDeleting.value) {
        typewriterText.value = currentWord.substring(0, charIndex.value - 1);
        charIndex.value--;
        typeSpeed.value = 50;
    } else {
        typewriterText.value = currentWord.substring(0, charIndex.value + 1);
        charIndex.value++;
        typeSpeed.value = 150;
    }

    if (!isDeleting.value && charIndex.value === currentWord.length) {
        isDeleting.value = true;
        typeSpeed.value = 2000; // Pause at end
    } else if (isDeleting.value && charIndex.value === 0) {
        isDeleting.value = false;
        typewriterIndex.value = (typewriterIndex.value + 1) % typewriterWords.length;
        typeSpeed.value = 500; // Pause before new word
    }

    setTimeout(type, typeSpeed.value);
};


const categoriesHeading = computed(() => CategoryStore.categories?.some((cat: any) => cat.is_popular) ? 'Popular Categories' : 'Categories');
const productsHeading = computed(() => products.value?.some((prod: any) => prod.is_best_selling) ? 'Best Sellers' : 'Products');
const showCarousel = computed(() => products.value?.some((prod: any) => prod.is_best_selling));
const isProductPage = computed(() => ['customer.product-search', 'customer.product-info.index', 'customer.product-category', 'customer.browse-products', 'customer.home.index', 'customer.cart', 'home'].includes(router.currentRoute.value.name as string));

const form: SearchProductInterface = reactive({ search: null });
const errors: SearchErrorInterface = reactive({ search: [] });

const goToBrowseProducts = () => router.push({ name: "customer.browse-products" });


const handleSearch = () => {
    if (form.search?.trim()) router.push({ name: 'customer.search-product', query: { q: form.search } });
    else if (router.currentRoute.value.name === 'customer.search-product') router.push({ name: 'customer.search-product' });
};

let searchTimeout: number | null = null;
watch(() => form.search, (newValue) => {
    if (router.currentRoute.value.name === 'customer.search-product') {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = window.setTimeout(() => {
            if (newValue?.trim()) router.push({ name: 'customer.search-product', query: { q: newValue } });
            else router.push({ name: 'customer.search-product' });
        }, 500);
    }
});



const loadBestSellingProducts = async () => {
    await loadBestSellingService.get("best-selling").then(() => {
        if (loadBestSellingService.request.status === 200 && loadBestSellingService.request.data) {
            products.value = loadBestSellingService.request.data;
            startCarousel();
        }
    });
};

const startCarousel = () => {
    const count = products.value.length;
    if (count > 1) {
        stopCarousel();
        carouselInterval = window.setInterval(() => {
            currentSlide.value = (currentSlide.value + 1) % count;
        }, 6000);
    }
};

const stopCarousel = () => {
    if (carouselInterval) {
        clearInterval(carouselInterval);
        carouselInterval = null;
    }
};

const nextSlide = () => {
    if (products.value.length > 0) {
        currentSlide.value = (currentSlide.value + 1) % products.value.length;
    }
};

const prevSlide = () => {
    if (products.value.length > 0) {
        currentSlide.value = (currentSlide.value - 1 + products.value.length) % products.value.length;
    }
};

const setSlide = (idx: number) => {
    currentSlide.value = idx;
};
const goRoute = (route: string, params: Record<string, string> = {}) => router.push({ name: route, params });
const handleImageError = (event: Event) => { (event.target as HTMLImageElement).style.display = 'none'; };

const handleLogout = async () => {
    await useAxiosUtil().post('logout').then(() => {
        Page.user = null;
        router.push({ name: 'home' });
        toast.success("Logged out successfully");
    });
};

const addToCart = async (productId: number) => {
    if (!Page.user) { router.push({ name: 'auth.login' }); return; }
    await addToCartService.post('customer/carts', { product_id: productId, quantity: 1 });
};

const goToProductDetails = (productId: number) => router.push({ name: 'customer.product-info.index', params: { id: productId } });

onMounted(() => { 
    CategoryStore.fetchCategories(); 
    loadBestSellingProducts(); 
    setTimeout(type, 1000);
});
onUnmounted(() => stopCarousel());
</script>

<style scoped>
/* Base */
.home-page { min-height: 100vh; background: #f8fafc; font-family: 'Inter', 'Poppins', sans-serif; }
.container { max-width: 1280px; margin: 0 auto; padding: 0 1rem; }

/* Navigation Header */
.nav-header { background: #2563eb; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.nav-container { display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 1rem; gap: 1rem; max-width: 1280px; margin: 0 auto; }
.nav-brand { display: flex; align-items: center; gap: 0.625rem; text-decoration: none; flex-shrink: 0; }
.nav-logo { width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #fff; display: flex; align-items: center; justify-content: center; padding: 2px; }
.nav-logo img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.nav-title { font-size: 1.125rem; font-weight: 700; color: #fff; white-space: nowrap; }
.nav-search { flex: 1; max-width: 400px; display: none; }
.nav-search-box { position: relative; }
.nav-search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.875rem; }
.nav-search-box input { width: 100%; padding: 0.5rem 1rem 0.5rem 2.5rem; border: 2px solid #fff; border-radius: 8px; font-size: 0.875rem; background: #fff; transition: all 0.2s; }
.nav-search-box input:focus { outline: none; box-shadow: 0 0 0 3px rgba(255,255,255,0.3); }
.nav-search-box input::placeholder { color: #94a3b8; }
.nav-actions { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; }
.nav-btn { width: 36px; height: 36px; border-radius: 50%; border: none; background: rgba(255,255,255,0.2); color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; text-decoration: none; }
.nav-btn:hover { background: rgba(255,255,255,0.3); }
.nav-btn i { font-size: 1rem; }
.nav-btn-fb { background: #fff; color: #2563eb; }
.nav-btn-fb:hover { background: #f1f5f9; color: #1d4ed8; }
.nav-btn-logout:hover { background: #fee2e2; color: #dc2626; }
.nav-mobile-search { display: block; padding: 0 1rem 0.625rem; background: #2563eb; }
.nav-mobile-search .nav-search-box input { border-color: rgba(255,255,255,0.3); }

/* Hero */
.hero { position: relative; background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); min-height: 500px; overflow: hidden; }
.hero-bg { position: absolute; inset: 0; background: radial-gradient(ellipse at top right, rgba(59,130,246,0.15) 0%, transparent 50%); }
.hero-content { position: relative; z-index: 10; max-width: 1280px; margin: 0 auto; padding: 3rem 1rem; }
.hero-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; align-items: center; }
.hero-text { text-align: center; position: relative; z-index: 5; }
.hero-watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: clamp(3rem, 15vw, 5rem); font-weight: 900; color: rgba(255,255,255,0.06); text-transform: uppercase; letter-spacing: 0.1em; white-space: nowrap; z-index: -1; pointer-events: none; user-select: none; width: 100%; display: flex; align-items: center; justify-content: center; }
.hero-badge { display: inline-block; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #94a3b8; font-size: 0.75rem; font-weight: 500; padding: 0.375rem 0.75rem; border-radius: 9999px; margin-bottom: 1rem; position: relative; z-index: 2; }
.hero-product-name { font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.2; margin-bottom: 0.5rem; position: relative; z-index: 2; }
.hero-category { color: #94a3b8; font-size: 0.875rem; margin-bottom: 1rem; position: relative; z-index: 2; }
.hero-price { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem; position: relative; z-index: 2; }
.hero-buttons { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; position: relative; z-index: 2; }
.hero-dots { display: flex; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; }
.dot { width: 8px; height: 8px; border-radius: 9999px; background: rgba(255,255,255,0.3); border: none; cursor: pointer; transition: all 0.3s; }
.dot.active { width: 24px; background: linear-gradient(90deg, #1e40af 0%, #00f2ff 100%); }
.hero-static { text-align: center; padding: 2rem 0; }
.hero-headline { font-size: 2rem; font-weight: 700; color: #fff; margin-bottom: 1rem; }
.text-accent { color: #60a5fa; }
.hero-subtext { color: #94a3b8; font-size: 1rem; margin-bottom: 1.5rem; max-width: 500px; margin-left: auto; margin-right: auto; }

/* Buttons */
.btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #fff; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.9375rem; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.06); }
.btn-primary:hover { background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%); transform: translateY(-1px); box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.btn-secondary { display: inline-flex; align-items: center; gap: 0.5rem; background: #fff; color: #2563eb; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; cursor: pointer; font-size: 0.9375rem; transition: all 0.2s ease; }
.btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; transform: translateY(-1px); }
.btn-lg { padding: 0.875rem 1.75rem; font-size: 1rem; border-radius: 0.875rem; }
.btn-outline { display: inline-flex; align-items: center; gap: 0.5rem; background: transparent; color: #fff; font-weight: 500; padding: 0.75rem 1.5rem; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.25); text-decoration: none; font-size: 0.875rem; transition: all 0.3s ease; }
.btn-outline:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.4); transform: translateY(-2px); }
.btn-view { display: block; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: #fff; text-align: center; padding: 0.5rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 500; text-decoration: none; transition: all 0.2s ease; }
.btn-view:hover { background: linear-gradient(135deg, #334155 0%, #475569 100%); }

/* Welcome Hero */
/* Clean White Background with Blurry Background Image */
.welcome-hero { position: relative; background: #ffffff; min-height: 85vh; display: flex; align-items: center; justify-content: center; overflow: hidden; padding-top: 80px; }
.hero-bg-container { position: absolute; inset: 0; z-index: 1; overflow: hidden; pointer-events: none; background: #ffffff; }

.hero-bg-image {
    position: absolute;
    inset: -20px;
    background-image: url('/bg.png');
    background-size: cover;
    background-position: center;
    filter: blur(4px);
    opacity: 0.2;
    z-index: 0;
}

.welcome-container { position: relative; z-index: 10; padding: 0 1.5rem; width: 100%; }
.welcome-content { max-width: 900px; margin: 0 auto; text-align: center; }
.welcome-badge-wrapper { margin-bottom: 1.5rem; }
.welcome-badge { display: inline-block; background: #fff; border: 1px solid #e2e8f0; color: #4b5563; font-size: 0.8125rem; font-weight: 600; padding: 0.4rem 1rem; border-radius: 9999px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.welcome-headline { font-size: clamp(2.5rem, 10vw, 5.5rem); font-weight: 800; color: #1a1f36; line-height: 1.1; letter-spacing: -0.02em; margin-bottom: 2rem; }
.typewriter-text { color: #2563eb; background: linear-gradient(135deg, #2563eb 0%, #7928ca 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.cursor { color: #2563eb; display: inline-block; animation: blink 1s step-end infinite; }
@keyframes blink { 50% { opacity: 0; } }
.welcome-subheadline { font-size: clamp(1.125rem, 3vw, 1.35rem); color: #4f566b; margin-bottom: 3.5rem; max-width: 650px; margin-left: auto; margin-right: auto; line-height: 1.6; font-weight: 400; }
.welcome-actions { display: flex; align-items: center; justify-content: center; gap: 1.5rem; }
.branch-footer-info { margin-top: 5rem; padding-top: 2rem; border-top: 1px solid rgba(0,0,0,0.08); }


/* Brands Section */
.brands-section { margin-top: 6rem; padding-bottom: 4rem; border-bottom: 1px solid #e2e8f0; }
.brands-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
.brand-item { display: flex; align-items: center; justify-content: center; padding: 2.5rem; background: #fff; border-radius: 1.5rem; border: 1px solid #f1f5f9; transition: all 0.4s; }
.brand-item:hover { transform: translateY(-5px); border-color: #2563eb; box-shadow: 0 15px 30px rgba(37, 99, 235, 0.05); }
.brand-img { max-width: 100%; max-height: 50px; object-fit: contain; filter: grayscale(1) opacity(0.7); transition: all 0.4s; }
.brand-item:hover .brand-img { filter: grayscale(0) opacity(1); }
.brand-text { display: none; }

/* Newsletter */
.newsletter-section { padding: 4rem 0; background: #f8fafc; }
.newsletter-card { background: #1e293b; border-radius: 2rem; padding: 3rem; display: flex; flex-direction: column; gap: 2rem; align-items: center; text-align: center; color: #fff; }
.newsletter-content h3 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
.newsletter-content p { color: #94a3b8; font-size: 1rem; }
.newsletter-form { display: flex; flex-direction: column; width: 100%; max-width: 500px; gap: 0.75rem; }
.newsletter-form input { flex: 1; padding: 0.75rem 1.25rem; border-radius: 0.5rem; border: none; background: rgba(255,255,255,0.1); color: #fff; outline: none; border: 1px solid rgba(255,255,255,0.2); width: 100%; }
.newsletter-form input:focus { border-color: #2563eb; background: rgba(255,255,255,0.15); }
.newsletter-form button { width: 100%; justify-content: center; }

/* Featured & Pinned Showcase Section */
.featured-showcase-section { padding: 5rem 0; background: #f8fafc; position: relative; }

.showcase-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 2rem; gap: 1.5rem; flex-wrap: wrap; }
.showcase-badge { display: inline-flex; align-items: center; gap: 0.375rem; background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.35rem 0.85rem; border-radius: 9999px; margin-bottom: 0.75rem; }
.showcase-badge i { font-size: 0.75rem; color: #3b82f6; }
.showcase-title { font-size: clamp(1.75rem, 3.5vw, 2.5rem); font-weight: 800; color: #0f172a; line-height: 1.2; letter-spacing: -0.02em; margin-bottom: 0.5rem; }
.showcase-subtitle { font-size: 0.95rem; color: #64748b; max-width: 600px; }

.showcase-controls { display: flex; align-items: center; gap: 0.75rem; background: #fff; padding: 0.375rem 0.5rem; border-radius: 9999px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.04); }
.control-btn { width: 34px; height: 34px; border-radius: 50%; border: 1px solid #e2e8f0; background: #f8fafc; color: #334155; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
.control-btn:hover { background: #2563eb; color: #fff; border-color: #2563eb; transform: scale(1.05); }
.slide-indicator { font-size: 0.8125rem; font-weight: 700; color: #475569; font-variant-numeric: tabular-nums; padding: 0 0.5rem; }

/* Showcase Main Stage */
.showcase-stage { background: linear-gradient(135deg, #0b1120 0%, #1e1b4b 60%, #0f172a 100%); border-radius: 2rem; overflow: hidden; position: relative; box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.35); border: 1px solid rgba(255,255,255,0.08); }
.stage-mesh-glow { position: absolute; top: -20%; right: 10%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(139, 92, 246, 0.15) 50%, transparent 70%); filter: blur(50px); pointer-events: none; }

.stage-content { display: grid; grid-template-columns: 1.15fr 0.85fr; min-height: 480px; align-items: center; padding: 3.5rem; gap: 3rem; position: relative; z-index: 2; }

/* Stage Info */
.stage-info { display: flex; flex-direction: column; gap: 1.25rem; }
.stage-tags { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.pill-pinned { display: inline-flex; align-items: center; gap: 0.35rem; background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.3rem 0.75rem; border-radius: 9999px; box-shadow: 0 2px 10px rgba(245, 158, 11, 0.3); }
.pill-pinned i { font-size: 0.6875rem; }
.pill-bestseller { display: inline-flex; align-items: center; gap: 0.35rem; background: linear-gradient(135deg, #ef4444, #f97316); color: #fff; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.3rem 0.75rem; border-radius: 9999px; }
.pill-category { display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15); color: #cbd5e1; font-size: 0.75rem; font-weight: 600; padding: 0.3rem 0.75rem; border-radius: 9999px; backdrop-filter: blur(8px); }
.pill-stock { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 600; padding: 0.3rem 0.75rem; border-radius: 9999px; }
.pill-stock.in-stock { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.25); }
.stock-dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; animation: pulseGlow 2s infinite; }
@keyframes pulseGlow { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

.stage-product-title { font-size: clamp(1.5rem, 3vw, 2.35rem); font-weight: 800; color: #fff; line-height: 1.2; letter-spacing: -0.01em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

.stage-rating-row { display: flex; align-items: center; gap: 0.5rem; }
.rating-stars { display: flex; gap: 2px; color: #475569; font-size: 0.8125rem; }
.rating-stars .active { color: #facc15; }
.rating-value { color: #fff; font-weight: 700; font-size: 0.875rem; }
.rating-count { color: #94a3b8; font-size: 0.8125rem; }

.stage-description { color: #94a3b8; font-size: 0.9375rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-width: 550px; }

.stage-price-box { display: flex; flex-direction: column; gap: 0.125rem; }
.price-label { font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; }
.price-value { font-size: clamp(1.85rem, 3.5vw, 2.75rem); font-weight: 900; background: linear-gradient(135deg, #38bdf8 0%, #60a5fa 50%, #c084fc 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.02em; }

.stage-actions { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem; }
.btn-spotlight-cart { display: inline-flex; align-items: center; justify-content: center; gap: 0.625rem; background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff; font-size: 0.9375rem; font-weight: 700; padding: 0.875rem 1.75rem; border-radius: 0.875rem; border: none; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35); }
.btn-spotlight-cart:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 15px 30px rgba(37, 99, 235, 0.45); background: linear-gradient(135deg, #1d4ed8, #2563eb); }
.btn-spotlight-cart:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-spotlight-details { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #e2e8f0; font-size: 0.9375rem; font-weight: 600; padding: 0.875rem 1.5rem; border-radius: 0.875rem; cursor: pointer; transition: all 0.3s; backdrop-filter: blur(10px); }
.btn-spotlight-details:hover { background: rgba(255,255,255,0.12); color: #fff; border-color: rgba(255,255,255,0.3); transform: translateX(3px); }

/* Right Visual Stage */
.stage-visual { position: relative; display: flex; align-items: center; justify-content: center; height: 380px; cursor: pointer; }
.visual-halo { position: absolute; width: 300px; height: 300px; border-radius: 50%; background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, rgba(99, 102, 241, 0.1) 60%, transparent 80%); filter: blur(30px); }
.visual-podium { position: absolute; bottom: 20px; width: 260px; height: 40px; border-radius: 50%; background: radial-gradient(ellipse at center, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 70%); filter: blur(6px); }
.visual-img-container { position: relative; z-index: 2; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 1.5rem; transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); }
.stage-visual:hover .visual-img-container { transform: scale(1.06) translateY(-8px); }
.stage-main-img { max-width: 100%; max-height: 320px; object-fit: contain; filter: drop-shadow(0 20px 30px rgba(0,0,0,0.4)); }
.stage-placeholder-img { width: 150px; height: 150px; border-radius: 1.5rem; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: #475569; font-size: 3rem; }

/* Bottom Thumbnail Track */
.stage-thumbnails-track { border-top: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.25); padding: 1.25rem 2rem; backdrop-filter: blur(10px); }
.thumbnails-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
.track-hint { font-size: 0.6875rem; color: #64748b; font-weight: 400; text-transform: none; }
.thumbnails-list { display: flex; gap: 0.75rem; overflow-x: auto; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.2) transparent; padding-bottom: 0.25rem; }
.thumb-card { display: flex; align-items: center; gap: 0.75rem; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 0.75rem; padding: 0.5rem 0.75rem; min-width: 210px; max-width: 240px; text-align: left; cursor: pointer; transition: all 0.25s ease; flex-shrink: 0; color: inherit; }
.thumb-card:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2); transform: translateY(-2px); }
.thumb-card.active { background: rgba(37, 99, 235, 0.18); border-color: #3b82f6; box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); }
.thumb-img-box { width: 44px; height: 44px; border-radius: 0.5rem; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0; padding: 2px; }
.thumb-img-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
.thumb-img-box i { font-size: 1rem; color: #64748b; }
.thumb-details { flex: 1; min-width: 0; }
.thumb-pinned-tag { display: inline-block; background: #d97706; color: #fff; font-size: 0.5625rem; font-weight: 700; text-transform: uppercase; padding: 0.05rem 0.35rem; border-radius: 4px; margin-bottom: 0.125rem; }
.thumb-title { font-size: 0.75rem; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.125rem; }
.thumb-price { font-size: 0.75rem; font-weight: 700; color: #38bdf8; }

/* Responsive Adjustments */
@media (max-width: 900px) {
    .stage-content { grid-template-columns: 1fr; padding: 2rem; gap: 2rem; min-height: auto; }
    .stage-visual { order: -1; height: 260px; }
    .stage-main-img { max-height: 220px; }
    .stage-thumbnails-track { padding: 1rem; }
}

/* Shine Effect for Primary Button */
.shine-effect { position: relative; overflow: hidden; }
.shine-effect::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: linear-gradient(45deg, transparent 45%, rgba(255,255,255,0.1) 50%, transparent 55%); transform: rotate(45deg); animation: shine 3s infinite; }
@keyframes shine { 0% { left: -100%; } 100% { left: 100%; } }

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

/* Main Content */
.main-content { padding: 2rem 0; }
.section-tabs { display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 2rem; }
.tab-btn { padding: 0.5rem 1rem; border-radius: 9999px; border: none; background: transparent; color: #64748b; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s; }
.tab-btn.active { background: #1e293b; color: #fff; }
.tab-btn:hover:not(.active) { background: #f1f5f9; color: #1e293b; }
.grid-section { min-height: 200px; }

/* Categories */
.category-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
.category-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1.5rem; padding: 3rem 2rem; text-align: center; cursor: pointer; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 300px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.category-card:hover { border-color: #2563eb; box-shadow: 0 20px 40px rgba(37, 99, 235, 0.08); transform: translateY(-8px); }
.category-image-box { width: 180px; height: 180px; margin-bottom: 2rem; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 1.5rem; transition: all 0.4s; }
.category-card:hover .category-image-box { transform: scale(1.05) rotate(2deg); background: #eff6ff; }
.category-image-box img { max-width: 85%; max-height: 85%; object-fit: contain; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.05)); }
.category-image-box i { font-size: 4rem; color: #cbd5e1; }
.category-name { font-size: 1.5rem; font-weight: 700; color: #1e293b; display: block; }

/* Products */
.product-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
.product-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; overflow: hidden; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.product-card:hover { border-color: #2563eb; box-shadow: 0 15px 30px rgba(0,0,0,0.08); transform: translateY(-4px); }
.product-image-box { position: relative; height: 200px; background: #f8fafc; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.pinned-badge { position: absolute; top: 0.75rem; right: 0.75rem; background: #2563eb; color: #fff; font-size: 0.625rem; font-weight: 600; padding: 0.25rem 0.5rem; border-radius: 9999px; display: flex; align-items: center; gap: 0.25rem; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.pinned-badge i { font-size: 0.5rem !important; color: #fff !important; }
.product-image-box img { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s; }
.product-card:hover .product-image-box img { transform: scale(1.05); }
.product-image-box i { font-size: 3rem; color: #cbd5e1; }
.product-info { padding: 1.25rem; }
.product-name { font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5rem; }

/* Empty State */
.empty-state { text-align: center; padding: 3rem 1rem; }
.empty-icon { width: 48px; height: 48px; background: #f1f5f9; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
.empty-icon i { font-size: 1.25rem; color: #94a3b8; }
.empty-state h3 { font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem; }
.empty-state p { font-size: 0.75rem; color: #64748b; }

/* Features */
.features-section { margin-top: 3rem; }
.section-header { text-align: center; margin-bottom: 2rem; }
.section-label { font-size: 0.75rem; font-weight: 500; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; }
.section-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
.features-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
.feature-card { text-align: center; padding: 1.5rem 1rem; }
.feature-icon { width: 40px; height: 40px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; }
.feature-icon i { font-size: 1rem; }
.feature-icon-green { background: #ecfdf5; color: #059669; }
.feature-icon-blue { background: #eff6ff; color: #2563eb; }
.feature-icon-purple { background: #f5f3ff; color: #7c3aed; }
.feature-icon-amber { background: #fffbeb; color: #d97706; }
.feature-card h3 { font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem; }
.feature-card p { font-size: 0.75rem; color: #64748b; }

/* Testimonials */
.testimonials-section { margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #e2e8f0; }
.testimonials-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
.testimonial-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.25rem; }
.testimonial-rating { display: flex; gap: 0.125rem; margin-bottom: 0.75rem; }
.testimonial-rating i { font-size: 0.75rem; color: #f59e0b; }
.testimonial-rating i.pi-star { color: #d1d5db; }
.testimonial-text { font-size: 0.8125rem; color: #475569; line-height: 1.6; margin-bottom: 1rem; font-style: italic; }
.testimonial-author { display: flex; align-items: center; gap: 0.75rem; }
.author-avatar { width: 36px; height: 36px; border-radius: 9999px; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; flex-shrink: 0; }
.author-info { display: flex; flex-direction: column; }
.author-name { font-size: 0.8125rem; font-weight: 600; color: #1e293b; }
.author-location { font-size: 0.6875rem; color: #64748b; }

/* CTA */
.cta-section { background: #ffffff; padding: 5rem 1rem; text-align: center; border-top: 1px solid #f1f5f9; }
.cta-section h2 { font-size: 2.25rem; font-weight: 800; color: #1e293b; margin-bottom: 1rem; letter-spacing: -0.02em; }
.cta-section > .container > p { color: #64748b; margin-bottom: 2.5rem; font-size: 1.1rem; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6; }
.cta-buttons { display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; }
.btn-outline { display: inline-flex; align-items: center; gap: 0.5rem; background: #f8fafc; color: #475569; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; text-decoration: none; font-size: 0.875rem; transition: all 0.3s ease; }
.btn-outline:hover { background: #f1f5f9; border-color: #cbd5e1; color: #1e293b; transform: translateY(-2px); }

/* Footer */
.footer { background: #0f172a; color: #94a3b8; padding: 2rem 1rem; }
.footer-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
.footer h3 { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem; }
.footer h4 { font-size: 0.875rem; font-weight: 600; color: #fff; margin-bottom: 0.75rem; }
.footer p { font-size: 0.75rem; line-height: 1.6; }
.footer ul { list-style: none; padding: 0; margin: 0; }
.footer ul li { margin-bottom: 0.375rem; }
.footer ul button { background: none; border: none; color: #94a3b8; font-size: 0.75rem; cursor: pointer; padding: 0; transition: color 0.2s; }
.footer ul button:hover { color: #fff; }
.social-links { display: flex; gap: 0.5rem; margin-bottom: 0.75rem; }
.social-links a { width: 32px; height: 32px; background: rgba(255,255,255,0.1); border-radius: 9999px; display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; transition: background 0.2s; }
.social-links a:hover { background: rgba(255,255,255,0.2); }
.contact-info { font-size: 0.75rem; display: flex; align-items: center; gap: 0.5rem; }
.footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); margin-top: 1.5rem; padding-top: 1.5rem; text-align: center; }
.footer-bottom p { font-size: 0.75rem; }

/* Dialog */
.dialog-footer { padding: 0.5rem; }
.dialog-footer-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
.dialog-admin-link { text-align: center; font-size: 0.75rem; color: #64748b; }
.admin-link { color: #059669; margin-left: 0.25rem; }
.dialog-footer-center { text-align: center; padding: 0.5rem; font-size: 0.875rem; }

/* Modal Content */
.modal-content { display: flex; flex-direction: column; gap: 1rem; }
.modal-content p { font-size: 0.875rem; color: #475569; line-height: 1.6; }
.modal-content h4 { font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-top: 0.5rem; }
.modal-content ul { list-style: disc; padding-left: 1.25rem; font-size: 0.875rem; color: #475569; }
.modal-content ul li { margin-bottom: 0.25rem; }
.modal-header-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
.modal-header-info h3 { font-size: 1.125rem; font-weight: 700; color: #1e293b; }
.modal-header-info p { font-size: 0.875rem; color: #64748b; }
.modal-icon { width: 48px; height: 48px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; }
.modal-icon i { font-size: 1.25rem; }
.modal-icon-blue { background: #eff6ff; color: #2563eb; }
.modal-icon-green { background: #ecfdf5; color: #059669; }
.contact-card { display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.5rem; }
.contact-card h4 { font-size: 0.875rem; font-weight: 600; color: #1e293b; margin: 0 0 0.25rem 0; }
.contact-card a { color: #2563eb; font-size: 0.875rem; }
.contact-card p { font-size: 0.875rem; color: #475569; margin: 0; }
.faq-item { border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; }
.faq-item:last-child { border-bottom: none; }
.faq-item h4 { margin: 0 0 0.25rem 0; }
.faq-item p { margin: 0; }
.info-banner { display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 0.5rem; }
.info-banner i { font-size: 1.5rem; }
.info-banner h4 { font-size: 0.875rem; font-weight: 600; margin: 0; }
.info-banner p { font-size: 0.75rem; margin: 0; }
.info-banner-blue { background: #eff6ff; color: #2563eb; }
.info-banner-green { background: #ecfdf5; color: #059669; }

/* Tablet */
@media (min-width: 768px) {
    .nav-container { padding: 0.875rem 1.5rem; }
    .nav-logo { width: 44px; height: 44px; }
    .nav-title { font-size: 1.25rem; }
    .nav-search { display: block; }
    .nav-mobile-search { display: none; }
    .nav-btn { width: 40px; height: 40px; }
    .hero-grid { grid-template-columns: 1fr 1fr; }
    .hero-text { text-align: left; }
    .hero-watermark { font-size: clamp(5rem, 10vw, 8rem); justify-content: flex-start; left: 0; transform: translateY(-50%); }
    .hero-buttons { justify-content: flex-start; }
    .hero-product-name { font-size: 3.5rem; }
    .hero-price { font-size: 2.5rem; }
    .hero-image { max-height: 450px; }
    .hero-watermark { font-size: 15rem; }
    .trust-grid { grid-template-columns: repeat(4, 1fr); }
    .product-grid { grid-template-columns: repeat(3, 1fr); }
    .features-grid { grid-template-columns: repeat(4, 1fr); }
    .testimonials-grid { grid-template-columns: repeat(3, 1fr); }
    .brands-grid { grid-template-columns: repeat(4, 1fr); }
    .newsletter-card { flex-direction: row; text-align: left; justify-content: space-between; padding: 4rem; }
    .newsletter-form { flex-direction: row; max-width: 400px; }
    .newsletter-form button { width: auto; }
    .footer-grid { grid-template-columns: repeat(2, 1fr); }
}

/* Desktop */
@media (min-width: 1024px) {
    .nav-container { padding: 0.75rem 2rem; }
    .nav-logo { width: 48px; height: 48px; }
    .nav-title { font-size: 1.375rem; }
    .nav-search { max-width: 500px; }
    .nav-btn { width: 42px; height: 42px; }
    .hero-content { padding: 4rem 2rem; }
    .hero-product-name { font-size: 3rem; }
    .hero-watermark { font-size: clamp(8rem, 12vw, 12rem); }
    .hero-image { max-height: 400px; }
    .product-grid { grid-template-columns: repeat(5, 1fr); }
    .footer-grid { grid-template-columns: repeat(4, 1fr); }
    .cta-section { padding: 4rem 2rem; }
    .cta-section h2 { font-size: 2rem; }
}
</style>