<template>
    <div class="customer-dashboard bg-gray-50/50 min-h-screen pb-12">
        <!-- Hero Section: Personalized Welcome -->
        <div class="relative h-64 md:h-80 bg-slate-900 overflow-hidden mb-8">
            <!-- Background Image (Cover Image) -->
            <img 
                v-if="Page.user?.cover_image" 
                :src="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.cover_image}`)" 
                class="w-full h-full object-cover opacity-60"
            >
            <div v-else class="w-full h-full bg-gradient-to-r from-blue-700 to-indigo-800 opacity-80"></div>
            
            <!-- Hero Overlay Content -->
            <div class="absolute inset-0 flex items-center">
                <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-white text-center md:text-left">
                        <h1 class="text-3xl md:text-5xl font-black mb-2 tracking-tight">Hello, {{ Page.user?.full_name?.split(' ')[0] }}!</h1>
                        <p class="text-blue-100 text-lg font-medium">Ready to find something amazing today?</p>
                        
                        <!-- Quick Stats as Activity Chips -->
                        <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-6">
                            <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span class="text-sm font-bold">{{ stats.completed_orders || 0 }} Delivered</span>
                            </div>
                            <div v-if="stats.pending_orders > 0" class="bg-blue-600/80 px-4 py-2 rounded-full border border-blue-400/30 flex items-center gap-2 animate-pulse">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                <span class="text-sm font-bold text-white">{{ stats.pending_orders }} Arriving Soon</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Action Floating Panel -->
                    <div class="hidden lg:flex gap-4 p-2 bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl">
                        <button @click="goToOrders" class="hero-action-btn">
                            <i class="pi pi-box"></i>
                            <span>Orders</span>
                        </button>
                        <button @click="goToCart" class="hero-action-btn">
                            <i class="pi pi-shopping-cart"></i>
                            <span>Cart</span>
                        </button>
                        <button @click="goToProfile" class="hero-action-btn">
                            <i class="pi pi-user"></i>
                            <span>Profile</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-6">
            <!-- Categories Row -->
            <div class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Explore by Category</h2>
                    <button @click="goToProducts" class="text-sm font-bold text-blue-600 hover:text-blue-700">View All</button>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                    <div 
                        v-for="cat in categories" 
                        :key="cat.category_id" 
                        @click="viewCategory(cat.category_id)"
                        class="category-card group"
                    >
                        <div class="w-full aspect-square rounded-3xl bg-white shadow-sm border border-gray-100 flex items-center justify-center p-4 transition-all duration-300 group-hover:scale-105 group-hover:shadow-md group-hover:border-blue-100">
                            <img v-if="cat.image" :src="UrlUtil.getBaseAppUrl(`storage/images/category/${cat.image}`)" class="max-w-full max-h-full object-contain">
                            <i v-else class="pi pi-tags text-2xl text-gray-200"></i>
                        </div>
                        <span class="mt-3 text-sm font-bold text-gray-600 group-hover:text-blue-600 transition-colors uppercase tracking-wider text-center block">{{ cat.category_name }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Main Grid: Featured Selection -->
                <div class="lg:col-span-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 leading-none">Featured for You</h2>
                            <p class="text-gray-500 text-sm mt-2 font-medium">Curated products based on your shopping style.</p>
                        </div>
                        <button @click="goToProducts" class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-gray-400">
                            <i class="pi pi-sliders-h"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div 
                            v-for="product in featuredProducts" 
                            :key="product.product_id"
                            class="product-luxury-card group"
                            @click="viewProduct(product.product_id)"
                        >
                            <div class="relative aspect-[4/3] bg-white rounded-3xl overflow-hidden mb-4 border border-gray-100 shadow-sm transition-all group-hover:shadow-xl group-hover:scale-[1.02]">
                                <img v-if="product.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" class="w-full h-full object-contain p-6">
                                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-full shadow-sm text-xs font-black text-blue-600 uppercase">
                                    {{ product.category?.category_name }}
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 line-clamp-1 mb-1">{{ product.product_name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-black text-blue-600">{{ CurrencyUtil.formatCurrency(product.product_price) }}</span>
                                <div class="flex items-center gap-1.5">
                                    <i class="pi pi-star-fill text-amber-400 text-xs"></i>
                                    <span class="text-sm font-bold text-gray-500">{{ Number(product.rates_avg_rate || 0).toFixed(1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Journey & Insights -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Shopping Journey Timeline -->
                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-xl font-bold text-gray-900">Recent History</h2>
                            <button @click="goToOrders" class="text-blue-600 hover:underline text-sm font-bold">Registry</button>
                        </div>

                        <div v-if="recentOrders.length > 0" class="space-y-8 relative before:absolute before:inset-0 before:left-[19px] before:w-px before:bg-gray-100 before:z-0">
                            <div v-for="order in recentOrders" :key="order.order_id" class="relative z-10 flex gap-6 group cursor-pointer" @click="viewOrder(order.order_id)">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110" :class="getStatusTimelineClass(order.status)">
                                    <i :class="getStatusIcon(order.status)" class="text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ CurrencyUtil.formatCurrency(order.total_amount) }}</p>
                                    <h4 class="text-sm font-bold text-gray-900 truncate">#{{ order.order_public_id }} - {{ order.product_orders?.[0]?.product?.product_name || 'Alcess Product' }}</h4>
                                    <p class="text-[10px] font-bold mt-1 uppercase tracking-tighter" :class="getStatusTextColor(order.status)">{{ order.status }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-10">
                            <i class="pi pi-history text-3xl text-gray-200 mb-4 block"></i>
                            <p class="text-gray-400 text-sm font-medium">Your journey starts here.</p>
                        </div>
                    </div>

                    <!-- Spending Insight (Simplified) -->
                    <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl shadow-blue-100">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-lg font-bold">Shopping Insight</h2>
                            <i class="pi pi-chart-line text-blue-400"></i>
                        </div>
                        <div class="h-48 w-full -mx-4">
                            <VueApexCharts
                                type="area"
                                height="100%"
                                :options="compactChartOptions"
                                :series="chartSeries"
                                v-if="chartSeries[0].data.length > 0"
                            />
                        </div>
                        <div class="pt-6 mt-6 border-t border-white/10 flex justify-between items-center">
                            <span class="text-sm text-slate-400 font-medium">Lifetime Spend</span>
                            <span class="text-lg font-black text-blue-400">{{ CurrencyUtil.formatCurrency(stats.total_spend || 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Support -->
        <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="fixed bottom-8 right-8 bg-blue-600 text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-3 hover:scale-105 active:scale-95 transition-all z-50">
            <i class="pi pi-comments text-xl"></i>
            <span class="font-bold uppercase tracking-widest text-xs">Live Support</span>
        </a>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import Page from '@/stores/Page'
import useAxiosUtil from '@/utils/AxiosUtil'
import CurrencyUtil from '@/utils/CurrencyUtil'
import UrlUtil from '@/utils/UrlUtil'
import VueApexCharts from 'vue3-apexcharts'

const router = useRouter()
const dashboardService = useAxiosUtil()

// Data state
const stats = ref<any>({})
const recentOrders = ref<any[]>([])
const featuredProducts = ref<any[]>([])
const categories = ref<any[]>([])
const chartSeries = ref<any[]>([{ name: 'Spend', data: [] }])

// Chart Config for the compact insight
const compactChartOptions = computed(() => ({
    chart: { type: 'area', toolbar: { show: false }, sparkline: { enabled: true } },
    colors: ['#3B82F6'],
    stroke: { curve: 'smooth', width: 4 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0 } },
    tooltip: { theme: 'dark', x: { show: false }, y: { formatter: (val: number) => CurrencyUtil.formatCurrency(val) } }
}))

// Fetch Data
const fetchDashboardData = async () => {
    await dashboardService.get('customer/dashboard').then(() => {
        if (dashboardService.request.status === 200 && dashboardService.request.data) {
            const data = dashboardService.request.data
            stats.value = data.stats
            recentOrders.value = data.recent_orders
            featuredProducts.value = data.featured_products
            categories.value = data.categories
            chartSeries.value = [{ name: 'Spent', data: data.monthly_spend.data }]
        }
    })
}

// Helpers
const getStatusTimelineClass = (status: string) => {
    switch (status) {
        case 'Completed': return 'bg-emerald-100 text-emerald-600 shadow-sm shadow-emerald-100'
        case 'Processing': return 'bg-blue-100 text-blue-600 shadow-sm shadow-blue-100 animate-pulse'
        case 'Shipped': return 'bg-purple-100 text-purple-600 shadow-sm shadow-purple-100'
        case 'Cancelled': return 'bg-red-100 text-red-600 shadow-sm shadow-red-100'
        default: return 'bg-gray-100 text-gray-400'
    }
}

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'Completed': return 'pi pi-check'
        case 'Processing': return 'pi pi-spin pi-cog'
        case 'Shipped': return 'pi pi-truck'
        case 'Cancelled': return 'pi pi-times'
        default: return 'pi pi-circle'
    }
}

const getStatusTextColor = (status: string) => {
    switch (status) {
        case 'Completed': return 'text-emerald-500'
        case 'Processing': return 'text-blue-500'
        case 'Shipped': return 'text-purple-500'
        case 'Cancelled': return 'text-red-500'
        default: return 'text-gray-400'
    }
}

// Navigation
const goToOrders = () => router.push({ name: 'customer.order.index' })
const goToCart = () => router.push({ name: 'customer.cart.index' })
const goToProfile = () => router.push({ name: 'customer.profile' })
const goToProducts = () => router.push({ name: 'customer.browse-products' })
const viewCategory = (id: number) => router.push({ name: 'customer.browse-products', query: { category: id } })
const viewProduct = (id: number) => router.push({ name: 'customer.product-info.index', params: { id } })
const viewOrder = (id: number) => router.push({ name: 'customer.order.index', hash: `#order-${id}` })

onMounted(() => {
    fetchDashboardData()
})
</script>

<style scoped>
.customer-dashboard { background-color: #f8fafc; }
.hero-action-btn {
    display: flex; flex-direction: column; items-center; justify-center; gap: 0.5rem;
    padding: 1.5rem; width: 6rem; background-color: rgba(255, 255, 255, 0.05);
    border-radius: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.1);
    color: white; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.hero-action-btn:hover { background-color: white; color: #1e293b; border-color: white; transform: translateY(-4px); }
.hero-action-btn i { font-size: 1.25rem; }
.hero-action-btn span { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }

.category-card { cursor: pointer; text-align: center; }
.product-luxury-card { cursor: pointer; }

@media (max-width: 768px) {
    .container { padding-left: 1.25rem; padding-right: 1.25rem; }
}
</style>
