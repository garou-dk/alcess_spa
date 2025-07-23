<template>
    <div>
        <button 
            type="button"
            class="border border-dotted rounded border-sky-800 h-28 w-28 hover:bg-gray-100 cursor-pointer"
            @click="selectImage()"
        >
            <i class="pi pi-plus" />
        </button>
        <input
            ref="uploadInput"
            type="file"
            name="image"
            id="image"
            class="hidden"
            accept="image/png, image/jpeg, image/jpg"
            @change="onFileSelect"
        />
        <Dialog
            v-model:visible="showCropperModal"
            modal
            header="Crop Image"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
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
            <div class="mt-2 flex justify-center">
                <Button
                    type="button"
                    label="Crop Image"
                    icon="pi pi-image"
                    class="primary-bg"
                    @click="getCropResult()"
                />
            </div>
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { FeaturedImageFormErrorInterface, FeaturedImageFormInterface } from '@/interfaces/FeaturedImageInterface';
import { ProductInterface } from '@/interfaces/ProductInterface';
import { reactive, ref } from 'vue';
import VuePictureCropper, { cropper } from "vue-picture-cropper";

interface Props {
    data: ProductInterface;
}

const form : FeaturedImageFormInterface = reactive({
    featured_image: null
});

const errors : FeaturedImageFormErrorInterface = reactive({
    featured_image: [],
});

const props = defineProps<Props>();
const img = ref<string>("");
const result: {
    dataURL: string;
    blobURL: string;
} = reactive({
    dataURL: "",
    blobURL: "",
});
const uploadInput = ref<HTMLInputElement | null>(null);
const showCropperModal = ref<boolean>(false);

const selectImage = () => {
    if (uploadInput.value) {
        uploadInput.value.click();
    }
};

const onFileSelect = (e: Event) => {
    img.value = "";
    result.dataURL = "";
    result.blobURL = "";
    form.featured_image = null;

    const { files } = e.target as HTMLInputElement;
    if (!files || !files.length) return;

    const file = files[0];
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        img.value = String(reader.result);
        showCropperModal.value = true;
        if (!uploadInput.value) return;
        uploadInput.value.value = "";
    };
};

const getCropResult = async () => {
    if (!cropper) return;
    const base64 = cropper.getDataURL();
    const blob: Blob | null = await cropper.getBlob();
    if (!blob) return;
    const file = await cropper.getFile({
        fileName: "cropped-image",
    });
    result.dataURL = base64;
    result.blobURL = URL.createObjectURL(blob);
    form.featured_image = file;
    showCropperModal.value = false;
};

const resetForm = () => {
    img.value = "";
    result.dataURL = "";
    result.blobURL = "";
    form.featured_image = null;
};

const clearErrors = () => {
    errors.featured_image = [];
};

 
</script>