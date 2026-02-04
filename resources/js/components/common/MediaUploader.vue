<template>
    <div class="media-uploader">
        <!-- Input Field (Hidden) -->
        <input
            ref="fileInput"
            type="file"
            :accept="accept"
            :multiple="multiple"
            class="hidden"
            @change="handleFileSelect"
        />

        <!-- Trigger Button / Drop Zone -->
        <div v-if="!hasFiles || multiple" class="mb-3">
             <div 
                v-if="!multiple && !hasFiles"
                @click="triggerSelect"
                class="cursor-pointer border-2 border-dotted border-gray-300 rounded-lg p-6 hover:bg-gray-50 transition-colors text-center"
                :class="{ 'border-red-500': hasError }"
            >
                <div class="flex flex-col items-center gap-2">
                    <i :class="[icon, 'text-2xl text-gray-400']"></i>
                    <span class="text-sm font-medium text-gray-600">{{ label || 'Select File' }}</span>
                    <span class="text-xs text-gray-400">Supported: {{ accept }}</span>
                </div>
            </div>

            <Button
                v-else
                type="button"
                :label="label || 'Add Files'"
                icon="pi pi-plus"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white w-full"
                @click="triggerSelect"
                :disabled="maxFiles && files.length >= maxFiles"
            />
             <small v-if="multiple && maxFiles" class="text-gray-500 text-xs mt-1">
                {{ files.length }} / {{ maxFiles }} files selected
            </small>
        </div>

        <!-- Preview Area -->
        <div v-if="hasFiles" class="flex flex-wrap gap-3">
             <!-- Single File Preview -->
            <div v-if="!multiple && files[0]" class="relative w-full">
                <!-- Image Preview -->
                <img 
                    v-if="isImage(files[0])" 
                    :src="getPreviewUrl(files[0])" 
                    class="w-full h-auto max-h-[300px] object-contain rounded-lg border border-gray-200" 
                />
                <!-- Video Preview -->
                <video 
                    v-else-if="isVideo(files[0])" 
                    :src="getPreviewUrl(files[0])" 
                    controls 
                    class="w-full h-auto max-h-[300px] object-contain rounded-lg border border-gray-200"
                ></video>
                <!-- Generic File -->
                <div v-else class="p-4 bg-gray-100 rounded-lg flex items-center gap-2">
                    <i class="pi pi-file text-xl"></i>
                    <span class="text-sm truncate">{{ files[0].name }}</span>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 mt-2 justify-end">
                    <Button 
                        v-if="isImage(files[0]) && allowCrop" 
                        type="button" 
                        label="Crop" 
                        icon="pi pi-pencil" 
                        size="small" 
                        outlined 
                        @click="openCropper(files[0])" 
                    />
                    <Button 
                        type="button" 
                        label="Remove" 
                        icon="pi pi-trash" 
                        size="small" 
                        severity="danger" 
                        outlined 
                        @click="removeFile(0)" 
                    />
                </div>
            </div>

            <!-- Multiple Files Preview -->
            <div v-else v-for="(file, index) in files" :key="index" class="relative group w-24 h-24">
                 <img 
                    v-if="isImage(file)" 
                    :src="getPreviewUrl(file)" 
                    class="w-full h-full object-cover rounded-lg border border-gray-200" 
                />
                 <video 
                    v-else-if="isVideo(file)" 
                    :src="getPreviewUrl(file)" 
                    class="w-full h-full object-cover rounded-lg border border-gray-200"
                ></video>
                 <div v-else class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                    <i class="pi pi-file text-xl text-gray-400"></i>
                </div>

                <!-- Remove Button (Overlay) -->
                <button 
                    type="button"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity"
                    @click="removeFile(index)"
                >
                    <i class="pi pi-times text-[10px]"></i>
                </button>
            </div>
        </div>
        
        <!-- Cropper Modal -->
        <Dialog
            v-model:visible="showCropper"
            modal
            header="Crop Image"
            :style="{ width: '30rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :pt="{
                header: { class: '!bg-blue-600 !text-white' },
                closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <div v-if="cropperImageStr">
                <VuePictureCropper
                    :boxStyle="{
                        width: '100%',
                        height: '100%',
                        backgroundColor: '#f8f8f8',
                        margin: 'auto',
                    }"
                    :img="cropperImageStr"
                    :options="{
                        viewMode: 1,
                        dragMode: 'crop',
                        aspectRatio: aspectRatio,
                    }"
                />
                <div class="mt-4 flex justify-end gap-2 pt-4 border-t border-gray-200">
                    <Button
                        type="button"
                        label="Cancel"
                        severity="secondary"
                        text
                        @click="showCropper = false"
                    />
                    <Button
                        type="button"
                        label="Apply Crop"
                        icon="pi pi-check"
                        class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                        @click="applyCrop"
                    />
                </div>
            </div>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onUnmounted } from 'vue';
import { useToast } from 'vue-toastification';
import VuePictureCropper, { cropper } from 'vue-picture-cropper';

interface Props {
    modelValue: File | File[] | null;
    accept?: string;
    multiple?: boolean;
    maxFiles?: number;
    allowCrop?: boolean;
    aspectRatio?: number;
    label?: string;
    icon?: string;
    maxSizeMB?: number;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    accept: 'image/*, video/*',
    multiple: false,
    maxFiles: 0,
    allowCrop: true,
    aspectRatio: 1,
    label: 'Upload Media',
    icon: 'pi pi-cloud-upload',
    maxSizeMB: 10
});

const emit = defineEmits(['update:modelValue', 'error']);
const toast = useToast();
const fileInput = ref<HTMLInputElement | null>(null);

// Internal state
const files = ref<File[]>([]);
const previewUrls = ref<Map<File, string>>(new Map());
const hasError = ref(false);

// Cropper state
const showCropper = ref(false);
const cropperImageStr = ref<string>('');
const fileToCrop = ref<File | null>(null);

// Watch external model changes
watch(() => props.modelValue, (val) => {
    if (!val) {
        files.value = [];
        return;
    }
    
    if (Array.isArray(val)) {
        files.value = [...val];
    } else {
        files.value = [val];
    }
}, { immediate: true });

const hasFiles = computed(() => files.value.length > 0);

const triggerSelect = () => {
    if (fileInput.value) {
        fileInput.value.value = ''; // Reset
        fileInput.value.click();
    }
};

const handleFileSelect = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (!input.files || input.files.length === 0) return;

    const selected = Array.from(input.files);
    
    // Check max files
    if (props.multiple && props.maxFiles && (files.value.length + selected.length) > props.maxFiles) {
        toast.error(`Maximum ${props.maxFiles} files allowed.`);
        hasError.value = true;
        return;
    }

    // Process each file
    for (const file of selected) {
        // Validate type
        // (Simple check - accept prop does the browser filter, but double check is good)
        
        // Validate size
        if (file.size > props.maxSizeMB * 1024 * 1024) {
             toast.error(`File ${file.name} exceeds ${props.maxSizeMB}MB limit.`);
             continue;
        }

        // Check if image and needs cropping
        if (!props.multiple && props.allowCrop && isImage(file)) {
             openCropper(file);
             // For single mode with crop, we wait for crop to apply before setting file
             return; 
        }

        if (props.multiple) {
            files.value.push(file);
        } else {
            files.value = [file];
        }
    }
    
    emitUpdate();
};

const openCropper = (file: File) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        cropperImageStr.value = String(reader.result);
        fileToCrop.value = file;
        showCropper.value = true;
    };
};

const applyCrop = async () => {
    if (!cropper) return;
    
    const blob = await cropper.getBlob();
    if (!blob || !fileToCrop.value) return;
    
    const croppedFile = new File([blob], fileToCrop.value.name, { 
        type: fileToCrop.value.type,
        lastModified: new Date().getTime()
    });
    
    if (props.multiple) {
        // Find if we are replacing an existing file or adding new?
        // Simpler: Just add it if strict logic not needed.
        // But wait, if multiple=true, usually we crop one by one? 
        // For now, allowCrop=true is mostly for Single avatars.
        // If multiple is true, we disable crop usually.
        // But if forced:
        files.value.push(croppedFile);
    } else {
        files.value = [croppedFile];
    }
    
    showCropper.value = false;
    emitUpdate();
};

const removeFile = (index: number) => {
    files.value.splice(index, 1);
    emitUpdate();
};

const emitUpdate = () => {
    if (props.multiple) {
        emit('update:modelValue', files.value);
    } else {
        emit('update:modelValue', files.value[0] || null);
    }
};

// Utilities
const isImage = (file: File) => file.type.startsWith('image/');
const isVideo = (file: File) => file.type.startsWith('video/');

const getPreviewUrl = (file: File) => {
    if (previewUrls.value.has(file)) {
        return previewUrls.value.get(file);
    }
    const url = URL.createObjectURL(file);
    previewUrls.value.set(file, url);
    return url;
};

// Cleanup URLs to prevent leaks
onUnmounted(() => {
    previewUrls.value.forEach(url => URL.revokeObjectURL(url));
});
</script>

<style scoped>
.hidden {
    display: none;
}
</style>
