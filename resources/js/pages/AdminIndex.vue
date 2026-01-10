<template>
    <div class="flex">
        <div
            v-if="!shouldUseMobileDrawer"
            class="fixed left-0 top-0 h-screen z-10 overflow-hidden transition-all duration-300"
            style="background-color: #2563EB;"
            :class="{
                'opacity-100': sideBar,
                'opacity-0': !sideBar,
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
            class="min-h-screen bg-gray-100 transition-all duration-300"
            :class="getMainContentClasses()"
            style="width: 100%; scroll-behavior: smooth;"
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
import { onMounted, provide, ref, computed } from "vue";
import { useResponsive } from "@/composables/useResponsive";

const sideBar = ref<boolean>(false); // Start with false to prevent initial overlay
provide("sideBar", sideBar);

// Use responsive composable
const responsive = useResponsive();
const { isMobile, isTablet, isDesktop } = responsive;

// Extract the function separately to ensure it's available
const getResponsiveClasses = responsive.getResponsiveClasses;

// Computed properties for responsive behavior
const shouldUseMobileDrawer = computed(() => isMobile.value);

const getSidebarStyles = () => {
    if (!sideBar.value) {
        return 'width: 0;';
    }
    
    if (isTablet.value) {
        return 'width: 260px;'; // Tablet width
    }
    return 'width: 288px;'; // Desktop width (original)
};

const getMainContentClasses = () => {
    if (shouldUseMobileDrawer.value || !sideBar.value) {
        return 'ml-0';
    }
    
    if (isTablet.value) {
        return 'ml-65'; // ml-65 = 260px
    }
    return 'ml-72'; // ml-72 = 288px (original desktop)
};

const setSideBar = () => {
    // Always close sidebar on mobile to prevent overlay issues
    if (isMobile.value) {
        sideBar.value = false;
    } else {
        // Only open on tablet/desktop after responsive detection is complete
        sideBar.value = true;
    }
};

onMounted(() => {
    // Add a small delay to ensure responsive detection is complete
    setTimeout(() => {
        setSideBar();
        window.addEventListener("resize", setSideBar);
    }, 100);
});
</script>
