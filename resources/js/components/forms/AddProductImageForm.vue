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
                <!-- Preview -->
                <div v-if="result.blobURL" class="mb-4">
                    <img :src="result.blobURL" alt="Product Preview" class="w-64 h-64 object-cover rounded-lg border-2 border-gray-300" />
                </div>
                
                <!-- Upload Button -->
                <div class="w-full max-w-md">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                    <input
                        ref="uploadInput"
                        type="file"
                        accept="image/*"
                        @change="selectFile"
                        class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-lg file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100
                            cursor-pointer"
                    />
                    <p class="text-xs text-gray-500 mt-2">Recommended: Square image, at least 500x500px</p>
                </div>

                <!-- Clear Button -->
                <button
                    v-if="result.blobURL"
                    type="button"
                    @click="clearImage"
                    class="mt-4 text-sm text-red-600 hover:text-red-700"
                >
                    <i class="pi pi-times mr-1"></i>
                    Remove Image
                </button>
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

        <!-- Cropper Modal -->
        <Dialog
            v-model:visible="showCropperModal"
            modal
            header="Crop Image"
            :dismissableMask="true"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :pt="{
                header: { class: '!bg-blue-600 !text-white' },
                closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <VuePictureCropper
                :boxStyle="{
                    width: '100%',
                    height: '100%',
                    backgroundColor: '#f8f8f8',
                    margin: 'auto',
                }"
                :img="img"
                :options="{
                    viewMode: 1,
                    dragMode: 'crop',
                    aspectRatio: 1,
                }"
            />
            <div class="flex justify-end mt-4">
                <Button
                    type="button"
                    label="Crop Image"
                    icon="pi pi-check"
                    class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                    @click="getCropResult()"
                />
            </div>
        </Dialog>
    </form>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';
import VuePictureCropper, { cropper } from 'vue-picture-cropper';

const emit = defineEmits(['next', 'back']);
const toast = useToast();

const uploadInput = ref<HTMLInputElement | null>(null);
const showCropperModal = ref<boolean>(false);
const img = ref<string>('');

const result = reactive({
    dataURL: '',
    blobURL: '',
});

const form = reactive({
    product_image: null as File | null,
});

const selectFile = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        img.value = String(reader.result);
        showCropperModal.value = true;
        if (!uploadInput.value) return;
        uploadInput.value.value = '';
    };
};

const getCropResult = async () => {
    if (!cropper) return;
    const base64 = cropper.getDataURL();
    const blob: Blob | null = await cropper.getBlob();
    if (!blob) return;
    const file = await cropper.getFile({
        fileName: 'product-image',
    });
    form.product_image = file;
    result.dataURL = base64;
    result.blobURL = URL.createObjectURL(blob);
    showCropperModal.value = false;
};

const clearImage = () => {
    form.product_image = null;
    result.dataURL = '';
    result.blobURL = '';
};

const handleNext = () => {
    toast.success('Product image saved. Continue to add specifications.');
    emit('next', form.product_image);
};
</script>
