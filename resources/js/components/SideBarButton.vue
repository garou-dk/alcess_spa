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
                    color: isActive ? '#ffffff' : '#64748b',
                    backgroundColor: isActive ? '#2563eb' : 'transparent',
                    borderRadius: isActive ? (isMobile ? '0' : '8px 0 0 8px') : (isMobile ? '0' : '8px'),
                    marginRight: isActive ? (isMobile ? '0' : isTablet ? '-10px' : '-12px') : '0',
                    paddingRight: isActive ? (isMobile ? '1rem' : isTablet ? 'calc(0.75rem + 10px)' : 'calc(1rem + 12px)') : (isMobile ? '1rem' : isTablet ? '0.75rem' : '1rem')
                }"
                @mouseenter="handleMouseEnter"
                @mouseleave="handleMouseLeave"
            >
                <i :class="[props.icon, getResponsiveClasses({
                    mobile: props.isSubItem 
                        ? 'text-sm transition-colors duration-200 opacity-90'
                        : 'text-base transition-colors duration-200',
                    tablet: props.isSubItem
                        ? 'text-sm transition-colors duration-200 opacity-90'
                        : 'text-base transition-colors duration-200',
                    desktop: props.isSubItem
                        ? 'text-base transition-colors duration-200 opacity-90'
                        : 'text-lg transition-colors duration-200'
                })]" />
                <span :class="getResponsiveClasses({
                    mobile: props.isSubItem
                        ? 'text-xs font-semibold opacity-95'
                        : 'text-sm font-semibold',
                    tablet: props.isSubItem
                        ? 'text-xs font-semibold opacity-95'
                        : 'text-sm font-semibold',
                    desktop: props.isSubItem
                        ? 'text-sm font-semibold opacity-95'
                        : 'text-base font-semibold'
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
        
        if (isMobile.value) {
            target.style.borderRadius = '0';
            target.style.marginRight = '0';
            target.style.paddingRight = '1rem';
        } else if (isTablet.value) {
            target.style.borderRadius = '8px 0 0 8px';
            target.style.marginRight = '-10px';
            target.style.paddingRight = 'calc(0.75rem + 10px)';
        } else {
            target.style.borderRadius = '8px 0 0 8px';
            target.style.marginRight = '-12px';
            target.style.paddingRight = 'calc(1rem + 12px)';
        }
    }
};

const handleMouseLeave = (event: MouseEvent) => {
    if (!isActive.value) {
        const target = event.currentTarget as HTMLElement;
        target.style.backgroundColor = 'transparent';
        target.style.color = '#64748b';
        target.style.marginRight = '0';
        
        if (isMobile.value) {
            target.style.borderRadius = '0';
            target.style.paddingRight = '1rem';
        } else if (isTablet.value) {
            target.style.borderRadius = '8px';
            target.style.paddingRight = '0.75rem';
        } else {
            target.style.borderRadius = '8px';
            target.style.paddingRight = '1rem';
        }
    }
};
</script>
