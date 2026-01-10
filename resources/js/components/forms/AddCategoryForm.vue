<template>
    <div>
        <form @submit.prevent="handleSubmit">
            <div class="p-2">
                <InputForm
                    :errors="errors.category_name"
                    id="category_name"
                    labelName="Category name*"
                    tag="label"
                >
                    <InputText
                        type="text"
                        v-model="form.category_name"
                        :invalid="errors.category_name.length > 0"
                        placeholder="Category name"
                        fluid
                        id="category_name"
                        autocomplete="off"
                    />
                </InputForm>
            </div>
            <div class="p-2">
                <InputForm
                    :errors="errors.category_image"
                    id="category_image"
                    label-name="Image"
                    tag="label"
                >
                    <input
                        ref="uploadInput"
                        type="file"
                        name="image"
                        id="image"
                        class="hidden"
                        accept="image/png, image/jpeg, image/jpg"
                        @change="onFileSelect"
                    />
                    <button
                        v-if="!form.category_image && !result.dataURL"
                        type="button"
                        class="w-full cursor-pointer border border-dotted border-sky-800 p-2"
                        :class="{
                            'border-red-500!': errors.category_image.length > 0,
                        }"
                        @click="selectImage()"
                    >
                        <div class="flex flex-col">
                            <i class="pi pi-image" />
                            <span class="text-sm">Select an Image</span>
                        </div>
                    </button>
                    <div v-else class="flex flex-col">
                        <img
                            :src="result.dataURL"
                            class="w-full"
                            alt="User Image"
                        />
                        <div class="mt-2 flex flex-wrap justify-center gap-2">
                            <Button
                                type="button"
                                label="Change Image"
                                icon="pi pi-pencil"
                                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                                @click="selectImage"
                            />
                            <Button
                                type="button"
                                label="Remove Image"
                                icon="pi pi-trash"
                                severity="danger"
                                @click="
                                    form.category_image = null;
                                    result.dataURL = '';
                                    result.blobURL = '';
                                "
                            />
                        </div>
                    </div>
                </InputForm>
            </div>
            <div class="flex justify-center p-2">
                <Button
                    type="submit"
                    label="Save"
                    icon="pi pi-save"
                    class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                    :loading="submitService.request.loading"
                />
            </div>
        </form>
        <Dialog
            v-model:visible="showCropperModal"
            modal
            header="Crop Image"
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
            <div class="mt-2 flex justify-center">
                <Button
                    type="button"
                    label="Crop Image"
                    icon="pi pi-image"
                    class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                    @click="getCropResult()"
                    :loading="imageProcessing"
                />
            </div>
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import {
    CategoryFormErrorInterface,
    CategoryFormInterface,
} from "@/interfaces/CategoryInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive, ref } from "vue";
import { useToast } from "vue-toastification";
import VuePictureCropper, { cropper } from "vue-picture-cropper";
import ValidatorUtil from "@/utils/ValidatorUtil";

const emit = defineEmits(["cb"]);
const toast = useToast();
const submitService = useAxiosUtil<FormData, null>();
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
const imageProcessing = ref<boolean>(false);

const form: CategoryFormInterface = reactive({
    category_name: null,
    category_image: null,
});
const errors: CategoryFormErrorInterface = reactive({
    category_name: [],
    category_image: [],
});

const selectImage = () => {
    if (uploadInput.value) {
        uploadInput.value.click();
    }
};

const onFileSelect = (e: Event) => {
    img.value = "";
    result.dataURL = "";
    result.blobURL = "";
    form.category_image = null;

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
    imageProcessing.value = true;
    if (!cropper) return;
    const base64 = cropper.getDataURL();
    const blob: Blob | null = await cropper.getBlob();
    if (!blob) return;
    const file = await cropper.getFile({
        fileName: "cropped-image",
    });
    form.category_image = file;
    result.dataURL = base64;
    result.blobURL = URL.createObjectURL(blob);
    showCropperModal.value = false;
    imageProcessing.value = false;
};

const clearError = () => {
    errors.category_name = [];
    errors.category_image = [];
};

const validate = (): FormData | false => {
    clearError();
    let formData = new FormData();
    if (!form.category_name) {
        errors.category_name.push("Category name is required");
    } else {
        formData.append("category_name", form.category_name);
    }
    if (!form.category_image) {
        errors.category_image.push("Category image is required");
    } else {
        if (!ValidatorUtil.isValidImageFormat(form.category_image)) {
            errors.category_image.push("Invalid image format");
        } else if (!ValidatorUtil.isValidImageSize(form.category_image)) {
            errors.category_image.push("Image size must be less than 2MB");
        } else {
            formData.append("category_image", form.category_image);
        }
    }
    const hasErrors = [
        errors.category_name.length > 0,
        errors.category_image.length > 0,
    ];
    return hasErrors.includes(true) ? false : formData;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService.postFormData("admin/categories", data).then(() => {
            if (
                submitService.request.status === 200 &&
                submitService.request.data
            ) {
                toast.success(submitService.request.message);
                emit("cb");
            } else {
                toast.error(
                    submitService.request.message ?? "Please try again",
                );
                if (submitService.request.errors) {
                    Object.keys(submitService.request.errors).forEach((key) => {
                        errors[key] = submitService.request.errors[key];
                    });
                }
            }
        });
    } else {
        toast.error("Please fill in the required fields.");
    }
};
</script>
