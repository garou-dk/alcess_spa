<template>
    <div :class="getResponsiveClasses({
        mobile: 'h-full flex flex-col',
        tablet: 'h-full flex flex-col',
        desktop: 'h-full flex flex-col scrollbar-hide'
    })" :style="getResponsiveStyles()">
        <!-- Premium Header Area -->
        <div :class="getResponsiveClasses({
            mobile: 'flex items-center justify-between gap-2 px-4 py-4',
            tablet: 'flex items-center justify-between gap-2 px-5 py-5',
            desktop: 'flex items-center justify-start px-6 py-6'
        })" class="border-b border-white/10 bg-[#1e40af]/30 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-lg transition-transform duration-300">
                    <i class="pi pi-shield text-[#2563eb] text-lg"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-black text-white tracking-tight leading-none uppercase">Alcess</span>
                    <span class="text-[9px] font-bold text-blue-100/60 tracking-[0.2em] mt-1 uppercase">Admin System</span>
                </div>
            </div>
            <!-- Close button for mobile only -->
            <button 
                v-if="isMobile"
                @click="closeSidebar"
                class="text-white/70 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10"
            >
                <i class="pi pi-times text-lg"></i>
            </button>
        </div>

        <!-- Navigation Section -->
        <div class="flex-1 overflow-y-auto scrollbar-hide py-4">
            <nav class="px-2 space-y-1">
                <!-- DASHBOARD Section Label -->
                <div class="px-4 pb-2">
                    <span class="text-[11px] font-black text-white/40 uppercase tracking-[0.15em]">Dashboard</span>
                </div>
    
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
                <div class="mx-4 my-4 border-t border-white/5"></div>
    
                <!-- MANAGEMENT Section Label -->
                <div class="px-4 pb-2">
                    <span class="text-[11px] font-black text-white/40 uppercase tracking-[0.15em]">Management</span>
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
                <div class="mx-4 my-4 border-t border-white/5"></div>
    
                <!-- SYSTEM Section Label -->
                <div class="px-4 pb-2">
                    <span class="text-[11px] font-black text-white/40 uppercase tracking-[0.15em]">System</span>
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

        <!-- Premium Footer -->
        <div class="p-4 border-t border-white/10 bg-[#172554]/40 backdrop-blur-md">
            <div class="flex items-center gap-3 px-2 mb-4">
                <div class="relative">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#2563eb] font-black text-xs shadow-lg uppercase tracking-tighter">
                        {{ userInitials }}
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-[#1e3a8a] rounded-full shadow-sm"></div>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[13px] font-black text-white truncate leading-tight">{{ userName }}</span>
                    <span class="text-[10px] font-bold text-blue-200/60 uppercase tracking-widest mt-0.5">Administrator</span>
                </div>
            </div>
            
            <button 
                @click="handleLogout"
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all duration-300 text-xs font-black border border-white/5 shadow-inner active:scale-95 group"
            >
                <i class="pi pi-sign-out group-hover:translate-x-0.5 transition-transform duration-300"></i>
                <span>SIGN OUT</span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import SideBarButton from "@/components/SideBarButton.vue";
import SideBarAccordion from "@/components/SideBarAccordion.vue";
import { useRoute, useRouter } from "vue-router";
import Page from "@/stores/Page";
import { RoleEnum } from "@/enums/RoleEnum";
import { useResponsive } from "@/composables/useResponsive";
import { computed, inject, onMounted } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";
import { useDashboardData } from "@/composables/useDashboardData";

const route = useRoute();
const router = useRouter();
const toast = useToast();
const logoutService = useAxiosUtil();

// Inject sideBar ref from parent
const sideBar = inject("sideBar");

// Use responsive composable
const responsive = useResponsive();
const { 
    isMobile, 
    isTablet, 
    isDesktop 
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

const handleLogout = async () => {
    await logoutService.post("logout", null).then(() => {
        Page.user = null;
        router.push({ name: "admin.login" });
        toast.success("Logged out successfully");
    });
};

const userName = computed(() => Page.user?.full_name || 'Admin User');

const userInitials = computed(() => {
    const name = userName.value;
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
});

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