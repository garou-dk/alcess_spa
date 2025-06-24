<template>
    <div class="px-3 py-1">
        <button type="button" @click="isActive = !isActive"
            class="flex gap-5 items-center px-4 py-2 hover:bg-sky-800! hover:text-white rounded-lg w-full cursor-pointer">
            <i :class="props.icon" class="text-2xl!" />
            <span class="text-lg font-semibold">{{ props.label }}</span>
            <i v-if="isActive" class="pi pi-chevron-up ml-auto" />
            <i v-else class="pi pi-chevron-down ml-auto" />
        </button>

        <Transition @before-enter="beforeEnter" @enter="enter" @leave="leave">
            <div v-show="isActive" class="ms-1 overflow-hidden">
                <slot />
            </div>
        </Transition>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue';

interface Props {
    icon: string;
    label: string;
    active?: boolean;
}

const props = defineProps<Props>();
const isActive = ref<boolean>(false);

const beforeEnter = (el: Element) => {
    const element = el as HTMLElement;
    element.style.height = '0';
    element.style.opacity = '0';
}

const enter = (el: Element) => {
    const element = el as HTMLElement;
    element.style.transition = 'all 0.3s ease';
    element.style.height = element.scrollHeight + 'px';
    element.style.opacity = '1';
}

const leave = (el: Element) => {
    const element = el as HTMLElement;
    element.style.transition = 'all 0.3s ease';
    element.style.height = '0';
    element.style.opacity = '0';
}

onMounted(() => {
    if (props.active) {
        isActive.value = true;
    }
});
</script>
