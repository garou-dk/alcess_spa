<template>
    <div class="customer-dashboard">
        <!-- Top Summary Cards -->
        <div class="container mx-auto px-4 py-8">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Welcome Back, {{ Page.user?.full_name }}!</h1>
                <p class="text-gray-600 mt-2">Here's what's happening with your account today.</p>
            </header>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card animate__animated animate__fadeInUp bg-gradient-to-br from-blue-500 to-blue-700 text-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Total Orders</p>
                            <h3 class="text-3xl font-bold mt-1">{{ stats.total_orders || 0 }}</h3>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <i class="pi pi-shopping-bag text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card animate__animated animate__fadeInUp animate__delay-1s bg-gradient-to-br from-emerald-500 to-emerald-700 text-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium uppercase tracking-wider">Total Spent</p>
                            <h3 class="text-3xl font-bold mt-1">{{ CurrencyUtil.formatCurrency(stats.total_spend || 0) }}</h3>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <i class="pi pi-wallet text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card animate__animated animate__fadeInUp animate__delay-2s bg-gradient-to-br from-amber-500 to-amber-700 text-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-amber-100 text-sm font-medium uppercase tracking-wider">Pending Orders</p>
                            <h3 class="text-3xl font-bold mt-1">{{ stats.pending_orders || 0 }}</h3>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <i class="pi pi-clock text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card animate__animated animate__fadeInUp animate__delay-3s bg-gradient-to-br from-purple-500 to-purple-700 text-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-purple-100 text-sm font-medium uppercase tracking-wider">Completed</p>
                            <h3 class="text-3xl font-bold mt-1">{{ stats.completed_orders || 0 }}</h3>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <i class="pi pi-check-circle text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Chart & Recent Orders -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Spend Chart -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Monthly Spending</h2>
                            <span class="text-sm text-gray-500">Last 6 Months</span>
                        </div>
                        <div class="h-80 w-full">
                            <VueApexCharts
                                type="area"
                                height="100%"
                                :options="chartOptions"
                                :series="chartSeries"
                                v-if="chartSeries[0].data.length > 0"
                            />
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Recent Orders</h2>
                            <button @click="goToOrders" class="text-blue-600 hover:text-blue-700 font-semibold text-sm flex items-center gap-1">
                                View All <i class="pi pi-arrow-right text-xs"></i>
                            </button>
                        </div>
                        <div v-if="recentOrders.length > 0" class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="py-3 px-2 text-sm font-semibold text-gray-500">Order ID</th>
                                        <th class="py-3 px-2 text-sm font-semibold text-gray-500">Date</th>
                                        <th class="py-3 px-2 text-sm font-semibold text-gray-500">Amount</th>
                                        <th class="py-3 px-2 text-sm font-semibold text-gray-500">Status</th>
                                        <th class="py-3 px-2 text-sm font-semibold text-gray-500">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in recentOrders" :key="order.order_id" class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-2 font-medium text-gray-900">#{{ order.order_public_id }}</td>
                                        <td class="py-4 px-2 text-gray-600">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                                        <td class="py-4 px-2 font-bold text-gray-900">{{ CurrencyUtil.formatCurrency(order.total_amount) }}</td>
                                        <td class="py-4 px-2">
                                            <span 
                                                class="px-3 py-1 rounded-full text-xs font-semibold"
                                                :class="getStatusClass(order.status)"
                                            >
                                                {{ order.status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-2">
                                            <button @click="viewOrder(order.order_id)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-lg transition-colors">
                                                <i class="pi pi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-12">
                            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="pi pi-inbox text-2xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500">No orders found yet.</p>
                            <button @click="goToProducts" class="mt-4 text-blue-600 font-semibold">Start Shopping</button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Quick Links & Featured -->
                <div class="space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <button @click="goToOrders" class="action-btn">
                                <i class="pi pi-truck bg-blue-100 text-blue-600"></i>
                                <span>My Orders</span>
                            </button>
                            <button @click="goToInvoices" class="action-btn">
                                <i class="pi pi-file-pdf bg-emerald-100 text-emerald-600"></i>
                                <span>Invoices</span>
                            </button>
                            <button @click="goToSecurity" class="action-btn">
                                <i class="pi pi-shield bg-purple-100 text-purple-600"></i>
                                <span>Security</span>
                            </button>
                            <button @click="goToAddress" class="action-btn">
                                <i class="pi pi-map-marker bg-amber-100 text-amber-600"></i>
                                <span>Addresses</span>
                            </button>
                        </div>
                    </div>

                    <!-- Featured Products -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Featured for You</h2>
                        </div>
                        <div class="space-y-4">
                            <div 
                                v-for="product in featuredProducts" 
                                :key="product.product_id"
                                class="flex items-center gap-4 group cursor-pointer"
                                @click="viewProduct(product.product_id)"
                            >
                                <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center p-2 group-hover:scale-105 transition-transform overflow-hidden font-bold">
                                    <img v-if="product.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" :alt="product.product_name" class="max-w-full max-h-full object-contain" />
                                    <i v-else class="pi pi-image text-gray-300"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-800 truncate">{{ product.product_name }}</h4>
                                    <p class="text-blue-600 font-bold text-sm">{{ CurrencyUtil.formatCurrency(product.product_price) }}</p>
                                    <div class="flex items-center gap-1 mt-1">
                                        <i class="pi pi-star-fill text-[10px] text-amber-400"></i>
                                        <span class="text-[10px] text-gray-500">{{ Number(product.rates_avg_rate || 0).toFixed(1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button @click="goToProducts" class="w-full mt-6 py-3 border-2 border-blue-100 text-blue-600 rounded-xl font-bold text-sm hover:bg-blue-50 transition-colors flex items-center justify-center gap-2">
                            Explore More <i class="pi pi-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Bubble -->
        <div class="fixed bottom-8 right-8 z-50">
            <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="bg-blue-600 text-white w-14 h-14 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all group">
                <i class="pi pi-facebook text-2xl"></i>
                <span class="absolute right-full mr-4 bg-white text-gray-800 px-4 py-2 rounded-xl border shadow-lg opacity-0 whitespace-nowrap pointer-events-none group-hover:opacity-100 transition-opacity font-medium">Need help? Message us!</span>
            </a>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
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
const chartSeries = ref<any[]>([{ name: 'Spend', data: [] }])
const chartOptions = ref<any>({
    chart: {
        type: 'area',
        toolbar: { show: false },
        zoom: { enabled: false }
    },
    colors: ['#3B82F6'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 3 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.45,
            opacityTo: 0.05,
            stops: [20, 100, 100, 100]
        }
    },
    xaxis: {
        categories: [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
            style: { colors: '#94a3b8', fontSize: '12px' }
        }
    },
    yaxis: {
        labels: {
            formatter: (val: number) => CurrencyUtil.formatCurrency(val),
            style: { colors: '#94a3b8', fontSize: '12px' }
        }
    },
    grid: {
        borderColor: '#f1f5f9',
        strokeDashArray: 4,
        xaxis: { lines: { show: true } }
    },
    tooltip: {
        theme: 'light',
        y: {
            formatter: (val: number) => CurrencyUtil.formatCurrency(val)
        }
    }
})

// Fetch Data
const fetchDashboardData = async () => {
    await dashboardService.get('customer/dashboard').then(() => {
        if (dashboardService.request.status === 200 && dashboardService.request.data) {
            const data = dashboardService.request.data
            stats.value = data.stats
            recentOrders.value = data.recent_orders
            featuredProducts.value = data.featured_products

            // Update Chart
            chartSeries.value = [{
                name: 'Spent',
                data: data.monthly_spend.data
            }]
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

// Helpers
const getStatusClass = (status: string) => {
    switch (status) {
        case 'Completed': return 'bg-emerald-100 text-emerald-700'
        case 'Processing': return 'bg-blue-100 text-blue-700'
        case 'Shipped': return 'bg-purple-100 text-purple-700'
        case 'Cancelled': return 'bg-red-100 text-red-700'
        default: return 'bg-gray-100 text-gray-700'
    }
}

// Navigation
const goToOrders = () => router.push({ name: 'customer.order.index' })
const goToInvoices = () => router.push({ name: 'customer-invoice' })
const goToSecurity = () => {
    // Current design is modal-based for security
    // We can emit or call a global to open the security dialog
    // For now, let's assume we can trigger it
    const event = new CustomEvent('open-security-form')
    window.dispatchEvent(event)
}
const goToAddress = () => {
    const event = new CustomEvent('open-address-form')
    window.dispatchEvent(event)
}
const goToProducts = () => router.push({ name: 'customer.browse-products' })
const viewOrder = (id: number) => router.push({ name: 'customer.order.index', hash: `#order-${id}` }) // Simple navigation
const viewProduct = (id: number) => router.push({ name: 'customer.product-info.index', params: { id } })

onMounted(() => {
    fetchDashboardData()
})
</script>

<style scoped>
.customer-dashboard {
    background-color: #f8fafc;
    min-height: calc(100vh - 80px); /* Adjust based on navbar height */
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem;
    background-color: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 1.25rem;
    transition: all 0.2s ease;
}

.action-btn:hover {
    background-color: #fff;
    border-color: #3B82F6;
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1);
    transform: translateY(-2px);
}

.action-btn i {
    width: 3rem;
    height: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1rem;
    font-size: 1.25rem;
}

.action-btn span {
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
}

.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
