<template>
    <form @submit.prevent="handleNext" class="p-6 max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Product Image</h2>
            <p class="text-sm text-gray-600">Upload a main product image (optional)</p>
        </div>

        <!-- Image Upload Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col items-center">
                <MediaUploader
                    v-model="form.product_image"
                    label="Upload Product Image"
                    :aspect-ratio="1"
                />
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between mt-6">
            <Button
                type="button"
                label="Back"
                icon="pi pi-arrow-left"
                class="!bg-gray-500 hover:!bg-gray-600 !text-white"
                @click="$emit('back')"
            />
            <Button
                type="submit"
                label="Next"
                icon="pi pi-arrow-right"
                iconPos="right"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
            />
        </div>
    </form>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import { useToast } from 'vue-toastification';
import MediaUploader from "@/components/common/MediaUploader.vue";

const emit = defineEmits(['next', 'back']);
const toast = useToast();

const form = reactive({
    product_image: null as File | null,
});

const handleNext = () => {
    if (form.product_image) {
        toast.success('Product image saved. Continue to add specifications.');
    }
    emit('next', form.product_image);
};
</script>
