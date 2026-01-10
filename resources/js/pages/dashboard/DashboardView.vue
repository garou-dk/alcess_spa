<template>
    <div>
        <!-- Full screen loading overlay for professional UX -->
        <LoadingOverlay 
            :show="isLoading && !isLoaded" 
            title="Loading Dashboard"
            subtitle="Fetching your latest data..."
        />

        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <!-- Real-time Update Indicator -->
        <Transition name="fade">
            <div v-if="isUpdating" class="mb-4 flex items-center justify-center">
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="pi pi-sync animate-spin"></i>
                    <span class="text-sm font-medium">Dashboard updated with latest data</span>
                </div>
            </div>
        </Transition>

        <!-- New Orders Section -->
        <div id="new-orders-section" :class="responsive.getResponsiveMargin() + ' scroll-mt-0'">
            <PendingReport />
        </div>

        <!-- Confirmed Orders Section -->
        <div id="confirmed-orders-section" :class="responsive.getResponsiveMargin() + ' scroll-mt-20'">
            <DashboardOrdersTable />
        </div>

        <!-- Nearly Out of Stock Section -->
        <div id="nearly-out-of-stock-section" :class="responsive.getResponsiveMargin() + ' scroll-mt-20'">
            <BatchForm />
        </div>

        <!-- Best Selling Products Section -->
        <div id="best-selling-products-section" :class="responsive.getResponsiveMargin() + ' scroll-mt-20'">
            <TopSoldProducts />
        </div>

        <!-- Inventory Movement Section -->
        <div id="inventory-movement-section" :class="responsive.getResponsiveMargin() + ' scroll-mt-20'">
            <StockInOut />
        </div>

        <!-- Sales Distribution Section -->
        <div id="sales-distribution-section" :class="responsive.getResponsiveMargin() + ' scroll-mt-20'">
            <!-- Charts Section - Desktop unchanged, mobile/tablet stacked -->
            <div 
                :class="[
                    responsive.getResponsiveGap(),
                    responsive.getResponsiveClasses({
                        mobile: 'flex flex-col',
                        tablet: 'flex flex-col', 
                        desktop: 'mb-5 flex flex-wrap' // Original desktop layout preserved
                    })
                ]"
            >
                <div 
                    :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full',
                        desktop: 'flex-1 min-w-0 max-lg:w-full lg:w-1/2' // Original desktop classes
                    })"
                >
                    <PieGraphReport />
                </div>
                <div 
                    :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full',
                        desktop: 'flex-1 min-w-0 max-lg:w-full lg:w-1/2' // Original desktop classes
                    })"
                >
                    <CategorySales />
                </div>
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
import { useDashboardData } from "@/composables/useDashboardData";
import { useResponsive } from "@/composables/useResponsive";
import LoadingOverlay from "@/components/LoadingOverlay.vue";
import { onMounted, onUnmounted, ref } from "vue";
import { useToast } from "vue-toastification";
import { useRoute, useRouter } from "vue-router";
import { watch, nextTick } from "vue";

const toast = useToast();
const route = useRoute();
const router = useRouter();

// Use global responsive composable (preserves desktop, optimizes mobile/tablet)
const responsive = useResponsive();

// Use the shared dashboard data composable
const { loadDashboardData, refreshDashboardData, cleanup, isLoading, isLoaded, isUpdating, error } = useDashboardData();

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