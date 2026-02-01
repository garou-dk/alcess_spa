<template>
    <div class="customer-dashboard">
        <!-- Hero Section -->
        <div class="dashboard-hero bg-slate-900 relative overflow-hidden mb-8">
            <div class="absolute inset-0 opacity-20">
                <div class="hero-pattern"></div>
            </div>
            <div class="container mx-auto px-4 py-12 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            {{ greeting }}, {{ Page.user?.full_name?.split(' ')[0] }}!
                        </h1>
                        <p class="text-slate-400 text-lg">What are you looking for today?</p>
                        
                        <!-- Mini Search -->
                        <div class="mt-6 max-w-md relative">
                            <input 
                                v-model="searchQuery" 
                                @keyup.enter="handleSearch"
                                type="text" 
                                placeholder="Search products, orders, or help..." 
                                class="w-full bg-white/10 border border-white/20 rounded-xl px-5 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all blur-none"
                            />
                            <button @click="handleSearch" class="absolute right-3 top-2.5 bg-blue-600 p-1.5 rounded-lg text-white hover:bg-blue-700 transition-colors">
                                <i class="pi pi-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Top Interests / Categories -->
                    <div v-if="topCategories.length > 0" class="flex flex-wrap gap-3">
                        <div v-for="cat in topCategories" :key="cat.category_id" class="px-4 py-2 bg-white/5 rounded-full border border-white/10 backdrop-blur-md text-white/80 text-sm font-medium hover:bg-white/10 transition-colors cursor-pointer" @click="goToCategory(cat.category_id)">
                            {{ cat.category_name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 pb-12">
            <!-- Active Notifications / Alerts -->
            <div v-if="cartSummary.count > 0" class="mb-8 animate__animated animate__fadeIn">
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-600 text-white w-12 h-12 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                            <i class="pi pi-shopping-cart text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-blue-900">You have items in your cart</h4>
                            <p class="text-blue-700 text-sm">Review your {{ cartSummary.count }} items and complete your purchase.</p>
                        </div>
                    </div>
                    <button @click="goToCart" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-md">
                        Check Out
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div v-for="(stat, index) in statCards" :key="index" 
                    class="stat-card animate__animated animate__fadeInUp p-6 rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 bg-white"
                    :style="{ animationDelay: `${index * 100}ms` }">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">{{ stat.label }}</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ stat.value }}</h3>
                            <p v-if="stat.subtext" class="text-[10px] text-gray-400 mt-1 font-medium">{{ stat.subtext }}</p>
                        </div>
                        <div :class="['p-3 rounded-xl flex items-center justify-center', stat.bg]">
                            <i :class="['pi text-xl', stat.icon, stat.color]"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Spending & Orders -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- Chart Section -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 italic">Spending Analytics</h2>
                                <p class="text-sm text-gray-500">How your spending is distributed</p>
                            </div>
                            <div class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">LIVE</div>
                        </div>
                        <div class="h-80 w-full">
                            <VueApexCharts
                                type="area"
                                height="100%"
                                :options="chartOptions"
                                :series="chartSeries"
                                v-if="chartSeries[0].data.length > 0"
                            />
                            <div v-else class="h-full flex flex-col items-center justify-center text-gray-400">
                                <i class="pi pi-chart-line text-4xl mb-2 opacity-20"></i>
                                <p>Start shopping to see your spend trends</p>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-xl font-bold text-gray-900 italic">Order Registry</h2>
                            <button @click="goToOrders" class="text-blue-600 hover:text-blue-700 font-bold text-sm bg-blue-50 px-4 py-2 rounded-xl transition-colors">
                                View History
                            </button>
                        </div>
                        
                        <div v-if="recentOrders.length > 0" class="overflow-x-auto -mx-6">
                            <table class="w-full text-left min-w-[600px]">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="py-4 px-6 text-[10px] font-black uppercase text-gray-400 tracking-tighter">Identity</th>
                                        <th class="py-4 px-2 text-[10px] font-black uppercase text-gray-400 tracking-tighter">Manifest</th>
                                        <th class="py-4 px-2 text-[10px] font-black uppercase text-gray-400 tracking-tighter">Value</th>
                                        <th class="py-4 px-2 text-[10px] font-black uppercase text-gray-400 tracking-tighter">Status</th>
                                        <th class="py-4 px-6 text-[10px] font-black uppercase text-gray-400 tracking-tighter text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in recentOrders" :key="order.order_id" class="group hover:bg-slate-50 transition-all border-b border-gray-50 last:border-0">
                                        <td class="py-5 px-6">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-900">#{{ order.order_public_id }}</span>
                                                <span class="text-[10px] text-gray-400">{{ formatDate(order.created_at) }}</span>
                                            </div>
                                        </td>
                                        <td class="py-5 px-2">
                                            <div class="flex -space-x-2">
                                                <div v-for="(po, i) in order.product_orders.slice(0, 3)" :key="i" class="w-8 h-8 rounded-full bg-white border-2 border-slate-50 overflow-hidden" :title="po.product?.product_name">
                                                    <img v-if="po.product?.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${po.product.product_image}`)" class="w-full h-full object-cover">
                                                    <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 text-[10px]"><i class="pi pi-image text-gray-300"></i></div>
                                                </div>
                                                <div v-if="order.product_orders.length > 3" class="w-8 h-8 rounded-full bg-slate-900 text-white text-[10px] flex items-center justify-center border-2 border-white">+{{ order.product_orders.length - 3 }}</div>
                                            </div>
                                        </td>
                                        <td class="py-5 px-2 font-black text-slate-900">{{ CurrencyUtil.formatCurrency(order.total_amount) }}</td>
                                        <td class="py-5 px-2">
                                            <div :class="['inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest', getStatusClass(order.status)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                {{ order.status }}
                                            </div>
                                        </td>
                                        <td class="py-5 px-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button @click="viewOrder(order.order_id)" class="p-2 bg-slate-100 hover:bg-slate-900 hover:text-white rounded-lg transition-all">
                                                    <i class="pi pi-eye text-sm"></i>
                                                </button>
                                                <button @click="reorder(order)" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-all" title="Buy Again">
                                                    <i class="pi pi-sync text-sm"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right: Quick Actions & Discover -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Dashboard Terminal (Quick Actions) -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden">
                        <div class="absolute -right-12 -top-12 w-32 h-32 bg-slate-900/5 rounded-full"></div>
                        <h2 class="text-xl font-bold text-gray-900 mb-6 italic">Quick Access</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <button v-for="action in quickActions" :key="action.label" @click="action.handler" class="dashboard-action-btn group">
                                <span :class="['icon-box transition-all group-hover:scale-110 group-hover:rotate-6', action.bg]">
                                    <i :class="['pi', action.icon, action.color]"></i>
                                </span>
                                <span class="text-sm font-black text-slate-700 uppercase tracking-tighter">{{ action.label }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Curated For You -->
                    <div class="bg-slate-900 p-6 rounded-3xl shadow-xl shadow-slate-200">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-lg font-bold text-white italic">Curated For You</h2>
                            <i class="pi pi-sparkles text-amber-400"></i>
                        </div>
                        <div class="space-y-6">
                            <div v-for="product in featuredProducts" :key="product.product_id" @click="viewProduct(product.product_id)" class="flex items-center gap-4 group cursor-pointer">
                                <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center p-3 border border-white/10 group-hover:bg-white/10 transition-all font-bold overflow-hidden relative">
                                    <img v-if="product.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" class="w-full h-full object-contain group-hover:scale-110 transition-transform">
                                    <i v-else class="pi pi-image text-white/20 text-2xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors truncate">{{ product.product_name }}</h4>
                                    <div class="text-blue-400 font-black text-base mt-1">{{ CurrencyUtil.formatCurrency(product.product_price) }}</div>
                                    <div class="flex items-center gap-4 mt-2">
                                        <div class="flex items-center gap-1">
                                            <i class="pi pi-star-fill text-[10px] text-amber-400"></i>
                                            <span class="text-[10px] text-white/50">{{ Number(product.rates_avg_rate || 0).toFixed(1) }}</span>
                                        </div>
                                        <span v-if="product.product_quantity <= 5" class="text-[10px] bg-red-500/20 text-red-400 px-2 py-0.5 rounded-md font-bold uppercase tracking-widest animate-pulse">Low Stock</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button @click="goToProducts" class="w-full mt-8 py-4 bg-white/5 border border-white/10 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                            Explore New Arrivals
                        </button>
                    </div>

                    <!-- Support Module -->
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-3xl text-white shadow-lg">
                        <h3 class="text-lg font-bold mb-2">Need Technical Assistance?</h3>
                        <p class="text-blue-100 text-sm mb-6 opacity-80 leading-relaxed">Our experts are available 24/7 to resolve any issues with your orders or account.</p>
                        <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="w-full bg-white text-blue-600 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center justify-center gap-2 shadow-sm">
                            <i class="pi pi-facebook"></i>
                            Open Live Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Page from '@/stores/Page'
import useAxiosUtil from '@/utils/AxiosUtil'
import CurrencyUtil from '@/utils/CurrencyUtil'
import UrlUtil from '@/utils/UrlUtil'
import VueApexCharts from 'vue3-apexcharts'

const router = useRouter()
const dashboardService = useAxiosUtil()

// State
const searchQuery = ref('')
const stats = ref<any>({})
const recentOrders = ref<any[]>([])
const featuredProducts = ref<any[]>([])
const cartSummary = ref<any>({ count: 0, total: 0 })
const topCategories = ref<any[]>([])
const chartSeries = ref<any[]>([{ name: 'Spend', data: [] }])

// Dynamic Greeting
const greeting = computed(() => {
    const hour = new Date().getHours()
    if (hour < 12) return 'Good Morning'
    if (hour < 18) return 'Good Afternoon'
    return 'Good Evening'
})

// Stat Cards Configuration
const statCards = computed(() => [
    { label: 'Purchases', value: stats.value.total_orders || 0, subtext: 'Total orders placed', icon: 'pi-shopping-bag', color: 'text-blue-600', bg: 'bg-blue-50' },
    { label: 'Invested', value: CurrencyUtil.formatCurrency(stats.value.total_spend || 0), subtext: 'Lifetime account value', icon: 'pi-wallet', color: 'text-emerald-600', bg: 'bg-emerald-50' },
    { label: 'Active', value: stats.value.pending_orders || 0, subtext: 'Processing shipments', icon: 'pi-truck', color: 'text-amber-600', bg: 'bg-amber-50' },
    { label: 'Arrived', value: stats.value.completed_orders || 0, subtext: 'Total items delivered', icon: 'pi-check-circle', color: 'text-purple-600', bg: 'bg-purple-50' }
])

// Chart Configuration
const chartOptions = ref<any>({
    chart: { type: 'area', toolbar: { show: false }, zoom: { enabled: false }, sparkline: { enabled: false }, fontFamily: 'Inter, sans-serif' },
    colors: ['#2563eb'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 4, lineCap: 'round' },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
    xaxis: { 
        categories: [], 
        axisBorder: { show: false }, 
        axisTicks: { show: false }, 
        labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } } 
    },
    yaxis: { 
        labels: { 
            formatter: (val: number) => CurrencyUtil.formatCurrency(val), 
            style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } 
        } 
    },
    grid: { borderColor: '#f8fafc', strokeDashArray: 4, padding: { left: 0, right: 0 } },
    tooltip: { theme: 'light', y: { formatter: (val: number) => CurrencyUtil.formatCurrency(val) } }
})

const quickActions = [
    { label: 'Orders', icon: 'pi-box', color: 'text-blue-600', bg: 'bg-blue-50', handler: () => goToOrders() },
    { label: 'Billing', icon: 'pi-file-pdf', color: 'text-emerald-600', bg: 'bg-emerald-50', handler: () => goToInvoices() },
    { label: 'Shield', icon: 'pi-shield', color: 'text-purple-600', bg: 'bg-purple-50', handler: () => goToSecurity() },
    { label: 'Address', icon: 'pi-map-marker', color: 'text-amber-600', bg: 'bg-amber-50', handler: () => goToAddress() }
]

// Methods
const fetchDashboardData = async () => {
    await dashboardService.get('customer/dashboard').then(() => {
        if (dashboardService.request.status === 200 && dashboardService.request.data) {
            const data = dashboardService.request.data
            stats.value = data.stats
            recentOrders.value = data.recent_orders
            featuredProducts.value = data.featured_products
            cartSummary.value = data.cart_summary
            topCategories.value = data.top_categories

            chartSeries.value = [{ name: 'Investment', data: data.monthly_spend.data }]
            chartOptions.value = {
                ...chartOptions.value,
                xaxis: {
                    ...chartOptions.value.xaxis,
                    categories: data.monthly_spend.labels.map((m: string) => {
                        const [year, month] = m.split('-')
                        return new Date(parseInt(year), parseInt(month) - 1).toLocaleDateString(undefined, { month: 'short' })
                    })
                }
            }
        }
    })
}

const getStatusClass = (status: string) => {
    switch (status) {
        case 'Completed': return 'bg-emerald-50 text-emerald-600'
        case 'Processing': return 'bg-blue-50 text-blue-600'
        case 'Shipped': return 'bg-purple-50 text-purple-600'
        case 'Cancelled': return 'bg-red-50 text-red-600'
        default: return 'bg-gray-50 text-gray-500'
    }
}

const formatDate = (date: string) => new Date(date).toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' })
const handleSearch = () => { if (searchQuery.value) router.push({ name: 'customer.search-product', query: { q: searchQuery.value } }) }
const goToOrders = () => router.push({ name: 'customer.order.index' })
const goToInvoices = () => router.push({ name: 'customer-invoice' })
const goToCart = () => router.push({ name: 'customer.cart.index' })
const goToSecurity = () => window.dispatchEvent(new CustomEvent('open-security-form'))
const goToAddress = () => window.dispatchEvent(new CustomEvent('open-address-form'))
const goToProducts = () => router.push({ name: 'customer.browse-products' })
const goToCategory = (id: number) => router.push({ name: 'customer.product-category', params: { id } })
const viewOrder = (id: number) => router.push({ name: 'customer.order.index', hash: `#order-${id}` })
const viewProduct = (id: number) => router.push({ name: 'customer.product-info.index', params: { id } })
const reorder = (order: any) => {
    // Navigate to first product of order or order details
    if (order.product_orders.length > 0) {
        viewProduct(order.product_orders[0].product_id)
    }
}

onMounted(() => fetchDashboardData())
</script>

<style scoped>
.customer-dashboard { background-color: #fbfcfe; min-height: 100vh; }
.dashboard-hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
.hero-pattern { background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px; width: 100%; height: 100%; }

.dashboard-action-btn {
    display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem 1rem;
    background-color: #ffffff; border: 1px solid #f1f5f9; border-radius: 1.5rem; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.dashboard-action-btn:hover { border-color: #3b82f6; background-color: #f8fafc; box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.1); transform: translateY(-4px); }
.icon-box { width: 3.5rem; height: 3.5rem; display: flex; align-items: center; justify-content: center; border-radius: 1.25rem; font-size: 1.5rem; }

/* Custom Scrollbar for the table */
.overflow-x-auto::-webkit-scrollbar { height: 4px; }
.overflow-x-auto::-webkit-scrollbar-track { background: #f1f5f9; }
.overflow-x-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.animate-delay-1 { animation-delay: 100ms; }
.animate-delay-2 { animation-delay: 200ms; }
.animate-delay-3 { animation-delay: 300ms; }
</style>
