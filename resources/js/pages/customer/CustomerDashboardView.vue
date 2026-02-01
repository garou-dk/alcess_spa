<template>
    <div class="customer-dashboard">
        <!-- Dashboard Hero: Mimicking Landing Page -->
        <section class="welcome-hero">
            <div class="welcome-overlay"></div>
            <!-- Dynamic User Cover Image -->
            <img 
                v-if="Page.user?.cover_image" 
                :src="UrlUtil.getBaseAppUrl(`storage/images/cover/${Page.user.cover_image}`)" 
                class="absolute inset-0 w-full h-full object-cover opacity-30 z-0"
            >
            <div class="welcome-watermark-container">
                <div class="moving-watermark">
                    <div class="watermark-row" v-for="n in 8" :key="n">
                        <span v-for="m in 10" :key="m" class="watermark-item">
                            <img :src="Logo" alt="logo" class="watermark-logo" />
                            ALCESS CUSTOMER &nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="container welcome-container relative z-10">
                <div class="welcome-content">
                    <div class="welcome-badge-wrapper">
                        <span class="welcome-badge">Account Hub</span>
                    </div>
                    <h1 class="welcome-headline">Welcome Back, <span class="text-accent">{{ Page.user?.full_name?.split(' ')[0] }}!</span></h1>
                    <p class="welcome-slogan">Everything you need to manage your tech lifestyle.</p>
                    
                    <!-- Quick Stats Activity Hub -->
                    <div class="flex flex-wrap justify-center gap-4 mt-8">
                        <div class="stat-chip">
                            <i class="pi pi-shopping-bag"></i>
                            <span>{{ stats.total_orders || 0 }} Total Orders</span>
                        </div>
                        <div class="stat-chip active" v-if="stats.pending_orders > 0">
                            <i class="pi pi-clock"></i>
                            <span>{{ stats.pending_orders }} Processing</span>
                        </div>
                        <div class="stat-chip highlight">
                            <i class="pi pi-verified"></i>
                            <span>Verified Member</span>
                        </div>
                    </div>

                    <div class="welcome-actions mt-10">
                        <button @click="goToProducts" class="btn-primary btn-lg shine-effect">
                            Continue Shopping <i class="pi pi-shopping-cart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions: Mimicking Landing Page Layout -->
        <section class="quick-actions-section py-16 bg-white">
            <div class="container">
                <div class="section-header mb-12 text-center">
                    <p class="section-label">Quick Control</p>
                    <h2 class="section-title">Your Management Suite</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div @click="goToOrders" class="action-card group">
                        <div class="action-icon bg-blue-50 text-blue-600">
                            <i class="pi pi-truck"></i>
                        </div>
                        <h3>My Orders</h3>
                        <p>Track your deliveries and view history.</p>
                        <i class="pi pi-arrow-right action-arrow"></i>
                    </div>

                    <div @click="goToProfile" class="action-card group">
                        <div class="action-icon bg-purple-50 text-purple-600">
                            <i class="pi pi-user"></i>
                        </div>
                        <h3>Identity</h3>
                        <p>Manage your profile and security.</p>
                        <i class="pi pi-arrow-right action-arrow"></i>
                    </div>

                    <div @click="goToInvoices" class="action-card group">
                        <div class="action-icon bg-emerald-50 text-emerald-600">
                            <i class="pi pi-file-pdf"></i>
                        </div>
                        <h3>Invoices</h3>
                        <p>Download billing and official logs.</p>
                        <i class="pi pi-arrow-right action-arrow"></i>
                    </div>

                    <div @click="goToAddress" class="action-card group">
                        <div class="action-icon bg-amber-50 text-amber-600">
                            <i class="pi pi-map-marker"></i>
                        </div>
                        <h3>Addresses</h3>
                        <p>Update your shipping destinations.</p>
                        <i class="pi pi-arrow-right action-arrow"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured & History: Hybrid Design -->
        <section class="py-16 bg-gray-50">
            <div class="container">
                <div class="lg:grid lg:grid-cols-12 lg:gap-12">
                    
                    <!-- Featured Section -->
                    <div class="lg:col-span-7 mb-12 lg:mb-0">
                        <div class="section-header text-left mb-8">
                            <p class="section-label">Recommendations</p>
                            <h2 class="section-title">Picked for You</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div 
                                v-for="product in featuredProducts" 
                                :key="product.product_id"
                                class="product-item-card group"
                                @click="viewProduct(product.product_id)"
                            >
                                <div class="product-img-box">
                                    <img v-if="product.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" class="group-hover:scale-110 transition-transform duration-500">
                                    <i v-else class="pi pi-image text-gray-200 text-3xl"></i>
                                </div>
                                <div class="p-5">
                                    <h4 class="font-bold text-gray-900 line-clamp-1">{{ product.product_name }}</h4>
                                    <div class="flex items-center justify-between mt-3">
                                        <span class="text-blue-600 font-black">{{ CurrencyUtil.formatCurrency(product.product_price) }}</span>
                                        <div class="flex items-center gap-1 text-[10px] text-gray-400 font-bold uppercase">
                                            <i class="pi pi-tag"></i>
                                            {{ product.category?.category_name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History Timeline -->
                    <div class="lg:col-span-5">
                        <div class="section-header text-left mb-8">
                            <p class="section-label">Activity</p>
                            <h2 class="section-title">Order Timeline</h2>
                        </div>

                        <div class="timeline-container bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
                            <div v-if="recentOrders.length > 0" class="space-y-8 relative">
                                <div v-for="(order, index) in recentOrders" :key="order.order_id" class="timeline-item flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="timeline-dot" :class="getStatusTimelineClass(order.status)">
                                            <i :class="getStatusIcon(order.status)"></i>
                                        </div>
                                        <div v-if="index !== recentOrders.length - 1" class="timeline-line"></div>
                                    </div>
                                    <div class="flex-1 pb-2">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ new Date(order.created_at).toLocaleDateString() }}</span>
                                        <h4 class="text-sm font-bold text-gray-900 mt-1">Order #{{ order.order_public_id }}</h4>
                                        <p class="text-[11px] text-gray-500 line-clamp-1 italic">{{ order.product_orders?.[0]?.product?.product_name || 'Electronics' }}</p>
                                        <div class="mt-2" :class="getStatusTextColor(order.status)">
                                            <span class="text-[9px] font-black uppercase bg-gray-50 px-2 py-1 rounded-md">{{ order.status }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-12">
                                <i class="pi pi-history text-4xl text-gray-100 mb-4 block"></i>
                                <p class="text-gray-400 text-sm font-medium">No recent orders found.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Spending insight (Floating Minimalist) -->
        <div class="container py-16">
            <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 blur-[100px] rounded-full"></div>
                <div class="flex flex-col md:flex-row justify-between items-center gap-10">
                    <div class="md:w-1/3">
                        <h2 class="text-2xl font-bold mb-4">Investment Tracking</h2>
                        <p class="text-slate-400 text-sm leading-relaxed mb-6">Total investment in your tech suite across the last 6 months.</p>
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Lifetime Managed</span>
                            <p class="text-2xl font-black text-blue-400 mt-1">{{ CurrencyUtil.formatCurrency(stats.total_spend || 0) }}</p>
                        </div>
                    </div>
                    <div class="flex-1 w-full h-64">
                         <VueApexCharts
                            type="area"
                            height="100%"
                            :options="chartOptions"
                            :series="chartSeries"
                            v-if="chartSeries[0].data.length > 0"
                        />
                    </div>
                </div>
            </div>
        </div>

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
import Logo from "@/../img/logo.png";

const router = useRouter()
const dashboardService = useAxiosUtil()

// Data state
const stats = ref<any>({})
const recentOrders = ref<any[]>([])
const featuredProducts = ref<any[]>([])
const categories = ref<any[]>([])
const chartSeries = ref<any[]>([{ name: 'Spend', data: [] }])

const chartOptions = computed(() => ({
    chart: { type: 'area', toolbar: { show: false }, sparkline: { enabled: false } },
    colors: ['#3B82F6'],
    stroke: { curve: 'smooth', width: 4 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0 } },
    grid: { show: false },
    xaxis: { labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { show: false } },
    tooltip: { theme: 'dark', y: { formatter: (val: number) => CurrencyUtil.formatCurrency(val) } }
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
        case 'Completed': return 'bg-emerald-500 text-white'
        case 'Processing': return 'bg-blue-500 text-white animate-pulse'
        case 'Shipped': return 'bg-purple-500 text-white'
        case 'Cancelled': return 'bg-red-500 text-white'
        default: return 'bg-gray-200 text-gray-500'
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
const goToInvoices = () => router.push({ name: 'customer-invoice' })
const goToProfile = () => router.push({ name: 'customer.profile' })
const goToAddress = () => { window.dispatchEvent(new CustomEvent('open-address-form')) }
const goToProducts = () => router.push({ name: 'customer.browse-products' })
const viewProduct = (id: number) => router.push({ name: 'customer.product-info.index', params: { id } })
const viewOrder = (id: number) => router.push({ name: 'customer.order.index', hash: `#order-${id}` })

onMounted(() => { fetchDashboardData() })
</script>

<style scoped>
/* Mimicking HomeView Style */
.customer-dashboard { background-color: #f8fafc; overflow-x: hidden; }

/* Hero Wrapper (Mimicking welcome-hero) */
.welcome-hero { position: relative; background: #0f172a; min-height: 500px; display: flex; align-items: center; overflow: hidden; padding: 4rem 0; }
.welcome-overlay { position: absolute; inset: 0; background: radial-gradient(circle at 70% 30%, rgba(37, 99, 235, 0.15) 0%, transparent 70%), linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
.welcome-watermark-container { position: absolute; inset: 0; overflow: hidden; opacity: 0.03; pointer-events: none; z-index: 5; }
.moving-watermark { transform: rotate(-25deg) scale(1.5); width: 250%; height: 250%; position: absolute; top: -50%; left: -75%; display: flex; flex-direction: column; gap: 4rem; animation: moveWatermark 60s linear infinite; }
.watermark-row { white-space: nowrap; font-size: 1.5rem; font-weight: 900; color: #fff; letter-spacing: 0.2rem; display: flex; align-items: center; }
.watermark-item { display: inline-flex; align-items: center; gap: 1rem; }
.watermark-logo { width: 32px; height: 32px; filter: grayscale(1) brightness(2); }

@keyframes moveWatermark { 0% { transform: rotate(-25deg) translate(0, 0) scale(1.5); } 100% { transform: rotate(-25deg) translate(-10%, -10%) scale(1.5); } }

.welcome-content { max-width: 800px; margin: 0 auto; text-align: center; }
.welcome-badge { background: rgba(37, 99, 235, 0.1); border: 1px solid rgba(37, 99, 235, 0.3); color: #60a5fa; font-size: 0.875rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2rem; display: inline-block; }
.welcome-headline { font-size: clamp(2.5rem, 6vw, 3.5rem); font-weight: 800; color: #fff; line-height: 1.1; margin-bottom: 1rem; }
.text-accent { color: #60a5fa; }
.welcome-slogan { font-size: 1.125rem; font-weight: 500; color: #94a3b8; letter-spacing: 0.025em; }

/* Stat Chips */
.stat-chip { display: flex; align-items: center; gap: 0.75rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.75rem 1.25rem; rounded-full; color: white; rounded-radius: 9999px; font-weight: 700; font-size: 0.8125rem; border-radius: 9999px; }
.stat-chip i { color: #60a5fa; }
.stat-chip.active { background: #2563eb; border-color: #3b82f6; box-shadow: 0 0 20px rgba(37, 99, 235, 0.3); }
.stat-chip.active i { color: white; }
.stat-chip.highlight { border-color: rgba(16, 185, 129, 0.3); background: rgba(16, 185, 129, 0.05); }

/* Buttons */
.btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; background: #2563eb; color: #fff; font-weight: 700; padding: 1rem 2rem; border-radius: 1rem; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.875rem; transition: background 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.btn-primary:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4); }
.shine-effect { position: relative; overflow: hidden; }
.shine-effect::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: linear-gradient(45deg, transparent 45%, rgba(255,255,255,0.2) 50%, transparent 55%); transform: rotate(45deg); animation: shine 4s infinite; }
@keyframes shine { 0% { left: -100%; } 100% { left: 100%; } }

/* Quick Action Cards */
.action-card { background: #fff; padding: 2.5rem; border-radius: 2rem; border: 1px solid #f1f5f9; cursor: pointer; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
.action-card:hover { transform: translateY(-8px); border-color: #2563eb; box-shadow: 0 20px 40px rgba(37, 99, 235, 0.08); }
.action-icon { width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.5rem; transition: transform 0.4s; }
.action-card:hover .action-icon { transform: scale(1.1) rotate(5deg); }
.action-card h3 { font-size: 1.125rem; font-weight: 800; color: #111827; margin-bottom: 0.5rem; }
.action-card p { font-size: 0.875rem; color: #6b7280; font-weight: 500; line-height: 1.5; padding-right: 1.5rem; }
.action-arrow { position: absolute; bottom: 2rem; right: 2rem; color: #2563eb; opacity: 0; transform: translateX(-10px); transition: all 0.3s; font-size: 0.75rem; }
.action-card:hover .action-arrow { opacity: 1; transform: translateX(0); }

/* Product Item Cards */
.product-item-card { background: #fff; border-radius: 1.5rem; overflow: hidden; border: 1px solid #f1f5f9; cursor: pointer; transition: all 0.3s; }
.product-item-card:hover { transform: translateY(-5px); border-color: #2563eb; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
.product-img-box { height: 180px; background: #fafafa; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
.product-img-box img { max-width: 100%; max-height: 100%; object-fit: contain; }

/* Timeline Stylings */
.timeline-item { position: relative; }
.timeline-dot { width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative; z-index: 2; font-size: 0.875rem; }
.timeline-line { position: absolute; top: 2.5rem; left: 1.25rem; bottom: -1rem; width: 1px; background-color: #f1f5f9; z-index: 1; }
.timeline-container { min-height: 400px; }

/* Label Styling */
.section-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #2563eb; letter-spacing: 0.1em; margin-bottom: 0.5rem; }
.section-title { font-size: 1.75rem; font-weight: 900; color: #0f172a; }

@media (max-width: 640px) {
    .container { padding: 0 1.5rem; }
}
</style>
