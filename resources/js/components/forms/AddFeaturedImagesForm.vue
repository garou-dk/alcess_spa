<template>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Featured Images</h2>
            <p class="text-sm text-gray-600">Add up to 6 featured images for your product (optional)</p>
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
        </div>

        <!-- Images Grid -->
        <div class="flex flex-wrap gap-4 mb-6">
            <div
                v-for="(image, index) in imagePreviews"
                :key="index"
                class="relative group"
            >
                <img
                    :src="image"
                    alt="Featured Image"
                    class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300"
                />
                <button
                    type="button"
                    @click="removeImage(index)"
                    class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                >
                    <i class="pi pi-times text-xs"></i>
                </button>
            </div>

            <!-- Add Image Button -->
            <label
                v-if="featuredImages.length < 6"
                class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors"
            >
                <i class="pi pi-plus text-2xl text-gray-400 mb-1"></i>
                <span class="text-xs text-gray-500">Add Image</span>
                <input
                    ref="uploadInput"
                    type="file"
                    accept="image/*"
                    @change="handleImageSelect"
                    class="hidden"
                />
            </label>
        </div>

        <div v-if="featuredImages.length === 0" class="text-center py-8 text-gray-500">
            <i class="pi pi-images text-4xl mb-2"></i>
            <p>No featured images added yet</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between mt-6 pt-6 border-t border-gray-200">
            <Button
                type="button"
                label="Back"
                icon="pi pi-arrow-left"
                class="!bg-gray-500 hover:!bg-gray-600 !text-white"
                @click="$emit('back')"
            />
            <Button
                type="button"
                label="Save Product"
                icon="pi pi-check"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white px-6"
                :loading="isSaving"
                @click="handleSaveProduct"
            />
        </div>

        <!-- Cropper Modal -->
        <Dialog
            v-model:visible="showCropperModal"
            modal
            header="Crop Featured Image"
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
    </div>
</template>

<script setup lang="ts">
import { inject, ref } from 'vue';
import { useToast } from 'vue-toastification';
import useAxiosUtil from '@/utils/AxiosUtil';
import { ProductInterface } from '@/interfaces/ProductInterface';
import VuePictureCropper, { cropper } from 'vue-picture-cropper';

const emit = defineEmits(['save', 'back']);
const toast = useToast();

const tempProductData = inject<any>('tempProductData');
const tempProductImage = inject<any>('tempProductImage');
const tempSpecifications = inject<any>('tempSpecifications');
const tempFeaturedImages = inject<any>('tempFeaturedImages');

const featuredImages = ref<File[]>(tempFeaturedImages?.value || []);
const imagePreviews = ref<string[]>([]);
const isSaving = ref(false);

const submitService = useAxiosUtil<FormData, ProductInterface>();

const showCropperModal = ref<boolean>(false);
const img = ref<string>('');
const uploadInput = ref<HTMLInputElement | null>(null);

const handleImageSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (!file) return;
    
    const remainingSlots = 6 - featuredImages.value.length;
    if (remainingSlots <= 0) {
        toast.warning('Maximum 6 images allowed');
        return;
    }
    
    // Open cropper for this image
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        img.value = String(reader.result);
        showCropperModal.value = true;
        if (target) {
            target.value = '';
        }
    };
};

const getCropResult = async () => {
    if (!cropper) return;
    const blob: Blob | null = await cropper.getBlob();
    if (!blob) return;
    const file = await cropper.getFile({
        fileName: `featured-image-${Date.now()}`,
    });
    
    featuredImages.value.push(file);
    
    // Create preview
    const previewURL = URL.createObjectURL(blob);
    imagePreviews.value.push(previewURL);
    
    showCropperModal.value = false;
    toast.success('Image added');
};

const removeImage = (index: number) => {
    featuredImages.value.splice(index, 1);
    imagePreviews.value.splice(index, 1);
    toast.success('Image removed');
};

const handleSaveProduct = async () => {
    if (!tempProductData?.value) {
        toast.error('Product information is missing');
        return;
    }
    
    isSaving.value = true;
    
    try {
        // Create FormData to handle file uploads
        const formData = new FormData();
        
        // Add product basic info
        Object.keys(tempProductData.value).forEach(key => {
            if (tempProductData.value[key] !== null && tempProductData.value[key] !== undefined) {
                formData.append(key, tempProductData.value[key]);
            }
        });
        
        // Add product image if exists
        if (tempProductImage?.value) {
            formData.append('product_image', tempProductImage.value);
        }
        
        // Add specifications as JSON
        if (tempSpecifications?.value && tempSpecifications.value.length > 0) {
            formData.append('specifications', JSON.stringify(tempSpecifications.value));
        }
        
        // Add featured images
        featuredImages.value.forEach((image, index) => {
            formData.append(`featured_images[${index}]`, image);
        });
        
        // Submit everything to server
        await submitService.postFormData('admin/products/create-with-media', formData).then(() => {
            if (submitService.request.status === 200 && submitService.request.data) {
                toast.success('Product created successfully!');
                emit('save', submitService.request.data);
            } else {
                // Check for specific error messages
                const errorMessage = submitService.request.message ?? 'Failed to create product';
                
                if (errorMessage.includes('Duplicate entry') && errorMessage.includes('sku')) {
                    toast.error('This SKU already exists. Please use a different SKU.');
                } else {
                    toast.error(errorMessage);
                }
            }
        });
    } catch (error) {
        toast.error('An error occurred while creating the product');
    } finally {
        isSaving.value = false;
    }
};
</script>
