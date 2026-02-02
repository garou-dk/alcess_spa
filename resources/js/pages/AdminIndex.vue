<template>
    <div class="flex h-screen overflow-hidden bg-gray-100">
        <!-- Sidebar for Desktop (Always visible) -->
        <div v-if="!isMobile" class="h-full flex-shrink-0 bg-blue-600 transition-all duration-300">
             <AdminSideBar />
        </div>

        <!-- Sidebar for Mobile (Drawer) -->
        <Drawer 
            v-if="isMobile"
            v-model:visible="sideBar" 
            :modal="true"
            class="!w-full !bg-blue-600 !border-0"
            :showCloseIcon="false"
             :pt="{
                root: { class: 'border-none' },
                header: { class: '!hidden' },
                content: { class: '!p-0' }
            }"
        >
            <AdminSideBar />
        </Drawer>
        
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <NavBar mode="admin" />
            
            <main class="w-full h-full p-6">
                <RouterView />
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, provide, ref, computed, watch } from "vue";
import { useResponsive } from "@/composables/useResponsive";
import NavBar from "@/components/NavBar.vue";
import AdminSideBar from "@/components/admin/AdminSideBar.vue";
import Drawer from "primevue/drawer";

// Responsive check
const responsive = useResponsive();
const { isMobile, isTablet, isDesktop } = responsive;

// Sidebar State
const sideBar = ref(false);

// Provide sideBar state to children (NavBar toggle, AdminSideBar close)
provide("sideBar", sideBar);

// Ensure correct initial state
const initSidebar = () => {
    if (!isMobile.value) {
        // On desktop/tablet, AdminSideBar is always visible in the layout,
        // so 'sideBar' ref usually controls the Mobile Drawer visibility.
        // However, NavBar toggle button might toggle this ref.
        // If we want the toggle button to do nothing on desktop OR hide the sidebar:
        // For now, let's assume Sidebar is persistent on Desktop.
        sideBar.value = false; 
    } else {
        sideBar.value = false; // Closed initially on mobile
    }
};

watch(isMobile, () => {
   initSidebar();
});

onMounted(() => {
    initSidebar();
});
</script>
