<template>
    <div :class="getResponsiveClasses({
        mobile: 'px-0 py-0.5 relative',
        tablet: 'px-2 py-0.5 relative',
        desktop: props.isSubItem ? 'px-0 py-1 relative' : 'px-3 py-1 relative'
    })">
        <RouterLink :to="{ name: props.to, hash: props.hash }">
            <div
                :class="getResponsiveClasses({
                    mobile: props.isSubItem 
                        ? 'group flex items-center gap-3 px-6 py-2.5 transition-all duration-300 ease-in-out cursor-pointer relative rounded-none w-full'
                        : 'group flex items-center gap-3 px-4 py-3 transition-all duration-300 ease-in-out cursor-pointer relative rounded-none w-full',
                    tablet: props.isSubItem
                        ? 'group flex items-center gap-3 px-5 py-2 transition-all duration-300 ease-in-out cursor-pointer relative'
                        : 'group flex items-center gap-3 px-3 py-2.5 transition-all duration-300 ease-in-out cursor-pointer relative',
                    desktop: props.isSubItem
                        ? 'group flex items-center gap-4 px-4 py-2.5 transition-all duration-300 ease-in-out cursor-pointer relative'
                        : 'group flex items-center gap-4 px-4 py-3 transition-all duration-300 ease-in-out cursor-pointer relative'
                })"
                :style="{
                    color: isActive ? '#2563eb' : '#000000',
                    backgroundColor: isActive ? '#eff6ff' : 'transparent',
                    boxShadow: isActive ? 'inset 4px 0 0 0 #2563eb' : 'none',
                    borderRadius: isActive ? (isMobile ? '0' : '0 8px 8px 0') : (isMobile ? '0' : '8px'),
                    marginRight: isActive ? '0' : '0',
                }"
                @mouseenter="handleMouseEnter"
                @mouseleave="handleMouseLeave"
            >
                <i :class="[props.icon, getResponsiveClasses({
                    mobile: props.isSubItem 
                        ? 'text-sm transition-colors duration-200'
                        : 'text-base transition-colors duration-200',
                    tablet: props.isSubItem
                        ? 'text-sm transition-colors duration-200'
                        : 'text-base transition-colors duration-200',
                    desktop: props.isSubItem
                        ? 'text-sm transition-colors duration-200'
                        : 'text-base transition-colors duration-200'
                })]" />
                <span :class="getResponsiveClasses({
                    mobile: props.isSubItem
                        ? 'text-xs font-black'
                        : 'text-sm font-black',
                    tablet: props.isSubItem
                        ? 'text-xs font-black'
                        : 'text-sm font-black',
                    desktop: props.isSubItem
                        ? 'text-[13px] font-black'
                        : 'text-[14px] font-black'
                })">{{ props.label }}</span>
            </div>
        </RouterLink>
    </div>
</template>
<script setup lang="ts">
import { useRoute } from "vue-router";
import { computed } from "vue";
import { useResponsive } from "@/composables/useResponsive";

interface Props {
    icon: string;
    label: string;
    to: string;
    hash?: string;
    isSubItem?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    hash: '',
    isSubItem: false
});
const route = useRoute();

// Use responsive composable
const responsive = useResponsive();
const { 
    isMobile, 
    isTablet, 
    isDesktop 
} = responsive;

// Extract the function separately to ensure it's available
const getResponsiveClasses = responsive.getResponsiveClasses;

const isActive = computed(() => {
    if (props.hash) {
        return route.name === props.to && route.hash === props.hash;
    }
    return route.name === props.to;
});

const handleMouseEnter = (event: MouseEvent) => {
    if (!isActive.value) {
        const target = event.currentTarget as HTMLElement;
        target.style.backgroundColor = '#f8fafc';
        target.style.color = '#2563eb';
    }
};

const handleMouseLeave = (event: MouseEvent) => {
    if (!isActive.value) {
        const target = event.currentTarget as HTMLElement;
        target.style.backgroundColor = 'transparent';
        target.style.color = '#000000';
    }
};
</script>
