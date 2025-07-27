<template>
    <div>
        <div v-if="!submitService.request.loading">
            <button
                type="button"
                class="h-28 w-28 cursor-pointer rounded border border-dotted border-sky-800 hover:bg-gray-100"
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
                accept="image/png, image/jpeg, image/jpg, video/mp4"
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
        <div v-else>
            <button
                type="button"
                class="h-28 w-28 cursor-pointer rounded border border-dotted border-sky-800 bg-gray-500 hover:bg-gray-100"
            >
                <i
                    class="pi pi-spin pi-spinner"
                    style="font-size: 2rem; color: white"
                ></i>
            </button>
        </div>
    </div>
</template>
<script setup lang="ts">
import {
    FeaturedImageFormErrorInterface,
    FeaturedImageFormInterface,
} from "@/interfaces/FeaturedImageInterface";
import { ProductInterface } from "@/interfaces/ProductInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import ValidatorUtil from "@/utils/ValidatorUtil";
import { reactive, ref } from "vue";
import VuePictureCropper, { cropper } from "vue-picture-cropper";
import { useToast } from "vue-toastification";

interface Props {
    data: ProductInterface;
}

const form: FeaturedImageFormInterface = reactive({
    featured_image: null,
});

const errors: FeaturedImageFormErrorInterface = reactive({
    featured_image: [],
});

const props = defineProps<Props>();
const emit = defineEmits(["cb"]);
const submitService = useAxiosUtil<FormData, ProductInterface>();
const toast = useToast();
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
    const fileType = ValidatorUtil.isImageOrMp4(file);
    if (!fileType) {
        toast.error(
            "Invalid file type, please select a jpeg, jpg, png or mp4 file",
        );
        return;
    }

    if (fileType === "video/mp4") {
        form.featured_image = file;
        handleSubmit();
    } else {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            img.value = String(reader.result);
            showCropperModal.value = true;
            if (!uploadInput.value) return;
            uploadInput.value.value = "";
        };
    }
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

const validate = (): FormData | false => {
    clearErrors();
    let formData = new FormData();
    if (ValidatorUtil.isEmpty(form.featured_image)) {
        errors.featured_image.push("Featured image is required");
    } else if (!ValidatorUtil.isImageOrMp4(form.featured_image)) {
        errors.featured_image.push("Invalid image format");
    } else if (!ValidatorUtil.checkSizeValidation(form.featured_image, 20)) {
        errors.featured_image.push(
            "Image size must be less than or equal to 20MB",
        );
    } else {
        formData.append("featured_image", form.featured_image);
    }
    const hasErrors = [errors.featured_image.length > 0];
    return hasErrors.includes(true) ? false : formData;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .postFormData(
                `admin/featured-images/${props.data.product_id}`,
                data,
            )
            .then(() => {
                if (submitService.request.status === 200) {
                    toast.success(submitService.request.message);
                    emit("cb", submitService.request.data);
                } else {
                    toast.error(
                        submitService.request.message ?? "Please try again",
                    );
                    if (submitService.request.errors) {
                        Object.keys(submitService.request.errors).forEach(
                            (key) => {
                                errors[key] = submitService.request.errors[key];
                            },
                        );
                    }
                }
            });
        resetForm();
    } else {
        toast.error("Please fill in the required fields.");
    }
};
</script>
