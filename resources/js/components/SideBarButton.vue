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
                    color: isActive ? '#ffffff' : '#e0e7ff',
                    backgroundColor: isActive ? 'rgba(255, 255, 255, 0.12)' : 'transparent',
                    backdropFilter: isActive ? 'blur(4px)' : 'none',
                    borderRadius: isActive ? (isMobile ? '0' : '8px 0 0 8px') : (isMobile ? '0' : '8px'),
                    marginRight: isActive ? (isMobile ? '0' : isTablet ? '-10px' : '-12px') : '0',
                    paddingRight: isActive ? (isMobile ? '1rem' : isTablet ? 'calc(0.75rem + 10px)' : 'calc(1rem + 12px)') : (isMobile ? '1rem' : isTablet ? '0.75rem' : '1rem'),
                    boxShadow: isActive ? '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)' : 'none'
                }"
                @mouseenter="handleMouseEnter"
                @mouseleave="handleMouseLeave"
            >
                <i :class="[props.icon, getResponsiveClasses({
                    mobile: props.isSubItem 
                        ? 'text-xs transition-colors duration-200 opacity-80'
                        : 'text-sm transition-colors duration-200',
                    tablet: props.isSubItem
                        ? 'text-xs transition-colors duration-200 opacity-80'
                        : 'text-sm transition-colors duration-200',
                    desktop: props.isSubItem
                        ? 'text-sm transition-colors duration-200 opacity-80'
                        : 'text-base transition-colors duration-200'
                })]" />
                <span :class="getResponsiveClasses({
                    mobile: props.isSubItem
                        ? 'text-[11px] font-medium'
                        : 'text-[12px] font-semibold',
                    tablet: props.isSubItem
                        ? 'text-[11px] font-medium'
                        : 'text-[12px] font-semibold',
                    desktop: props.isSubItem
                        ? 'text-[12px] font-medium'
                        : 'text-[13px] font-semibold'
                })">{{ props.label }}</span>

                <!-- Badge Support -->
                <div v-if="props.badge !== undefined && props.badge !== null && props.badge !== 0" 
                    class="ml-auto px-1.5 py-0.5 rounded-full text-[9px] font-bold min-w-[17px] h-[17px] flex items-center justify-center shadow-sm"
                    :class="isActive ? 'bg-white text-blue-600' : 'bg-red-500 text-white'"
                >
                    {{ props.badge > 99 ? '99+' : props.badge }}
                </div>
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
    badge?: number | string | null;
}
const props = withDefaults(defineProps<Props>(), {
    hash: '',
    isSubItem: false,
    badge: null
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
        target.style.backgroundColor = 'rgba(255, 255, 255, 0.08)';
        target.style.color = '#ffffff';
        
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
        target.style.color = '#e0e7ff';
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
