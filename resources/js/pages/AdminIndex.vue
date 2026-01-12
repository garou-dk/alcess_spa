<template>
    <div class="flex overflow-x-hidden">
        <div
            v-if="!shouldUseMobileDrawer"
            class="fixed left-0 top-0 h-screen z-10 overflow-hidden transition-all duration-300"
            style="background-color: #2563EB;"
            :class="{
                'opacity-100': sideBar,
                'opacity-0 pointer-events-none': !sideBar,
            }"
            :style="getSidebarStyles()"
        >
            <AdminSideBar />
        </div>
        <Drawer 
            v-if="shouldUseMobileDrawer" 
            v-model:visible="sideBar" 
            position="left"
            :showCloseIcon="false"
            :pt="{
                root: { 
                    class: getResponsiveClasses({
                        mobile: 'w-full max-w-[280px]',
                        tablet: 'w-full max-w-[300px]',
                        desktop: 'w-full max-w-xs'
                    })
                },
                content: { 
                    class: 'p-0 h-full m-0',
                    style: 'padding: 0 !important; margin: 0 !important; height: 100vh !important;'
                },
                header: { 
                    class: 'hidden',
                    style: 'display: none !important; height: 0 !important; padding: 0 !important; margin: 0 !important;'
                },
                closeButton: {
                    class: 'hidden',
                    style: 'display: none !important;'
                },
                mask: { 
                    class: 'bg-black bg-opacity-50',
                    style: 'z-index: 1000;'
                }
            }"
        >
            <AdminSideBar />
        </Drawer>
        <div 
            id="main-content"
            class="min-h-screen bg-gray-100 transition-all duration-300 overflow-x-hidden"
            :class="getMainContentClasses()"
            :style="getMainContentStyles()"
        >
            <AdminNavBar />
            <div :class="responsive.getResponsivePadding()">
                <RouterView />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import AdminNavBar from "@/components/admin/AdminNavBar.vue";
import AdminSideBar from "@/components/admin/AdminSideBar.vue";
import { onMounted, onUnmounted, provide, ref, computed, watch } from "vue";
import { useResponsive } from "@/composables/useResponsive";

// Determine initial sidebar state based on screen width synchronously
const getInitialSidebarState = () => {
    if (typeof window !== 'undefined') {
        return window.innerWidth >= 1024; // Desktop: show sidebar
    }
    return false;
};

const sideBar = ref<boolean>(getInitialSidebarState());
const isLayoutReady = ref<boolean>(false);
provide("sideBar", sideBar);

// Use responsive composable
const responsive = useResponsive();
const { isMobile, isTablet, isDesktop } = responsive;

// Extract the function separately to ensure it's available
const getResponsiveClasses = responsive.getResponsiveClasses;

// Computed properties for responsive behavior
const shouldUseMobileDrawer = computed(() => isMobile.value);

const getSidebarWidth = () => {
    if (!sideBar.value) return 0;
    if (isTablet.value) return 260;
    return 288; // Desktop width
};

const getSidebarStyles = () => {
    const width = getSidebarWidth();
    return `width: ${width}px;`;
};

const getMainContentClasses = () => {
    // Use flex-based layout for more reliable positioning
    const baseClasses = 'flex-1 min-w-0';
    
    if (shouldUseMobileDrawer.value || !sideBar.value) {
        return `${baseClasses} ml-0`;
    }
    
    if (isTablet.value) {
        return `${baseClasses} ml-[260px]`;
    }
    return `${baseClasses} ml-[288px]`;
};

const getMainContentStyles = () => {
    return 'width: 100%; scroll-behavior: smooth;';
};

const setSideBar = () => {
    // Always close sidebar on mobile to prevent overlay issues
    if (isMobile.value) {
        sideBar.value = false;
    } else {
        // Open sidebar on tablet/desktop
        sideBar.value = true;
    }
};

// Watch for responsive changes and adjust sidebar accordingly
watch([isMobile, isTablet, isDesktop], () => {
    setSideBar();
}, { immediate: false });

onMounted(() => {
    // Set initial state after mount to ensure proper SSR/CSR consistency
    requestAnimationFrame(() => {
        setSideBar();
        isLayoutReady.value = true;
    });
    
    window.addEventListener("resize", setSideBar);
});

onUnmounted(() => {
    window.removeEventListener("resize", setSideBar);
});
</script>
