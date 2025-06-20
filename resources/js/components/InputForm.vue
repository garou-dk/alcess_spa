<template>
    <div class="flex flex-col gap-1" :class="props.addClass">
        <label v-if="props.tag === 'label'" :for="props.id">{{
            props.labelName
        }}</label>
        <span v-else>{{ props.labelName }}</span>
        <slot />
        <span
            v-for="(error, index) in props.errors"
            :key="index"
            class="first-letter-cap text-sm text-red-500"
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
