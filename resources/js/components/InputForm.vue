<template>
    <div class="flex flex-col" :class="props.addClass">
        <label 
            v-if="props.tag === 'label'" 
            :for="props.id"
            class="block text-sm font-medium text-gray-700 mb-2"
        >
            {{ props.labelName }}
        </label>
        <span v-else class="block text-sm font-medium text-gray-700 mb-2">
            {{ props.labelName }}
        </span>
        <slot />
        <span
            v-for="(error, index) in props.errors"
            :key="index"
            class="block mt-1 text-sm text-red-600"
        >
            {{ error }}
        </span>
    </div>
</template>
<script setup lang="ts">
interface Props {
    tag: "label" | "span";
    labelName: string;
    id?: string;
    errors: string[];
    addClass?: string;
}
const props = defineProps<Props>();
</script>
