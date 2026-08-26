<template>
    <div class="customer-home">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-bg">
                <div class="hero-wave wave-1"></div>
                <div class="hero-wave wave-2"></div>
                <div class="hero-wave wave-3"></div>
                <div class="hero-orb orb-1"></div>
                <div class="hero-orb orb-2"></div>
            </div>
            <div class="hero-content">
                <div v-if="products.length > 0" class="hero-showcase-wrapper" @mouseenter="stopCarousel" @mouseleave="startCarousel">
                    <!-- Top Controls Bar -->
                    <div class="showcase-top-bar" v-if="products.length > 1">
                        <div class="showcase-counter">
                            <span class="pulse-dot"></span>
                            <span>Featured Spotlight ({{ String(currentSlide + 1).padStart(2, '0') }}/{{ String(products.length).padStart(2, '0') }})</span>
                        </div>
                        <div class="showcase-nav-btns">
                            <button @click="prevSlide" class="nav-arrow-btn" title="Previous"><i class="pi pi-chevron-left"></i></button>
                            <button @click="nextSlide" class="nav-arrow-btn" title="Next"><i class="pi pi-chevron-right"></i></button>
                        </div>
                    </div>

                    <!-- Main Showcase Grid -->
                    <div class="hero-grid">
                        <div class="hero-text">
                            <!-- Branch Watermark -->
                            <div class="hero-watermark">{{ BranchUtil.getBranchShortName() }}</div>
                            
                            <div class="hero-tag-row">
                                <span v-if="products[currentSlide]?.is_pinned" class="pill-pinned">
                                    <i class="pi pi-bookmark-fill"></i> Pinned
                                </span>
                                <span v-else-if="products[currentSlide]?.is_best_selling" class="pill-bestseller">
                                    <i class="pi pi-bolt"></i> Best Seller
                                </span>
                                <span class="pill-category">
                                    <i class="pi pi-tag"></i> {{ products[currentSlide]?.category?.category_name || 'Tech' }}
                                </span>
                            </div>

                            <h2 class="hero-title">{{ products[currentSlide]?.product_name }}</h2>
                            
                            <div class="hero-rating">
                                <div class="stars-box">
                                    <i v-for="i in 5" :key="i" :class="i <= Math.round(Number(products[currentSlide]?.rates_avg_rate) || 0) ? 'pi pi-star-fill active' : 'pi pi-star'"></i>
                                </div>
                                <span class="rating-num">{{ products[currentSlide]?.rates_avg_rate ? Number(products[currentSlide]?.rates_avg_rate).toFixed(1) : '5.0' }}</span>
                                <span class="rating-sub">({{ products[currentSlide]?.rates_count || 0 }} reviews)</span>
                            </div>

                            <div class="hero-price">{{ CurrencyUtil.formatCurrency(products[currentSlide]?.product_price) }}</div>

                            <div class="hero-buttons">
                                <button @click="openCartModal(products[currentSlide])" class="btn-primary">
                                    <i class="pi pi-shopping-cart"></i> Add to Cart
                                </button>
                                <button @click="viewDetails(products[currentSlide]?.product_id)" class="btn-secondary">
                                    View Details <i class="pi pi-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <div class="hero-image-container" @click="viewDetails(products[currentSlide]?.product_id)">
                            <div class="image-stage-glow"></div>
                            <img v-if="products[currentSlide]?.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${products[currentSlide]?.product_image}`)" :alt="products[currentSlide]?.product_name" class="hero-image" @error="handleImageError" />
                            <div v-else class="hero-placeholder"><i class="pi pi-image"></i></div>
                        </div>
                    </div>

                    <!-- Bottom Thumbnail Strip -->
                    <div class="hero-thumbs-strip" v-if="products.length > 1">
                        <button 
                            v-for="(prod, idx) in products" 
                            :key="prod.product_id"
                            @click="setSlide(idx)"
                            :class="['strip-thumb-btn', { active: currentSlide === idx }]"
                        >
                            <img v-if="prod.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${prod.product_image}`)" :alt="prod.product_name" />
                            <i v-else class="pi pi-image"></i>
                            <span v-if="prod.is_pinned" class="strip-pinned-dot" title="Pinned"></span>
                        </button>
                    </div>
                </div>
                <div v-else class="hero-static">
                    <span class="hero-badge">Premium Tech Store</span>
                    <h2 class="hero-title">Welcome to {{ appName }}</h2>
                    <p class="hero-subtitle">Discover premium laptops, phones & computers at unbeatable prices.</p>
                </div>
            </div>
        </section>

        <!-- Trust Bar -->
        <section class="trust-bar">
            <div class="container">
                <div class="trust-grid">
                    <div class="trust-item"><div class="trust-icon trust-green"><i class="pi pi-verified"></i></div><div><p class="trust-title">100% Genuine</p><p class="trust-sub">Authentic Products</p></div></div>
                    <div class="trust-item"><div class="trust-icon trust-blue"><i class="pi pi-truck"></i></div><div><p class="trust-title">Fast Delivery</p><p class="trust-sub">Nationwide Shipping</p></div></div>
                    <div class="trust-item"><div class="trust-icon trust-purple"><i class="pi pi-shield"></i></div><div><p class="trust-title">Warranty</p><p class="trust-sub">Official Coverage</p></div></div>
                    <div class="trust-item"><div class="trust-icon trust-amber"><i class="pi pi-headphones"></i></div><div><p class="trust-title">24/7 Support</p><p class="trust-sub">We're Here to Help</p></div></div>
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
                            <div class="category-img">
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
                            <div class="product-img">
                                <div v-if="product.is_pinned" class="pinned-badge">
                                    <i class="pi pi-bookmark-fill"></i> Pinned
                                </div>
                                <img v-if="product.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" :alt="product.product_name" />
                                <i v-else class="pi pi-image"></i>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">{{ product.product_name }}</h3>
                                <p class="product-price">{{ CurrencyUtil.formatCurrency(product.product_price) }}</p>
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

                <!-- Features -->
                <section class="features-section">
                    <div class="section-header"><p class="section-label">Why Choose Us</p><h2 class="section-title">Built for Trust</h2></div>
                    <div class="features-grid">
                        <div class="feature-card"><div class="feature-icon feature-green"><i class="pi pi-check-circle"></i></div><h3>Quality Guarantee</h3><p>100% genuine and verified products</p></div>
                        <div class="feature-card"><div class="feature-icon feature-blue"><i class="pi pi-truck"></i></div><h3>Fast Delivery</h3><p>Quick nationwide shipping</p></div>
                        <div class="feature-card"><div class="feature-icon feature-purple"><i class="pi pi-shield"></i></div><h3>Secure Payment</h3><p>Protected payment information</p></div>
                        <div class="feature-card"><div class="feature-icon feature-amber"><i class="pi pi-sync"></i></div><h3>Easy Returns</h3><p>7-day replacements for defects</p></div>
                    </div>
                </section>

                <!-- Testimonials -->
                <section class="testimonials-section">
                    <div class="section-header"><p class="section-label">Customer Reviews</p><h2 class="section-title">What Our Customers Say</h2></div>
                    <div class="testimonials-grid">
                        <div class="testimonial-card">
                            <div class="testimonial-rating"><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i></div>
                            <p class="testimonial-text">"Excellent service! Got my laptop delivered in just 2 days. The product is 100% genuine and works perfectly!"</p>
                            <div class="testimonial-author"><div class="author-avatar">JD</div><div><span class="author-name">Juan Dela Cruz</span><span class="author-location">Gensan City</span></div></div>
                        </div>
                        <div class="testimonial-card">
                            <div class="testimonial-rating"><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i></div>
                            <p class="testimonial-text">"Best prices in Gensan! I compared with other stores and Alcess has the most competitive rates."</p>
                            <div class="testimonial-author"><div class="author-avatar">MR</div><div><span class="author-name">Maria Reyes</span><span class="author-location">Tagum City</span></div></div>
                        </div>
                        <div class="testimonial-card">
                            <div class="testimonial-rating"><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i><i class="pi pi-star-fill"></i><i class="pi pi-star"></i></div>
                            <p class="testimonial-text">"Bought a gaming laptop for my son. The warranty claim process was smooth. Very trustworthy store!"</p>
                            <div class="testimonial-author"><div class="author-avatar">PS</div><div><span class="author-name">Pedro Santos</span><span class="author-location">Digos City</span></div></div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-grid">
                    <div><h3>{{ appName }}</h3><p>Your trusted destination for quality laptops, phones, and computers with genuine products and warranty.</p></div>
                    <div><h4>Quick Links</h4><ul><li><a href="#">About Us</a></li><li><a href="#">Contact</a></li><li><a href="#">FAQs</a></li></ul></div>
                    <div><h4>Customer Service</h4><ul><li><a href="#">Returns & Warranty</a></li><li><a href="#">Privacy Policy</a></li><li><a href="#">Terms & Conditions</a></li></ul></div>
                    <div><h4>Connect With Us</h4><div class="social-links"><a href="https://www.facebook.com/alcesslaptopstore" target="_blank"><i class="pi pi-facebook"></i></a></div><p class="contact-info"><i class="pi pi-envelope"></i> support@alcess.com</p></div>
                </div>
                <div class="footer-bottom"><p>&copy; {{ new Date().getFullYear() }} {{ appName }}. All rights reserved.</p></div>
            </div>
        </footer>

        <!-- Add to Cart Modal -->
        <Dialog v-model:visible="showCartModal" modal :style="{ width: '400px' }" :pt="{ root: { class: 'rounded-xl' }, header: { class: 'bg-blue-600 text-white rounded-t-xl px-5 py-3' }, content: { class: 'px-5 pt-5 pb-4' }, footer: { class: 'px-5 pb-4 pt-0' } }">
            <template #header><h2 class="text-lg font-bold">Add to Cart</h2></template>
            <div v-if="selectedProduct" class="cart-modal-content">
                <div class="cart-product">
                    <div class="cart-product-img">
                        <img v-if="selectedProduct.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${selectedProduct.product_image}`)" :alt="selectedProduct.product_name" />
                        <i v-else class="pi pi-image"></i>
                    </div>
                    <div class="cart-product-info">
                        <h3>{{ selectedProduct.product_name }}</h3>
                        <p class="cart-price">{{ CurrencyUtil.formatCurrency(selectedProduct.product_price) }}</p>
                        <div class="quantity-control">
                            <label>Quantity</label>
                            <div class="qty-buttons">
                                <button @click="decreaseQuantity" :disabled="cartQuantity <= 1" class="qty-btn"><i class="pi pi-minus"></i></button>
                                <input v-model.number="cartQuantity" type="number" :min="1" :max="selectedProduct.product_quantity" />
                                <button @click="increaseQuantity" :disabled="cartQuantity >= selectedProduct.product_quantity" class="qty-btn"><i class="pi pi-plus"></i></button>
                            </div>
                            <span class="stock-info">Available: {{ selectedProduct.product_quantity }} units</span>
                        </div>
                    </div>
                </div>
                <div class="cart-total"><span>Total:</span><span class="total-price">{{ CurrencyUtil.formatCurrency(selectedProduct.product_price * cartQuantity) }}</span></div>
            </div>
            <template #footer>
                <div class="cart-modal-footer">
                    <button @click="showCartModal = false" class="btn-cancel">Cancel</button>
                    <button @click="confirmAddToCart" :disabled="addToCartService.request.loading" class="btn-confirm">
                        <i v-if="addToCartService.request.loading" class="pi pi-spin pi-spinner"></i>
                        Add to Cart
                    </button>
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from "vue";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";
import BranchUtil from "@/utils/BranchUtil";
import Dialog from 'primevue/dialog';
import { useToast } from 'vue-toastification';
import Page from '@/stores/Page';
import { useRouter } from "vue-router";
import { CartFormInterface } from "@/interfaces/CartInterface";

const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const addToCartService = useAxiosUtil<CartFormInterface, null>();
const products = ref<ProductInterface[]>([]);
const activeView = ref<'categories' | 'products'>('categories');
const currentSlide = ref(0);
let carouselInterval: number | null = null;
const showCartModal = ref(false);
const selectedProduct = ref<ProductInterface | null>(null);
const cartQuantity = ref(1);
const toast = useToast();
const router = useRouter();
const appName = import.meta.env.VITE_APP_NAME;

const categoriesHeading = computed(() => CategoryStore.categories?.some((cat: any) => cat.is_popular) ? 'Popular Categories' : 'Categories');
const productsHeading = computed(() => products.value?.some((prod: any) => prod.is_best_selling) ? 'Best Sellers' : 'Products');
const showCarousel = computed(() => products.value?.some((prod: any) => prod.is_best_selling));

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
const handleImageError = (event: Event) => { (event.target as HTMLImageElement).style.display = 'none'; };

const openCartModal = (product: ProductInterface) => {
    if (!Page.user) { toast.error('Please login to add items to cart'); return; }
    selectedProduct.value = product; cartQuantity.value = 1; showCartModal.value = true;
};

const increaseQuantity = () => { if (selectedProduct.value && cartQuantity.value < selectedProduct.value.product_quantity) cartQuantity.value++; };
const decreaseQuantity = () => { if (cartQuantity.value > 1) cartQuantity.value--; };

const confirmAddToCart = async () => {
    if (!selectedProduct.value) return;
    await addToCartService.post('customer/carts', { product_id: selectedProduct.value.product_id, quantity: cartQuantity.value }).then(() => {
        if (addToCartService.request.status === 200) { toast.success(`Added ${cartQuantity.value} ${selectedProduct.value?.product_name} to cart`); showCartModal.value = false; selectedProduct.value = null; cartQuantity.value = 1; }
    }).catch(() => { toast.error(addToCartService.request.message || 'Failed to add product to cart'); });
};

const viewDetails = (productId: number) => router.push({ name: 'customer.product-info.index', params: { id: productId } });

onMounted(() => { CategoryStore.fetchCategories(); loadBestSellingProducts(); });
onUnmounted(() => stopCarousel());
</script>

<style scoped>
/* Base */
.customer-home { min-height: 100vh; background: #f8fafc; font-family: 'Inter', 'Poppins', sans-serif; }
.container { max-width: 1280px; margin: 0 auto; padding: 0 1rem; }

/* Hero Section */
.hero { position: relative; background: #0f172a; min-height: 460px; overflow: hidden; }
.hero-bg { position: absolute; inset: 0; overflow: hidden; }

.hero-content { position: relative; z-index: 10; max-width: 1280px; margin: 0 auto; padding: 2rem 1rem; }

/* Showcase Wrapper */
.hero-showcase-wrapper { background: linear-gradient(135deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.9) 100%); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 1.5rem; padding: 1.75rem; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4); backdrop-filter: blur(12px); }

.showcase-top-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06); padding-bottom: 0.75rem; }
.showcase-counter { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
.pulse-dot { width: 6px; height: 6px; border-radius: 50%; background: #38bdf8; box-shadow: 0 0 8px #38bdf8; animation: pulseGlow 2s infinite; }

.showcase-nav-btns { display: flex; gap: 0.5rem; }
.nav-arrow-btn { width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #cbd5e1; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 0.6875rem; }
.nav-arrow-btn:hover { background: #2563eb; color: #fff; border-color: #2563eb; transform: scale(1.08); }

.hero-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem; align-items: center; min-height: 280px; }
.hero-text { text-align: left; position: relative; z-index: 5; display: flex; flex-direction: column; gap: 0.625rem; }
.hero-watermark { position: absolute; top: 40%; left: 0; transform: translateY(-50%); font-size: clamp(2.5rem, 8vw, 4.5rem); font-weight: 900; color: rgba(255,255,255,0.03); text-transform: uppercase; letter-spacing: 0.1em; white-space: nowrap; z-index: -1; pointer-events: none; user-select: none; }

.hero-tag-row { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.pill-pinned { display: inline-flex; align-items: center; gap: 0.25rem; background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; padding: 0.2rem 0.6rem; border-radius: 9999px; }
.pill-bestseller { display: inline-flex; align-items: center; gap: 0.25rem; background: linear-gradient(135deg, #ef4444, #f97316); color: #fff; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; padding: 0.2rem 0.6rem; border-radius: 9999px; }
.pill-category { display: inline-flex; align-items: center; gap: 0.25rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); color: #cbd5e1; font-size: 0.6875rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 9999px; }

.hero-title { font-size: clamp(1.25rem, 2.5vw, 1.85rem); font-weight: 800; color: #fff; line-height: 1.25; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

.hero-rating { display: flex; align-items: center; gap: 0.375rem; }
.stars-box { display: flex; gap: 2px; color: #475569; font-size: 0.75rem; }
.stars-box .active { color: #fbbf24; }
.rating-num { font-size: 0.8125rem; color: #fff; font-weight: 700; }
.rating-sub { font-size: 0.75rem; color: #94a3b8; }

.hero-price { font-size: clamp(1.5rem, 3vw, 2.15rem); font-weight: 900; background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

.hero-buttons { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.25rem; }

.hero-image-container { position: relative; display: flex; justify-content: center; align-items: center; height: 240px; cursor: pointer; }
.image-stage-glow { position: absolute; width: 180px; height: 180px; border-radius: 50%; background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, transparent 70%); filter: blur(20px); }
.hero-image { max-height: 200px; width: auto; object-fit: contain; filter: drop-shadow(0 15px 30px rgba(0,0,0,0.5)); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.hero-image-container:hover .hero-image { transform: scale(1.08) translateY(-4px); }
.hero-placeholder { width: 160px; height: 160px; background: rgba(255,255,255,0.04); border-radius: 1rem; display: flex; align-items: center; justify-content: center; color: #475569; font-size: 2.5rem; }

/* Bottom Thumbnail Strip */
.hero-thumbs-strip { display: flex; gap: 0.5rem; overflow-x: auto; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.06); margin-top: 1rem; scrollbar-width: none; }
.strip-thumb-btn { position: relative; width: 44px; height: 44px; border-radius: 0.5rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 2px; flex-shrink: 0; transition: all 0.2s; }
.strip-thumb-btn img { max-width: 100%; max-height: 100%; object-fit: contain; }
.strip-thumb-btn i { font-size: 0.875rem; color: #64748b; }
.strip-thumb-btn.active { border-color: #38bdf8; background: rgba(56, 189, 248, 0.15); box-shadow: 0 0 10px rgba(56, 189, 248, 0.3); transform: scale(1.08); }
.strip-pinned-dot { position: absolute; top: -2px; right: -2px; width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; border: 1px solid #0f172a; }

.hero-static { text-align: center; padding: 2rem 0; }
.hero-subtitle { color: #94a3b8; font-size: 0.9375rem; max-width: 400px; margin: 0 auto; }

@media (max-width: 768px) {
    .hero-grid { grid-template-columns: 1fr; text-align: center; gap: 1.25rem; }
    .hero-text { text-align: center; align-items: center; }
    .hero-buttons { justify-content: center; }
    .hero-image-container { order: -1; height: 180px; }
    .hero-image { max-height: 160px; }
}

/* Buttons */
.btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #fff; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.8125rem; transition: all 0.2s; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2); }
.btn-primary:hover { background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%); transform: translateY(-1px); }
.btn-secondary { display: inline-flex; align-items: center; gap: 0.5rem; background: #fff; color: #2563eb; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; cursor: pointer; font-size: 0.8125rem; transition: all 0.2s; }
.btn-secondary:hover { background: #f8fafc; transform: translateY(-1px); }
.btn-view { display: block; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: #fff; text-align: center; padding: 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; text-decoration: none; transition: all 0.2s; }
.btn-view:hover { background: linear-gradient(135deg, #334155 0%, #475569 100%); }

/* Trust Bar */
.trust-bar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0.875rem 0; }
.trust-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
.trust-item { display: flex; align-items: center; gap: 0.625rem; }
.trust-icon { width: 32px; height: 32px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.trust-icon i { font-size: 0.8125rem; }
.trust-green { background: #ecfdf5; color: #059669; }
.trust-blue { background: #eff6ff; color: #2563eb; }
.trust-purple { background: #f5f3ff; color: #7c3aed; }
.trust-amber { background: #fffbeb; color: #d97706; }
.trust-title { font-size: 0.6875rem; font-weight: 600; color: #1e293b; }
.trust-sub { font-size: 0.5625rem; color: #64748b; }

/* Main Content */
.main-content { padding: 1.5rem 0; }
.section-tabs { display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 1.5rem; }
.tab-btn { padding: 0.5rem 1rem; border-radius: 9999px; border: none; background: transparent; color: #64748b; font-weight: 500; font-size: 0.8125rem; cursor: pointer; transition: all 0.2s; }
.tab-btn.active { background: #1e293b; color: #fff; }
.tab-btn:hover:not(.active) { background: #f1f5f9; color: #1e293b; }
.grid-section { min-height: 150px; }

/* Categories */
.category-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; }
.category-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.875rem; padding: 2rem 1.25rem; text-align: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 200px; }
.category-card:hover { border-color: #2563eb; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.1); transform: translateY(-4px); }
.category-img { width: 80px; height: 80px; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 0.875rem; transition: transform 0.3s; }
.category-card:hover .category-img { transform: scale(1.1); }
.category-img img { max-width: 80%; max-height: 80%; object-fit: contain; }
.category-img i { font-size: 2rem; color: #cbd5e1; }
.category-name { font-size: 1.125rem; font-weight: 700; color: #1e293b; display: block; }

/* Products */
.product-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.625rem; }
.product-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.625rem; overflow: hidden; transition: all 0.2s; }
.product-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.product-img { position: relative; height: 100px; background: #f8fafc; display: flex; align-items: center; justify-content: center; }
.pinned-badge { position: absolute; top: 0.375rem; right: 0.375rem; background: #2563eb; color: #fff; font-size: 0.5625rem; font-weight: 600; padding: 0.125rem 0.375rem; border-radius: 9999px; display: flex; align-items: center; gap: 0.1875rem; z-index: 10; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.pinned-badge i { font-size: 0.5rem !important; color: #fff !important; }
.product-img img { max-width: 100%; max-height: 100%; object-fit: contain; }
.product-img i { font-size: 1.75rem; color: #cbd5e1; }
.product-info { padding: 0.625rem; }
.product-name { font-size: 0.6875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2rem; }
.product-price { font-size: 0.8125rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }

/* Empty State */
.empty-state { text-align: center; padding: 2.5rem 1rem; }
.empty-icon { width: 44px; height: 44px; background: #f1f5f9; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; }
.empty-icon i { font-size: 1.125rem; color: #94a3b8; }
.empty-state h3 { font-size: 0.8125rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem; }
.empty-state p { font-size: 0.6875rem; color: #64748b; }

/* Features */
.features-section { margin-top: 2rem; }
.section-header { text-align: center; margin-bottom: 1.5rem; }
.section-label { font-size: 0.6875rem; font-weight: 500; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; }
.section-title { font-size: 1.25rem; font-weight: 700; color: #1e293b; }
.features-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
.feature-card { text-align: center; padding: 1.25rem 0.75rem; }
.feature-icon { width: 36px; height: 36px; border-radius: 0.625rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.625rem; }
.feature-icon i { font-size: 0.9375rem; }
.feature-green { background: #ecfdf5; color: #059669; }
.feature-blue { background: #eff6ff; color: #2563eb; }
.feature-purple { background: #f5f3ff; color: #7c3aed; }
.feature-amber { background: #fffbeb; color: #d97706; }
.feature-card h3 { font-size: 0.8125rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem; }
.feature-card p { font-size: 0.6875rem; color: #64748b; }

/* Testimonials */
.testimonials-section { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; }
.testimonials-grid { display: grid; grid-template-columns: 1fr; gap: 0.75rem; }
.testimonial-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.625rem; padding: 1rem; }
.testimonial-rating { display: flex; gap: 0.125rem; margin-bottom: 0.625rem; }
.testimonial-rating i { font-size: 0.6875rem; color: #f59e0b; }
.testimonial-rating i.pi-star { color: #d1d5db; }
.testimonial-text { font-size: 0.75rem; color: #475569; line-height: 1.5; margin-bottom: 0.75rem; font-style: italic; }
.testimonial-author { display: flex; align-items: center; gap: 0.625rem; }
.author-avatar { width: 32px; height: 32px; border-radius: 9999px; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.6875rem; font-weight: 600; flex-shrink: 0; }
.author-info { display: flex; flex-direction: column; }
.author-name { font-size: 0.75rem; font-weight: 600; color: #1e293b; }
.author-location { font-size: 0.625rem; color: #64748b; }

/* Footer */
.footer { background: #0f172a; color: #94a3b8; padding: 1.5rem 1rem; margin-top: 2rem; }
.footer-grid { display: grid; grid-template-columns: 1fr; gap: 1.25rem; }
.footer h3 { font-size: 0.9375rem; font-weight: 700; color: #fff; margin-bottom: 0.375rem; }
.footer h4 { font-size: 0.8125rem; font-weight: 600; color: #fff; margin-bottom: 0.5rem; }
.footer p { font-size: 0.6875rem; line-height: 1.5; }
.footer ul { list-style: none; padding: 0; margin: 0; }
.footer ul li { margin-bottom: 0.25rem; }
.footer ul a { color: #94a3b8; font-size: 0.6875rem; text-decoration: none; transition: color 0.2s; }
.footer ul a:hover { color: #fff; }
.social-links { display: flex; gap: 0.5rem; margin-bottom: 0.5rem; }
.social-links a { width: 28px; height: 28px; background: rgba(255,255,255,0.1); border-radius: 9999px; display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; transition: background 0.2s; }
.social-links a:hover { background: rgba(255,255,255,0.2); }
.contact-info { font-size: 0.6875rem; display: flex; align-items: center; gap: 0.375rem; }
.footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); margin-top: 1.25rem; padding-top: 1.25rem; text-align: center; }
.footer-bottom p { font-size: 0.6875rem; }

/* Cart Modal */
.cart-modal-content { display: flex; flex-direction: column; gap: 1rem; }
.cart-product { display: flex; gap: 1rem; }
.cart-product-img { width: 90px; height: 90px; background: #f8fafc; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.cart-product-img img { max-width: 100%; max-height: 100%; object-fit: contain; }
.cart-product-img i { font-size: 2rem; color: #cbd5e1; }
.cart-product-info { flex: 1; }
.cart-product-info h3 { font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
.cart-price { font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; }
.quantity-control { display: flex; flex-direction: column; gap: 0.375rem; }
.quantity-control label { font-size: 0.75rem; font-weight: 600; color: #475569; }
.qty-buttons { display: flex; align-items: center; gap: 0.375rem; }
.qty-btn { width: 28px; height: 28px; border-radius: 0.375rem; border: none; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s; }
.qty-btn:hover:not(:disabled) { background: #1d4ed8; }
.qty-btn:disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; }
.qty-btn i { font-size: 0.625rem; }
.qty-buttons input { width: 44px; height: 28px; text-align: center; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600; }
.qty-buttons input:focus { outline: none; border-color: #2563eb; }
.stock-info { font-size: 0.6875rem; color: #64748b; }
.cart-total { display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; border-top: 1px solid #e2e8f0; }
.cart-total span { font-size: 0.875rem; font-weight: 600; color: #1e293b; }
.total-price { font-size: 1.125rem; font-weight: 700; }
.cart-modal-footer { display: flex; gap: 0.5rem; }
.btn-cancel { flex: 1; padding: 0.625rem; background: #f1f5f9; color: #475569; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.btn-cancel:hover { background: #e2e8f0; }
.btn-confirm { flex: 1; padding: 0.625rem; background: #2563eb; color: #fff; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.375rem; }
.btn-confirm:hover:not(:disabled) { background: #1d4ed8; }
.btn-confirm:disabled { opacity: 0.6; cursor: not-allowed; }

/* Tablet */
@media (min-width: 768px) {
    .hero { min-height: 480px; }
    .hero-watermark { font-size: 8rem; }
    .hero-content { padding: 3rem 1.5rem; }
    .hero-grid { grid-template-columns: 1fr 1fr; }
    .hero-text { text-align: left; }
    .hero-watermark { font-size: clamp(5rem, 10vw, 8rem); justify-content: flex-start; left: 0; transform: translateY(-50%); }
    .hero-title { font-size: 2rem; }
    .hero-price { font-size: 1.75rem; }
    .hero-rating { justify-content: flex-start; }
    .hero-buttons { justify-content: flex-start; }
    .hero-image { max-height: 300px; }
    .trust-grid { grid-template-columns: repeat(4, 1fr); }
    .trust-icon { width: 36px; height: 36px; }
    .product-grid { grid-template-columns: repeat(3, 1fr); }
    .features-grid { grid-template-columns: repeat(4, 1fr); }
    .testimonials-grid { grid-template-columns: repeat(3, 1fr); }
    .footer-grid { grid-template-columns: repeat(2, 1fr); }
}

/* Desktop */
@media (min-width: 1024px) {
    .hero-content { padding: 4rem 2rem; }
    .hero-watermark { font-size: clamp(8rem, 12vw, 12rem); }
    .hero-title { font-size: 2.5rem; }
    .hero-image { max-height: 360px; }
    .product-grid { grid-template-columns: repeat(5, 1fr); }
    .footer-grid { grid-template-columns: repeat(4, 1fr); }
}
</style>
