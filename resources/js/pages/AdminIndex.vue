<template>
    <div class="flex h-screen overflow-hidden bg-gray-50">
        <!-- Sidebar for Desktop -->
        <aside 
            v-if="!isMobile" 
            :class="[
                'h-full flex-shrink-0 bg-white border-r border-gray-100 transition-all duration-300 ease-in-out z-40 overflow-hidden',
                sideBar ? 'w-72' : 'w-0'
            ]"
        >
             <AdminSideBar />
        </aside>

        <!-- Sidebar for Mobile (Drawer) -->
        <Drawer 
            v-if="isMobile"
            v-model:visible="sideBar" 
            :modal="true"
            class="!w-72 !bg-white !border-0"
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
            
            <main class="w-full min-h-screen p-4 md:p-6">
                <!-- Wrapper for content to allow spacing from navbar -->
                <div class="container mx-auto">
                    <RouterView />
                </div>
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
        sideBar.value = true; // Open by default on desktop
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
