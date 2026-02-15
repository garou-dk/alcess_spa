<template>
    <div class="h-full flex flex-col bg-[#2563eb] text-white shadow-2xl transition-all duration-300 relative overflow-hidden border-r border-white/10">
        <!-- Subtle Pattern Overlay -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjZmZmIiAvPgo8cmVjdCB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ0cmFuc3BhcmVudCIgLz4KPC9zdmc+')]"></div>
        
        <!-- Premium Header Area -->
        <div :class="getResponsiveClasses({
            mobile: 'flex items-center justify-between h-16 px-4 border-b border-white/10 relative z-10',
            tablet: 'flex items-center justify-between h-20 px-5 border-b border-white/10 relative z-10',
            desktop: 'flex items-center gap-3.5 h-20 px-6 border-b border-white/20 relative z-10'
        })" class="bg-[#1e40af]/40 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-lg transition-transform duration-300">
                    <i class="pi pi-shield text-[#2563eb] text-lg"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-lg leading-none tracking-tight">ALCESS</span>
                    <span class="text-[10px] font-bold text-blue-100 uppercase tracking-[0.2em] mt-1 opacity-80">Admin Core</span>
                </div>
            </div>
            
            <!-- Mobile Toggle -->
            <button 
                v-if="!isDesktop"
                @click="emit('closeSidebar')"
                class="p-2 rounded-xl hover:bg-white/15 transition-all text-white active:scale-95"
            >
                <i class="pi pi-times text-lg"></i>
            </button>
        </div>

        <!-- Scrollable Navigation Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar py-6 relative z-10">
            <div class="flex flex-col gap-6 px-4">
                
                <!-- Main Dashboard Section -->
                <nav>
                    <p class="px-3 text-[10px] font-extrabold text-blue-100/60 uppercase tracking-[0.15em] mb-3">Core Monitor</p>
                    <div class="space-y-1">
                        <SideBarButton 
                            icon="pi pi-home" 
                            label="Dashboard Overview" 
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
                </nav>

                <!-- Reports Section -->
                <nav>
                    <p class="px-3 text-[10px] font-extrabold text-blue-100/60 uppercase tracking-[0.15em] mb-3 pt-2">Analytics</p>
                    <div class="space-y-1">
                        <SideBarButton 
                            icon="pi pi-chart-line" 
                            label="Sales & Revenue" 
                            to="admin.dashboard" 
                            hash="#sales-revenue-section"
                        />
                        <SideBarButton 
                            icon="pi pi-exclamation-circle" 
                            label="Stock Alerts" 
                            to="admin.dashboard" 
                            hash="#nearly-out-of-stock-section"
                        />
                         <SideBarButton 
                            icon="pi pi-star" 
                            label="Best Selling" 
                            to="admin.dashboard" 
                            hash="#best-selling-products-section"
                        />
                        <SideBarButton 
                            icon="pi pi-arrow-right-arrow-left" 
                            label="Stock Movement" 
                            to="admin.dashboard" 
                            hash="#inventory-movement-section"
                        />
                    </div>
                </nav>

                <!-- Management Section -->
                <nav>
                    <p class="px-3 text-[10px] font-extrabold text-blue-100/60 uppercase tracking-[0.15em] mb-3 pt-2">Management</p>
                    <div class="space-y-1">
                        <SideBarButton icon="pi pi-shopping-cart" label="Order Handling" to="admin.order.index" />
                        <SideBarButton icon="pi pi-users" label="Customer Base" to="admin.customer.index" />
                        <SideBarButton icon="pi pi-box" label="Product Catalog" to="admin.product.index" />
                        <SideBarButton icon="pi pi-tags" label="Categories" to="admin.category.index" />
                        <SideBarButton icon="pi pi-ticket" label="Unit Specs" to="admin.unit.index" />
                        <SideBarButton icon="pi pi-calculator" label="Point of Sale" to="admin.pos.index" />
                    </div>
                </nav>

                <!-- System Section -->
                <nav>
                    <p class="px-3 text-[10px] font-extrabold text-blue-100/60 uppercase tracking-[0.15em] mb-3 pt-2">Infrastructure</p>
                    <div class="space-y-1">
                        <SideBarButton icon="pi pi-users" label="Staff Accounts" to="admin.user.index" />
                        <SideBarButton icon="pi pi-cog" label="System Config" to="admin.settings.index" />
                    </div>
                </nav>
            </div>
        </div>

        <!-- User Profile Area -->
        <div class="p-5 border-t border-white/10 bg-[#1e40af]/30 backdrop-blur-md relative z-10">
            <div class="flex items-center gap-3.5 mb-4 px-1">
                <div class="relative group">
                    <div class="w-11 h-11 rounded-xl bg-white/10 backdrop-blur-xl flex items-center justify-center text-white font-black text-sm shadow-xl border border-white/20 group-hover:scale-105 transition-transform duration-300">
                        {{ userInitials }}
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-[#2563eb] rounded-full shadow-sm"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-bold text-white truncate drop-shadow-sm">{{ userName }}</p>
                    <p class="text-[10px] font-medium text-blue-100/80 truncate uppercase tracking-widest mt-0.5">Administrator</p>
                </div>
            </div>
            
            <button 
                @click="handleLogout" 
                class="w-full flex items-center justify-center gap-2.5 px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all duration-300 text-[13px] font-bold border border-white/5 shadow-inner active:scale-[0.98] group"
            >
                <i class="pi pi-sign-out text-blue-100 group-hover:translate-x-0.5 transition-transform duration-300"></i>
                <span>Sign Out</span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import SideBarButton from "../SideBarButton.vue";
import Page from "@/stores/Page";
import { useResponsive } from "@/composables/useResponsive";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

const emit = defineEmits(['closeSidebar']);
const router = useRouter();
const toast = useToast();
const logoutService = useAxiosUtil();

// Use responsive composable
const { isDesktop, getResponsiveClasses } = useResponsive();

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
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 3px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Ensure SideBarButton works well with our specific blue background */
:deep(.p-router-link-active) {
    background: rgba(255, 255, 255, 0.1) !important;
}
</style>
