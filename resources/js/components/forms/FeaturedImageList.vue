<template>
    <div>
        <div class="p-5">
            <div v-if="props.data.featured_images.length > 0"></div>
            <FeaturedImageForm :data="props.data" />
        </div>
        <p
            v-if="props.data.featured_images.length == 0"
            class="mb-5 text-center"
        >
            No Featured Image
        </p>
    </div>
</template>
<script setup lang="ts">
import { ProductInterface } from "@/interfaces/ProductInterface";
import FeaturedImageForm from "@/components/forms/FeaturedImageForm.vue";

interface Props {
    data: ProductInterface;
}

const props = defineProps<Props>();
// const emit = defineEmits(['cb']);
</script>
