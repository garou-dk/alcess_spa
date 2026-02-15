<template>
    <div :class="getResponsiveClasses({
        mobile: 'h-full overflow-auto',
        tablet: 'h-full overflow-auto pb-2',
        desktop: 'h-full overflow-auto pb-2 scrollbar-hide'
    })" :style="getResponsiveStyles()">
        <!-- Clean Header - No Logo/Name -->
        <div :class="getResponsiveClasses({
            mobile: 'flex items-center justify-between gap-2 px-4 py-3',
            tablet: 'flex items-center justify-between gap-2 px-4 py-3',
            desktop: 'flex items-center justify-center px-5 py-4'
        })" class="border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="pi pi-th-large text-blue-600 text-sm"></i>
                </div>
                <span class="text-sm font-bold text-gray-800 tracking-wide uppercase">Admin Panel</span>
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
        <nav class="px-2 py-3 space-y-0.5">
            <!-- DASHBOARD Section Label -->
            <div class="px-3 pt-3 pb-1.5">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dashboard</span>
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
            <div class="mx-3 my-2 border-t border-white/10"></div>

            <!-- MANAGEMENT Section Label -->
            <div class="px-3 pt-2 pb-1.5">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Management</span>
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
            <div class="mx-3 my-2 border-t border-white/10"></div>

            <!-- SYSTEM Section Label -->
            <div class="px-3 pt-2 pb-1.5">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">System</span>
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
</template>
<script setup lang="ts">
import SideBarButton from "@/components/SideBarButton.vue";
import SideBarAccordion from "@/components/SideBarAccordion.vue";
import { useRoute, useRouter } from "vue-router";
import Page from "@/stores/Page";
import { RoleEnum } from "@/enums/RoleEnum";
import { useResponsive } from "@/composables/useResponsive";
import { inject } from "vue";

const route = useRoute();
const router = useRouter();

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
        sideBar.value = false;
    }
};

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