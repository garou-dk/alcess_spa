<template>
    <div :class="getResponsiveClasses({
        mobile: 'h-full overflow-auto',
        tablet: 'h-full overflow-auto pb-2',
        desktop: 'h-full overflow-auto pb-2 scrollbar-hide'
    })" :style="getResponsiveStyles()">
        <div :class="getResponsiveClasses({
            mobile: 'flex items-center justify-between gap-2 p-3 border-b',
            tablet: 'flex items-center justify-center gap-2 p-4 border-b',
            desktop: 'flex items-center justify-center gap-3 p-6 border-b'
        })" class="border-white/5">
            <div class="flex items-center gap-2">
                <div :class="getResponsiveClasses({
                    mobile: 'rounded-full bg-white p-1',
                    tablet: 'rounded-full bg-white p-1.5',
                    desktop: 'rounded-full bg-white p-2'
                })">
                    <img
                        :src="Logo"
                        alt="Store Icon"
                        :class="getResponsiveClasses({
                            mobile: 'h-6 w-6 object-contain',
                            tablet: 'h-8 w-8 object-contain',
                            desktop: 'h-10 w-10 object-contain'
                        })"
                    />
                </div>
                <h1 :class="getResponsiveClasses({
                    mobile: 'text-lg font-bold text-white select-none',
                    tablet: 'text-xl font-bold text-white select-none',
                    desktop: 'text-2xl font-bold text-white select-none'
                })">ALCESS</h1>
            </div>
            <!-- Close button for mobile only -->
            <button 
                v-if="isMobile"
                @click="closeSidebar"
                class="text-white hover:text-gray-200 transition-colors p-2 rounded-lg hover:bg-white/10"
            >
                <i class="pi pi-times text-lg"></i>
            </button>
        </div>
        <SideBarAccordion
            icon="pi pi-objects-column"
            label="Dashboard"
            :active="route.name === 'admin.dashboard.index'"
            :clickable="true"
            :keep-open="route.name === 'admin.dashboard.index'"
            @header-click="handleDashboardClick"
        >
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
                label="Sales/Revenue"
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
        </SideBarAccordion>
        <SideBarAccordion
            icon="pi pi-shopping-cart"
            label="Order"
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
    </div>
</template>
<script setup lang="ts">
import Logo from "@/../img/logo.png";
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
    const baseStyle = 'background-color: #0b1426;';
    
    if (isMobile.value) {
        return `${baseStyle} width: 100%; min-width: 100%; min-height: 100vh;`;
    }
    if (isTablet.value) {
        return `${baseStyle} width: 260px; min-width: 260px;`;
    }
    // Desktop - original size
    return `${baseStyle} width: 288px; min-width: 288px;`;
};

const handleDashboardClick = () => {
    if (route.name === 'admin.dashboard.index') {
        // Already on dashboard, scroll to overview section
        router.push({ name: 'admin.dashboard.index', hash: '#overview-section' });
    } else {
        // Navigate to dashboard overview
        router.push({ name: 'admin.dashboard.index', hash: '#overview-section' });
    }
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
