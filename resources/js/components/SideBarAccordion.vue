<template>
    <div :class="getResponsiveClasses({
        mobile: 'px-0 py-0.5',
        tablet: 'px-2 py-0.5',
        desktop: 'px-3 py-1'
    })">
        <button
            type="button"
            @click="handleHeaderClick"
            :class="getResponsiveClasses({
                mobile: 'flex w-full cursor-pointer items-center gap-3 px-4 py-3 transition-all duration-200 rounded-none',
                tablet: 'flex w-full cursor-pointer items-center gap-3 px-3 py-2.5 transition-all duration-200',
                desktop: 'flex w-full cursor-pointer items-center gap-4 px-4 py-3 transition-all duration-200'
            })"
            style="color: #ffffff; border-radius: 8px; background-color: transparent;"
            @mouseenter="($event.currentTarget as HTMLElement).style.backgroundColor = 'rgba(255, 255, 255, 0.15)'"
            @mouseleave="($event.currentTarget as HTMLElement).style.backgroundColor = 'transparent'"
        >
            <i :class="[props.icon, getResponsiveClasses({
                mobile: 'text-sm',
                tablet: 'text-sm',
                desktop: 'text-base'
            })]" />
            <span :class="getResponsiveClasses({
                mobile: 'text-[11px] font-semibold',
                tablet: 'text-[11px] font-semibold',
                desktop: 'text-[12px] font-semibold'
            })">{{ props.label }}</span>
            <i v-if="isActive" :class="getResponsiveClasses({
                mobile: 'pi pi-chevron-up ml-auto text-[10px]',
                tablet: 'pi pi-chevron-up ml-auto text-[10px]',
                desktop: 'pi pi-chevron-up ml-auto text-[11px]'
            })" />
            <i v-else :class="getResponsiveClasses({
                mobile: 'pi pi-chevron-down ml-auto text-[10px]',
                tablet: 'pi pi-chevron-down ml-auto text-[10px]',
                desktop: 'pi pi-chevron-down ml-auto text-[11px]'
            })" />
        </button>

        <Transition @before-enter="beforeEnter" @enter="enter" @leave="leave">
            <div v-show="isActive" :class="getResponsiveClasses({
                mobile: 'ms-2 overflow-hidden',
                tablet: 'ms-3 overflow-hidden',
                desktop: 'ms-4 overflow-hidden'
            })">
                <slot />
            </div>
        </Transition>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { useResponsive } from "@/composables/useResponsive";

interface Props {
    icon: string;
    label: string;
    active?: boolean;
    clickable?: boolean;
    keepOpen?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    clickable: false,
    keepOpen: false
});
const emit = defineEmits(['header-click']);
const isActive = ref<boolean>(false);

// Use responsive composable
const responsive = useResponsive();
const { 
    isMobile, 
    isTablet, 
    isDesktop 
} = responsive;

// Extract the function separately to ensure it's available
const getResponsiveClasses = responsive.getResponsiveClasses;

const handleHeaderClick = () => {
    if (props.clickable) {
        emit('header-click');
    }
    // Always allow toggling
    isActive.value = !isActive.value;
};

const beforeEnter = (el: Element) => {
    const element = el as HTMLElement;
    element.style.height = "0";
    element.style.opacity = "0";
};

const enter = (el: Element) => {
    const element = el as HTMLElement;
    element.style.transition = "all 0.3s ease";
    element.style.height = element.scrollHeight + "px";
    element.style.opacity = "1";
};

const leave = (el: Element) => {
    const element = el as HTMLElement;
    element.style.transition = "all 0.3s ease";
    element.style.height = "0";
    element.style.opacity = "0";
};

onMounted(() => {
    if (props.active || props.keepOpen) {
        isActive.value = true;
    }
});

// Watch keepOpen and active props to ensure accordion opens when needed
watch(() => props.keepOpen, (newValue) => {
    if (newValue && !isActive.value) {
        isActive.value = true;
    }
});

watch(() => props.active, (newValue) => {
    if (newValue && !isActive.value) {
        isActive.value = true;
    }
});
</script>
