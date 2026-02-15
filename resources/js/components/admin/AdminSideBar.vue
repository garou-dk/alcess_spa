<template>
    <div :class="getResponsiveClasses({
        mobile: 'h-full flex flex-col',
        tablet: 'h-full flex flex-col',
        desktop: 'h-full flex flex-col scrollbar-hide'
    })" :style="getResponsiveStyles()">
        <!-- Clickable Large Dashboard Header -->
        <div :class="getResponsiveClasses({
            mobile: 'px-4 py-8',
            tablet: 'px-5 py-10',
            desktop: 'px-6 py-12'
        })" class="border-b border-white/5">
            <RouterLink 
                :to="{ name: 'admin.dashboard.index', hash: '#overview-section' }"
                class="block group transition-transform duration-300 active:scale-95"
            >
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-1 h-6 bg-white rounded-full transition-all duration-300 group-hover:h-8 group-hover:bg-blue-400"></div>
                    <h1 class="text-3xl font-black text-white tracking-tighter uppercase leading-none group-hover:text-blue-200 transition-colors">
                        Dashboard
                    </h1>
                </div>
                <p class="text-[10px] font-bold text-white/30 uppercase tracking-[0.3em] ml-3 transition-opacity duration-300 group-hover:opacity-100">
                    System Overview
                </p>
            </RouterLink>
            
            <!-- Close button for mobile only -->
            <button 
                v-if="isMobile"
                @click="closeSidebar"
                class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10"
            >
                <i class="pi pi-times text-lg"></i>
            </button>
        </div>

        <!-- Navigation Section -->
        <div class="flex-1 overflow-y-auto scrollbar-hide py-3">
            <nav class="px-2 space-y-0.5">
                <!-- New order priority items -->
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-plus-circle"
                    label="New Orders"
                    :hash="'#new-orders-section'"
                    :badge="pendingOrdersCount"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-check-circle"
                    label="Confirmed Orders"
                    :hash="'#confirmed-orders-section'"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-star"
                    label="Best Selling Product"
                    :hash="'#best-selling-products-section'"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-chart-pie"
                    label="Sales / Revenue"
                    :hash="'#sales-distribution-section'"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-exclamation-triangle"
                    label="Nearly out of stock"
                    :hash="'#nearly-out-of-stock-section'"
                    :badge="lowStockCount"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-arrows-h"
                    label="Inventory movement"
                    :hash="'#inventory-movement-section'"
                />
    
                <!-- Divider -->
                <div class="mx-3 my-4 border-t border-white/5"></div>
    
                <!-- MANAGEMENT Section Label -->
                <div class="px-3 pb-1.5">
                    <span class="text-[9.5px] font-bold text-white/40 uppercase tracking-[0.2em]">Management</span>
                </div>
    
                <SideBarAccordion
                    icon="pi pi-shopping-cart"
                    label="Orders"
                    :active="['admin.order.index', 'admin.pos.index', 'admin.pos.find'].includes(route.name as string)"
                >
                    <SideBarButton
                        to="admin.pos.index"
                        icon="pi pi-shop"
                        label="Walk-In"
                        :isSubItem="true"
                    />
                    <SideBarButton
                        to="admin.order.index"
                        icon="pi pi-globe"
                        label="Online"
                        :isSubItem="true"
                    />
                </SideBarAccordion>
                <SideBarAccordion
                    v-if="Page.user && Page.user.role.role_name === RoleEnum.ADMIN"
                    icon="pi pi-dollar"
                    label="Sales"
                    :active="['admin.sales.walkin', 'admin.sales.online'].includes(route.name as string)"
                >
                    <SideBarButton
                        to="admin.sales.walkin"
                        icon="pi pi-shop"
                        label="Walk-In"
                        :isSubItem="true"
                    />
                    <SideBarButton
                        to="admin.sales.online"
                        icon="pi pi-globe"
                        label="Online"
                        :isSubItem="true"
                    />
                </SideBarAccordion>
                <SideBarButton
                    to="admin.product.index"
                    icon="pi pi-book"
                    label="Inventory"
                />
    
                <!-- Divider -->
                <div class="mx-3 my-4 border-t border-white/5"></div>
    
                <!-- SYSTEM Section Label -->
                <div class="px-3 pb-1.5">
                    <span class="text-[9.5px] font-bold text-white/40 uppercase tracking-[0.2em]">System</span>
                </div>
    
                <SideBarButton
                    v-if="Page.user && Page.user.role.role_name === RoleEnum.ADMIN"
                    to="admin.users.index"
                    icon="pi pi-users"
                    label="Users"
                />
                <SideBarAccordion
                    v-if="Page.user && Page.user.role.role_name === RoleEnum.ADMIN"
                    icon="pi pi-cog"
                    label="Settings"
                    :active="['admin.unit.index', 'admin.category.index', 'admin.setting.index'].includes(route.name as string)"
                >
                    <SideBarButton
                        to="admin.unit.index"
                        icon="pi pi-box"
                        label="Units"
                        :isSubItem="true"
                    />
                    <SideBarButton
                        to="admin.category.index"
                        icon="pi pi-tag"
                        label="Categories"
                        :isSubItem="true"
                    />
                    <SideBarButton
                        to="admin.setting.index"
                        icon="pi pi-cog"
                        label="Settings"
                        :isSubItem="true"
                    />
                </SideBarAccordion>
                <SideBarButton
                    v-if="Page.user && Page.user.role.role_name === RoleEnum.ADMIN"
                    to="admin.report.index"
                    icon="pi pi-chart-bar"
                    label="Reports"
                />
                <SideBarButton
                    to="admin.rate.index"
                    icon="pi pi-star"
                    label="Ratings"
                />
            </nav>
        </div>
    </div>
</template>

<script setup lang="ts">
import SideBarButton from "@/components/SideBarButton.vue";
import SideBarAccordion from "@/components/SideBarAccordion.vue";
import { useRoute } from "vue-router";
import Page from "@/stores/Page";
import { RoleEnum } from "@/enums/RoleEnum";
import { useResponsive } from "@/composables/useResponsive";
import { computed, inject, onMounted } from "vue";
import { useDashboardData } from "@/composables/useDashboardData";

const route = useRoute();

// Inject sideBar ref from parent
const sideBar = inject("sideBar");

// Use responsive composable
const responsive = useResponsive();
const { 
    isMobile, 
    isTablet, 
} = responsive;

// Dashboard data for live badges
const { dashboardData, loadDashboardData } = useDashboardData();

// Computed properties for live badges
const pendingOrdersCount = computed(() => dashboardData.pending_orders?.count || dashboardData.pending_orders_list?.length || 0);
const lowStockCount = computed(() => dashboardData.nearly_out_of_stock?.length || 0);

// Extract the function separately to ensure it's available
const getResponsiveClasses = responsive.getResponsiveClasses;

const closeSidebar = () => {
    if (sideBar) {
        (sideBar as any).value = false;
    }
};

const getResponsiveStyles = () => {
    const baseStyle = 'background: linear-gradient(180deg, #1e40af 0%, #172554 100%); border-right: 1px solid rgba(255, 255, 255, 0.1);';
    
    if (isMobile.value) {
        return `${baseStyle} width: 100%; min-width: 100%; min-height: 100vh;`;
    }
    if (isTablet.value) {
        return `${baseStyle} width: 260px; min-width: 260px;`;
    }
    // Desktop - original size
    return `${baseStyle} width: 272px; min-width: 272px;`;
};

onMounted(() => {
    // Load dashboard data for badges if not already loaded
    loadDashboardData();
});
</script>

<style scoped>
.scrollbar-hide {
    /* Hide scrollbar for Chrome, Safari and Opera */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* Internet Explorer 10+ */
}

.scrollbar-hide::-webkit-scrollbar {
    display: none; /* Safari and Chrome */
}
</style>