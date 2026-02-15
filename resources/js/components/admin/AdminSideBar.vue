<template>
    <div class="h-full flex flex-col bg-[#2563eb] text-white shadow-xl transition-all duration-300 relative overflow-hidden">
        <!-- Sidebar Header -->
        <div :class="getResponsiveClasses({
            mobile: 'flex items-center justify-between h-16 px-4 border-b border-white/10 relative z-10',
            tablet: 'flex items-center justify-between h-20 px-5 border-b border-white/10 relative z-10',
            desktop: 'flex items-center gap-3 h-20 px-6 border-b border-white/10 relative z-10'
        })">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center shadow-sm">
                    <i class="pi pi-th-large text-white text-lg"></i>
                </div>
                <div class="flex flex-col">
                    <h1 class="font-bold text-lg tracking-wide uppercase leading-none">Alcess</h1>
                    <span class="text-[10px] text-blue-100 font-medium tracking-widest mt-1">ADMIN PANEL</span>
                </div>
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
                
                <!-- Main Dashboard -->
                <nav class="mb-2">
                    <p class="px-3 text-[11px] font-bold text-blue-200 uppercase tracking-wider mb-2 opacity-70">Main Dashboard</p>
                    <SideBarButton icon="pi pi-home" label="Dashboard" to="admin.dashboard" />
                    <SideBarButton icon="pi pi-calculator" label="POS" to="admin.pos.index" />
                    <SideBarButton icon="pi pi-shopping-bag" label="Orders" to="admin.order.index" />
                </nav>

                <!-- Inventory -->
                <nav class="mb-2 pt-2 border-t border-white/10">
                    <p class="px-3 text-[11px] font-bold text-blue-200 uppercase tracking-wider mb-2 mt-1 opacity-70">Inventory</p>
                    <SideBarButton icon="pi pi-box" label="Products" to="admin.product.index" />
                    <SideBarButton icon="pi pi-tags" label="Categories" to="admin.category.index" />
                    <SideBarButton icon="pi pi-ticket" label="Units" to="admin.unit.index" />
                </nav>

                <!-- Sales Record -->
                <nav class="mb-2 pt-2 border-t border-white/10">
                    <p class="px-3 text-[11px] font-bold text-blue-200 uppercase tracking-wider mb-2 mt-1 opacity-70">Sales Record</p>
                    <SideBarButton icon="pi pi-user-plus" label="Walk-in Sales" to="admin.sales.walkin" />
                    <SideBarButton icon="pi pi-globe" label="Online Sales" to="admin.sales.online" />
                </nav>

                <!-- System & Management -->
                <nav class="mb-2 pt-2 border-t border-white/10">
                    <p class="px-3 text-[11px] font-bold text-blue-200 uppercase tracking-wider mb-2 mt-1 opacity-70">Management</p>
                    <SideBarButton icon="pi pi-users" label="Users" to="admin.users.index" />
                    <SideBarButton icon="pi pi-chart-bar" label="Reports" to="admin.report.index" />
                    <SideBarButton icon="pi pi-star" label="Ratings" to="admin.rate.index" />
                    <SideBarButton icon="pi pi-cog" label="Settings" to="admin.settings.index" />
                </nav>
            </div>
        </div>

        <!-- User Information & Logout -->
        <div class="p-4 border-t border-white/10 bg-white/5 backdrop-blur-md relative z-10">
            <div class="flex items-center gap-3 mb-3 px-2">
                <div class="w-10 h-10 rounded-full bg-[#2563eb] flex items-center justify-center text-white font-bold border-2 border-white/20 shadow-sm">
                    {{ userInitials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ userName }}</p>
                    <p class="text-[11px] text-blue-100 truncate opacity-80 capitalize">{{ userRole }}</p>
                </div>
            </div>
            
            <button 
                @click="handleLogout" 
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-all duration-200 text-sm font-medium border border-white/5 active:scale-95 group"
            >
                <i class="pi pi-sign-out text-white group-hover:translate-x-1 transition-transform"></i>
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

const userName = computed(() => Page.user?.full_name || 'Admin User');
const userRole = computed(() => Page.user?.role?.role_name || 'Administrator');

const userInitials = computed(() => {
    const name = userName.value;
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
});

const handleLogout = async () => {
    await logoutService.post("logout", null).then(() => {
        Page.user = null;
        router.push({ name: "admin.login" });
        toast.success("Logged out successfully");
    });
};
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

/* Ensure SideBarButton has consistent hover/active behavior for this blue theme */
:deep(.router-link-active > div) {
    background-color: white !important;
    color: #2563eb !important;
}
</style>
