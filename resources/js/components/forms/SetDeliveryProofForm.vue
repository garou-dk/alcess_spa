<template>
    <form @submit.prevent="handleSubmit">
        <div class="mb-4">
            <p class="text-sm text-gray-700 mb-4">
                Upload proof of preparation before shipping. You can upload up to 3 images and/or 1 video (max 15 seconds).
            </p>
            
            <!-- Image Upload -->
            <div class="mb-4">
                <MediaUploader
                    v-model="selectedImages"
                    multiple
                    :max-files="3"
                    :aspect-ratio="1"
                    label="Upload Images (Max 3)"
                    accept="image/*"
                />
                <div v-if="errors.images.length > 0" class="text-xs text-red-600 mt-1">
                    {{ errors.images[0] }}
                </div>
            </div>

            <!-- Video Upload -->
            <div class="mb-4">
                <MediaUploader
                    v-model="selectedVideo"
                    :allow-crop="false"
                    label="Upload Video (Max 15s)"
                    accept="video/*"
                    icon="pi pi-video"
                />
                <div v-if="errors.video.length > 0" class="text-xs text-red-600 mt-1">
                    {{ errors.video[0] }}
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
import MediaUploader from "@/components/common/MediaUploader.vue";

interface Props {
    order: IOrder | null;
}

const props = defineProps<Props>();
const emit = defineEmits(['cb']);
const toast = useToast();

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

// Note: MediaUploader handles file selection update via v-model

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
