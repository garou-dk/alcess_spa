<template>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Featured Images</h2>
            <p class="text-sm text-gray-600">Manage featured images for your product (optional)</p>
        </div>

        <!-- Image Count Info -->
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="pi pi-info-circle text-blue-600"></i>
                    <span class="text-sm text-blue-800 font-medium">
                        Featured Images: {{ featuredImages.length }} / 6
                    </span>
                </div>
                <span v-if="featuredImages.length >= 6" class="text-xs text-orange-600 font-semibold">
                    Maximum limit reached
                </span>
            </div>
            <p class="text-xs text-gray-600 mt-2">
                Upload images one at a time. Each image will be saved immediately after cropping.
            </p>
        </div>

        <!-- Images Grid -->
        <div class="flex flex-wrap gap-4 mb-6">
            <div
                v-for="(image, index) in featuredImages"
                :key="index"
                class="relative group"
            >
                <img
                    :src="getFeaturedImageUrl(image)"
                    alt="Featured Image"
                    class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300"
                />
                <button
                    type="button"
                    @click="deleteImage(image.featured_image_id)"
                    :disabled="deleteService.request.loading"
                    class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity disabled:opacity-50"
                >
                    <i v-if="deleteService.request.loading && deletingId === image.featured_image_id" class="pi pi-spin pi-spinner text-xs"></i>
                    <i v-else class="pi pi-times text-xs"></i>
                </button>
            </div>

            <!-- Add Image Button -->
            <div v-if="remainingSlots > 0">
                <FeaturedImageForm 
                    @cb="setDataCb" 
                    :data="props.data"
                    :key="formKey"
                />
            </div>
        </div>

        <div v-if="featuredImages.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-gray-200">
            <i class="pi pi-images text-4xl mb-2"></i>
            <p>No featured images added yet</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
            <Button
                type="button"
                label="Next"
                icon="pi pi-arrow-right"
                iconPos="right"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                @click="saveProduct()"
            />
        </div>
    </div>
</template>
<script setup lang="ts">
import { ProductInterface } from "@/interfaces/ProductInterface";
import FeaturedImageForm from "@/components/forms/FeaturedImageForm.vue";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";
import { computed, ref } from "vue";

interface Props {
    data: ProductInterface;
}

const props = defineProps<Props>();
const emit = defineEmits(["cb"]);
const toast = useToast();
const deleteService = useAxiosUtil<null, ProductInterface>();
const deletingId = ref<number | null>(null);
const formKey = ref(0);

const featuredImages = computed(() => {
    // Backend returns 'featured_images' (snake_case) after relationship rename
    return props.data?.featured_images || (props.data as any)?.featuredImages || [];
});

const remainingSlots = computed(() => {
    return 6 - featuredImages.value.length;
});

function getFeaturedImageUrl(image: { thumbnail?: string; featured_image?: string }) {
    const thumb = image.thumbnail ?? (image as any).thumbnail;
    const full = image.featured_image ?? (image as any).featured_image;
    if (thumb) {
        return UrlUtil.getBaseAppUrl(`storage/images/thumbnail/${thumb}`);
    }
    if (full) {
        return UrlUtil.getBaseAppUrl(`storage/images/featured/${full}`);
    }
    return '';
}

const setDataCb = (data: ProductInterface) => {
    formKey.value++; // Force re-render of form to allow another upload
    emit("cb", data, false); // Pass false to prevent dialog from closing
};

const deleteImage = async (imageId: number) => {
    deletingId.value = imageId;
    await deleteService.deleteRequest(`admin/featured-images/${imageId}`, null as any).then(() => {
        if (deleteService.request.status === 200 && deleteService.request.data) {
            toast.success('Image deleted successfully');
            emit("cb", deleteService.request.data, false); // Pass false to prevent dialog from closing
        } else {
            toast.error(deleteService.request.message ?? 'Failed to delete image');
        }
        deletingId.value = null;
    });
};

const saveProduct = () => {
    emit("cb", props.data, true);
};
</script>
