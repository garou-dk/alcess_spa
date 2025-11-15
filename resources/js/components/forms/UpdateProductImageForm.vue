<template>
    <form @submit.prevent="handleSubmit" class="p-6 max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Product Image</h2>
            <p class="text-sm text-gray-600">Upload or update your product image</p>
        </div>

        <!-- Image Upload Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-2 p-2">
                <InputForm
                    :errors="errors.product_image"
                    id="product_image"
                    label-name="Product Image"
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

                    <!-- Current Image Display -->
                    <div
                        v-if="!form.product_image && !result.dataURL && props.data.product_image"
                        class="mb-2 flex justify-center"
                    >
                        <Image
                            :src="UrlUtil.getBaseAppUrl(`storage/images/product/${props.data.product_image}`)"
                            class="h-64 w-64 rounded border border-gray-300 object-cover"
                        />
                    </div>

                    <!-- Upload Prompt -->
                    <button
                        v-if="!form.product_image && !result.dataURL"
                        type="button"
                        class="w-full cursor-pointer border-2 border-dashed border-blue-400 rounded-lg p-12 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                        @click="selectImage()"
                    >
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-200">
                                <i class="pi pi-cloud-upload text-3xl text-blue-600"></i>
                            </div>
                            <span class="text-lg font-semibold text-gray-800">Select an Image</span>
                            <span class="text-sm text-gray-600">PNG, JPG or JPEG</span>
                        </div>
                    </button>

                    <!-- Preview Section -->
                    <div v-else class="flex flex-col">
                        <div class="flex justify-center mb-4">
                            <div class="relative">
                                <img
                                    :src="result.dataURL"
                                    class="h-64 w-64 rounded-lg border-2 border-green-400 object-cover shadow-lg"
                                    alt="User Image"
                                />
                                <div class="absolute top-2 right-2 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-md flex items-center gap-1">
                                    <i class="pi pi-check"></i>
                                    Ready
                                </div>
                            </div>
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
        </div>

        <!-- Save Button -->
        <div v-if="form.product_image" class="flex justify-center mt-6">
            <Button
                type="submit"
                label="Save Image"
                icon="pi pi-save"
                class="primary-bg px-6 py-2"
                :loading="submitService.request.loading"
            />
        </div>

        <!-- Cropper Modal -->
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
import {
    ProductInterface,
    UpdateProductImageFormErrorInterface,
    UpdateProductImageFormInterface,
} from "@/interfaces/ProductInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import UrlUtil from "@/utils/UrlUtil";
import { reactive, ref } from "vue";
import { useToast } from "vue-toastification";
import VuePictureCropper, { cropper } from "vue-picture-cropper";

interface Props {
    data: ProductInterface;
}

const submitService = useAxiosUtil<FormData, unknown>();
const toast = useToast();
const emit = defineEmits(["cb"]);
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

const form: UpdateProductImageFormInterface = reactive({
    product_image: null,
});

const errors: UpdateProductImageFormErrorInterface = reactive({
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
        errors.product_image.push("Product image is required.");
    } else {
        data.append("product_image", form.product_image);
    }

    const isValid = [errors.product_image.length > 0];
    return isValid.includes(true) ? false : data;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .patchFormData(
                `admin/products/update-image/${props.data.product_id}`,
                data,
            )
            .then(() => {
                if (
                    submitService.request.status === 200 &&
                    submitService.request.data
                ) {
                    toast.success(submitService.request.message);
                    emit("cb", submitService.request.data);
                    clearForm();
                } else {
                    toast.error(
                        submitService.request.message ?? "Please try again",
                    );
                    if (submitService.request.errors) {
                        errors.product_image =
                            submitService.request.errors.product_image || [];
                    }
                }
            });
    } else {
        toast.error("Please fill in all required fields and try again.");
    }
};
</script>