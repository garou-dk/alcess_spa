<template>
    <div>
        <div class="p-5">
            <div v-if="props.data.featured_images.length > 0" class="flex flex-wrap border justify-center">
                <div class="p-2" v-for="(image, index) in props.data.featured_images">
                    <img class="h-28 w-28 border border-gray-500 rounded" :src="UrlUtil.getBaseAppUrl(`storage/images/thumbnail/${image.thumbnail}`)" :key="index" alt="Image Thumbnail"></img>
                </div>
                <div class="p-2">
                    <FeaturedImageForm @cb="setDataCb" :data="props.data" />
                </div>
            </div>
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
import UrlUtil from "@/utils/UrlUtil";

interface Props {
    data: ProductInterface;
}

const props = defineProps<Props>();
const emit = defineEmits(['cb']);

const setDataCb = (data: ProductInterface) => {
    emit('cb', data);
}
</script>
