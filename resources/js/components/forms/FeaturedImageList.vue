<template>
    <div>
        <div class="p-5">
            <div class="flex flex-wrap justify-center border">
                <div
                    class="p-2"
                    v-for="(image, index) in props.data.featured_images"
                    :key="index"
                >
                    <img
                        class="h-28 w-28 rounded border border-gray-500"
                        :src="
                            UrlUtil.getBaseAppUrl(
                                `storage/images/thumbnail/${image.thumbnail}`,
                            )
                        "
                        :key="index"
                        alt="Image Thumbnail"
                    />
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
const emit = defineEmits(["cb"]);

const setDataCb = (data: ProductInterface) => {
    emit("cb", data);
};
</script>
