<template>
    <div class="space-y-6 lg:space-y-8">
        <!-- Full screen loading overlay -->
        <LoadingOverlay 
            :show="isLoading && !isLoaded" 
            title="Loading Dashboard"
            subtitle="Fetching your latest data..."
        />

        <!-- Page Header & Time -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 lg:p-6 rounded-xl shadow-sm border border-gray-100 mb-2">
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-800">Overview</h1>
                <p class="text-xs lg:text-sm text-gray-500 mt-1">Summary of your store's performance and inventory.</p>
            </div>
            <DateTimeDisplay class="!mb-0" />
        </div>

        <!-- KPI Cards Grid - 4 Columns on desktop -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            <TotalRevenueReport />
            <OrderPending />
            <InventoryValue />
            <StatCard 
                label="Best Selling Item" 
                :value="topSellerName" 
                :subtitle="topSellerUnits > 0 ? topSellerUnits + ' units sold' : 'No data'"
                icon="pi pi-star-fill"
                iconBgColor="bg-yellow-50"
                iconColor="#EAB308"
            />
        </div>

        <!-- Real-time Update Indicator Case -->
        <Transition name="fade">
            <div v-if="isUpdating" class="flex items-center justify-center">
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-full flex items-center gap-2 shadow-sm">
                    <i class="pi pi-sync animate-spin text-sm"></i>
                    <span class="text-xs font-semibold">Live updates active</span>
                </div>
            </div>
        </Transition>

        <!-- Main Dashboard Layout Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
            
            <!-- Left Column: Primary Charts & Main Activity (Spans 8 cols on XL) -->
            <div class="xl:col-span-8 space-y-6 lg:space-y-8">
                
                <!-- Charts Section -->
                <div id="sales-distribution-section" class="grid grid-cols-1 lg:grid-cols-2 gap-6 scroll-mt-24">
                    <PieGraphReport />
                    <CategorySales />
                </div>

                <!-- Main Transaction/Orders Section -->
                <div id="confirmed-orders-section" class="scroll-mt-24">
                    <DashboardOrdersTable />
                </div>

                <!-- Product Performance / Movement Section -->
                <div id="inventory-movement-section" class="scroll-mt-24">
                    <StockInOut />
                </div>
            </div>

            <!-- Right Column: Sidebar Actions & Lists (Spans 4 cols on XL) -->
            <div class="xl:col-span-4 space-y-6 lg:space-y-8">
                
                <!-- New Orders Highlight -->
                <div id="new-orders-section" class="scroll-mt-24">
                    <PendingReport />
                </div>
                
                <!-- Important Inventory Alerts -->
                <div id="nearly-out-of-stock-section" class="scroll-mt-24">
                    <BatchForm />
                </div>

                <!-- Rankings -->
                <div id="best-selling-products-section" class="scroll-mt-24">
                    <TopSoldProducts />
                </div>

                <!-- Quick Help / Summary Card (Optional/New) -->
                <BoxShadow class="p-5 bg-gradient-to-br from-blue-600 to-blue-700 text-white">
                    <h3 class="font-bold text-lg mb-2">Need Help?</h3>
                    <p class="text-xs text-blue-100 leading-relaxed mb-4">
                        Review your daily sales reports and stay on top of low stock alerts to ensure smooth operations.
                    </p>
                    <RouterLink :to="{ name: 'admin.report.index' }">
                        <button class="w-full bg-white text-blue-600 font-bold py-2 rounded-lg text-xs hover:bg-blue-50 transition-colors">
                            View Full Reports
                        </button>
                    </RouterLink>
                </BoxShadow>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import BatchForm from "@/components/forms/BatchForm.vue";
import CategorySales from "@/components/reports/CategorySales.vue";
import DashboardOrdersTable from "@/components/reports/DashboardOrdersTable.vue";
import PendingReport from "@/components/reports/PendingReport.vue";
import PieGraphReport from "@/components/reports/PieGraphReport.vue";
import StockInOut from "@/components/reports/StockInOut.vue";
import TopSoldProducts from "@/components/reports/TopSoldProducts.vue";
import DateTimeDisplay from "@/components/DateTimeDisplay.vue";
import TotalRevenueReport from "@/components/reports/TotalRevenueReport.vue";
import InventoryValue from "@/components/reports/InventoryValue.vue";
import OrderPending from "@/components/reports/OrderPending.vue";
import StatCard from "@/components/StatCard.vue";
import BoxShadow from "@/components/BoxShadow.vue";
import { useDashboardData } from "@/composables/useDashboardData";
import { useResponsive } from "@/composables/useResponsive";
import LoadingOverlay from "@/components/LoadingOverlay.vue";
import { onMounted, onUnmounted, ref, computed } from "vue";
import { useToast } from "vue-toastification";
import { useRoute, useRouter } from "vue-router";
import { watch, nextTick } from "vue";

const toast = useToast();
const route = useRoute();
const router = useRouter();

// Use global responsive composable
const responsive = useResponsive();

// Use the shared dashboard data composable
const { dashboardData, loadDashboardData, refreshDashboardData, cleanup, isLoading, isLoaded, isUpdating, error } = useDashboardData();

// Computed properties for top selling item stat card
const topSellerName = computed(() => {
    const top = dashboardData.top_sellers?.[0];
    return top ? top.product_name : 'No sales yet';
});

const topSellerUnits = computed(() => {
    const top = dashboardData.top_sellers?.[0];
    return top ? top.units_sold : 0;
});

// Track programmatic scrolling to prevent scroll-spy interference
const isProgrammaticScroll = ref(false);
let scrollTimeout: number | null = null;
let programmaticScrollTimeout: number | null = null;
let scrollObserver: IntersectionObserver | null = null;

// Retry handler for failed data loads
const handleRetry = async () => {
    try {
        await refreshDashboardData();
        toast.success("Dashboard data reloaded successfully");
    } catch (err) {
        toast.error("Retry failed. Please try again later.");
    }
};

// Scroll to section based on hash
const scrollToSection = (sectionId: string) => {
    // Mark as programmatic scroll
    isProgrammaticScroll.value = true;
    
    // Clear any pending timeouts
    if (scrollTimeout) {
        clearTimeout(scrollTimeout);
    }
    if (programmaticScrollTimeout) {
        clearTimeout(programmaticScrollTimeout);
    }
    
    nextTick(() => {
        // Special handling for first section - scroll to top of page
        if (sectionId === 'new-orders-section') {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        
        // Re-enable scroll-spy after animation completes (smooth scroll takes ~500-800ms)
        programmaticScrollTimeout = window.setTimeout(() => {
            isProgrammaticScroll.value = false;
        }, 800);
    });
};

// Watch for hash changes in route (when clicking sidebar)
watch(() => route.hash, (newHash) => {
    if (newHash) {
        const sectionId = newHash.replace('#', '');
        scrollToSection(sectionId);
    }
}, { immediate: true });

// Setup Intersection Observer for scroll-spy
const setupScrollSpy = () => {
    const sections = [
        'new-orders-section',
        'confirmed-orders-section',
        'nearly-out-of-stock-section',
        'best-selling-products-section',
        'inventory-movement-section',
        'sales-distribution-section'
    ];

    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -60% 0px', // Trigger when section is in the middle of viewport
        threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            // Only update hash if NOT programmatically scrolling and section is intersecting
            if (entry.isIntersecting && !isProgrammaticScroll.value) {
                const sectionId = entry.target.id;
                const newHash = `#${sectionId}`;
                
                // Update route hash without scrolling
                if (route.hash !== newHash) {
                    // Clear any pending scroll timeout
                    if (scrollTimeout) {
                        clearTimeout(scrollTimeout);
                    }
                    
                    // Debounce the hash update
                    scrollTimeout = window.setTimeout(() => {
                        router.replace({ 
                            name: route.name as string, 
                            hash: newHash 
                        });
                    }, 150);
                }
            }
        });
    }, observerOptions);

    // Observe all sections
    sections.forEach((sectionId) => {
        const element = document.getElementById(sectionId);
        if (element) {
            observer.observe(element);
        }
    });

    return observer;
};

// Load dashboard data on mount
onMounted(() => {
    loadDashboardData();
    
    // Setup scroll spy after content loads
    setTimeout(() => {
        scrollObserver = setupScrollSpy();
    }, 1000);
    
    // Scroll to section if hash exists
    if (route.hash) {
        const sectionId = route.hash.replace('#', '');
        setTimeout(() => scrollToSection(sectionId), 500); // Delay to ensure content is loaded
    }
});

// Cleanup on unmount
onUnmounted(() => {
    if (scrollObserver) {
        scrollObserver.disconnect();
    }
    cleanup(); // Cleanup WebSocket connections
    if (scrollTimeout) {
        clearTimeout(scrollTimeout);
    }
    if (programmaticScrollTimeout) {
        clearTimeout(programmaticScrollTimeout);
    }
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>