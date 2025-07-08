<template>
    <div class="px-3 py-1">
        <RouterLink :to="{ name: props.to }">
            <div
                class="flex items-center gap-5 rounded-lg px-4 py-2 hover:bg-sky-800! hover:text-white"
                :class="{
                    'bg-sky-800! text-white': route.name == props.to,
                }"
            >
                <i :class="props.icon" class="text-2xl!" />
                <span class="text-lg font-semibold">{{ props.label }}</span>
            </div>
        </RouterLink>
    </div>
</template>
<script setup lang="ts">
import { useRoute } from "vue-router";

interface Props {
    icon: string;
    label: string;
    to: string;
}
const props = defineProps<Props>();
const route = useRoute();
</script>
