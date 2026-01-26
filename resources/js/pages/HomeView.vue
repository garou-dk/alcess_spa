<template>
    <div class="home-page">
        <!-- Header -->
        <header class="nav-header">
            <div class="nav-container">
                <RouterLink :to="{ name: 'home' }" class="nav-brand">
                    <div class="nav-logo">
                        <img :src="Icon" alt="Store Icon" />
                    </div>
                    <span class="nav-title">{{ appName }}</span>
                </RouterLink>
                
                <div v-if="isProductPage" class="nav-search">
                    <form @submit.prevent="handleSearch">
                        <div class="nav-search-box">
                            <i class="pi pi-search" />
                            <input v-model="form.search" type="text" placeholder="Search products..." />
                        </div>
                    </form>
                </div>
                
                <div class="nav-actions">
                    <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="nav-btn nav-btn-fb" title="Facebook">
                        <i class="pi pi-facebook"></i>
                    </a>
                    <button type="button" class="nav-btn" @click="goToBrowseProducts" title="Browse Products">
                        <i class="pi pi-th-large"></i>
                    </button>
                    <button v-if="!Page.user" type="button" class="nav-btn" @click="openLoginForm()" title="Login">
                        <i class="pi pi-user"></i>
                    </button>
                </div>
            </div>
            
            <div v-if="isProductPage" class="nav-mobile-search">
                <form @submit.prevent="handleSearch">
                    <div class="nav-search-box">
                        <i class="pi pi-search" />
                        <input v-model="form.search" type="text" placeholder="Search products..." />
                    </div>
                </form>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-bg"></div>
            <!-- Davao Branch Banner -->
            <div class="branch-banner">
                <i class="pi pi-map-marker"></i>
                <span>Davao Branch</span>
            </div>
            <div class="hero-content">
                <div v-if="showCarousel && products.length > 0" class="hero-carousel">
                    <div class="hero-grid">
                        <div class="hero-text">
                            <span class="hero-badge">Featured Product</span>
                            <h2 class="hero-product-name">{{ products[currentSlide]?.product_name }}</h2>
                            <p class="hero-category">{{ products[currentSlide]?.category?.category_name || 'Premium Electronics' }}</p>
                            <div class="hero-price">{{ CurrencyUtil.formatCurrency(products[currentSlide]?.product_price) }}</div>
                            <div class="hero-buttons">
                                <button @click="addToCart(products[currentSlide]?.product_id)" :disabled="addToCartService.request.loading" class="btn-primary">
                                    <i :class="addToCartService.request.loading ? 'pi pi-spin pi-spinner' : 'pi pi-shopping-cart'"></i>
                                    Add to Cart
                                </button>
                                <button @click="goToProductDetails(products[currentSlide]?.product_id)" class="btn-secondary">
                                    View Details
                                </button>
                            </div>
                        </div>
                        <div class="hero-image-container">
                            <div class="hero-image-wrapper">
                                <img v-if="products[currentSlide]?.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${products[currentSlide]?.product_image}`)" :alt="products[currentSlide]?.product_name" class="hero-image" @error="handleImageError" />
                                <div v-else class="hero-image-placeholder"><i class="pi pi-image"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="hero-dots">
                        <button v-for="(_, index) in products.slice(0, 3)" :key="index" @click="currentSlide = index" :class="['dot', { active: currentSlide === index }]"></button>
                    </div>
                </div>
                <div v-else class="hero-static">
                    <span class="hero-badge">Premium Tech Store</span>
                    <h2 class="hero-headline">Welcome to <span class="text-accent">{{ appName }}</span></h2>
                    <p class="hero-subtext">Discover premium laptops, phones & computers at unbeatable prices with genuine warranty.</p>
                    <button @click="goToBrowseProducts" class="btn-primary btn-lg">Shop Now <i class="pi pi-arrow-right"></i></button>
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

                <!-- Features -->
                <section class="features-section">
                    <div class="section-header">
                        <p class="section-label">Why Choose Us</p>
                        <h2 class="section-title">Built for Trust</h2>
                    </div>
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon feature-icon-green"><i class="pi pi-check-circle"></i></div>
                            <h3>Quality Guarantee</h3>
                            <p>100% genuine and verified products</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon feature-icon-blue"><i class="pi pi-truck"></i></div>
                            <h3>Fast Delivery</h3>
                            <p>Quick nationwide shipping</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon feature-icon-purple"><i class="pi pi-shield"></i></div>
                            <h3>Secure Payment</h3>
                            <p>Protected payment information</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon feature-icon-amber"><i class="pi pi-sync"></i></div>
                            <h3>Easy Returns</h3>
                            <p>7-day replacements for defects</p>
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
                                    <span class="author-location">Davao City</span>
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
                            <p class="testimonial-text">"Best prices in Davao! I compared with other stores and Alcess has the most competitive rates. Great customer support too."</p>
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
                    <button @click="goToBrowseProducts" class="btn-primary btn-lg">Browse Products <i class="pi pi-arrow-right"></i></button>
                    <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="btn-outline"><i class="pi pi-facebook"></i> Message Us</a>
                </div>
                <div class="cta-stats">
                    <div><span class="stat-number">500+</span><span class="stat-label">Products</span></div>
                    <div><span class="stat-number">1K+</span><span class="stat-label">Happy Customers</span></div>
                    <div><span class="stat-number">4.8</span><span class="stat-label">Star Rating</span></div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-grid">
                    <div>
                        <h3>{{ appName }}</h3>
                        <p>Your trusted destination for quality laptops, phones, and computers with genuine products and warranty.</p>
                    </div>
                    <div>
                        <h4>Quick Links</h4>
                        <ul>
                            <li><button @click="openFooterModal('about')">About Us</button></li>
                            <li><button @click="openFooterModal('contact')">Contact</button></li>
                            <li><button @click="openFooterModal('faqs')">FAQs</button></li>
                            <li><button @click="openFooterModal('shipping')">Shipping Info</button></li>
                        </ul>
                    </div>
                    <div>
                        <h4>Customer Service</h4>
                        <ul>
                            <li><button @click="openFooterModal('returns')">Returns & Warranty</button></li>
                            <li><button @click="openFooterModal('privacy')">Privacy Policy</button></li>
                            <li><button @click="openFooterModal('terms')">Terms & Conditions</button></li>
                        </ul>
                    </div>
                    <div>
                        <h4>Connect With Us</h4>
                        <div class="social-links">
                            <a href="https://www.facebook.com/alcesslaptopstore" target="_blank"><i class="pi pi-facebook"></i></a>
                        </div>
                        <p class="contact-info"><i class="pi pi-envelope"></i> support@alcess.com</p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; {{ new Date().getFullYear() }} {{ appName }}. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Dialogs -->
        <Dialog v-model:visible="loginFormVisible" modal header="Login" :style="{ width: '28rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!">
            <LoginForm @success="loginFormVisible = false">
                <template #footer>
                    <div class="dialog-footer">
                        <div class="dialog-footer-row">
                            <Button type="button" variant="link" label="Forgot password?" class="p-0! text-black! text-sm!" pt:label:class="font-normal!" @click="openResetPasswordForm()" />
                            <div class="text-right">
                                <span class="text-sm">Don't have an account?</span>
                                <Button type="button" variant="link" label="Register" class="p-0! text-sm! text-blue-600!" pt:label:class="font-normal!" @click="openRegisterForm()" />
                            </div>
                        </div>
                        <div class="dialog-admin-link">
                            <span>Are you an administrator? </span>
                            <RouterLink :to="{ name: 'admin.login' }" class="admin-link">Login</RouterLink>
                        </div>
                    </div>
                </template>
            </LoginForm>
        </Dialog>

        <Dialog v-model:visible="registerFormVisible" modal header="Register" :style="{ width: '28rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!">
            <RegisterForm>
                <template #footer>
                    <div class="dialog-footer-center">
                        <span>Already have an account?</span>
                        <Button type="button" variant="link" label="Login" class="p-0!" pt:label:class="font-normal!" @click="openLoginForm()" />
                    </div>
                </template>
            </RegisterForm>
        </Dialog>

        <Dialog v-model:visible="resetPasswordFormVisible" modal header="Reset Account" :style="{ width: '28rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!">
            <ResetPasswordForm @code-sent="handleCodeSent" />
        </Dialog>

        <Dialog v-model:visible="verifyCodeFormVisible" modal header="Verify Code" :style="{ width: '28rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!">
            <VerifyCodeForm :email="resetEmail" :current-password="resetCurrentPassword" :new-password="resetNewPassword" :new-password-confirmation="resetNewPasswordConfirmation" @success="handleResetSuccess" />
        </Dialog>

        <Dialog v-model:visible="footerModalVisible" modal :header="footerModalTitle" :style="{ width: isMobile ? '95vw' : '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!">
            <div v-if="footerModalType === 'about'" class="modal-content">
                <div class="modal-header-info"><div class="modal-icon modal-icon-blue"><i class="pi pi-building"></i></div><div><h3>{{ appName }}</h3><p>Your Trusted Tech Partner</p></div></div>
                <p>Welcome to {{ appName }}! We are a dedicated online retailer specializing in high-quality laptops, smartphones, desktop computers, and accessories.</p>
                <p>Our mission is to provide our customers with the latest technology products at competitive prices, backed by excellent customer service.</p>
            </div>
            <div v-if="footerModalType === 'contact'" class="modal-content">
                <p>Have questions or need assistance? We're here to help!</p>
                <div class="contact-card"><div class="modal-icon modal-icon-blue"><i class="pi pi-facebook"></i></div><div><h4>Facebook</h4><a href="https://www.facebook.com/alcesslaptopstore" target="_blank">facebook.com/alcesslaptopstore</a></div></div>
                <div class="contact-card"><div class="modal-icon modal-icon-green"><i class="pi pi-envelope"></i></div><div><h4>Email</h4><p>support@alcess.com</p></div></div>
            </div>
            <div v-if="footerModalType === 'faqs'" class="modal-content">
                <div class="faq-item"><h4>How do I place an order?</h4><p>Browse our products, add items to your cart, and proceed to checkout.</p></div>
                <div class="faq-item"><h4>What payment methods do you accept?</h4><p>We accept bank transfers, GCash, and cash on delivery for select areas.</p></div>
                <div class="faq-item"><h4>Are your products genuine?</h4><p>Yes! All products are 100% genuine with official manufacturer warranty.</p></div>
                <div class="faq-item"><h4>How long does delivery take?</h4><p>Delivery typically takes 3-7 business days depending on your location.</p></div>
            </div>
            <div v-if="footerModalType === 'shipping'" class="modal-content">
                <div class="info-banner info-banner-blue"><i class="pi pi-truck"></i><div><h4>Nationwide Delivery</h4><p>We deliver to all provinces in the Philippines</p></div></div>
                <h4>Delivery Timeline</h4>
                <ul><li>Metro Manila: 5-7 business days</li><li>Luzon: 7-10 business days</li><li>Visayas: 3-6 business days</li><li>Mindanao: 1-3 business days</li></ul>
            </div>
            <div v-if="footerModalType === 'returns'" class="modal-content">
                <div class="info-banner info-banner-green"><i class="pi pi-shield"></i><div><h4>Warranty Coverage</h4><p>All products come with manufacturer warranty</p></div></div>
                <h4>Return Policy</h4>
                <ul><li>7-day replacement for defective products</li><li>Products must be in original packaging</li><li>Contact us first before returning any item</li></ul>
            </div>
            <div v-if="footerModalType === 'privacy'" class="modal-content">
                <h4>Information We Collect</h4><p>We collect information you provide when creating an account or placing orders.</p>
                <h4>How We Use Your Information</h4><p>Your information is used to process orders and provide customer support. We never sell your data.</p>
            </div>
            <div v-if="footerModalType === 'terms'" class="modal-content">
                <h4>Order Acceptance</h4><p>All orders are subject to acceptance and availability.</p>
                <h4>Pricing</h4><p>Prices are subject to change without notice.</p>
                <h4>Product Descriptions</h4><p>We strive to provide accurate descriptions. Actual products may vary slightly.</p>
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
import { SearchErrorInterface, SearchProductInterface } from "@/interfaces/SearchProductInterface";
import Page from "@/stores/Page";
import { computed, onMounted, onUnmounted, reactive, ref, watch } from "vue";
import { useResponsive } from "@/composables/useResponsive";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";
import { useRouter } from "vue-router";
import { CartFormInterface } from "@/interfaces/CartInterface";

const { isMobile } = useResponsive();
const appName = import.meta.env.VITE_APP_NAME;
const loginFormVisible = ref(false);
const registerFormVisible = ref(false);
const resetPasswordFormVisible = ref(false);
const verifyCodeFormVisible = ref(false);
const resetEmail = ref<string | null>(null);
const resetCurrentPassword = ref<string | null>(null);
const resetNewPassword = ref<string | null>(null);
const resetNewPasswordConfirmation = ref<string | null>(null);
const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const addToCartService = useAxiosUtil<CartFormInterface, null>();
const products = ref<ProductInterface[]>([]);
const currentSlide = ref(0);
let carouselInterval: number | null = null;
const activeView = ref<'categories' | 'products'>('categories');
const footerModalVisible = ref(false);
const footerModalType = ref('about');
const router = useRouter();

const footerModalTitle = computed(() => {
    const titles: Record<string, string> = { 'about': 'About Us', 'contact': 'Contact Us', 'faqs': 'FAQs', 'shipping': 'Shipping Information', 'returns': 'Returns & Warranty', 'privacy': 'Privacy Policy', 'terms': 'Terms & Conditions' };
    return titles[footerModalType.value] || 'Information';
});

const categoriesHeading = computed(() => CategoryStore.categories?.some((cat: any) => cat.is_popular) ? 'Popular Categories' : 'Categories');
const productsHeading = computed(() => products.value?.some((prod: any) => prod.is_best_selling) ? 'Best Sellers' : 'Products');
const showCarousel = computed(() => products.value?.some((prod: any) => prod.is_best_selling));
const isProductPage = computed(() => ['customer.product-search', 'customer.product-info.index', 'customer.product-category', 'customer.browse-products', 'customer.home.index', 'customer.cart', 'home'].includes(router.currentRoute.value.name as string));

const form: SearchProductInterface = reactive({ search: null });
const errors: SearchErrorInterface = reactive({ search: [] });

const openFooterModal = (type: string) => { footerModalType.value = type; footerModalVisible.value = true; };
const goToBrowseProducts = () => router.push({ name: "customer.browse-products" });
const openLoginForm = () => { registerFormVisible.value = false; resetPasswordFormVisible.value = false; loginFormVisible.value = true; };
const openRegisterForm = () => { loginFormVisible.value = false; resetPasswordFormVisible.value = false; registerFormVisible.value = true; };
const openResetPasswordForm = () => { loginFormVisible.value = false; registerFormVisible.value = false; resetPasswordFormVisible.value = true; };

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

const handleCodeSent = (data: { email: string; current_password: string; new_password: string; new_password_confirmation: string }) => {
    resetEmail.value = data.email; resetCurrentPassword.value = data.current_password; resetNewPassword.value = data.new_password; resetNewPasswordConfirmation.value = data.new_password_confirmation;
    resetPasswordFormVisible.value = false; verifyCodeFormVisible.value = true;
};

const handleResetSuccess = () => { verifyCodeFormVisible.value = false; resetPasswordFormVisible.value = false; loginFormVisible.value = true; };

const loadBestSellingProducts = async () => {
    await loadBestSellingService.get("best-selling").then(() => {
        if (loadBestSellingService.request.status === 200 && loadBestSellingService.request.data) {
            products.value = loadBestSellingService.request.data;
            startCarousel();
        }
    });
};

const startCarousel = () => {
    const count = Math.min(products.value.length, 3);
    if (count > 1) carouselInterval = window.setInterval(() => { currentSlide.value = (currentSlide.value + 1) % count; }, 5000);
};

const stopCarousel = () => { if (carouselInterval) { clearInterval(carouselInterval); carouselInterval = null; } };
const goRoute = (route: string, params: Record<string, string>) => router.push({ name: route, params });
const handleImageError = (event: Event) => { (event.target as HTMLImageElement).style.display = 'none'; };

const addToCart = async (productId: number) => {
    if (!Page.user) { openLoginForm(); return; }
    await addToCartService.post('customer/carts', { product_id: productId, quantity: 1 });
};

const goToProductDetails = (productId: number) => router.push({ name: 'customer.product-info.index', params: { id: productId } });

onMounted(() => { CategoryStore.fetchCategories(); loadBestSellingProducts(); });
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
.nav-mobile-search { display: block; padding: 0 1rem 0.625rem; background: #2563eb; }
.nav-mobile-search .nav-search-box input { border-color: rgba(255,255,255,0.3); }

/* Hero */
.hero { position: relative; background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); min-height: 500px; overflow: hidden; }
.hero-bg { position: absolute; inset: 0; background: radial-gradient(ellipse at top right, rgba(59,130,246,0.15) 0%, transparent 50%); }
.hero-content { position: relative; z-index: 10; max-width: 1280px; margin: 0 auto; padding: 3rem 1rem; }
.hero-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; align-items: center; }
.hero-text { text-align: center; position: relative; z-index: 2; }
.hero-badge { display: inline-block; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #94a3b8; font-size: 0.75rem; font-weight: 500; padding: 0.375rem 0.75rem; border-radius: 9999px; margin-bottom: 1rem; }
.hero-product-name { font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.2; margin-bottom: 0.5rem; }
.hero-category { color: #94a3b8; font-size: 0.875rem; margin-bottom: 1rem; }
.hero-price { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem; }
.hero-buttons { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
.hero-image-container { display: flex; justify-content: center; position: relative; }
.hero-image-wrapper { position: relative; z-index: 2; }
.hero-image { max-height: 280px; width: auto; object-fit: contain; filter: drop-shadow(0 25px 50px rgba(0,0,0,0.3)); }
.hero-image-placeholder { width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 1rem; display: flex; align-items: center; justify-content: center; color: #475569; font-size: 3rem; }
.branch-banner { background: rgba(255,255,255,0.95); color: #1e40af; text-align: center; padding: 0.625rem 1rem; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.5rem; position: relative; z-index: 20; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.branch-banner i { font-size: 1rem; color: #dc2626; }
.hero-dots { display: flex; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; }
.dot { width: 8px; height: 8px; border-radius: 9999px; background: rgba(255,255,255,0.3); border: none; cursor: pointer; transition: all 0.3s; }
.dot.active { width: 24px; background: #fff; }
.hero-static { text-align: center; padding: 2rem 0; }
.hero-headline { font-size: 2rem; font-weight: 700; color: #fff; margin-bottom: 1rem; }
.text-accent { color: #60a5fa; }
.hero-subtext { color: #94a3b8; font-size: 1rem; margin-bottom: 1.5rem; max-width: 500px; margin-left: auto; margin-right: auto; }

/* Buttons */
.btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; background: #2563eb; color: #fff; font-weight: 600; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem; transition: background 0.2s; }
.btn-primary:hover { background: #1d4ed8; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-secondary { display: inline-flex; align-items: center; gap: 0.5rem; background: transparent; color: #fff; font-weight: 600; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.3); cursor: pointer; font-size: 0.875rem; transition: background 0.2s; }
.btn-secondary:hover { background: rgba(255,255,255,0.1); }
.btn-lg { padding: 0.75rem 1.5rem; font-size: 1rem; }
.btn-outline { display: inline-flex; align-items: center; gap: 0.5rem; background: transparent; color: #fff; font-weight: 500; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.3); text-decoration: none; font-size: 0.875rem; transition: background 0.2s; }
.btn-outline:hover { background: rgba(255,255,255,0.1); }
.btn-view { display: block; background: #1e293b; color: #fff; text-align: center; padding: 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; text-decoration: none; transition: background 0.2s; }
.btn-view:hover { background: #334155; }

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
.category-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
.category-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; text-align: center; cursor: pointer; transition: all 0.2s; }
.category-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.category-image-box { width: 48px; height: 48px; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 0.5rem; }
.category-image-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
.category-image-box i { font-size: 1.25rem; color: #cbd5e1; }
.category-name { font-size: 0.75rem; font-weight: 600; color: #334155; display: block; }

/* Products */
.product-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
.product-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem; overflow: hidden; transition: all 0.2s; }
.product-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.product-image-box { position: relative; height: 120px; background: #f8fafc; display: flex; align-items: center; justify-content: center; }
.product-image-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
.product-image-box i { font-size: 2rem; color: #cbd5e1; }
.product-info { padding: 0.75rem; }
.product-name { font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.25rem; }

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
.cta-section { background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); padding: 3rem 1rem; text-align: center; }
.cta-section h2 { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem; }
.cta-section > .container > p { color: #94a3b8; margin-bottom: 1.5rem; font-size: 0.875rem; }
.cta-buttons { display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 2rem; }
.cta-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem; }
.stat-number { display: block; font-size: 1.5rem; font-weight: 700; color: #fff; }
.stat-label { font-size: 0.625rem; color: #64748b; }

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
    .hero-buttons { justify-content: flex-start; }
    .hero-product-name { font-size: 2.5rem; }
    .hero-price { font-size: 2rem; }
    .hero-image { max-height: 350px; }
    .branch-banner { font-size: 1rem; padding: 0.75rem 1.5rem; }
    .trust-grid { grid-template-columns: repeat(4, 1fr); }
    .category-grid { grid-template-columns: repeat(3, 1fr); }
    .product-grid { grid-template-columns: repeat(3, 1fr); }
    .features-grid { grid-template-columns: repeat(4, 1fr); }
    .testimonials-grid { grid-template-columns: repeat(3, 1fr); }
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
    .hero-image { max-height: 400px; }
    .branch-banner { font-size: 1.125rem; }
    .category-grid { grid-template-columns: repeat(6, 1fr); }
    .product-grid { grid-template-columns: repeat(5, 1fr); }
    .footer-grid { grid-template-columns: repeat(4, 1fr); }
    .cta-section { padding: 4rem 2rem; }
    .cta-section h2 { font-size: 2rem; }
}
</style>