<template>
    <form @submit.prevent="handleSubmit">
        <div class="mb-4">
            <p class="text-sm text-gray-700 mb-4">
                Upload proof of preparation before shipping. You can upload up to 3 images and/or 1 video (max 15 seconds).
            </p>
            
            <!-- Image Upload -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Images (Max 3)
                </label>
                <input
                    type="file"
                    ref="imageInput"
                    @change="handleImageUpload"
                    accept="image/*"
                    multiple
                    class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100"
                />
                <p class="text-xs text-gray-500 mt-1">{{ selectedImages.length }}/3 images selected</p>
                <div v-if="errors.images.length > 0" class="text-xs text-red-600 mt-1">
                    {{ errors.images[0] }}
                </div>
            </div>

            <!-- Video Upload -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Video (Max 15 seconds)
                </label>
                <input
                    type="file"
                    ref="videoInput"
                    @change="handleVideoUpload"
                    accept="video/*"
                    class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-purple-50 file:text-purple-700
                        hover:file:bg-purple-100"
                />
                <p v-if="selectedVideo" class="text-xs text-gray-500 mt-1">Video selected: {{ selectedVideo.name }}</p>
                <div v-if="errors.video.length > 0" class="text-xs text-red-600 mt-1">
                    {{ errors.video[0] }}
                </div>
            </div>

            <!-- Preview Section -->
            <div v-if="selectedImages.length > 0 || selectedVideo" class="mb-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                <div class="flex flex-wrap gap-2">
                    <div v-for="(img, index) in selectedImages" :key="index" class="relative">
                        <img :src="getImagePreview(img)" class="w-20 h-20 object-cover rounded" />
                        <button
                            type="button"
                            @click="removeImage(index)"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                        >
                            ×
                        </button>
                    </div>
                    <div v-if="selectedVideo" class="relative">
                        <div class="w-20 h-20 bg-purple-100 rounded flex items-center justify-center">
                            <i class="pi pi-video text-purple-600 text-2xl"></i>
                        </div>
                        <button
                            type="button"
                            @click="removeVideo"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                        >
                            ×
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estimated Delivery Date Range -->
            <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm font-semibold text-gray-800 mb-3">Estimated Delivery Date Range*</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <DatePicker
                            v-model="deliveryDateStart"
                            dateFormat="yy-mm-dd"
                            :minDate="new Date()"
                            placeholder="Select start date"
                            fluid
                            showIcon
                            :class="{ '!border-red-500': errors.deliveryDateStart.length > 0 }"
                        />
                        <div v-if="errors.deliveryDateStart.length > 0" class="text-xs text-red-600 mt-1">
                            {{ errors.deliveryDateStart[0] }}
                        </div>
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <DatePicker
                            v-model="deliveryDateEnd"
                            dateFormat="yy-mm-dd"
                            :minDate="deliveryDateStart || new Date()"
                            placeholder="Select end date"
                            fluid
                            showIcon
                            :class="{ '!border-red-500': errors.deliveryDateEnd.length > 0 }"
                        />
                        <div v-if="errors.deliveryDateEnd.length > 0" class="text-xs text-red-600 mt-1">
                            {{ errors.deliveryDateEnd[0] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <Button
                label="Cancel"
                type="button"
                severity="secondary"
                outlined
                @click="emit('cb')"
            />
            <Button
                label="Confirm Delivery"
                type="submit"
                class="!bg-blue-600 hover:!bg-blue-700"
                :loading="isSubmitting"
            />
        </div>
    </form>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { IOrder } from '@/interfaces/IOrder';
import { useToast } from 'vue-toastification';
import useAxiosUtil from '@/utils/AxiosUtil';

interface Props {
    order: IOrder | null;
}

const props = defineProps<Props>();
const emit = defineEmits(['cb']);
const toast = useToast();

const imageInput = ref<HTMLInputElement | null>(null);
const videoInput = ref<HTMLInputElement | null>(null);
const selectedImages = ref<File[]>([]);
const selectedVideo = ref<File | null>(null);
const deliveryDateStart = ref<Date | null>(null);
const deliveryDateEnd = ref<Date | null>(null);
const isSubmitting = ref(false);

const errors = reactive({
    images: [] as string[],
    video: [] as string[],
    deliveryDateStart: [] as string[],
    deliveryDateEnd: [] as string[],
});

const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = Array.from(target.files || []);
    
    errors.images = [];
    
    if (files.length > 3) {
        errors.images.push('Maximum 3 images allowed');
        return;
    }
    
    selectedImages.value = files;
};

const handleVideoUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    errors.video = [];
    
    if (file) {
        // Check video duration (will be validated on backend)
        selectedVideo.value = file;
    }
};

const getImagePreview = (file: File) => {
    return URL.createObjectURL(file);
};

const removeImage = (index: number) => {
    selectedImages.value.splice(index, 1);
};

const removeVideo = () => {
    selectedVideo.value = null;
    if (videoInput.value) {
        videoInput.value.value = '';
    }
};

const submitService = useAxiosUtil<FormData, IOrder>();

const handleSubmit = async () => {
    errors.images = [];
    errors.video = [];
    errors.deliveryDateStart = [];
    errors.deliveryDateEnd = [];
    
    // Validate delivery proof
    if (selectedImages.value.length === 0 && !selectedVideo.value) {
        toast.error('Please upload at least one image or video');
        return;
    }
    
    // Validate delivery dates
    if (!deliveryDateStart.value) {
        errors.deliveryDateStart.push('Start date is required');
        toast.error('Please select delivery date range');
        return;
    }
    
    if (!deliveryDateEnd.value) {
        errors.deliveryDateEnd.push('End date is required');
        toast.error('Please select delivery date range');
        return;
    }
    
    if (deliveryDateEnd.value < deliveryDateStart.value) {
        errors.deliveryDateEnd.push('End date must be after start date');
        toast.error('End date must be after start date');
        return;
    }
    
    const formData = new FormData();
    
    // Add images
    selectedImages.value.forEach((image, index) => {
        formData.append(`images[${index}]`, image);
    });
    
    // Add video
    if (selectedVideo.value) {
        formData.append('video', selectedVideo.value);
    }
    
    // Add delivery dates
    formData.append('estimated_delivery_date_start', deliveryDateStart.value.toISOString().split('T')[0]);
    formData.append('estimated_delivery_date_end', deliveryDateEnd.value.toISOString().split('T')[0]);
    
    isSubmitting.value = true;
    
    try {
        console.log('📤 Submitting delivery proof:', {
            orderId: props.order?.order_id,
            imagesCount: selectedImages.value.length,
            hasVideo: !!selectedVideo.value,
            formData: Array.from(formData.entries())
        });
        
        await submitService.postFormData(`admin/orders/set-delivery/${props.order?.order_id}`, formData);
        
        console.log('📥 Response:', {
            status: submitService.request.status,
            data: submitService.request.data,
            message: submitService.request.message
        });
        
        if (submitService.request.status === 200) {
            toast.success('Order set to delivery successfully');
            emit('cb');
        } else {
            console.error('❌ Failed to set delivery:', submitService.request);
            toast.error(submitService.request.message ?? 'Failed to set delivery');
        }
    } catch (error) {
        console.error('❌ Error submitting delivery proof:', error);
        toast.error('An error occurred');
    } finally {
        isSubmitting.value = false;
    }
};
</script>
