<template>
    <form @submit.prevent="handleSubmit">
        <div class="p-2 mb-2">
            <InputForm
                :errors="errors.product_image"
                id="product_image"
                label-name="Image"
                tag="label"
            >
                <input
                    ref="uploadInput"
                    type="file"
                    name="product_image"
                    id="product_image"
                    class="hidden"
                    accept="image/png, image/jpeg, image/jpg"
                    @change="onFileSelect"
                />
                <div v-if="!form.product_image && !result.dataURL && props.data.product_image" class="flex justify-center mb-2">
                    <Image
                        :src="UrlUtil.getBaseAppUrl(`storage/images/product/${props.data.product_image}`)"
                        class="w-64 h-64 object-cover rounded border border-gray-300 flex justify-center"
                    />
                </div>
                <button
                    v-if="!form.product_image && !result.dataURL"
                    type="button"
                    class="w-full cursor-pointer border border-dotted border-sky-800 p-2"
                    @click="selectImage()"
                >
                    <div class="flex flex-col">
                        <i class="pi pi-image" />
                        <span class="text-sm">Select an Image</span>
                    </div>
                </button>
                <div v-else class="flex flex-col">
                    <div class="flex justify-center">
                        <img
                            :src="result.dataURL"
                            class="w-64 h-64 object-cover rounded border border-gray-300"
                            alt="User Image"
                        />
                    </div>
                    <div class="mt-2 flex flex-wrap justify-center gap-2">
                        <Button
                            type="button"
                            label="Change Image"
                            icon="pi pi-pencil"
                            class="primary-bg"
                            @click="selectImage"
                        />
                        <Button
                            type="button"
                            label="Remove Image"
                            icon="pi pi-trash"
                            severity="danger"
                            @click="
                                form.product_image = null;
                                result.dataURL = '';
                                result.blobURL = '';
                            "
                        />
                    </div>
                </div>
            </InputForm>
        </div>
        <div v-if="form.product_image" class="flex justify-center">
            <Button
                type="submit"
                label="Save Image"
                icon="pi pi-save"
                class="primary-bg"
                :loading="submitService.request.loading"
            />
        </div>
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
    </form>
</template>
<script setup lang="ts">
import { ProductInterface, UpdateProductImageFormErrorInterface, UpdateProductImageFormInterface } from '@/interfaces/ProductInterface';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import { reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';
import VuePictureCropper, { cropper } from "vue-picture-cropper";

interface Props {
    data: ProductInterface;
}

const submitService = useAxiosUtil<FormData, any>();
const toast = useToast();
const emit = defineEmits(['cb']);
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

const form : UpdateProductImageFormInterface = reactive({
    product_image: null,
});

const errors : UpdateProductImageFormErrorInterface = reactive({
    product_image: [],
});

const clearForm = () => {
    form.product_image = null;
    img.value = "";
    result.dataURL = "";
    result.blobURL = "";
};

const selectImage = () => {
    if (uploadInput.value) {
        uploadInput.value.click();
    }
};

const onFileSelect = (e: Event) => {
    img.value = "";
    result.dataURL = "";
    result.blobURL = "";
    form.product_image = null;

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
    form.product_image = file;
    showCropperModal.value = false;
};

const clearErrors = () => {
    errors.product_image = [];
};

const validate = () => {
    clearErrors();
    let data = new FormData();
    if (!form.product_image) {
        errors.product_image.push('Product image is required.');
    }
    else {
        data.append('product_image', form.product_image);
    }

    const isValid = [errors.product_image.length > 0];
    return isValid.includes(true) ? false : data;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService.patchFormData(`admin/products/update-image/${props.data.product_id}`, data).then(() => {
            if (submitService.request.status === 200 && submitService.request.data) {
                toast.success(submitService.request.message);
                emit('cb', submitService.request.data);
                clearForm();
            } else {
                toast.error(submitService.request.message ?? 'Please try again');
                if (submitService.request.errors) {
                    errors.product_image = submitService.request.errors.product_image || [];
                }
            }
        });
    }
    else {
        toast.error('Please fill in all required fields and try again.');
    }
};
</script>