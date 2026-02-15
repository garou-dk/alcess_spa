<template>
    <div :class="getResponsiveClasses({
        mobile: 'h-full flex flex-col',
        tablet: 'h-full flex flex-col',
        desktop: 'h-full flex flex-col scrollbar-hide'
    })" :style="getResponsiveStyles()">
        <!-- Clean Header - No Logo/Name -->
        <div :class="getResponsiveClasses({
            mobile: 'flex items-center justify-between gap-2 px-4 py-4',
            tablet: 'flex items-center justify-between gap-2 px-5 py-5',
            desktop: 'flex items-center justify-start px-6 py-6'
        })" class="border-b border-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-200">
                    <i class="pi pi-shield text-white text-base"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-black text-black tracking-tight leading-none uppercase">Alcess</span>
                    <span class="text-[9px] font-bold text-gray-700 tracking-[0.2em] mt-1 uppercase">Admin System</span>
                </div>
            </div>
            <!-- Close button for mobile only -->
            <button 
                v-if="isMobile"
                @click="closeSidebar"
                class="text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-lg hover:bg-gray-100"
            >
                <i class="pi pi-times text-lg"></i>
            </button>
        </div>

        <!-- Navigation Section -->
        <div class="flex-1 overflow-y-auto scrollbar-hide">
            <nav class="px-2 py-4 space-y-1">
                <!-- DASHBOARD Section Label -->
                <div class="px-4 pt-2 pb-2">
                    <span class="text-[11px] font-black text-black uppercase tracking-[0.15em]">Dashboard</span>
                </div>
    
                <!-- Dashboard link -->
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-objects-column"
                    label="Dashboard"
                    :hash="'#overview-section'"
                />
    
                <!-- Dashboard sub-items displayed directly -->
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-plus-circle"
                    label="New Orders"
                    :hash="'#new-orders-section'"
                    :isSubItem="true"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-check-circle"
                    label="Confirmed Orders"
                    :hash="'#confirmed-orders-section'"
                    :isSubItem="true"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-chart-pie"
                    label="Sales / Revenue"
                    :hash="'#sales-distribution-section'"
                    :isSubItem="true"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-exclamation-triangle"
                    label="Nearly Out of Stock"
                    :hash="'#nearly-out-of-stock-section'"
                    :isSubItem="true"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-star"
                    label="Best Selling Products"
                    :hash="'#best-selling-products-section'"
                    :isSubItem="true"
                />
                <SideBarButton
                    to="admin.dashboard.index"
                    icon="pi pi-arrows-h"
                    label="Inventory Movement"
                    :hash="'#inventory-movement-section'"
                    :isSubItem="true"
                />
    
                <!-- Divider -->
                <div class="mx-4 my-4 border-t border-gray-50"></div>
    
                <!-- MANAGEMENT Section Label -->
                <div class="px-4 pt-2 pb-2">
                    <span class="text-[11px] font-black text-black uppercase tracking-[0.15em]">Management</span>
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
                <div class="mx-4 my-4 border-t border-gray-50"></div>
    
                <!-- SYSTEM Section Label -->
                <div class="px-4 pt-2 pb-2">
                    <span class="text-[11px] font-black text-black uppercase tracking-[0.15em]">System</span>
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
        <div class="p-4 border-t border-gray-50 bg-gray-50/30">
            <div class="flex items-center gap-3 px-2 mb-4">
                <div class="relative">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black text-xs shadow-lg shadow-blue-100 uppercase tracking-tighter">
                        {{ userInitials }}
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full shadow-sm"></div>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[13px] font-black text-black truncate leading-tight">{{ userName }}</span>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">Administrator</span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-2">
                <button 
                    @click="handleLogout"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-red-50 text-black hover:text-red-600 rounded-xl transition-all duration-300 text-xs font-black border border-gray-100 shadow-sm active:scale-95 group"
                >
                    <i class="pi pi-sign-out group-hover:translate-x-0.5 transition-transform duration-300"></i>
                    <span>SIGN OUT</span>
                </button>
            </div>
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
import { computed, inject } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";

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
    const baseStyle = 'background: #ffffff; border-right: 1px solid #f1f5f9;';
    
    if (isMobile.value) {
        return `${baseStyle} width: 100%; min-width: 100%; min-height: 100vh;`;
    }
    if (isTablet.value) {
        return `${baseStyle} width: 260px; min-width: 260px;`;
    }
    // Desktop - original size
    return `${baseStyle} width: 272px; min-width: 272px;`;
};
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