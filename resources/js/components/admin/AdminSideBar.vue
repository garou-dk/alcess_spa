<template>
    <div class="h-full flex flex-col bg-blue-600 text-white shadow-xl transition-all duration-300 relative overflow-hidden backdrop-blur-sm">
        <!-- Subtle Pattern Overlay for Texture -->
        <div class="absolute inset-0 opacity-5 pointer-events-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjZmZmIiAvPgo8cmVjdCB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ0cmFuc3BhcmVudCIgLz4KPC9zdmc+')]"></div>
        
        <!-- Clean Header - No Logo/Name -->
        <div :class="getResponsiveClasses({
            mobile: 'flex items-center justify-between h-14 px-4 border-b border-white/10 relative z-10',
            tablet: 'flex items-center justify-between h-16 px-4 border-b border-white/10 relative z-10',
            desktop: 'flex items-center gap-3 h-16 px-6 border-b border-white/10 relative z-10'
        })" class="bg-blue-700/30">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center shadow-sm">
                    <i class="pi pi-th-large text-white text-sm"></i>
                </div>
                <h1 class="font-bold text-lg tracking-wide text-white">Admin Panel</h1>
            </div>
            
            <!-- Mobile Toggle -->
            <button 
                v-if="!isDesktop"
                @click="emit('closeSidebar')"
                class="p-2 rounded-lg hover:bg-white/10 transition-colors text-white"
            >
                <i class="pi pi-times text-lg"></i>
            </button>
        </div>

        <!-- Scrollable Navigation Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar py-4 relative z-10">
            <div class="flex flex-col gap-1 px-3">
                
                <!-- Main Dashboard Section -->
                <div class="mb-2">
                    <p class="px-3 text-[11px] font-bold text-blue-200 uppercase tracking-wider mb-2 mt-1">Dashboard</p>
                    <SideBarButton 
                        icon="pi pi-home" 
                        label="Overview" 
                        to="admin.dashboard" 
                        hash="#overview-section"
                    />
                    <SideBarButton 
                        icon="pi pi-bell" 
                        label="New Orders" 
                        to="admin.dashboard" 
                        hash="#new-orders-section"
                    />
                    <SideBarButton 
                        icon="pi pi-check-circle" 
                        label="Confirmed Orders" 
                        to="admin.dashboard" 
                        hash="#confirmed-orders-section"
                    />
                </div>

                <!-- Reports Section -->
                <div class="mb-2 pt-2 border-t border-white/10">
                    <p class="px-3 text-[11px] font-bold text-blue-200 uppercase tracking-wider mb-2 mt-2">Reports</p>
                    <SideBarButton 
                        icon="pi pi-chart-line" 
                        label="Sales & Revenue" 
                        to="admin.dashboard" 
                        hash="#sales-revenue-section"
                    />
                    <SideBarButton 
                        icon="pi pi-exclamation-circle" 
                        label="Nearly Out of Stock" 
                        to="admin.dashboard" 
                        hash="#nearly-out-of-stock-section"
                    />
                     <SideBarButton 
                        icon="pi pi-star" 
                        label="Best Selling Products" 
                        to="admin.dashboard" 
                        hash="#best-selling-products-section"
                    />
                    <SideBarButton 
                        icon="pi pi-arrow-right-arrow-left" 
                        label="Inventory Movement" 
                        to="admin.dashboard" 
                        hash="#inventory-movement-section"
                    />
                </div>

                <!-- Management Section -->
                <div class="mb-2 pt-2 border-t border-white/10">
                    <p class="px-3 text-[11px] font-bold text-blue-200 uppercase tracking-wider mb-2 mt-2">Management</p>
                    <SideBarButton icon="pi pi-shopping-cart" label="Orders" to="admin.order.index" />
                    <SideBarButton icon="pi pi-users" label="Customers" to="admin.customer.index" />
                    <SideBarButton icon="pi pi-box" label="Products" to="admin.product.index" />
                    <SideBarButton icon="pi pi-tags" label="Categories" to="admin.category.index" />
                    <SideBarButton icon="pi pi-ticket" label="Units" to="admin.unit.index" />
                    <SideBarButton icon="pi pi-calculator" label="POS" to="admin.pos.index" />
                </div>

                <!-- System Section -->
                <div class="mb-2 pt-2 border-t border-white/10">
                    <p class="px-3 text-[11px] font-bold text-blue-200 uppercase tracking-wider mb-2 mt-2">System</p>
                    <SideBarButton icon="pi pi-users" label="User Management" to="admin.user.index" />
                    <SideBarButton icon="pi pi-cog" label="Settings" to="admin.settings.index" />
                </div>
            </div>
        </div>

        <!-- User Profile / Footer - Fixed at bottom -->
        <div class="p-4 border-t border-white/10 bg-blue-700/30 relative z-10">
            <div class="flex items-center gap-3 mb-3 px-2">
                <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white font-bold shadow-sm border border-white/10">
                    {{ userInitials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ userName }}</p>
                    <p class="text-xs text-blue-200 truncate">Administrator</p>
                </div>
            </div>
            
            <button 
                @click="authService.logout()" 
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-all duration-200 text-sm font-medium border border-white/5 shadow-sm group"
            >
                <i class="pi pi-sign-out text-blue-200 group-hover:text-white transition-colors"></i>
                <span>Sign Out</span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import AuthService from "@/services/AuthService";
import SideBarButton from "../SideBarButton.vue";
import Page from "@/stores/Page";
import { useResponsive } from "@/composables/useResponsive";

const emit = defineEmits(['closeSidebar']);

// Use responsive composable
const { isDesktop, getResponsiveClasses } = useResponsive();

const authService = new AuthService();

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
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}
</style>
